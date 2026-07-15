<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
       
        DB::table('expense_categories')->insert([
    ['name' => 'Pakan',          'description' => 'Biaya pakan ternak'],
    ['name' => 'Pupuk',          'description' => 'Pupuk tanah'],
    ['name' => 'Bibit',          'description' => 'Bibit tanaman'],
    ['name' => 'Obat-obatan',    'description' => 'Obat hewan dan tanaman'],
    ['name' => 'Transportasi',   'description' => 'Ongkos pengiriman'],
]);
    }
}
