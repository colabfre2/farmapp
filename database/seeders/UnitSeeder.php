<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Kilogram', 'symbol' => 'Kg'],
            ['name' => 'Gram', 'symbol' => 'g'],
            ['name' => 'Liter', 'symbol' => 'L'],
            ['name' => 'Ekor', 'symbol' => 'ekor'],
            ['name' => 'Karung', 'symbol' => 'krg'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}