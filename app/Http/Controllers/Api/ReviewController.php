<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends BaseApiController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id'   => 'required|exists:orders,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string',
        ]);

        // Check if user has already reviewed this product for this order
        $existing = ProductReview::where('product_id', $validated['product_id'])
            ->where('order_id', $validated['order_id'])
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return $this->error('Anda sudah memberikan ulasan untuk produk ini.', 400);
        }

        $validated['user_id'] = Auth::id();
        $review = ProductReview::create($validated);

        return $this->success($review, 'Ulasan berhasil dikirim.');
    }
}
