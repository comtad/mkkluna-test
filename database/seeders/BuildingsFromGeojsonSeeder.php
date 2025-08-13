<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use MatanYadaev\EloquentSpatial\Objects\Point;

class BuildingsFromGeojsonSeeder extends Seeder
{
    public function run()
    {
        $filePath = database_path('dataset/source.geojson');

        if (!File::exists($filePath)) {
            $this->command->error("Файл GeoJSON не найден: {$filePath}");
            return;
        }

        $counter = 0;
        $errors = 0;
        $maxRecords = 100;

        File::lines($filePath)
            ->take($maxRecords)
            ->map(function ($line) {
                return json_decode(trim($line), true);
            })
            ->filter()
            ->each(function ($data) use (&$counter, &$errors) {
                try {
                    if (($data['type'] ?? '') !== 'Feature' ||
                        ($data['geometry']['type'] ?? '') !== 'Point' ||
                        count($data['geometry']['coordinates'] ?? []) !== 2)
                    {
                        throw new \Exception('Неверная структура GeoJSON');
                    }

                    $address = Collection::make([
                        'city' => $data['properties']['city'] ?? '',
                        'street' => $data['properties']['street'] ?? '',
                        'number' => $data['properties']['number'] ?? '',
                        'unit' => $data['properties']['unit'] ?? '',
                    ])->filter()->join(', ');

                    Building::create([
                        'address' => $address,
                        'coordinates' => new Point(
                            $data['geometry']['coordinates'][1], // lat
                            $data['geometry']['coordinates'][0]  // lng
                        )
                    ]);

                    $counter++;
                    $this->command->info("Добавлено здание: {$address}");
                } catch (\Exception $e) {
                    $errors++;
                    $this->command->warn("Ошибка обработки записи: " . $e->getMessage());
                }
            });

        $this->command->info("Успешно импортировано {$counter} зданий");
        if ($errors > 0) {
            $this->command->warn("Возникло {$errors} ошибок во время импорта");
        }
    }
}