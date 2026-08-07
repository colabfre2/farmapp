<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Folder sumber gambar yang sudah disiapkan sendiri (di dalam project, bukan storage).
     * Taruh file-file gambar berikut di folder ini sebelum menjalankan seeder:
     * telur_ayam_kampung.png, bawang_merah.jpg, bawang_putih.png, cabai_ijo.jpg,
     * cabai_merah.png, daging_ayam.jpg, daging_sapi.jpg, kacang_tanah.png,
     * manggaarumanis.jpg, susu_segar.jpg
     */
    protected string $sourceFolder = 'database/seeders/images/products';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('products');

        $products = [
            ['user_id'=>1,'category_id'=>4,'unit_id'=>5,'name'=>'Telur Ayam Kampung','description'=>'Telur ayam kampung segar pilihan.','price'=>35000,'stock'=>200,'is_active'=>1,'image_file'=>'telur_ayam_kampung.png'],
            ['user_id'=>1,'category_id'=>1,'unit_id'=>1,'name'=>'Bawang Merah','description'=>'Bawang merah segar berkualitas.','price'=>32000,'stock'=>180,'is_active'=>1,'image_file'=>'bawang_merah.jpg'],
            ['user_id'=>1,'category_id'=>1,'unit_id'=>1,'name'=>'Bawang Putih','description'=>'Bawang putih segar pilihan.','price'=>28000,'stock'=>170,'is_active'=>1,'image_file'=>'bawang_putih.png'],
            ['user_id'=>1,'category_id'=>1,'unit_id'=>1,'name'=>'Cabai Hijau','description'=>'Cabai hijau segar pedas.','price'=>22000,'stock'=>140,'is_active'=>1,'image_file'=>'cabai_ijo.jpg'],
            ['user_id'=>1,'category_id'=>1,'unit_id'=>1,'name'=>'Cabai Merah','description'=>'Cabai merah segar pedas.','price'=>45000,'stock'=>130,'is_active'=>1,'image_file'=>'cabai_merah.png'],
            ['user_id'=>1,'category_id'=>4,'unit_id'=>1,'name'=>'Daging Ayam','description'=>'Daging ayam segar potongan pilihan.','price'=>35000,'stock'=>90,'is_active'=>1,'image_file'=>'dagingayam.jpg'],
            ['user_id'=>1,'category_id'=>4,'unit_id'=>1,'name'=>'Daging Sapi','description'=>'Daging sapi segar potongan pilihan.','price'=>135000,'stock'=>60,'is_active'=>1,'image_file'=>'dagingsapi.jpg'],
            ['user_id'=>1,'category_id'=>2,'unit_id'=>1,'name'=>'Kacang Tanah','description'=>'Kacang tanah segar kualitas terbaik.','price'=>25000,'stock'=>110,'is_active'=>1,'image_file'=>'kacangtanah.png'],
            ['user_id'=>1,'category_id'=>2,'unit_id'=>1,'name'=>'Mangga Harum Manis','description'=>'Mangga manis dan harum pilihan.','price'=>25000,'stock'=>120,'is_active'=>1,'image_file'=>'manggaarumanis.jpg'],
            ['user_id'=>1,'category_id'=>3,'unit_id'=>3,'name'=>'Susu Segar','description'=>'Susu segar langsung dari sapi perah.','price'=>18000,'stock'=>150,'is_active'=>1,'image_file'=>'susu_segar.jpg'],
        ];

        foreach ($products as $item) {
            $imagePath = $this->resolveImage($item['image_file']);

            DB::table('products')->insert([
                'user_id'     => $item['user_id'],
                'category_id' => $item['category_id'],
                'unit_id'     => $item['unit_id'],
                'name'        => $item['name'],
                'description' => $item['description'],
                'price'       => $item['price'],
                'stock'       => $item['stock'],
                'image'       => $imagePath,
                'is_active'   => $item['is_active'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('10 produk dummy berhasil dibuat beserta gambarnya!');
    }

    /**
     * Copy file gambar dari folder sumber ke storage/app/public/products.
     * Kalau file tidak ditemukan, return null (fallback ikon di view tetap jalan).
     */
    protected function resolveImage(string $filename): ?string
    {
        $sourcePath = base_path($this->sourceFolder) . '/' . $filename;

        if (!file_exists($sourcePath)) {
            $this->command->warn("Gambar '{$filename}' tidak ditemukan di '{$this->sourceFolder}'. Produk akan dibuat tanpa gambar.");
            return null;
        }

        $extension    = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $storedName   = 'products/' . Str::slug(pathinfo($filename, PATHINFO_FILENAME)) . '-' . Str::random(8) . '.' . $extension;

        Storage::disk('public')->put($storedName, file_get_contents($sourcePath));

        return $storedName;
    }
}