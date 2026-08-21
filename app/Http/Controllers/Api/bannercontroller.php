<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BannerResource;
use App\Models\Banner;

class BannerController extends BaseApiController
{
    /**
     * List banner aktif untuk carousel di home Flutter.
     * Public — tidak perlu login, sama seperti /products.
     */
    public function index()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('order')
            ->get();

        return $this->success(
            BannerResource::collection($banners),
            'Daftar banner berhasil diambil.'
        );
    }
}