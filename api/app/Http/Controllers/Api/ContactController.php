<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Customer;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(
        private ContactService $contactService
    ) {}

    public function index(Customer $customer): JsonResponse
    {
        $contacts = $this->contactService->getContactsByCustomer($customer);
        $formattedContacts = $this->contactService->formatContactsForResponse($contacts);

        return response()->json($formattedContacts);
    }

    public function store(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        $contact = $this->contactService->createContact($customer, $validated);
        $formattedContact = $this->contactService->formatContactForResponse($contact);

        return response()->json($formattedContact, 201);
    }

    public function update(Request $request, Contact $contact): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        $contact = $this->contactService->updateContact($contact, $validated);
        $formattedContact = $this->contactService->formatContactForResponse($contact);

        return response()->json($formattedContact);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $this->contactService->deleteContact($contact);

        return response()->json(['message' => 'Contact deleted successfully']);
    }
}
