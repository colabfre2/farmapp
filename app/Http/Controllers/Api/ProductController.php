<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query      = $request->input('q');
        $categoryId = $request->input('category_id');

        $products = Product::with('category', 'unit')
            ->where('is_active', true)
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->latest()
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'description' => $p->description,
                'price'       => $p->price,
                'stock'       => $p->stock,
                'image'       => $p->image ? asset('storage/' . $p->image) : null,
                'category'    => $p->category->name ?? '-',
                'unit'        => $p->unit->symbol ?? '-',
                'badge'       => $p->badge,
                'rating'      => $p->rating,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }

    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'stock'       => $product->stock,
                'image'       => $product->image ? asset('storage/' . $product->image) : null,
                'category'    => $product->category->name ?? '-',
                'unit'        => $product->unit->symbol ?? '-',
                'badge'       => $product->badge,
                'rating'      => $product->rating,
            ],
        ]);
    }

    public function categories()
    {
        $categories = Category::all(['id', 'name']);

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }
}