<?php

namespace App\DTOs;

class CropTypeDTO
{
    public function __construct(
        public string $name,
        public string $description,
    ) {}

    public static function fromRequest($request)
    {
        return new self(
            name: $request->name,
            description: $request->description,
        );
    }
}