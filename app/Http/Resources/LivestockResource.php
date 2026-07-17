<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivestockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'quantity' => $this->quantity,

            'avg_weight' => $this->avg_weight,

            'health_status' => $this->health_status,

            'notes' => $this->notes,

            'livestock_type' => [
                'id' => $this->livestockType->id,
                'name' => $this->livestockType->name,
            ],

            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            'created_at' => $this->created_at,
        ];
    }
}