<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RajaOngkirService;

class RajaOngkirController extends Controller
{
    protected $rajaOngkirService;

    public function __construct(RajaOngkirService $rajaOngkirService)
    {
        $this->rajaOngkirService = $rajaOngkirService;
    }

    /**
     * Menampilkan daftar provinsi dari API Raja Ongkir
     */
    public function index()
    {
        // Pakai service yang sudah dibuat
        $provinces = $this->rajaOngkirService->getProvinces();

        return view('rajaongkir', compact('provinces'));
    }

    /**
     * Menampilkan kota berdasarkan provinsi
     */
    public function getCities($provinceId)
    {
        $cities = $this->rajaOngkirService->getCities($provinceId);

        return response()->json($cities);
    }

    /**
     * Mencari lokasi (kecamatan)
     */
    public function searchDestination(Request $request)
    {
        $keyword = $request->get('q');
        $results = $this->rajaOngkirService->searchDestination($keyword);

        return response()->json($results);
    }
}