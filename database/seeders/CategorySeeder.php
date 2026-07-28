<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Sayuran Hijau 🥬', 'description' => 'Berbagai macam sayuran daun'],
            ['name' => 'Buah-buahan 🍅', 'description' => 'Hasil panen buah segar'],
            ['name' => 'Ternak Hidup 🐄', 'description' => 'Hewan ternak siap jual'],
            ['name' => 'Produk Olahan 🥛', 'description' => 'Susu, telur, dan olahan lainnya'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}