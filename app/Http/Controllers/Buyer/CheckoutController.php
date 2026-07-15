<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Notifications\NewOrderNotification;
use App\Models\User;

class CheckoutController extends Controller
{
    //
    public function index()
{
    $cart = session()->get('cart', []);

    if (empty($cart)) {
        return redirect()->route('buyer.cart')->with('error', 'Your cart is empty!');
    }

    $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

    return view('buyer.checkout', compact('cart', 'total'));
}
public function store(Request $request)
{
    $cart = session()->get('cart', []);

    if (empty($cart)) {
        return redirect()->route('buyer.cart')->with('error', 'Your cart is empty!');
    }

    $request->validate([
        'shipping_name'    => 'required|string|max:255',
        'shipping_phone'   => 'required|string|max:20',
        'shipping_address' => 'required|string',
        'shipping_city'    => 'nullable|string|max:100',
        'payment_method'   => 'required|in:card,transfer,cod',
    ]);

    $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

    // 1. Buat order
    $order = Order::create([
        'user_id'          => auth()->id(),
        'order_number'     => 'ORD-' . strtoupper(Str::random(8)),
        'status'           => 'Pending',
        'total_amount'     => $total,
        'shipping_name'    => $request->shipping_name,
        'shipping_phone'   => $request->shipping_phone,
        'shipping_address' => $request->shipping_address,
        'shipping_city'    => $request->shipping_city,
        'payment_method'   => $request->payment_method,
    ]);

    // 2. Buat order items dan kurangi stok di dalam loop
    foreach ($cart as $item) {
        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $item['id'],
            'product_name' => $item['name'],
            'unit_price'   => $item['price'],
            'quantity'     => $item['quantity'],
            'subtotal'     => $item['price'] * $item['quantity'],
        ]);

        // Kurangi stok
        $product = Product::find($item['id']);
        if ($product) {
            $product->decrement('stock', $item['quantity']);
        }
    }

    // --- BAGIAN INI SUDAH DIPINDAHKAN KE LUAR FOREACH ---

    // 3. Kirim notifikasi ke semua admin (Cukup 1 kali saja per order)
    $admins = User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        $admin->notify(new NewOrderNotification($order));
    }

    // Catatan: Kode Income::create di sini SAYA HAPUS.
    // Biarkan sistem mencatat pemasukan HANYA dari TransactionController 
    // saat status order diubah menjadi 'Completed' oleh Admin.

    // 4. Kosongkan cart
    session()->forget('cart');

    return redirect()->route('buyer.orders')->with('success', 'Order placed successfully!');
}
}
