<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
class CropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    
        DB::table('crops')->insert([
    ['user_id'=>1,'crop_type_id'=>1,'name'=>'Sawah Blok A','planted_at'=>'2024-03-01','expected_harvest_at'=>'2024-07-15','status'=>'Pertumbuhan','created_at'=>now(),'updated_at'=>now()],
    ['user_id'=>1,'crop_type_id'=>3,'name'=>'Kebun Cabai B','planted_at'=>'2024-04-10','expected_harvest_at'=>'2024-08-20','status'=>'Pertumbuhan','created_at'=>now(),'updated_at'=>now()],
    ['user_id'=>1,'crop_type_id'=>2,'name'=>'Ladang Jagung C','planted_at'=>'2024-02-20','expected_harvest_at'=>'2024-06-10','status'=>'Dipanen','created_at'=>now(),'updated_at'=>now()],
    ['user_id'=>1,'crop_type_id'=>4,'name'=>'Greenhouse Tomat','planted_at'=>'2024-05-01','expected_harvest_at'=>'2024-09-01','status'=>'Dipanen','created_at'=>now(),'updated_at'=>now()],
]);
    }
}
