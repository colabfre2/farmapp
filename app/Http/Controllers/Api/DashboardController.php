<?php

namespace App\Http\Controllers\Api;

use App\Models\Crop;
use App\Models\Feed;
use App\Models\Product;
use App\Models\Harvest;
use App\Models\Medicine;
use App\Models\Livestock;
use App\Models\LivestockMovement;
use Illuminate\Http\Request;

class DashboardController extends BaseApiController
{
    public function index(Request $request)
    {
        // 1. Data Summary (Angka Total)
        $summary = [
            'products'   => Product::count(),
            'crops'      => Crop::count(),
            'livestocks' => Livestock::sum('quantity'), // Total ekor, bukan total batch
            'harvests'   => Harvest::count(),
            'feeds'      => Feed::count(),
            'medicines'  => Medicine::count(),
        ];

        // 2. Data Recent Activities (Aktivitas Terkini buat List di Mobile)
        $recentActivities = [
            // 5 Mutasi Ternak Terakhir
            'recent_livestock_movements' => LivestockMovement::with(['livestock', 'user:id,name'])
                ->latest()
                ->take(5)
                ->get(),
                
            // 5 Panen Terakhir
            'recent_harvests' => Harvest::with(['crop', 'user:id,name', 'unit'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return $this->success(
            [
                'summary' => $summary,
                'recent_activities' => $recentActivities
            ],
            'Dashboard monitoring data retrieved successfully.'
        );
    }
}