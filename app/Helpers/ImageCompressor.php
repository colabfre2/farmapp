<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressor
{
    /**
     * Kompres, ubah ke WebP, dan simpan gambar ke storage public.
     * 
     * @param UploadedFile $file
     * @param string $folder (contoh: 'avatars', 'products')
     * @param int $maxWidth (lebar maksimal gambar, misal 800px)
     * @param int $quality (kualitas kompresi 1-100, misal 75)
     * @return string path file berformat .webp yang tersimpan
     */
    public static function compressAndStore(UploadedFile $file, string $folder = 'uploads', int $maxWidth = 800, int $quality = 75): string
    {
        // Generate nama file unik ber-ekstensi .webp
        $filename = Str::uuid() . '.webp';
        
        // Path tujuan di storage/app/public/{folder}
        $destinationPath = storage_path('app/public/' . $folder);
        
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $filePath = $destinationPath . '/' . $filename;

        // Baca ukuran & tipe asli gambar
        list($origWidth, $origHeight, $imageType) = getimagesize($file->getRealPath());

        // Hitung dimensi baru agar aspect ratio tetap aman
        if ($origWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = floor($origHeight * ($maxWidth / $origWidth));
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }

        // Buat resource gambar sementara berdasarkan format aslinya
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($file->getRealPath());
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($file->getRealPath());
                break;
            case IMAGETYPE_WEBP:
                $sourceImage = imagecreatefromwebp($file->getRealPath());
                break;
            default:
                // Fallback kalau format tidak dikenal
                return $file->store($folder, 'public');
        }

        // Buat kanvas gambar baru
        $virtualImage = imagecreatetruecolor($newWidth, $newHeight);

        // Pertahankan transparansi untuk PNG/WEBP
        imagecolortransparent($virtualImage, imagecolorallocatealpha($virtualImage, 0, 0, 0, 127));
        imagealphablending($virtualImage, false);
        imagesavealpha($virtualImage, true);

        // Resize gambar
        imagecopyresampled($virtualImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // SIMPAN SEBAGAI WEBP dengan kualitas tertentu (0 - 100)
        imagewebp($virtualImage, $filePath, $quality);

        // Hapus cache memori PHP
        imagedestroy($sourceImage);
        imagedestroy($virtualImage);

        return $folder . '/' . $filename;
    }
}