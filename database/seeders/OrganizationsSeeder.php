<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use App\Models\OrganizationPhone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OrganizationsSeeder extends Seeder
{
    public function run()
    {
        DB::table('organization_phones')->delete();
        DB::table('activity_organization')->delete();
        DB::table('organizations')->delete();

        $faker = Faker::create('ru_RU');

        $buildings = Building::all();

        $activities = Activity::with('children.children')->get();

        $leafActivities = $activities->filter(function ($activity) {
            return $activity->children->isEmpty() ||
                $activity->children->every(function ($child) {
                    return $child->children->isEmpty();
                });
        });

        for ($i = 0; $i < 3000; $i++) {
            $building = $buildings->random();

            $organization = Organization::create([
                'name' => $faker->company,
                'building_id' => $building->id,
            ]);

            $phoneCount = rand(1, 3);
            for ($j = 0; $j < $phoneCount; $j++) {
                OrganizationPhone::create([
                    'organization_id' => $organization->id,
                    'number' => $faker->unique()->phoneNumber(),
                ]);
            }

            $activityCount = rand(1, 10);
            $selectedActivities = collect();

            for ($k = 0; $k < $activityCount; $k++) {
                $activity = $leafActivities->random();

                $chain = collect();

                if ($activity->children->isNotEmpty()) {
                    $grandchild = $activity->children->random();
                    $chain->push($grandchild);

                    $chain->push($activity);

                    if ($activity->parent) {
                        $chain->push($activity->parent);
                    }
                }
                elseif ($activity->children->isEmpty() && $activity->parent) {
                    $chain->push($activity);
                    $chain->push($activity->parent);
                }
                else {
                    $chain->push($activity);
                }

                $selectedActivities = $selectedActivities->merge($chain);
            }

            $organization->activities()->attach(
                $selectedActivities->unique('id')->pluck('id')
            );

            if ($i % 100 === 0) {
                $this->command->info("Создано {$i} организаций");
            }
        }

        $this->command->info("Успешно создано 3000 организаций");
    }
}