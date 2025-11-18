<?php

namespace App\Services;

use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class CustomerService
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {}

    public function getAllCustomers(array $filters = []): Collection
    {
        return $this->customerRepository->all($filters);
    }

    public function getCustomerById(int $id): ?Customer
    {
        return $this->customerRepository->find($id);
    }

    public function createCustomer(array $data): Customer
    {
        return $this->customerRepository->create($data);
    }

    public function updateCustomer(Customer $customer, array $data): Customer
    {
        return $this->customerRepository->update($customer, $data);
    }

    public function deleteCustomer(Customer $customer): bool
    {
        return $this->customerRepository->delete($customer);
    }

    public function formatCustomerForResponse(Customer $customer, bool $includeContacts = false): array
    {
        $data = [
            'id' => $customer->id,
            'name' => $customer->name,
            'reference' => $customer->reference,
            'category' => $customer->category?->name,
            'category_id' => $customer->customer_category_id,
            'customer_category_id' => $customer->customer_category_id,
            'start_date' => $customer->start_date?->format('m/d/Y'),
            'description' => $customer->description,
            'contacts_count' => $customer->contacts_count ?? ($customer->relationLoaded('contacts') ? $customer->contacts->count() : 0),
        ];

        if ($includeContacts && $customer->relationLoaded('contacts')) {
            $data['contacts'] = $customer->contacts->map(fn($contact) => [
                'id' => $contact->id,
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
            ])->toArray();
        }

        return $data;
    }

    public function formatCustomersForResponse(Collection $customers): array
    {
        return $customers->map(fn($customer) => $this->formatCustomerForResponse($customer))->toArray();
    }
}

