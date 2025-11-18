<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

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

    public function show(Customer $customer): JsonResponse
    {
        $customer = $this->customerService->getCustomerById($customer->id);
        
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $formattedCustomer = $this->customerService->formatCustomerForResponse($customer, true);

        return response()->json($formattedCustomer);
    }

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

    public function destroy(Customer $customer): JsonResponse
    {
        $this->customerService->deleteCustomer($customer);
        
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
