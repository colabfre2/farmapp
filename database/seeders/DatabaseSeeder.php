<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            UnitSeeder::class,
            CropTypeSeeder::class,
            LivestockTypeSeeder::class,
            ExpenseCategorySeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            CropSeeder::class,
            LivestockSeeder::class,
            IncomeSourceSeeder::class,
        ]);
    }
}
