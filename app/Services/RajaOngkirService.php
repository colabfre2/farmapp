<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $originDistrictId;

    public function __construct()
    {
        $this->apiKey           = config('rajaongkir.api_key');
        // rtrim buat mastiin nggak ada double slash (//) pas manggil URL
        $this->baseUrl          = rtrim(config('rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1'), '/');
        $this->originDistrictId = config('rajaongkir.origin_district_id', '73393');
    }

    // ── Cari Destinasi (Kecamatan) ───────────────────────
    public function searchDestination(string $keyword): array
    {
        $keyword = trim($keyword);
        if (strlen($keyword) < 3) {
            return [];
        }

        try {
            // Wajib withoutVerifying buat XAMPP/Laragon Windows
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders(['key' => $this->apiKey])
                ->get("{$this->baseUrl}/destination/domestic-destination", [
                    'search' => $keyword,
                    'limit'  => 20,
                ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
            
            Log::error('API Komerce nolak (Search): ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Koneksi cURL gagal (Search): ' . $e->getMessage());
        }

        return [];
    }

    // ── Hitung Ongkir dari Curug ─────────────────
    public function getCost(string $destinationId, int $weight, string $courier): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders(['key' => $this->apiKey])
                ->asForm()
                ->post("{$this->baseUrl}/calculate/domestic-cost", [
                    'origin'      => $this->originDistrictId,
                    'destination' => $destinationId,
                    'weight'      => $weight,
                    'courier'     => $courier,
                    'price'       => 'lowest',
                ]);

            if ($response->successful()) {
                $json = $response->json();
                $data = $json['data'] ?? $json;
                return is_array($data) ? $data : [];
            }

            Log::error('API Komerce nolak (Cost): ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Koneksi cURL gagal (Cost): ' . $e->getMessage());
        }

        return [];
    }

    public static function supportedCouriers(): array
    {
        return [
            'jne'     => 'JNE',
            'jnt'     => 'J&T Express',
            'sicepat' => 'SiCepat',
        ];
    }
}