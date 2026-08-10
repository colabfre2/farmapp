<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends BaseApiController
{
    /**
     * Ubah path gambar mentah dari DB jadi URL absolut, konsisten dengan
     * pola yang dipakai controller API lain (Order, dsb).
     */
    private function withImageUrl(Request $request, ?string $image): ?string
    {
        if (!$image) return null;
        return $request->getSchemeAndHttpHost() . '/storage/' . $image;
    }

    // GET /cart — daftar isi keranjang milik user yang login
    public function index(Request $request)
    {
        $items = CartItem::with('product.unit')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $items->each(function ($item) use ($request) {
            if ($item->product) {
                $item->product->image = $this->withImageUrl($request, $item->product->getRawOriginal('image'));
            }
        });

        $total = $items->sum(fn($item) => ($item->product->price ?? 0) * $item->quantity);

        return $this->success([
            'items' => $items,
            'total' => $total,
        ], 'Cart berhasil diambil.');
    }

    // POST /cart — tambah produk ke keranjang (atau tambah quantity kalau sudah ada)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (!$product->is_active || $product->stock <= 0) {
            return $this->error('Produk sedang tidak tersedia atau stok habis.', 422);
        }

        $existing = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        $existingQty = $existing?->quantity ?? 0;
        $totalQty    = $existingQty + $validated['quantity'];

        if ($totalQty > $product->stock) {
            $sisa = max($product->stock - $existingQty, 0);
            return $this->error(
                $sisa > 0
                    ? "Stok tidak cukup! Kamu sudah punya {$existingQty} di keranjang, sisa slot yang bisa ditambah: {$sisa}."
                    : "Produk ini di keranjang sudah mencapai batas stok yang tersedia ({$product->stock}).",
                422
            );
        }

        $cartItem = CartItem::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $product->id],
            ['quantity' => $totalQty]
        );

        return $this->success(
            $cartItem->load('product.unit'),
            'Produk berhasil ditambahkan ke keranjang.',
            201
        );
    }

    // PUT /cart/{cartItem} — update quantity
    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorizeOwnership($cartItem);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = $cartItem->product;

        if ($product && $validated['quantity'] > $product->stock) {
            return $this->error(
                "Stok {$product->name} tersisa {$product->stock}, tidak bisa mengubah jumlah melebihi itu.",
                422
            );
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return $this->success(
            $cartItem->load('product.unit'),
            'Jumlah item berhasil diperbarui.'
        );
    }

    // DELETE /cart/{cartItem} — hapus 1 item dari keranjang
    public function destroy(CartItem $cartItem)
    {
        $this->authorizeOwnership($cartItem);
        $cartItem->delete();

        return $this->success(null, 'Item berhasil dihapus dari keranjang.');
    }

    // DELETE /cart — kosongkan seluruh keranjang milik user
    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();

        return $this->success(null, 'Keranjang berhasil dikosongkan.');
    }

    private function authorizeOwnership(CartItem $cartItem): void
    {
        if ($cartItem->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengubah item keranjang ini.');
        }
    }
}
