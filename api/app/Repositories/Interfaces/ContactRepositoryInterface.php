<?php

namespace App\Repositories\Interfaces;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

interface ContactRepositoryInterface
{
    public function findByCustomer(Customer $customer): Collection;
    
    public function find(int $id): ?Contact;
    
    public function create(Customer $customer, array $data): Contact;
    
    public function update(Contact $contact, array $data): Contact;
    
    public function delete(Contact $contact): bool;
}

