<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CropSeeder extends Seeder
{
    public function run(): void
    {
        $crops = [
            [
                'user_id' => 1,
                'crop_type_id' => 1, // Padi
                'crop_variety_id' => 1, // Ciherang
                'farm_id' => 1,
                'name' => 'Penanaman Padi Musim Hujan',
                'planted_at' => Carbon::now()->subDays(30),
                'expected_harvest_at' => Carbon::now()->addDays(60),
                'status' => 'Pertumbuhan',
                'notes' => 'Perlu pupuk urea tambahan minggu depan',
            ],
            [
                'user_id' => 1,
                'crop_type_id' => 3, // Cabai
                'crop_variety_id' => 3, // Rawit Setan
                'farm_id' => 2,
                'name' => 'Cabai Greenhouse Blok B',
                'planted_at' => Carbon::now()->subDays(10),
                'expected_harvest_at' => Carbon::now()->addDays(80),
                'status' => 'Bibit',
                'notes' => 'Irigasi tetes sudah aktif',
            ]
        ];

        foreach ($crops as $crop) {
            Crop::create($crop);
        }
    }
}