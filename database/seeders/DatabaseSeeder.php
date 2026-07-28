<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            // 1. MASTER DATA UTAMA 
            UserSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            CropTypeSeeder::class,
            LivestockTypeSeeder::class,
            ExpenseCategorySeeder::class,
            IncomeSourceSeeder::class,
            FarmSeeder::class,
            
            // --- MASTER DATA BARU ---
            PlantCareSeeder::class,
            MedicineSeeder::class,
            FeedSeeder::class,
            // ------------------------

            // 2. MASTER DATA SEKUNDER 
            CropVarietySeeder::class,
            KandangSeeder::class,
            

            // 3. DATA OPERASIONAL/TRANSAKSIONAL
            ProductSeeder::class,
            CropSeeder::class,
            LivestockSeeder::class,
        ]);
    }
}