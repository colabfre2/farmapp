<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'description' => $this->description,

            'price' => $this->price,

            'stock' => $this->stock,

            'image' => $this->image
                ? asset('storage/' . $this->image)
                : null,

            'is_active' => (bool) $this->is_active,

            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],

            'unit' => [
                'id' => $this->unit?->id,
                'name' => $this->unit?->name,
                'symbol' => $this->unit?->symbol,
            ],

            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ],

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}