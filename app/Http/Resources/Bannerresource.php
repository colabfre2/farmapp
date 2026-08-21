<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Sama seperti ProductResource: bangun URL gambar dari host yang
        // BENERAN dipakai buat manggil API ini (bukan asset() yang berbasis
        // APP_URL), biar selalu nyambung dari emulator, HP fisik, dsb.
        $baseUrl = $request->getSchemeAndHttpHost();

        return [
            'id'       => $this->id,
            'title'    => $this->title,
            'image'    => $this->image ? $baseUrl . '/storage/' . $this->image : null,
            'link_url' => $this->link_url,
            'order'    => (int) $this->order,
        ];
    }
}