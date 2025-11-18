<?php

namespace App\Repositories\Interfaces;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CustomerRepositoryInterface
{
    public function all(array $filters = []): Collection;
    
    public function find(int $id): ?Customer;
    
    public function create(array $data): Customer;
    
    public function update(Customer $customer, array $data): Customer;
    
    public function delete(Customer $customer): bool;
    
    public function search(string $query): Collection;
    
    public function filterByCategory(int $categoryId): Collection;
}

