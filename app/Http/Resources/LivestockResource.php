<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivestockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'arrival_date'   => optional($this->arrival_date)->format('Y-m-d'),
            'quantity'       => (int) $this->quantity,
            'avg_weight'     => (float) $this->avg_weight,
            'health_status'  => $this->health_status,
            'notes'          => $this->notes,
            'livestock_type' => [
                'id'   => $this->livestockType?->id,
                'name' => $this->livestockType?->name,
            ],
            'kandang' => [
                'id'       => $this->kandang?->id,
                'name'     => $this->kandang?->name,
                'capacity' => $this->kandang?->capacity,
                'location' => $this->kandang?->location,
            ],
            'user' => [
                'id'   => $this->user?->id,
                'name' => $this->user?->name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}