<?php

namespace Database\Seeders;

use App\Models\PlantCare;
use Illuminate\Database\Seeder;

class PlantCareSeeder extends Seeder
{
    public function run(): void
    {
        $cares = [
            ['name' => 'Pupuk Urea', 'type' => 'Pupuk', 'unit_id' => 1, 'stock' => 100, 'price_per_unit' => 5000, 'description' => 'Pupuk nitrogen untuk daun'],
            ['name' => 'Pestisida Nabati', 'type' => 'Pestisida', 'unit_id' => 3, 'stock' => 50, 'price_per_unit' => 35000, 'description' => 'Pembasmi hama organik'],
            ['name' => 'Pupuk Kompos', 'type' => 'Pupuk', 'unit_id' => 1, 'stock' => 200, 'price_per_unit' => 2000, 'description' => 'Pupuk dasar dari kotoran ternak'],
        ];

        foreach ($cares as $care) {
            PlantCare::create($care);
        }
    }
}