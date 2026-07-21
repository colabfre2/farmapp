<?php

namespace App\Repositories;

use App\DTOs\CategoryDTO;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getFiltered(?string $query): Collection
    {
        return Category::when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))->get();
    }

    public function getAll(): Collection
    {
        return Category::all();
    }

    public function createCategory(CategoryDTO $dto): Category
    {
        return Category::create($dto->toArray());
    }

    public function updateCategory(Category $category, CategoryDTO $dto): bool
    {
        return $category->update($dto->toArray());
    }

    public function deleteCategory(Category $category): bool
    {
        return $category->delete();
    }
}
