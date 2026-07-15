<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts  = \App\Models\Product::count();
        $totalCrops     = \App\Models\Crop::where('status', '!=', 'Harvested')->count();
        $totalLivestock = \App\Models\Livestock::sum('quantity');
        $totalOrders    = \App\Models\Order::count();
        $totalRevenue   = \App\Models\Income::sum('amount');
        $totalExpenses  = \App\Models\Expense::sum('amount');
        $netProfit      = $totalRevenue - $totalExpenses;

        // Eager load relasi 'items' dan 'unit' agar tidak terjadi N+1 Query
        $recentOrders   = \App\Models\Order::with(['user', 'items'])->latest()->take(4)->get();
        $recentHarvests = \App\Models\Harvest::with('unit')->latest()->take(4)->get();

        $monthlyRevenue = \App\Models\Income::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyExpenses = \App\Models\Expense::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Format ke array 12 bulan
        $revenueData  = [];
        $expensesData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueData[]  = $monthlyRevenue[$i]  ?? 0;
            $expensesData[] = $monthlyExpenses[$i] ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCrops',
            'totalLivestock',
            'totalOrders',
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'recentOrders',
            'recentHarvests',
            'revenueData',
            'expensesData'
        ));
    }
}