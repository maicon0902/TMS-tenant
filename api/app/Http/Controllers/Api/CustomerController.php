<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Customers", description: "Customer management endpoints")]
class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    #[OA\Get(
        path: "/api/customers",
        summary: "Get list of customers",
        tags: ["Customers"],
        parameters: [
            new OA\Parameter(name: "search", in: "query", required: false, schema: new OA\Schema(type: "string"), description: "Search by name or reference"),
            new OA\Parameter(name: "category", in: "query", required: false, schema: new OA\Schema(type: "integer"), description: "Filter by category ID"),
        ],
        responses: [
            new OA\Response(response: 200, description: "List of customers")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $filters = [];
        
        if ($request->has('search') && $request->search) {
            $filters['search'] = $request->search;
        }
        
        if ($request->has('category') && $request->category) {
            $filters['category'] = $request->category;
        }

        $customers = $this->customerService->getAllCustomers($filters);
        $formattedCustomers = $this->customerService->formatCustomersForResponse($customers);

        return response()->json($formattedCustomers);
    }

    #[OA\Post(
        path: "/api/customers",
        summary: "Create a new customer",
        tags: ["Customers"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    required: ["name", "reference"],
                    properties: [
                        new OA\Property(property: "name", type: "string", example: "Acme Corporation"),
                        new OA\Property(property: "reference", type: "string", example: "ACME001"),
                        new OA\Property(property: "customer_category_id", type: "integer", nullable: true, example: 1),
                        new OA\Property(property: "start_date", type: "string", format: "date", nullable: true),
                        new OA\Property(property: "description", type: "string", nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Customer created successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reference' => 'required|string|max:255|unique:customers,reference',
            'customer_category_id' => 'nullable|exists:customer_categories,id',
            'start_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $customer = $this->customerService->createCustomer($validated);
        $formattedCustomer = $this->customerService->formatCustomerForResponse($customer);

        return response()->json($formattedCustomer, 201);
    }

    #[OA\Get(
        path: "/api/customers/{id}",
        summary: "Get customer by ID",
        tags: ["Customers"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), description: "Customer ID"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Customer details"),
            new OA\Response(response: 404, description: "Customer not found")
        ]
    )]
    public function show(Customer $customer): JsonResponse
    {
        $customer = $this->customerService->getCustomerById($customer->id);
        
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $formattedCustomer = $this->customerService->formatCustomerForResponse($customer, true);

        return response()->json($formattedCustomer);
    }

    #[OA\Put(
        path: "/api/customers/{id}",
        summary: "Update customer",
        tags: ["Customers"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), description: "Customer ID"),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    required: ["name", "reference"],
                    properties: [
                        new OA\Property(property: "name", type: "string", example: "Acme Corporation"),
                        new OA\Property(property: "reference", type: "string", example: "ACME001"),
                        new OA\Property(property: "customer_category_id", type: "integer", nullable: true, example: 1),
                        new OA\Property(property: "start_date", type: "string", format: "date", nullable: true),
                        new OA\Property(property: "description", type: "string", nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Customer updated successfully"),
            new OA\Response(response: 404, description: "Customer not found"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function update(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reference' => 'required|string|max:255|unique:customers,reference,' . $customer->id,
            'customer_category_id' => 'nullable|exists:customer_categories,id',
            'start_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $customer = $this->customerService->updateCustomer($customer, $validated);
        $formattedCustomer = $this->customerService->formatCustomerForResponse($customer);

        return response()->json($formattedCustomer);
    }

    #[OA\Delete(
        path: "/api/customers/{id}",
        summary: "Delete customer",
        tags: ["Customers"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"), description: "Customer ID"),
        ],
        responses: [
            new OA\Response(response: 200, description: "Customer deleted successfully"),
            new OA\Response(response: 404, description: "Customer not found")
        ]
    )]
    public function destroy(Customer $customer): JsonResponse
    {
        $this->customerService->deleteCustomer($customer);
        
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
