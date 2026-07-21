<?php

namespace App\Repositories\Interfaces;

use App\DTOs\CategoryDTO;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function getFiltered(?string $query): Collection;
    public function getAll(): Collection;
    public function createCategory(CategoryDTO $dto): Category;
    public function updateCategory(Category $category, CategoryDTO $dto): bool;
    public function deleteCategory(Category $category): bool;
}