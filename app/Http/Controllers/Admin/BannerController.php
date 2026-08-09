<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Helpers\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            // 🚀 FIX: naikkan limit upload mentah jadi 10MB — file akan otomatis
            // dikompres ke WebP setelah lolos validasi, jadi limit ketat di sini
            // cuma menghalangi kompresi berjalan.
            'image'    => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'link_url' => 'nullable|url|max:255',
            'order'    => 'nullable|integer|min:0',
        ], [
            // 🚀 FIX: pesan custom manual, gak bergantung ke file lang/validation.php
            'image.required' => 'Gambar banner wajib diupload.',
            'image.image'    => 'File yang diupload harus berupa gambar.',
            'image.mimes'    => 'Format gambar harus JPG, PNG, JPG, atau WEBP.',
            'image.max'      => 'Ukuran gambar terlalu besar! Maksimal 10 MB (akan otomatis dikompres oleh sistem setelah diupload).',
        ]);

        // Kompres ke WebP, lebar maks 1600px, kualitas 70% (ringan buat koneksi lambat)
        $imagePath = ImageCompressor::compressAndStore($request->file('image'), 'banners', 1600, 70);

        Banner::create([
            'title'     => $request->title,
            'image'     => $imagePath,
            'link_url'  => $request->link_url,
            'order'     => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner berhasil ditambahkan!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'link_url' => 'nullable|url|max:255',
            'order'    => 'nullable|integer|min:0',
        ], [
            'image.image' => 'File yang diupload harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPG, PNG, JPG, atau WEBP.',
            'image.max'   => 'Ukuran gambar terlalu besar! Maksimal 10 MB (akan otomatis dikompres oleh sistem setelah diupload).',
        ]);

        $imagePath = $banner->image;
        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            // Kompres ke WebP, lebar maks 1600px, kualitas 70% (ringan buat koneksi lambat)
            $imagePath = ImageCompressor::compressAndStore($request->file('image'), 'banners', 1600, 70);
        }

        $banner->update([
            'title'     => $request->title,
            'image'     => $imagePath,
            'link_url'  => $request->link_url,
            'order'     => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner berhasil diperbarui!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner berhasil dihapus!');
    }

    public function toggleActive(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        return redirect()->route('admin.banners.index')->with('success', 'Status banner berhasil diubah!');
    }
}