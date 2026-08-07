<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\OrderItem;

class ReviewController extends Controller
{
    public function create(Order $order, $productId)
    {
        if ($order->user_id !== auth()->id() || $order->status !== 'Completed') {
            abort(403);
        }

        $orderItem = OrderItem::where('order_id', $order->id)->where('product_id', $productId)->firstOrFail();
        $product = \App\Models\Product::findOrFail($productId);

        $existingReview = ProductReview::where('product_id', $productId)->where('order_id', $order->id)->first();

        return view('buyer.reviews.create', compact('order', 'product', 'existingReview'));
    }

    public function store(Request $request, Order $order, $productId)
    {
        if ($order->user_id !== auth()->id() || $order->status !== 'Completed') {
            abort(403);
        }

        // 🚀 FIX: pastikan produk ini memang ada di dalam order tersebut,
        // mencegah user mengirim review untuk produk yang tidak pernah dibeli
        // (misal dengan mengubah product_id secara manual saat submit).
        $orderItem = OrderItem::where('order_id', $order->id)->where('product_id', $productId)->firstOrFail();

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        ProductReview::updateOrCreate(
            ['product_id' => $productId, 'order_id' => $order->id],
            [
                'user_id' => auth()->id(),
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]
        );

        // Update cache rating di tabel products
        $product = \App\Models\Product::find($productId);
        $avgRating = ProductReview::where('product_id', $productId)->avg('rating');
        $product->update(['rating' => round($avgRating, 1)]);

        return redirect()->route('buyer.orders.show', $order)->with('success', 'Ulasan berhasil disimpan!');
    }
}