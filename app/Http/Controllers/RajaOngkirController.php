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
     * Halaman kalkulator ongkir (search box, bukan dropdown lagi)
     */
    public function index()
    {
        return view('rajaongkir');
    }

    /**
     * Cari destinasi (provinsi/kota/kecamatan) buat autocomplete
     */
    public function searchDestination(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:3',
        ]);

        $results = $this->rajaOngkirService->searchDestination($request->query('q'));

        return response()->json($results);
    }
}