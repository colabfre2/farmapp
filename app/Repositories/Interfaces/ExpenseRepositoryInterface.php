<?php

namespace App\Repositories\Interfaces;

use App\DTOs\ExpenseDTO;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Collection;

interface ExpenseRepositoryInterface
{
    public function getFiltered(?string $query): Collection;
    public function getTotal(): float;
    public function getCategories(): Collection;
    public function create(ExpenseDTO $dto): Expense;
    public function update(Expense $expense, ExpenseDTO $dto): bool;
    public function delete(Expense $expense): bool;
    public function getMonthlyByYear(int $year): array;
    public function getTotalByYear(int $year): float;
}
