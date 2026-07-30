<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    
        DB::table('products')->insert([
    ['user_id'=>1,'category_id'=>1,'unit_id'=>1,'name'=>'Tomat Organik Segar','description'=>'Tomat matang dari kebun organik.','price'=>15000,'stock'=>150,'is_active'=>1,'created_at'=>now(),'updated_at'=>now()],
    ['user_id'=>1,'category_id'=>3,'unit_id'=>1,'name'=>'Beras Premium','description'=>'Beras pulen berkualitas tinggi.','price'=>12000,'stock'=>500,'is_active'=>1,'created_at'=>now(),'updated_at'=>now()],
    ['user_id'=>1,'category_id'=>4,'unit_id'=>5,'name'=>'Telur Ayam Kampung','description'=>'Telur ayam kampung segar.','price'=>35000,'stock'=>200,'is_active'=>1,'created_at'=>now(),'updated_at'=>now()],
    ['user_id'=>1,'category_id'=>3,'unit_id'=>3,'name'=>'Susu Segar Murni','description'=>'Susu segar langsung dari sapi perah.','price'=>8000,'stock'=>300,'is_active'=>1,'created_at'=>now(),'updated_at'=>now()],
    ['user_id'=>1,'category_id'=>2,'unit_id'=>1,'name'=>'Mangga Harum Manis','description'=>'Mangga manis dan harum pilihan.','price'=>25000,'stock'=>120,'is_active'=>1,'created_at'=>now(),'updated_at'=>now()],
]);
    }
}
