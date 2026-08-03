<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserAddress;
use App\Services\RajaOngkirService;
use Illuminate\Support\Str;
use App\Notifications\NewOrderNotification;
use App\Models\User;

class CheckoutController extends Controller
{
    protected RajaOngkirService $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    // ── Halaman Checkout ──────────────────────────────────────

    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('buyer.cart')->with('error', 'Keranjang belanja kosong!');
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // Ambil alamat tersimpan milik user, urutkan yang default di paling atas
        $addresses = UserAddress::where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        return view('buyer.checkout', compact('cart', 'subtotal', 'addresses', 'defaultAddress'));
    }

    // ── Simpan Order ──────────────────────────────────────────

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('buyer.cart')->with('error', 'Keranjang belanja kosong!');
        }

        // Validasi Fix: Field manual menjadi nullable jika address_id (alamat tersimpan) dipilih
        $request->validate([
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
            'payment_method'    => 'required|in:card,transfer,cod',
            'save_address'      => 'nullable|boolean',
            'address_label'     => 'nullable|string|max:50',
        ]);

        // Resolve data alamat — dari alamat tersimpan atau input manual form
        if ($request->filled('address_id')) {
            $addr = UserAddress::where('id', $request->address_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $shippingName     = $addr->recipient_name;
            $shippingPhone    = $addr->phone;
            $shippingAddress  = $addr->address_detail;
            $shippingCity     = $addr->city;
            $shippingDistrict = $addr->district;
            $province         = $addr->province;
            $destinationId    = $addr->destination_id;
        } else {
            $shippingName     = $request->shipping_name;
            $shippingPhone    = $request->shipping_phone;
            $shippingAddress  = $request->shipping_address;
            $shippingCity     = $request->shipping_city;
            $shippingDistrict = $request->shipping_district;
            $province         = $request->province;
            $destinationId    = $request->destination_id;

            // Simpan alamat baru ke database kalau user centang "Simpan alamat ini"
            if ($request->boolean('save_address')) {
                $hasAddress = UserAddress::where('user_id', auth()->id())->exists();
                UserAddress::create([
                    'user_id'        => auth()->id(),
                    'label'          => $request->address_label ?: 'Rumah',
                    'recipient_name' => $shippingName,
                    'phone'          => $shippingPhone,
                    'province'       => $province,
                    'city'           => $shippingCity,
                    'district'       => $shippingDistrict,
                    'destination_id' => $destinationId,
                    'address_detail' => $shippingAddress,
                    'is_default'     => !$hasAddress, // Jadikan default jika ini alamat pertama
                ]);
            }
        }

        $subtotal     = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shippingCost = (float) $request->shipping_cost;
        $totalAmount  = $subtotal + $shippingCost;

        // Insert data Order ke tabel orders
        $order = Order::create([
            'user_id'           => auth()->id(),
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
            'courier'           => $request->courier,
            'courier_service'   => $request->courier_service,
            'shipping_cost'     => $shippingCost,
            'payment_method'    => $request->payment_method,
        ]);

        // Insert Order Items dan update stok produk (Sesuai aturan bisnis: stok berkurang otomatis saat order)
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item['id'],
                'product_name' => $item['name'],
                'unit_price'   => $item['price'],
                'quantity'     => $item['quantity'],
                'subtotal'     => $item['price'] * $item['quantity'],
            ]);

            $product = Product::find($item['id']);
            if ($product) {
                // Pastikan tipe data stok integer agar kalkulasi akurat
                $product->decrement('stock', $item['quantity']);
            }
        }

        // Notifikasi ke seluruh user dengan role 'admin'
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewOrderNotification($order));
        }

        // Bersihkan session keranjang belanja setelah checkout berhasil
        session()->forget('cart');

        return redirect()->route('buyer.orders')->with('success', 'Pesanan berhasil dibuat!');
    }
}