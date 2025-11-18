<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\Customer;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ContactRepository implements ContactRepositoryInterface
{
    public function findByCustomer(Customer $customer): Collection
    {
        return $customer->contacts;
    }

    public function find(int $id): ?Contact
    {
        return Contact::find($id);
    }

    public function create(Customer $customer, array $data): Contact
    {
        return $customer->contacts()->create($data);
    }

    public function update(Contact $contact, array $data): Contact
    {
        $contact->update($data);
        return $contact->fresh();
    }

    public function delete(Contact $contact): bool
    {
        return $contact->delete();
    }
}

