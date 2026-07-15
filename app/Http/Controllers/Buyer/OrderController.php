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

}
