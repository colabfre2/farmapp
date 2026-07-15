<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class MarketplaceController extends Controller
{
    //
    public function index(Request $request)
{
    $query = $request->input('q');
    $categoryId = $request->input('category');

    $products = Product::with('category', 'unit')
        ->where('is_active', true)
        ->when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })
        ->when($categoryId, function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        })
        ->latest()
        ->get();

    $categories = Category::all();

    return view('buyer.marketplace', compact('products', 'categories', 'query', 'categoryId'));
}

public function show(Product $product)
{
    return view('buyer.product-detail', compact('product'));
}

}
