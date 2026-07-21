<?php

namespace App\Services;

use App\DTOs\IncomeDTO;
use App\Models\Income;
use App\Repositories\Interfaces\IncomeRepositoryInterface;

class IncomeService
{
    public function __construct(
        protected IncomeRepositoryInterface $repository
    ) {}

    public function getIncomeData(?string $query): array
    {
        return [
            'incomes'     => $this->repository->getFiltered($query),
            'totalIncome' => $this->repository->getTotal(),
        ];
    }

    public function createIncome(IncomeDTO $dto): Income
    {
        return $this->repository->create($dto);
    }

    public function updateIncome(Income $income, IncomeDTO $dto): bool
    {
        return $this->repository->update($income, $dto);
    }

    public function deleteIncome(Income $income): bool
    {
        return $this->repository->delete($income);
    }
}