<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ActivitiesSeeder extends Seeder
{
    const MAX_DEPTH = 3;

    public function run()
    {
        Activity::truncate();

        $filePath = database_path('dataset/categories.json');

        if (!File::exists($filePath)) {
            $this->command->error("Файл категорий не найден: {$filePath}");
            return;
        }

        $jsonContent = File::get($filePath);
        $categories = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("Неверный формат JSON: " . json_last_error_msg());
            return;
        }

        Collection::make($categories)->each(function ($category) {
            $this->createCategory($category, null, 0);
        });

        $this->command->info('Успешно создано дерево видов деятельности');
    }

    protected function createCategory(array $data, ?Activity $parent = null, int $depth = 0)
    {
        if ($depth >= self::MAX_DEPTH) {
            $this->command->warn("Достигнута максимальная глубина для: {$data['name']}");
            return;
        }

        $activity = Activity::create([
            'name' => $data['name'],
            'parent_id' => $parent ? $parent->id : null
        ]);

        $this->command->info(str_repeat('  ', $depth) . "Создано: {$activity->name} (глубина: {$depth})");

        if (isset($data['subcategories'])) {
            Collection::make($data['subcategories'])->each(function ($subcategory) use ($activity, $depth) {
                if (is_string($subcategory)) {
                    $this->createCategory([
                        'name' => $subcategory
                    ], $activity, $depth + 1);
                } elseif (is_array($subcategory)) {
                    $this->createCategory($subcategory, $activity, $depth + 1);
                }
            });
        }
    }
}