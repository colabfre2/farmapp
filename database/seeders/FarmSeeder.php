<?php

namespace Database\Seeders;

use App\Models\Farm;
use Illuminate\Database\Seeder;

class FarmSeeder extends Seeder
{
    public function run(): void
    {
        $farms = [
            ['name' => 'Blok A - Lahan Padi', 'area_size' => 1000.00, 'area_unit' => 'm²', 'description' => 'Lahan basah area depan'],
            ['name' => 'Blok B - Sayur Mayur', 'area_size' => 500.00, 'area_unit' => 'm²', 'description' => 'Lahan kering untuk hortikultura'],
            ['name' => 'Kandang Sapi Utama', 'area_size' => 200.00, 'area_unit' => 'm²', 'description' => 'Kapasitas maksimal 20 ekor'],
            ['name' => 'Greenhouse Hortikultura', 'area_size' => 150.00, 'area_unit' => 'm²', 'description' => 'Area irigasi tetes otomatis'],
        ];

        foreach ($farms as $farm) {
            Farm::create($farm);
        }
    }
}