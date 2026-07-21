<?php

namespace App\Services;

use App\DTOs\ExpenseDTO;
use App\Models\Expense;
use App\Repositories\Interfaces\ExpenseRepositoryInterface;

class ExpenseService
{
    public function __construct(
        protected ExpenseRepositoryInterface $repository
    ) {}

    public function getExpenseData(?string $query): array
    {
        return [
            'expenses'     => $this->repository->getFiltered($query),
            'totalExpense' => $this->repository->getTotal(),
        ];
    }

    public function getExpenseCategories()
    {
        return $this->repository->getCategories();
    }

    public function createExpense(ExpenseDTO $dto): Expense
    {
        return $this->repository->create($dto);
    }

    public function updateExpense(Expense $expense, ExpenseDTO $dto): bool
    {
        return $this->repository->update($expense, $dto);
    }

    public function deleteExpense(Expense $expense): bool
    {
        return $this->repository->delete($expense);
    }
}