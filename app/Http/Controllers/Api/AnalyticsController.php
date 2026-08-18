<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends BaseApiController
{
    public function adminInsights()
    {
        $paidOrders = Order::whereIn('payment_status', ['success', 'settlement', 'capture', 'paid']);

        $totalRevenue = $paidOrders->sum('total_amount') - $paidOrders->sum('shipping_cost');

        $totalOrders = Order::count();
        $totalSellers = User::where('role', 'seller')->count();

        $completedOrders = Order::where('status', 'Completed')->count();
        $conversionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;

        // Revenue Chart (last 7 days)
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $val = Order::whereDate('created_at', $date)
                ->whereIn('payment_status', ['success', 'settlement', 'capture', 'paid'])
                ->sum('total_amount');
            $revenueChart[] = ['label' => $date, 'value' => (float)$val];
        }

        // Category Distribution
        $totalProducts = Product::count();
        $categoryDistribution = Category::withCount('products')
            ->get()
            ->map(function($cat) use ($totalProducts) {
                return [
                    'category' => $cat->name,
                    'percentage' => $totalProducts > 0 ? round(($cat->products_count / $totalProducts) * 100, 1) : 0
                ];
            });

        // Top Products
        $topProducts = Product::withSum(['orderItems as sold_count' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->whereIn('payment_status', ['success', 'settlement', 'capture', 'paid']);
                });
            }], 'quantity')
            ->orderByDesc('sold_count')
            ->take(5)
            ->get()
            ->map(function($p) {
                return [
                    'product_id' => $p->id,
                    'product_name' => $p->name,
                    'views' => 0, // Set to 0 since not tracked in DB yet
                    'orders' => (int)$p->sold_count,
                    'revenue' => (float)($p->sold_count * $p->price),
                    'growth' => 0.0 // Set to 0.0
                ];
            });

        $todayOrders = Order::whereDate('created_at', now()->toDateString())
            ->whereIn('payment_status', ['success', 'settlement', 'capture', 'paid']);

        $todayRevenue = $todayOrders->sum('total_amount') - $todayOrders->sum('shipping_cost');

        return $this->success([
            'total_revenue' => (float)$totalRevenue,
            'today_revenue' => (float)$todayRevenue,
            'total_orders' => $totalOrders,
            'total_sellers' => $totalSellers,
            'total_customers' => User::where('role', 'buyer')->count(),
            'average_order_value' => $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0,
            'conversion_rate' => $conversionRate,
            'revenue_chart' => $revenueChart,
            'category_distribution' => $categoryDistribution,
            'top_products' => $topProducts
        ]);
    }

    public function sellerInsights(Request $request)
    {
        // For now, same as admin but conceptually filtered by seller_id
        // Since we don't have multiple sellers yet, we return global stats
        return $this->adminInsights();
    }

    public function productInsights(Product $product)
    {
        $totalSold = \App\Models\OrderItem::where('product_id', $product->id)
            ->whereHas('order', function($q) {
                $q->whereIn('payment_status', ['success', 'settlement', 'capture']);
            })
            ->sum('quantity');

        return $this->success([
            'total_sold' => (int)$totalSold,
            'revenue' => (float)($totalSold * $product->price),
        ]);
    }

    public function inventorySummary()
    {
        $totalItems = Product::count();
        $totalValue = Product::selectRaw('SUM(stock * price) as total')->value('total');

        $lowStockThreshold = 10;
        $lowStockCount = Product::where('stock', '>', 0)
            ->where('stock', '<=', $lowStockThreshold)
            ->count();

        $outOfStockCount = Product::where('stock', 0)->count();

        return $this->success([
            'total_items' => $totalItems,
            'total_value' => (float)$totalValue,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
        ]);
    }
}
