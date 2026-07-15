<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
       DB::table('units')->insert([
    ['name' => 'Kilogram', 'symbol' => 'kg'],
    ['name' => 'Gram',     'symbol' => 'g'],
    ['name' => 'Liter',    'symbol' => 'L'],
    ['name' => 'Buah',     'symbol' => 'bh'],
    ['name' => 'Lusin',    'symbol' => 'lsn'],
    ['name' => 'Ikat',     'symbol' => 'ikat'],
]);
    }
}
