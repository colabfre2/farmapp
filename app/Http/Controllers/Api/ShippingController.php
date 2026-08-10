<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Services\RajaOngkirService;

class ShippingController extends BaseApiController
{
    protected RajaOngkirService $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    // GET /shipping/search?q=...
    public function searchDestination(Request $request)
    {
        $keyword = $request->query('q', '');
        $results = $this->rajaOngkir->searchDestination($keyword);

        return $this->success($results, 'Hasil pencarian destinasi berhasil diambil.');
    }

    // POST /shipping/ongkir
    public function getOngkir(Request $request)
    {
        $request->validate([
            'destination_id' => 'required',
            'weight'         => 'required|integer|min:1',
            'courier'        => 'required|in:jne,jnt,sicepat',
        ]);

        $costs = $this->rajaOngkir->getCost(
            $request->destination_id,
            $request->weight,
            $request->courier
        );

        return $this->success($costs, 'Ongkos kirim berhasil dihitung.');
    }

    // GET /shipping/couriers
    public function couriers()
    {
        return $this->success(RajaOngkirService::supportedCouriers(), 'Daftar kurir berhasil diambil.');
    }
}
