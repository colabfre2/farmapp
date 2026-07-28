<?php

namespace Database\Seeders;

use App\Models\LivestockType;
use Illuminate\Database\Seeder;

class LivestockTypeSeeder extends Seeder
{
    public function run(): void
    {
        $livestocks = [
            ['name' => 'Sapi Potong (Limousin)', 'description' => 'Fokus untuk penggemukan dan daging'],
            ['name' => 'Sapi Perah (FH)', 'description' => 'Fokus untuk produksi susu harian'],
            ['name' => 'Kambing Etawa', 'description' => 'Ternak perah dan pedaging'],
            ['name' => 'Ayam Petelur', 'description' => 'Fokus untuk produksi telur'],
        ];

        foreach ($livestocks as $livestock) {
            LivestockType::create($livestock);
        }
    }
}