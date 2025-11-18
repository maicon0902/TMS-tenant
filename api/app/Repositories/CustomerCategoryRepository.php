<?php

namespace App\Repositories;

use App\Models\CustomerCategory;
use App\Repositories\Interfaces\CustomerCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CustomerCategoryRepository implements CustomerCategoryRepositoryInterface
{
    public function all(): Collection
    {
        return CustomerCategory::orderBy('name')->get();
    }

    public function find(int $id): ?CustomerCategory
    {
        return CustomerCategory::find($id);
    }
}

