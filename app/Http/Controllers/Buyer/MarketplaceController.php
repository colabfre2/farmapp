<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', $request->input('q'));
        $categoryId = $request->input('category');

        // Query utama untuk produk yang ditampilkan
        $query = Product::with('category', 'unit')->where('is_active', true);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        // Ambil semua kategori
        $categories = Category::all();

        // HITUNG JUMLAH DINAMIS BERDASARKAN SEARCH
        // Total produk aktif yang sesuai pencarian (untuk badge "Semua Produk")
        $totalSearchQuery = Product::where('is_active', true);
        if ($search) {
            $totalSearchQuery->where('name', 'like', "%{$search}%");
        }
        $totalSearchCount = $totalSearchQuery->count();

        // Hitung jumlah produk per kategori dengan memperhitungkan search yang aktif
        $categoryCounts = [];
        foreach ($categories as $cat) {
            $catQuery = Product::where('is_active', true)->where('category_id', $cat->id);
            if ($search) {
                $catQuery->where('name', 'like', "%{$search}%");
            }
            $categoryCounts[$cat->id] = $catQuery->count();
        }

        return view('buyer.marketplace', compact('products', 'categories', 'search', 'categoryId', 'totalSearchCount', 'categoryCounts'));
    }

    public function show(Product $product)
    {
        $product->load('category', 'unit');

        $reviews = $product->reviews()
            ->with('user')
            ->latest()
            ->paginate(5);

        $reviewCount = $product->reviews()->count();

        return view('buyer.product-detail', compact('product', 'reviews', 'reviewCount'));
    }
}