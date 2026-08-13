<?php

namespace App\Http\Controllers\Api;

use App\Services\RajaOngkirService;
use Illuminate\Http\Request;

class ShippingController extends BaseApiController
{
    protected RajaOngkirService $rajaOngkirService;

    public function __construct(RajaOngkirService $rajaOngkirService)
    {
        $this->rajaOngkirService = $rajaOngkirService;
    }

    /**
     * Get Shipping Rates
     */
    public function getRates(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|string',
            'weight'         => 'required|integer|min:1',
            'courier'        => 'required|string|in:jne,jnt,sicepat',
        ]);

        $rates = $this->rajaOngkirService->getCost(
            $validated['destination_id'],
            $validated['weight'],
            $validated['courier']
        );

        return $this->success($rates, 'Tarif pengiriman berhasil ditarik.');
    }

    /**
     * Search Destination for Address form
     */
    public function searchDestination(Request $request)
    {
        $request->validate(['query' => 'required|string|min:3']);

        $results = $this->rajaOngkirService->searchDestination($request->query('query'));

        return $this->success($results, 'Daftar destinasi ditemukan.');
    }
}