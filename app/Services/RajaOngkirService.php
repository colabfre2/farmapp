<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl = 'https://rajaongkir.komerce.id/api/v1/destination';

    public function __construct()
    {
        $this->apiKey = env('RAJAONGKIR_API_KEY');
    }

    public function getProvinces()
    {
        $response = Http::withHeaders(['key' => $this->apiKey])
            ->get("{$this->baseUrl}/province");

        return $response->json()['data'] ?? [];
    }

    public function getCities($provinceId = null)
{
    $url = "{$this->baseUrl}/city";
    if ($provinceId) {
        $url .= "/{$provinceId}";
    }

    $response = Http::withHeaders(['key' => $this->apiKey])
        ->get($url);

    return $response->json()['data'] ?? [];
}


    public function getOngkir($origin, $destination, $weight, $courier)
{
    $response = Http::withHeaders([
        'key' => $this->apiKey,
    ])->asForm()->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
        'origin'      => $origin,
        'destination' => $destination,
        'weight'      => $weight,
        'courier'     => $courier,
        'price'       => 'lowest',
    ]);

    return $response->json();
}
}