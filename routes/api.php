<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'laravel-api',
        'version' => '1.0.0',
    ]);
});

// Products REST API
Route::apiResource('products', ProductController::class);
