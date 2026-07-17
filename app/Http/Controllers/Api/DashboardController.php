<?php

namespace App\Http\Controllers\Api;

use App\Models\Crop;
use App\Models\Feed;
use App\Models\Product;
use App\Models\Harvest;
use App\Models\Medicine;
use App\Models\Livestock;

class DashboardController extends BaseApiController
{
    public function index()
    {
        $dashboard = [
            'products' => Product::count(),
            'crops' => Crop::count(),
            'livestocks' => Livestock::count(),
            'harvests' => Harvest::count(),
            'feeds' => Feed::count(),
            'medicines' => Medicine::count(),
        ];

        return $this->success(
            $dashboard,
            'Dashboard data retrieved successfully.'
        );
    }
}