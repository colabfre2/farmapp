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
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\ReviewController;


// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);

// Midtrans Webhook (Public)
Route::post('/payment/notification', [PaymentController::class, 'notification']);

// Public storage route for CORS support in development
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');

// Protected routes (perlu token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Addresses
    Route::apiResource('addresses', AddressController::class);
    Route::patch('/addresses/{address}/default', [AddressController::class, 'setDefault']);

    // Shipping
    Route::post('/shipping/rates', [ShippingController::class, 'getRates']);
    Route::get('/shipping/destination', [ShippingController::class, 'searchDestination']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);

    // Admin & Seller Orders
    Route::get('/admin/orders', [OrderController::class, 'adminIndex']);
    Route::get('/seller/orders', [OrderController::class, 'sellerIndex']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);

    // Analytics / BI
    Route::get('/admin/insights', [AnalyticsController::class, 'adminInsights']);
    Route::get('/seller/insights', [AnalyticsController::class, 'sellerInsights']);
    Route::get('/products/{product}/insights', [AnalyticsController::class, 'productInsights']);
    Route::get('/inventory/summary', [AnalyticsController::class, 'inventorySummary']);

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);

    Route::post('/profile/avatar', [AuthController::class, 'updateAvatar']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::apiResource('crops', CropController::class);

    Route::apiResource('harvests', HarvestController::class);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::apiResource('livestock-movements', \App\Http\Controllers\Api\LivestockMovementController::class)->only(['index']);
    Route::get('/livestocks', [LivestockController::class, 'index']);
    Route::get('/livestocks/{livestock}', [LivestockController::class, 'show']);
    Route::patch('/livestocks/{livestock}/health', [LivestockController::class, 'updateHealth']);
});
