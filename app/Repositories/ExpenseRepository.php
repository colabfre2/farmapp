<?php

namespace App\Repositories;

use App\DTOs\ExpenseDTO;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Repositories\Interfaces\ExpenseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function getFiltered(?string $query): Collection
    {
        return Expense::with('expenseCategory')
            ->when($query, fn($q) => $q->where('description', 'like', "%{$query}%"))
            ->latest()
            ->get();
    }

    public function getTotal(): float
    {
        return Expense::sum('amount');
    }

    public function getCategories(): Collection
    {
        return ExpenseCategory::all();
    }

    public function create(ExpenseDTO $dto): Expense
    {
        return Expense::create($dto->toArray());
    }

    public function update(Expense $expense, ExpenseDTO $dto): bool
    {
        return $expense->update($dto->toArray());
    }

    public function delete(Expense $expense): bool
    {
        return $expense->delete();
    }

    public function getMonthlyByYear(int $year): array
    {
        return Expense::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
    }

    public function getTotalByYear(int $year): float
    {
        return Expense::whereYear('date', $year)->sum('amount');
    }
}
