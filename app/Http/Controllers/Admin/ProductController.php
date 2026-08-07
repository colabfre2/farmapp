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
        // Bersihkan format harga: ambil angka murninya saja secara mutlak
        $cleanPrice = preg_replace('/[^0-9]/', '', $request->input('price', 0));

        $request->merge([
            'price' => (int) $cleanPrice
        ]);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'unit_id'     => 'required|exists:units,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0|max:999999999', // Batasi max biar gak out of range
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active'   => 'nullable',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
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

    /**
     * Tampilkan detail satu produk
     */
    public function show(Product $product)
    {
        $product->load([
            'category',
            'unit',
            'user',
            'reviews.user',
            'orderItems.order',
        ]);

        // Statistik ringkas buat kartu insight
        $totalSold   = $product->orderItems->sum('quantity');
        $totalOmzet  = $product->orderItems->sum('subtotal');
        $totalReview = $product->reviews->count();
        $avgRating   = $product->reviews->count() > 0
            ? round($product->reviews->avg('rating'), 1)
            : null;

        // Riwayat pesanan terbaru yang memuat produk ini (10 terakhir)
        $recentOrderItems = $product->orderItems()
            ->with('order.user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.products.show', compact(
            'product',
            'totalSold',
            'totalOmzet',
            'totalReview',
            'avgRating',
            'recentOrderItems'
        ));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $units = Unit::all();
        return view('admin.products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        // Bersihkan format harga secara aman agar tidak melipat ganda
        $cleanPrice = preg_replace('/[^0-9]/', '', $request->input('price', 0));

        $request->merge([
            'price' => (int) $cleanPrice
        ]);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'unit_id'     => 'required|exists:units,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0|max:999999999',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active'   => 'nullable',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
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