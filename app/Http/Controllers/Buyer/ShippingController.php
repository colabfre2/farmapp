<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RajaOngkirService;

class ShippingController extends Controller
{
    protected RajaOngkirService $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    // GET /buyer/shipping/search?q=...
    public function searchDestination(Request $request)
    {
        $keyword = $request->query('q', '');
        $results = $this->rajaOngkir->searchDestination($keyword);

        return response()->json($results);
    }

    // POST /buyer/shipping/ongkir
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

        return response()->json($costs);
    }
}