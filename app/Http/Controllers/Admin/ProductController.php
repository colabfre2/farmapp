<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Helpers\ImageCompressor;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $products = Product::with('category', 'unit')
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->latest()
            ->get();

        return view('admin.products.index', compact('products', 'query'));
    }

    public function create()
    {
        $categories = Category::all();
        $units = Unit::all();
        return view('admin.products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        // Trik Dual-Input: Bersihkan format titik Rupiah jadi integer murni
        $request->merge([
            'price' => preg_replace('/[^0-9]/', '', $request->price)
        ]);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'unit_id'     => 'required|exists:units,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active'   => 'nullable',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Kompres otomatis jadi .webp (Maks lebar 800px, kualitas 80%)
            $imagePath = ImageCompressor::compressAndStore($request->file('image'), 'products', 800, 80);
        }

        Product::create([
            'user_id'     => auth()->id(),
            'category_id' => $request->category_id,
            'unit_id'     => $request->unit_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'image'       => $imagePath,
            'is_active'   => $request->has('is_active'),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $units = Unit::all();
        return view('admin.products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $request->merge([
            'price' => preg_replace('/[^0-9]/', '', $request->price)
        ]);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'unit_id'     => 'required|exists:units,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active'   => 'nullable',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Kompres otomatis ke .webp
            $imagePath = ImageCompressor::compressAndStore($request->file('image'), 'products', 800, 80);
        }

        $product->update([
            'category_id' => $request->category_id,
            'unit_id'     => $request->unit_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            // Stok sengaja tidak di-update dari sini demi keamanan aturan bisnis log stok
            'image'       => $imagePath,
            'is_active'   => $request->has('is_active'),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dipindahkan ke sampah!');
    }

    public function trash()
    {
        $products = Product::onlyTrashed()->with('category', 'unit')->latest()->get();
        return view('admin.products.trash', compact('products'));
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('admin.products.trash')->with('success', 'Produk berhasil dipulihkan!');
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->forceDelete();

        return redirect()->route('admin.products.trash')->with('success', 'Produk berhasil dihapus permanen!');
    }
}