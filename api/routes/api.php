<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerCategoryController;
use OpenApi\Attributes as OA;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

#[OA\Info(
    version: "1.0.0",
    title: "TMS API Documentation",
    description: "API documentation for TMS (Tenant Management System) - Customer and Contact Management"
)]
#[OA\Server(
    url: "http://localhost:8081/api",
    description: "Local development server"
)]
class ApiRoutes {}

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Customer Categories (read-only for dropdown)
Route::get('/customer-categories', [CustomerCategoryController::class, 'index']);

// Customers
Route::apiResource('customers', CustomerController::class);

// Contacts (nested under customers)
Route::get('/customers/{customer}/contacts', [ContactController::class, 'index']);
Route::post('/customers/{customer}/contacts', [ContactController::class, 'store']);
Route::put('/contacts/{contact}', [ContactController::class, 'update']);
Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);

