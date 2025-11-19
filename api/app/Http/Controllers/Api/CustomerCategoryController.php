<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerCategoryService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Customer Categories", description: "Customer category endpoints")]
class CustomerCategoryController extends Controller
{
    public function __construct(
        private CustomerCategoryService $categoryService
    ) {}

    #[OA\Get(
        path: "/api/customer-categories",
        summary: "Get list of customer categories",
        tags: ["Customer Categories"],
        responses: [
            new OA\Response(response: 200, description: "List of customer categories")
        ]
    )]
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        $formattedCategories = $this->categoryService->formatCategoriesForResponse($categories);

        return response()->json($formattedCategories);
    }
}
