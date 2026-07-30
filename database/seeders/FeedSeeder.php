<?php

namespace Database\Seeders;

use App\Models\Feed;
use Illuminate\Database\Seeder;

class FeedSeeder extends Seeder
{
    public function run(): void
    {
        $feeds = [
            ['name' => 'Konsentrat Sapi Potong', 'type' => 'Konsentrat', 'unit_id' => 1, 'stock' => 500, 'price_per_unit' => 4500, 'description' => 'Pakan penggemukan protein tinggi'],
            ['name' => 'Rumput Odot', 'type' => 'Hijauan', 'unit_id' => 1, 'stock' => 1000, 'price_per_unit' => 500, 'description' => 'Hijauan segar panen harian'],
            ['name' => 'Dedak Padi', 'type' => 'Campuran', 'unit_id' => 1, 'stock' => 300, 'price_per_unit' => 3000, 'description' => 'Bahan campuran pakan'],
        ];

        foreach ($feeds as $feed) {
            Feed::create($feed);
        }
    }
}