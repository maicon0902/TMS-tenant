<?php

namespace App\Services;

use App\Repositories\Interfaces\CustomerCategoryRepositoryInterface;
use App\Models\CustomerCategory;
use Illuminate\Database\Eloquent\Collection;

class CustomerCategoryService
{
    public function __construct(
        private CustomerCategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function getCategoryById(int $id): ?CustomerCategory
    {
        return $this->categoryRepository->find($id);
    }

    public function formatCategoryForResponse(CustomerCategory $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
        ];
    }

    public function formatCategoriesForResponse(Collection $categories): array
    {
        return $categories->map(fn($category) => $this->formatCategoryForResponse($category))->toArray();
    }
}

