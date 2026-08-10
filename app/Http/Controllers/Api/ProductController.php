<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\StoreProductRequest;
use App\Http\Requests\Api\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Helpers\ImageCompressor;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with([
            'category',
            'unit',
            'user'
        ])
            // 🚀 FIX: withAvg biar field 'rating' di response konsisten sama
            // yang ditampilin web app (dihitung dari review asli, bukan kolom statis)
            ->withAvg('reviews as average_rating', 'rating')
            ->withCount('reviews')
            ->where('is_active', true);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(10);

        return $this->success(
            ProductResource::collection($products),
            'Products retrieved successfully.'
        );
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'user'])
            ->loadAvg('reviews as average_rating', 'rating')
            ->loadCount('reviews');

        return $this->success(
            new ProductResource($product),
            'Product retrieved successfully.'
        );
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = Auth::id();

        // 🚀 FIX: pakai ImageCompressor yang sama kayak web (resize + convert
        // ke WebP) biar ukuran file konsisten & lebih ringan buat mobile.
        if ($request->hasFile('image')) {
            $data['image'] = ImageCompressor::compressAndStore($request->file('image'), 'products', 800, 80);
        }

        $product = Product::create($data);

        return $this->success(
            new ProductResource(
                $product->load('category', 'unit', 'user')
            ),
            'Product created successfully.',
            201
        );
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {

            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = ImageCompressor::compressAndStore($request->file('image'), 'products', 800, 80);
        }

        $product->update($data);

        return $this->success(
            new ProductResource(
                $product->fresh()->load('category', 'unit', 'user')
            ),
            'Product updated successfully.'
        );
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return $this->success(
            null,
            'Product deleted successfully.'
        );
    }

    /**
     * Get all product categories.
     */
    public function categories()
    {
        return $this->success(
            Category::select('id', 'name')->get(),
            'Categories retrieved successfully.'
        );
    }
}