<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $buyers = User::where('role', 'buyer')
            ->withCount('orders')
            ->withSum('completedOrders as total_belanja', 'total_amount')
            ->withMax('orders as last_order_at', 'created_at')
            ->when($query, fn($q) => $q->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            }))
            ->orderByDesc('last_order_at')
            ->get();

        return view('admin.buyers.index', compact('buyers', 'query'));
    }

    public function show(User $buyer)
    {
        abort_if($buyer->role !== 'buyer', 404);

        $summary = [
            'total_order'    => $buyer->orders()->count(),
            'total_belanja'  => $buyer->completedOrders()->sum('total_amount'),
            'per_status'     => $buyer->orders()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'last_order_at'  => $buyer->orders()->latest()->value('created_at'),
        ];

        return view('admin.buyers.show', compact('buyer', 'summary'));
    }
}
