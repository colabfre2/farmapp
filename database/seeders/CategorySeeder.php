<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('categories')->insert([
    ['name' => 'Sayuran',  'description' => 'Sayuran segar dari kebun'],
    ['name' => 'Buah-buahan', 'description' => 'Buah tropis dan musiman'],
    ['name' => 'Beras', 'description' => 'Semua jenis beras'],
    ['name' => 'Telur', 'description' => 'Telur unggas'],
    ['name' => 'Ayam', 'description' => 'Produk unggas'],
    ['name' => 'Susu', 'description' => 'Produk susu segar'],
]);
    }
}
