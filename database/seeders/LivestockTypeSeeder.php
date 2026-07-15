<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LivestockTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
       DB::table('livestock_types')->insert([
    ['name' => 'Ayam Broiler', 'description' => 'Ayam pedaging'],
    ['name' => 'Bebek',        'description' => 'Bebek lokal'],
    ['name' => 'Kambing',      'description' => 'Kambing potong dan perah'],
    ['name' => 'Sapi Perah',   'description' => 'Sapi penghasil susu'],
    ['name' => 'Domba',        'description' => 'Domba wool dan daging'],
]);
    }
}
