<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HarvestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'crop' => [
                'id' => $this->crop->id,
                'name' => $this->crop->name,
            ],

            'quantity' => $this->quantity,

            'unit' => [
                'id' => $this->unit->id,
                'name' => $this->unit->name,
            ],

            'selling_price' => $this->selling_price,

            'total_value' => $this->total_value,

            'harvested_at' => $this->harvested_at,

            'notes' => $this->notes,

            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            'created_at' => $this->created_at,
        ];
    }
}