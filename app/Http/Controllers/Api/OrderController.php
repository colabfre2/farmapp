<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserAddress;
use App\Models\StockMovement;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends BaseApiController
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with([
            'user:id,name,email,phone',
            'items.product' => function($query) {
                $query->withTrashed()->select('id', 'name', 'price', 'image');
            }
        ])
            ->where('user_id', Auth::id())
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(50);

        return $this->success($orders, 'Data list order berhasil ditarik.');
    }

    public function adminIndex(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with([
            'user:id,name,email',
            'items.product' => function($query) {
                $query->withTrashed()->select('id', 'name', 'price', 'image');
            }
        ])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(50);

        // REWRITE: Statistik Dashboard Admin yang Real-Time
        $summary = [
            'pending_count'    => Order::where('status', 'Pending')->count(),
            'processing_count' => Order::where('status', 'Processing')->count(),
            // Pendapatan bersih (Harga Barang saja) dari order yang bukan Pending/Cancelled
            'total_revenue'    => (float) Order::whereIn('status', ['Processing', 'Shipped', 'Completed'])
                                    ->sum(DB::raw('total_amount - shipping_cost')),
            'today_revenue'    => (float) Order::whereDate('created_at', now()->toDateString())
                                    ->whereIn('status', ['Processing', 'Shipped', 'Completed'])
                                    ->sum(DB::raw('total_amount - shipping_cost')),
        ];

        $data = $orders->toArray();
        $data['summary'] = $summary;

        return $this->success($data, 'Data list all order berhasil ditarik.');
    }

    public function sellerIndex(Request $request)
    {
        $status = $request->query('status');

        // Filter orders that have products belonging to this seller
        // Note: For now, we assume all products are global or we don't have multiple sellers yet.
        // If we have 'seller_id' in products, we would filter here.
        // For demonstration, seller sees orders where they are involved.
        $orders = Order::with([
            'user:id,name,email',
            'items.product' => function($query) {
                $query->withTrashed()->select('id', 'name', 'price', 'image');
            }
        ])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(50);

        return $this->success($orders, 'Data list seller order berhasil ditarik.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status'          => 'required|string',
            'tracking_number' => 'nullable|string',
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        // If order completed and it's COD, mark payment as success
        if ($newStatus === 'Completed' && $order->payment_method === 'cod') {
            $validated['payment_status'] = 'success';
        }

        $order->update($validated);

        // Jika dibatalkan, kembalikan stok
        if ($newStatus === 'Cancelled' && $oldStatus !== 'Cancelled') {
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'user_id'    => Auth::id(),
                    'type'       => 'in',
                    'quantity'   => $item->quantity,
                    'reason'     => 'Pesanan Dibatalkan (Order ' . $order->order_number . ')',
                ]);
            }
        }

        return $this->success($order, 'Status order berhasil diperbarui.');
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return $this->error('Bukan pesanan elu bro.', 403);
        }

        $order->load(['user:id,name,email,phone', 'items.product:id,name,image']);

        return $this->success($order, 'Detail order berhasil ditarik.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'address_id'       => 'required|exists:user_addresses,id',
            'courier'          => 'required|string',
            'courier_service'  => 'required|string',
            'shipping_cost'    => 'required|numeric',
            'payment_method'   => 'required|string|in:midtrans,cod',

            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $address = UserAddress::findOrFail($validated['address_id']);

                // 1. Kalkulasi Subtotal & Cek Stok
                $subtotal = 0;
                $orderItemsData = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::lockForUpdate()->find($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stok {$product->name} tidak cukup (Sisa: {$product->stock})");
                    }

                    $itemSubtotal = $product->price * $item['quantity'];
                    $subtotal += $itemSubtotal;

                    $orderItemsData[] = [
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'unit_price'   => $product->price,
                        'quantity'     => $item['quantity'],
                        'subtotal'     => $itemSubtotal,
                    ];
                }

                $totalAmount = $subtotal + $validated['shipping_cost'];

                // 2. Buat Order
                $order = Order::create([
                    'user_id'          => Auth::id(),
                    'order_number'     => 'ALMS-' . time() . strtoupper(Str::random(4)),
                    'status'           => 'Pending',
                    'total_amount'     => $totalAmount,
                    'shipping_name'    => $address->recipient_name,
                    'shipping_phone'   => $address->phone,
                    'shipping_address' => $address->address_detail,
                    'shipping_city'    => $address->city,
                    'shipping_district' => $address->district,
                    'province'         => $address->province,
                    'destination_id'   => $address->destination_id,
                    'courier'          => $validated['courier'],
                    'courier_service'  => $validated['courier_service'],
                    'shipping_cost'    => $validated['shipping_cost'],
                    'payment_method'   => $validated['payment_method'],
                ]);

                // 3. Simpan Order Items & Kurangi Stok
                foreach ($orderItemsData as $itemData) {
                    $order->items()->create($itemData);

                    $product = Product::find($itemData['product_id']);
                    $product->decrement('stock', $itemData['quantity']);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id'    => Auth::id(),
                        'type'       => 'out',
                        'quantity'   => $itemData['quantity'],
                        'reason'     => 'Terjual (Order ' . $order->order_number . ')',
                    ]);
                }

                // 4. Integrasi Pembayaran
                $orderData = $order->load(['user:id,name,email', 'items.product:id,name,price,image'])->toArray();

                if ($validated['payment_method'] === 'midtrans') {
                    $midtrans = new MidtransService();
                    $transaction = $midtrans->createTransaction($order);
                    $snapToken = $transaction->token;
                    $redirectUrl = $transaction->redirect_url;

                    $order->update(['snap_token' => $snapToken]);
                    $orderData['snap_token'] = $snapToken;
                    $orderData['redirect_url'] = $redirectUrl;
                } else {
                    // COD or other manual methods
                    $order->update(['payment_status' => 'pending']);
                    $orderData['payment_status'] = 'pending';
                }

                return $this->success(
                    $orderData,
                    'Pesanan berhasil dibuat, silakan proses selanjutnya bro.'
                );
            });

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
