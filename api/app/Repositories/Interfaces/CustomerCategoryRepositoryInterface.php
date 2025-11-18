<?php

namespace App\Repositories\Interfaces;

use App\Models\CustomerCategory;
use Illuminate\Database\Eloquent\Collection;

interface CustomerCategoryRepositoryInterface
{
    public function all(): Collection;
    
    public function find(int $id): ?CustomerCategory;
}

