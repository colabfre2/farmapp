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

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] += $request->input('quantity', 1);
    } else {
        $cart[$id] = [
            'id'       => $product->id,
            'name'     => $product->name,
            'price'    => $product->price,
            'image'    => $product->image,
            'unit'     => $product->unit->symbol ?? '',
            'quantity' => $request->input('quantity', 1),
            'stock'    => $product->stock,
        ];
    }

    session()->put('cart', $cart);

    return redirect()->back()->with('success', $product->name . ' added to cart!');
}

public function update(Request $request, $id)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] = $request->input('quantity', 1);
        session()->put('cart', $cart);
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
