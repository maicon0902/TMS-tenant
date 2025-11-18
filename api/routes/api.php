<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerCategoryController;

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

