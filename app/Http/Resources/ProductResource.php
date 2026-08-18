<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 🚀 FIX: sebelumnya pakai asset('storage/...') yang generate URL
        // berdasarkan APP_URL di .env (misal http://localhost, tanpa port).
        // Ini bikin gambar GA NYAMPE di Flutter (emulator/HP), karena
        // "localhost" di sana ngerujuk ke device itu sendiri, bukan ke
        // komputer server. Sekarang kita bangun URL dari HOST YANG BENERAN
        // DIPAKE buat manggil API ini (misal 10.0.2.2:8000 di emulator,
        // atau IP LAN kalau dari HP fisik) — otomatis selalu nyambung,
        // ga peduli device-nya manggil API dari host mana.
        $baseUrl = $request->getSchemeAndHttpHost();

        // Rating dinamis (rata-rata review asli) kalau ada, fallback ke
        // kolom rating statis kalau belum pernah ada yang review —
        // biar konsisten sama yang ditampilin di web app (home & marketplace).
        $rating = $this->average_rating !== null
            ? round((float) $this->average_rating, 1)
            : (float) $this->rating;

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => (int) $this->price,
            'stock'       => (int) $this->stock,
            'sold_count'  => (int) ($this->sold_count ?? 0),
            'rating'      => $rating,
            'reviews_count' => (int) ($this->reviews_count ?? 0),
            'badge'       => $this->badge,
            'image'       => $this->image ? $baseUrl . '/storage/' . $this->image : null,
            'is_active'   => (bool) $this->is_active,
            'category'    => [
                'id'   => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'unit'        => [
                'id'     => $this->unit?->id,
                'name'   => $this->unit?->name,
                'symbol' => $this->unit?->symbol,
            ],
            'user'        => [
                'id'   => $this->user?->id,
                'name' => $this->user?->name,
            ],
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
