<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;


class CartController extends Controller
{
    //
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('buyer.cart', compact('cart', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);
        $id = $product->id;

        $requestedQty = (int) $request->input('quantity', 1);
        $existingQty  = $cart[$id]['quantity'] ?? 0;
        $totalQty     = $existingQty + $requestedQty;

        // 🚀 FIX: cegah quantity melebihi stok yang tersedia
        if ($product->stock <= 0) {
            return redirect()->back()->with('error', 'Maaf, stok ' . $product->name . ' sedang habis!');
        }

        if ($totalQty > $product->stock) {
            $sisaBisaDitambah = max($product->stock - $existingQty, 0);

            if ($sisaBisaDitambah <= 0) {
                return redirect()->back()->with('error', $product->name . ' di keranjang sudah mencapai batas stok yang tersedia (' . $product->stock . ').');
            }

            return redirect()->back()->with('error', 'Stok ' . $product->name . ' tidak cukup! Tersisa ' . $product->stock . ', kamu sudah punya ' . $existingQty . ' di keranjang.');
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $totalQty;
            $cart[$id]['stock']    = $product->stock; // sinkronkan stok terbaru
        } else {
            $cart[$id] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $product->price,
                'image'    => $product->image,
                'unit'     => $product->unit->symbol ?? '',
                'quantity' => $requestedQty,
                'stock'    => $product->stock,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', $product->name . ' added to cart!');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Item tidak ditemukan di keranjang.'], 404);
            }
            return redirect()->route('buyer.cart')->with('error', 'Item tidak ditemukan di keranjang.');
        }

        $product = Product::find($id);
        $requestedQty = (int) $request->input('quantity', 1);

        // 🚀 FIX: cegah update quantity melebihi stok terkini
        if ($product) {
            if ($requestedQty > $product->stock) {
                $message = 'Stok ' . $product->name . ' tersisa ' . $product->stock . ', tidak bisa mengubah jumlah melebihi itu.';

                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return redirect()->route('buyer.cart')->with('error', $message);
            }
            $cart[$id]['stock'] = $product->stock; // sinkronkan stok terbaru
        }

        $cart[$id]['quantity'] = max($requestedQty, 1);
        session()->put('cart', $cart);

        // 🚀 FIX: kalau request dari AJAX (fetch), balikin JSON tanpa redirect —
        // biar halaman tidak reload dan checkbox yang sudah dicentang tidak reset.
        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'quantity' => $cart[$id]['quantity'],
                'subtotal' => $cart[$id]['price'] * $cart[$id]['quantity'],
            ]);
        }

        return redirect()->route('buyer.cart')->with('success', 'Cart updated!');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);

        return redirect()->route('buyer.cart')->with('success', 'Item removed from cart!');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('buyer.cart')->with('success', 'Cart cleared!');
    }

}