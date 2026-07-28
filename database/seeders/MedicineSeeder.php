<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = [
            ['name' => 'Vaksin PMK', 'type' => 'Vaksin', 'unit_id' => 3, 'stock' => 20, 'price_per_unit' => 150000, 'description' => 'Vaksin wajib Penyakit Mulut dan Kuku'],
            ['name' => 'Vitamin B-Complex', 'type' => 'Vitamin', 'unit_id' => 3, 'stock' => 30, 'price_per_unit' => 45000, 'description' => 'Injeksi vitamin untuk nafsu makan sapi'],
            ['name' => 'Antibiotik LA', 'type' => 'Antibiotik', 'unit_id' => 3, 'stock' => 15, 'price_per_unit' => 85000, 'description' => 'Antibiotik long-acting untuk infeksi'],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
    }
}