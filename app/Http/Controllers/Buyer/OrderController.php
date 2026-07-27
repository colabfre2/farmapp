<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    //
    public function index(){
        $orders = Order::where('user_id', auth()->id())
        ->with('items')
        ->latest()
        ->get();
        return view ('buyer.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        if($order->user_id !== auth()->id()){
            abort(403);
        }
        $order->load('items');
        return view ('buyer.order-detail', compact('order'));
    }
    public function cancel(Order $order)
{
    if ($order->user_id !== auth()->id()) {
        abort(403);
    }

    if ($order->status !== 'Pending') {
        return redirect()->back()->with('error', 'Pesanan tidak bisa dibatalkan karena sudah diproses!');
    }

    // Kembalikan stok produk
    foreach ($order->items as $item) {
        $product = \App\Models\Product::find($item->product_id);
        if ($product) {
            $product->increment('stock', $item->quantity);
        }
    }

    $order->update(['status' => 'Cancelled']);

    return redirect()->route('buyer.orders')->with('success', 'Pesanan berhasil dibatalkan!');
    }
}
