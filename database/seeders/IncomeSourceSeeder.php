<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncomeSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('income_sources')->insert([
    ['name' => 'Penjualan Marketplace',    'description' => 'Penjualan via marketplace online'],
    ['name' => 'Penjualan Offline',        'description' => 'Penjualan langsung ke pembeli'],
    ['name' => 'Penjualan ke Distributor', 'description' => 'Penjualan ke distributor atau agen'],
    ['name' => 'Hasil Panen Langsung',     'description' => 'Penjualan hasil panen langsung'],
    ['name' => 'Lainnya',                  'description' => 'Sumber pemasukan lainnya'],
]);
    }
}
