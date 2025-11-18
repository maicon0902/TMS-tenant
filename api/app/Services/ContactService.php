<?php

namespace App\Services;

use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

class ContactService
{
    public function __construct(
        private ContactRepositoryInterface $contactRepository,
        private CustomerRepositoryInterface $customerRepository
    ) {}

    public function getContactsByCustomer(Customer $customer): Collection
    {
        return $this->contactRepository->findByCustomer($customer);
    }

    public function getContactById(int $id): ?Contact
    {
        return $this->contactRepository->find($id);
    }

    public function createContact(Customer $customer, array $data): Contact
    {
        return $this->contactRepository->create($customer, $data);
    }

    public function updateContact(Contact $contact, array $data): Contact
    {
        return $this->contactRepository->update($contact, $data);
    }

    public function deleteContact(Contact $contact): bool
    {
        return $this->contactRepository->delete($contact);
    }

    public function formatContactForResponse(Contact $contact): array
    {
        return [
            'id' => $contact->id,
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
        ];
    }

    public function formatContactsForResponse(Collection $contacts): array
    {
        return $contacts->map(fn($contact) => $this->formatContactForResponse($contact))->toArray();
    }
}

