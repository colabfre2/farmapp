<?php

namespace App\Services;

use App\DTOs\CategoryDTO;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryService
{
    protected CategoryRepositoryInterface $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getFiltered(?string $query)
    {
        return $this->repository->getFiltered($query);
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function createCategory(CategoryDTO $dto)
    {
        return $this->repository->createCategory($dto);
    }

    public function updateCategory(Category $category, CategoryDTO $dto)
    {
        return $this->repository->updateCategory($category, $dto);
    }

    public function deleteCategory(Category $category)
    {
        return $this->repository->deleteCategory($category);
    }
}
