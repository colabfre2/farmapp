<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CropController;
use App\Http\Controllers\Api\LivestockController;
use App\Http\Controllers\Api\HarvestController;
use App\Http\Controllers\Api\NotificationController;


// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);

// Protected routes (perlu token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/profile/avatar', [AuthController::class, 'updateAvatar']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::apiResource('crops', CropController::class);
    
    Route::apiResource('harvests', HarvestController::class);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::apiResource('livestock-movements', \App\Http\Controllers\Api\LivestockMovementController::class)->only(['index']); // hapus 'store'
    Route::get('/livestocks', [LivestockController::class, 'index']);
    Route::get('/livestocks/{livestock}', [LivestockController::class, 'show']);
    Route::patch('/livestocks/{livestock}/health', [LivestockController::class, 'updateHealth']);
});