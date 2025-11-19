<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Customer;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Contacts", description: "Contact management endpoints")]
class ContactController extends Controller
{
    public function __construct(
        private ContactService $contactService
    ) {}

    #[OA\Get(
        path: "/api/customers/{customerId}/contacts",
        summary: "Get contacts for a customer",
        tags: ["Contacts"],
        parameters: [
            new OA\Parameter(name: "customerId", in: "path", required: true, schema: new OA\Schema(type: "integer"), description: "Customer ID"),
        ],
        responses: [
            new OA\Response(response: 200, description: "List of contacts"),
            new OA\Response(response: 404, description: "Customer not found")
        ]
    )]
    public function index(Customer $customer): JsonResponse
    {
        $contacts = $this->contactService->getContactsByCustomer($customer);
        $formattedContacts = $this->contactService->formatContactsForResponse($contacts);

        return response()->json($formattedContacts);
    }

    #[OA\Post(
        path: "/api/customers/{customerId}/contacts",
        summary: "Create a new contact for a customer",
        tags: ["Contacts"],
        parameters: [
            new OA\Parameter(name: "customerId", in: "path", required: true, schema: new OA\Schema(type: "integer"), description: "Customer ID"),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    required: ["first_name"],
                    properties: [
                        new OA\Property(property: "first_name", type: "string", example: "John"),
                        new OA\Property(property: "last_name", type: "string", nullable: true, example: "Doe"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Contact created successfully"),
            new OA\Response(response: 404, description: "Customer not found"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
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

    #[OA\Put(
        path: "/api/contacts/{id}",
        summary: "Update contact",
        tags: ["Contacts"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), description: "Contact ID"),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    required: ["first_name"],
                    properties: [
                        new OA\Property(property: "first_name", type: "string", example: "John"),
                        new OA\Property(property: "last_name", type: "string", nullable: true, example: "Doe"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Contact updated successfully"),
            new OA\Response(response: 404, description: "Contact not found"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
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

    #[OA\Delete(
        path: "/api/contacts/{id}",
        summary: "Delete contact",
        tags: ["Contacts"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), description: "Contact ID"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Contact deleted successfully"),
            new OA\Response(response: 404, description: "Contact not found")
        ]
    )]
    public function destroy(Contact $contact): JsonResponse
    {
        $this->contactService->deleteContact($contact);

        return response()->json(['message' => 'Contact deleted successfully']);
    }
}
