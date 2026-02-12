<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoyaltyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/users/{user}/achievements', [LoyaltyController::class, 'show']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
