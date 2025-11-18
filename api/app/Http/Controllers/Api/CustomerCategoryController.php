<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerCategoryService;
use Illuminate\Http\JsonResponse;

class CustomerCategoryController extends Controller
{
    public function __construct(
        private CustomerCategoryService $categoryService
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        $formattedCategories = $this->categoryService->formatCategoriesForResponse($categories);

        return response()->json($formattedCategories);
    }
}
