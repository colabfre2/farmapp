<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\StockMovement;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items')
            ->when($request->filled('status') && $request->status !== 'all', function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->get();

        return view('buyer.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        $order->load('items');
        return view('buyer.order-detail', compact('order'));
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'Pending') {
            return redirect()->back()->with('error', 'Pesanan tidak bisa dibatalkan karena sudah diproses!');
        }

        // Kembalikan stok produk + catat mutasi stok masuk (pembatalan)
        foreach ($order->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if ($product) {
                // 🚀 FIX: catat mutasi stok masuk biar konsisten dengan histori Stok Masuk
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id'    => auth()->id(),
                    'type'       => 'in',
                    'quantity'   => $item->quantity,
                    'reason'     => 'Pembatalan Pesanan',
                    'notes'      => 'Stok dikembalikan karena Pesanan ' . $order->order_number . ' dibatalkan buyer',
                ]);

                $product->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => 'Cancelled']);

        return redirect()->route('buyer.orders')->with('success', 'Pesanan berhasil dibatalkan!');
    }
}