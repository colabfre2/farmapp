<?php

namespace Database\Seeders;

use App\Models\CropType;
use Illuminate\Database\Seeder;

class CropTypeSeeder extends Seeder
{
    public function run(): void
    {
        $crops = [
            ['name' => 'Padi', 'description' => 'Tanaman pangan utama', 'harvest_type' => 'Sekali Panen'],
            ['name' => 'Kangkung', 'description' => 'Sayur daun cepat panen', 'harvest_type' => 'Sekali Panen'],
            ['name' => 'Cabai Rawit 🌶️', 'description' => 'Bisa dipanen bertahap', 'harvest_type' => 'Panen Berkelanjutan'],
            ['name' => 'Tomat Apel', 'description' => 'Panen berkala setiap musim', 'harvest_type' => 'Panen Berkelanjutan'],
        ];

        foreach ($crops as $crop) {
            CropType::create($crop);
        }
    }
}