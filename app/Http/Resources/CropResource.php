<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CropResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
     public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'crop_type' => [
                'id' => $this->cropType->id,
                'name' => $this->cropType->name,
            ],

            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            'planted_at' => $this->planted_at,

            'expected_harvest_at' => $this->expected_harvest_at,

            'actual_harvest_at' => $this->actual_harvest_at,

            'status' => $this->status,

            'notes' => $this->notes,

            'created_at' => $this->created_at,
        ];
    }
}