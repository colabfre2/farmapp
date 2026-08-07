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
            'image'    => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|url|max:255',
            'order'    => 'nullable|integer|min:0',
        ]);

        $imagePath = ImageCompressor::compressAndStore($request->file('image'), 'banners', 1600, 80);

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
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|url|max:255',
            'order'    => 'nullable|integer|min:0',
        ]);

        $imagePath = $banner->image;
        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $imagePath = ImageCompressor::compressAndStore($request->file('image'), 'banners', 1600, 80);
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