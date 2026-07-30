<?php

namespace Database\Seeders;

use App\Models\CropVariety;
use Illuminate\Database\Seeder;

class CropVarietySeeder extends Seeder
{
    public function run(): void
    {
        $varieties = [
            ['crop_type_id' => 1, 'name' => 'Padi Ciherang', 'description' => 'Tahan hama wereng'],
            ['crop_type_id' => 1, 'name' => 'Padi IR64', 'description' => 'Umur panen lebih singkat'],
            ['crop_type_id' => 3, 'name' => 'Cabai Rawit Setan', 'description' => 'Tingkat kepedasan tinggi'],
            ['crop_type_id' => 4, 'name' => 'Tomat Cherry', 'description' => 'Cocok untuk salad'],
        ];

        foreach ($varieties as $variety) {
            CropVariety::create($variety);
        }
    }
}