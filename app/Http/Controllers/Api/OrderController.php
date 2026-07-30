<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends BaseApiController
{
    /**
     * Nampilin list order buat monitoring Admin di Flutter
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with(['user:id,name,email', 'items.product:id,name,price,image'])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return $this->success($orders, 'Data list order berhasil ditarik bro.');
    }

    /**
     * Nampilin detail order spesifik
     */
    public function show(Order $order)
    {
        // Load relasi biar datanya lengkap pas ditampilin di detail Flutter
        $order->load(['user:id,name,email,phone', 'items.product:id,name,image']);

        return $this->success($order, 'Detail order berhasil ditarik.');
    }

    /**
     * Create Order Baru (Transaksi)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_name'    => 'required|string|max:255',
            'shipping_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city'    => 'nullable|string|max:100',
            'payment_method'   => 'required|in:card,transfer,cod',
            
            // Array of items yang dibeli
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Kalkulasi Total & Cek Stok
            $totalAmount = 0;
            $orderItemsData = [];
            
            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                
                // Cegah order kalau stok gak cukup
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok produk {$product->name} gak cukup bro! Sisa: {$product->stock}");
                }

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'unit_price'   => $product->price, // Pakai integer/decimal murni
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $subtotal,
                ];
            }

            // 2. Insert ke tabel Orders
            $order = Order::create([
                'user_id'          => Auth::id(),
                'order_number'     => 'ORD-' . strtoupper(Str::random(8)),
                'status'           => 'Pending',
                'total_amount'     => $totalAmount,
                'shipping_name'    => $validated['shipping_name'],
                'shipping_phone'   => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city'    => $validated['shipping_city'] ?? null,
                'payment_method'   => $validated['payment_method'],
            ]);

            // 3. Insert ke OrderItems & Mutasi Stok (Penting buat Rule FarmApp!)
            foreach ($orderItemsData as $itemData) {
                // Save Order Item
                $order->items()->create($itemData);

                // Kurangi stok produk secara langsung
                $product = Product::find($itemData['product_id']);
                $product->decrement('stock', $itemData['quantity']);

                // Catat di StockMovement biar log-nya rapi (Rule Sakti Kita!)
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id'    => Auth::id(),
                    'type'       => 'out',
                    'quantity'   => $itemData['quantity'],
                    'reason'     => 'Terjual (Pesanan ' . $order->order_number . ')',
                    'notes'      => 'Terjual otomatis via API Order',
                ]);
            }

            DB::commit();

            return $this->success(
                $order->load('items'), 
                'Mantap bro! Pesanan berhasil dibuat.', 
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 400);
        }
    }
}