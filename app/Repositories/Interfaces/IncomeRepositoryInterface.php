<?php

namespace App\Repositories\Interfaces;

use App\DTOs\IncomeDTO;
use App\Models\Income;
use Illuminate\Database\Eloquent\Collection;

interface IncomeRepositoryInterface
{
    public function getFiltered(?string $query): Collection;
    public function getTotal(): float;
    public function create(IncomeDTO $dto): Income;
    public function update(Income $income, IncomeDTO $dto): bool;
    public function delete(Income $income): bool;
    public function getMonthlyByYear(int $year): array;
    public function getTotalByYear(int $year): float;
}