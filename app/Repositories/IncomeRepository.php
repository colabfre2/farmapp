<?php

namespace App\Repositories;

use App\DTOs\IncomeDTO;
use App\Models\Income;
use App\Repositories\Interfaces\IncomeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class IncomeRepository implements IncomeRepositoryInterface
{
    public function getFiltered(?string $query): Collection
    {
        return Income::when($query, fn($q) => $q->where('source', 'like', "%{$query}%"))
            ->latest()
            ->get();
    }

    public function getTotal(): float
    {
        return Income::sum('amount');
    }

    public function create(IncomeDTO $dto): Income
    {
        return Income::create($dto->toArray());
    }

    public function update(Income $income, IncomeDTO $dto): bool
    {
        return $income->update($dto->toArray());
    }

    public function delete(Income $income): bool
    {
        return $income->delete();
    }

    public function getMonthlyByYear(int $year): array
    {
        return Income::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
    }

    public function getTotalByYear(int $year): float
    {
        return Income::whereYear('date', $year)->sum('amount');
    }
}
