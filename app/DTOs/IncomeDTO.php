<?php

namespace App\DTOs;

class IncomeDTO
{
    public function __construct(
        public string $date,
        public string $source,
        public float $amount,
        public ?string $notes = null,
        public ?int $userId = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            date: $data['date'],
            source: $data['source'],
            amount: $data['amount'],
            notes: $data['notes'] ?? null,
            userId: $data['user_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'user_id' => $this->userId,
            'date'    => $this->date,
            'source'  => $this->source,
            'amount'  => $this->amount,
            'notes'   => $this->notes,
        ], fn($value) => $value !== null);
    }
}
