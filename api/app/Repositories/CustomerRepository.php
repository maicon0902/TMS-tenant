<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function all(array $filters = []): Collection
    {
        $query = Customer::with(['category', 'contacts'])->withCount('contacts');

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('reference', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['category']) && $filters['category']) {
            $query->where('customer_category_id', $filters['category']);
        }

        return $query->get();
    }

    public function find(int $id): ?Customer
    {
        return Customer::with(['category', 'contacts'])
            ->withCount('contacts')
            ->find($id);
    }

    public function create(array $data): Customer
    {
        $customer = Customer::create($data);
        $customer->load(['category', 'contacts']);
        $customer->loadCount('contacts');
        return $customer;
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        $customer->load(['category', 'contacts']);
        $customer->loadCount('contacts');
        return $customer;
    }

    public function delete(Customer $customer): bool
    {
        return $customer->delete();
    }

    public function search(string $query): Collection
    {
        return Customer::where('name', 'like', "%{$query}%")
            ->orWhere('reference', 'like', "%{$query}%")
            ->with(['category', 'contacts'])
            ->withCount('contacts')
            ->get();
    }

    public function filterByCategory(int $categoryId): Collection
    {
        return Customer::where('customer_category_id', $categoryId)
            ->with(['category', 'contacts'])
            ->withCount('contacts')
            ->get();
    }
}

