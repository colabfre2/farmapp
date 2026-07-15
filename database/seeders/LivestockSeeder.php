<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LivestockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    
        DB::table('livestocks')->insert([
    ['user_id'=>1,'livestock_type_id'=>1,'name'=>'Kandang Ayam #1','quantity'=>250,'avg_weight'=>'2,1 kg rata-rata','health_status'=>'SehaT','created_at'=>now(),'updated_at'=>now()],
    ['user_id'=>1,'livestock_type_id'=>2,'name'=>'Kolam Bebek #1','quantity'=>80,'avg_weight'=>'1,8 kg rata-rata','health_status'=>'Sakit','created_at'=>now(),'updated_at'=>now()],
    ['user_id'=>1,'livestock_type_id'=>3,'name'=>'Kandang Kambing #2','quantity'=>30,'avg_weight'=>'35 kg rata-rata','health_status'=>'Pemantauan','created_at'=>now(),'updated_at'=>now()],
    ['user_id'=>1,'livestock_type_id'=>4,'name'=>'Kandang Sapi A','quantity'=>12,'avg_weight'=>'420 kg rata-rata','health_status'=>'Sehat','created_at'=>now(),'updated_at'=>now()],
]);
    }
}
