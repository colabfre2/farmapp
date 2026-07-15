<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CropTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
       DB::table('crop_types')->insert([
    ['name' => 'Padi',   'description' => 'Padi dan beras olahan'],
    ['name' => 'Jagung', 'description' => 'Jagung kuning dan putih'],
    ['name' => 'Cabai',  'description' => 'Berbagai jenis cabai'],
    ['name' => 'Tomat',  'description' => 'Tomat merah dan ceri'],
    ['name' => 'Bawang', 'description' => 'Bawang merah dan putih'],
]);
    }
}
