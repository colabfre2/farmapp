<?php

namespace App\DTOs;

class CropTypeDTO
{
    public $name, $description;
    public function __construct(array $data) {
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
    }
}