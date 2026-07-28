<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $expenses = [
            ['name' => 'Pakan Ternak', 'description' => 'Beli konsentrat dan rumput'],
            ['name' => 'Pupuk & Pestisida', 'description' => 'Perawatan lahan pertanian'],
            ['name' => 'Benih & Bibit', 'description' => 'Modal awal tanam/ternak'],
            ['name' => 'Gaji Karyawan', 'description' => 'Upah pekerja harian/bulanan'],
        ];

        foreach ($expenses as $exp) {
            ExpenseCategory::create($exp);
        }
    }
}