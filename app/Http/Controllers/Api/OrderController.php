<?php

namespace App\Http\Controllers\Api;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\UserAddress;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends BaseApiController
{
    /**
     * Ubah path gambar produk mentah jadi URL absolut berdasarkan host yang
     * benar-benar dipakai request (bukan APP_URL statis) — biar thumbnail
     * nyambung juga dari emulator/HP Flutter.
     */
    private function withProductImageUrls(Request $request, $orders)
    {
        $baseUrl = $request->getSchemeAndHttpHost();
        $orders->each(function ($order) use ($baseUrl) {
            $order->items->each(function ($item) use ($baseUrl) {
                if ($item->product && $item->product->image) {
                    $item->product->image = $baseUrl . '/storage/' . $item->product->image;
                }
            });
        });
    }

    /**
     * GET /orders — monitoring SEMUA order, khusus admin.
     * Digating role:admin di routes/api.php.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with(['user:id,name,email', 'items.product:id,name,price,image'])
            ->when($status, fn($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10);

        $this->withProductImageUrls($request, $orders->getCollection());

        return $this->success($orders, 'Data list order berhasil ditarik.');
    }

    /**
     * GET /my-orders — riwayat pesanan milik user yang login sendiri.
     * Ini yang sebelumnya belum ada — buyer tidak punya cara lihat riwayat
     * pesanannya sendiri dari mobile selain lewat endpoint admin index() di atas
     * (yang menampilkan SEMUA order, bukan cuma miliknya).
     */
    public function myOrders(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with(['items.product:id,name,price,image'])
            ->where('user_id', Auth::id())
            ->when($status, fn($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10);

        $this->withProductImageUrls($request, $orders->getCollection());

        return $this->success($orders, 'Riwayat pesanan berhasil diambil.');
    }

    /**
     * GET /orders/{order} — detail order.
     * Buyer hanya boleh lihat order miliknya sendiri; admin boleh lihat semua.
     */
    public function show(Request $request, Order $order)
    {
        if (Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses order ini.');
        }

        $order->load(['user:id,name,email,phone', 'items.product:id,name,image']);

        $baseUrl = $request->getSchemeAndHttpHost();
        $order->items->each(function ($item) use ($baseUrl) {
            if ($item->product && $item->product->image) {
                $item->product->image = $baseUrl . '/storage/' . $item->product->image;
            }
        });

        return $this->success($order, 'Detail order berhasil ditarik.');
    }

    /**
     * POST /orders — checkout dari mobile.
     * Disamakan penuh dengan Buyer\CheckoutController@store di web:
     * - Sumber item: cart_items milik user (bukan array manual dari body request)
     * - Support address_id (alamat tersimpan) ATAU alamat manual + opsi simpan
     * - Field pengiriman lengkap (courier, ongkir, destination_id, dst)
     * - Fee layanan harian dinamis (Order::nextDailyFee())
     * - payment_method termasuk 'midtrans', set payment_status awal
     * - Lock produk saat checkout, re-cek stok real-time (cegah race condition)
     * - Kirim notifikasi ke semua admin, sama seperti web
     */
    public function store(Request $request)
    {
        $cartItems = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return $this->error('Keranjang belanja kosong!', 422);
        }

        $validated = $request->validate([
            'address_id'        => 'nullable|exists:user_addresses,id',
            'shipping_name'     => 'required_without:address_id|nullable|string|max:255',
            'shipping_phone'    => 'required_without:address_id|nullable|string|max:20',
            'shipping_address'  => 'required_without:address_id|nullable|string',
            'destination_id'    => 'required|string',
            'province'          => 'required_without:address_id|nullable|string|max:255',
            'shipping_city'     => 'required_without:address_id|nullable|string|max:255',
            'shipping_district' => 'required_without:address_id|nullable|string|max:255',
            'courier'           => 'required|in:jne,jnt,sicepat',
            'courier_service'   => 'required|string',
            'shipping_cost'     => 'required|numeric|min:0',
            'payment_method'    => 'required|in:card,transfer,cod,midtrans',
            'save_address'      => 'nullable|boolean',
            'address_label'     => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // Lock & re-validasi stok real-time — cegah race condition & stok berubah
            // sejak item masuk cart
            $lockedProducts = [];
            foreach ($cartItems as $cartItem) {
                $product = Product::lockForUpdate()->find($cartItem->product_id);

                if (!$product || !$product->is_active) {
                    throw new \Exception("Produk \"{$cartItem->product->name}\" sudah tidak tersedia. Silakan hapus dari keranjang.");
                }

                if ($product->stock < $cartItem->quantity) {
                    throw new \Exception("Stok \"{$product->name}\" tidak cukup! Tersisa {$product->stock}, kamu memesan {$cartItem->quantity}.");
                }

                $lockedProducts[$cartItem->product_id] = $product;
            }

            // Resolve alamat — dari alamat tersimpan atau input manual
            if (!empty($validated['address_id'])) {
                $addr = UserAddress::where('id', $validated['address_id'])
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

                $shippingName     = $addr->recipient_name;
                $shippingPhone    = $addr->phone;
                $shippingAddress  = $addr->address_detail;
                $shippingCity     = $addr->city;
                $shippingDistrict = $addr->district;
                $province         = $addr->province;
                $destinationId    = $addr->destination_id;
            } else {
                $shippingName     = $validated['shipping_name'];
                $shippingPhone    = $validated['shipping_phone'];
                $shippingAddress  = $validated['shipping_address'];
                $shippingCity     = $validated['shipping_city'];
                $shippingDistrict = $validated['shipping_district'];
                $province         = $validated['province'];
                $destinationId    = $validated['destination_id'];

                if ($request->boolean('save_address')) {
                    $hasAddress = UserAddress::where('user_id', Auth::id())->exists();
                    UserAddress::create([
                        'user_id'        => Auth::id(),
                        'label'          => $validated['address_label'] ?? 'Rumah',
                        'recipient_name' => $shippingName,
                        'phone'          => $shippingPhone,
                        'province'       => $province,
                        'city'           => $shippingCity,
                        'district'       => $shippingDistrict,
                        'destination_id' => $destinationId,
                        'address_detail' => $shippingAddress,
                        'is_default'     => !$hasAddress,
                    ]);
                }
            }

            $subtotal     = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
            $shippingCost = (float) $validated['shipping_cost'];
            $fee          = Order::nextDailyFee();
            $totalAmount  = $subtotal + $shippingCost + $fee;

            $order = Order::create([
                'user_id'           => Auth::id(),
                'order_number'      => 'ORD-' . strtoupper(Str::random(8)),
                'status'            => 'Pending',
                'total_amount'      => $totalAmount,
                'shipping_name'     => $shippingName,
                'shipping_phone'    => $shippingPhone,
                'shipping_address'  => $shippingAddress,
                'shipping_city'     => $shippingCity,
                'shipping_district' => $shippingDistrict,
                'province'          => $province,
                'destination_id'    => $destinationId,
                'courier'           => $validated['courier'],
                'courier_service'   => $validated['courier_service'],
                'shipping_cost'     => $shippingCost,
                'fee'               => $fee,
                'payment_method'    => $validated['payment_method'],
                'payment_status'    => 'pending',
            ]);

            foreach ($cartItems as $cartItem) {
                $product = $lockedProducts[$cartItem->product_id];

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'unit_price'   => $product->price,
                    'quantity'     => $cartItem->quantity,
                    'subtotal'     => $product->price * $cartItem->quantity,
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id'    => Auth::id(),
                    'type'       => 'out',
                    'quantity'   => $cartItem->quantity,
                    'reason'     => 'Terjual',
                    'notes'      => 'Terjual otomatis via Checkout API (Pesanan ' . $order->order_number . ')',
                ]);

                $product->decrement('stock', $cartItem->quantity);
            }

            // Kosongkan cart setelah checkout berhasil
            CartItem::where('user_id', Auth::id())->delete();

            DB::commit();

            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewOrderNotification($order));
            }

            return $this->success(
                $order->load('items'),
                'Pesanan berhasil dibuat!',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * PATCH /orders/{order}/cancel — buyer batalkan order miliknya sendiri
     * selama masih berstatus Pending (belum diproses admin).
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak membatalkan order ini.');
        }

        if ($order->status !== 'Pending') {
            return $this->error('Order yang sudah diproses tidak bisa dibatalkan lagi.', 422);
        }

        $order->update(['status' => 'Cancelled']);

        return $this->success($order->fresh(), 'Order berhasil dibatalkan.');
    }
}
