<?php

namespace Database\Seeders;

use App\Models\Livestock;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LivestockSeeder extends Seeder
{
    public function run(): void
    {
        $livestocks = [
            [
                'user_id' => 1,
                'livestock_type_id' => 1, // Sapi Potong
                'arrival_date' => Carbon::now()->subMonths(2),
                'name' => 'Batch Sapi Limousin Q1',
                'quantity' => 15,
                'avg_weight' => '350',
                'health_status' => 'Sehat',
                'notes' => 'Vaksin PMK dosis 1 sudah selesai',
            ],
            [
                'user_id' => 1,
                'livestock_type_id' => 2, // Sapi Perah
                'arrival_date' => Carbon::now()->subMonths(6),
                'name' => 'Sapi Perah FH Kandang A',
                'quantity' => 10,
                'avg_weight' => '400',
                'health_status' => 'Pemantauan',
                'notes' => 'Satu ekor perlu cek kuku',
            ]
        ];

        foreach ($livestocks as $livestock) {
            Livestock::create($livestock);
        }
    }
}