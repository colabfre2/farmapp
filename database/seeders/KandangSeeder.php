<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kandang;

class KandangSeeder extends Seeder
{
    public function run(): void
    {
        Kandang::insert([
            [
                'livestock_type_id' => 4,
                'name' => 'Kandang Ayam #1',
                'capacity' => 300,
                'location' => 'Area Utara',
                'description' => 'Kandang khusus ayam petelur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'livestock_type_id' => 4,
                'name' => 'Kandang Ayam #2',
                'capacity' => 300,
                'location' => 'Area Utara',
                'description' => 'Kandang cadangan untuk rotasi batch',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'livestock_type_id' => 3,
                'name' => 'Kandang Kambing #1',
                'capacity' => 40,
                'location' => 'Area Selatan',
                'description' => 'Kandang panggung, mudah dibersihkan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'livestock_type_id' => 1,
                'name' => 'Kandang Sapi Potong',
                'capacity' => 20,
                'location' => 'Area Barat',
                'description' => 'Kandang sapi potong Limousin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'livestock_type_id' => 2,
                'name' => 'Kandang Sapi Perah',
                'capacity' => 15,
                'location' => 'Area Timur',
                'description' => 'Kandang sapi perah dengan sistem pemerahan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}