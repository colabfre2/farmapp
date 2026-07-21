<?php

namespace App\DTOs;

class CategoryDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
        ], fn($value) => $value !== null);
    }
}
