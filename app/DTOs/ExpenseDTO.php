<?php

namespace App\DTOs;

class ExpenseDTO
{
    public function __construct(
        public int $expenseCategoryId,
        public string $date,
        public string $description,
        public float $amount,
        public ?string $notes = null,
        public ?int $userId = null
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            expenseCategoryId: $data['expense_category_id'],
            date: $data['date'],
            description: $data['description'],
            amount: $data['amount'],
            notes: $data['notes'] ?? null,
            userId: $data['user_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'user_id'             => $this->userId,
            'expense_category_id' => $this->expenseCategoryId,
            'date'                => $this->date,
            'description'         => $this->description,
            'amount'              => $this->amount,
            'notes'               => $this->notes,
        ], fn($value) => $value !== null);
    }
}
