<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RajaOngkirService;



class ShippingController extends Controller
{
    protected $rajaOngkir;
    //
    public function __construct(RajaOngkirService $rajaOngkir)
{

    $this->rajaOngkir = $rajaOngkir;
}

public function getProvinces()
{
    $provinces = $this->rajaOngkir->getProvinces();
    return response()->json($provinces);
}

public function getCities($provinceId)
{
    $cities = $this->rajaOngkir->getCities($provinceId);
    return response()->json($cities);
}

public function getOngkir(Request $request)
{
    $results = $this->rajaOngkir->getOngkir(
        $request->origin,
        $request->destination,
        $request->weight,
        $request->courier
    );

    return response()->json($results);
}
}
