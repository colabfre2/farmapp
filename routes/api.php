<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CropController;
use App\Http\Controllers\Api\LivestockController;
use App\Http\Controllers\Api\HarvestController;
use App\Http\Controllers\Api\NotificationController;

// ── Public routes ────────────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);

Route::get('/storage/{path}', function ($path){
    $fullpath = storage_path('app/public/' . $path);
    if(!file_exists($fullpath)){
        abort (404);
    }
    return response()->file($fullpath);

})->where('path', '.*');

Route::middleware('auth:sanctum')->group(function () {

    // ── Umum (semua role login — admin & buyer sama-sama pakai) ────────────────
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/avatar', [AuthController::class, 'updateAvatar']);

    // Detail order: dibiarkan di luar role gate karena dipakai admin (lihat semua)
    // MAUPUN buyer (lihat punya sendiri) — kontrol akses sudah di level controller
    // (OrderController::show() cek kepemilikan/role secara eksplisit), bukan di route.
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::get('/orders/{order}/snap-token', [PaymentController::class, 'getSnapToken']);

    // ── Khusus BUYER (mirror grup 'buyer' di routes/web.php) ────────────────────
    Route::middleware('role:buyer')->group(function () {

        // Riwayat pesanan & checkout milik sendiri
        Route::get('/my-orders', [OrderController::class, 'myOrders']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel']);

        // Keranjang belanja (server-side, persisten per akun)
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::put('/cart/{cartItem}', [CartController::class, 'update']);
        Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);
        Route::delete('/cart', [CartController::class, 'clear']);

        // Alamat tersimpan
        Route::get('/addresses', [AddressController::class, 'index']);
        Route::get('/addresses/{address}', [AddressController::class, 'show']);
        Route::post('/addresses', [AddressController::class, 'store']);
        Route::put('/addresses/{address}', [AddressController::class, 'update']);
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
        Route::patch('/addresses/{address}/set-default', [AddressController::class, 'setDefault']);

        // Ongkir & pencarian destinasi (RajaOngkir)
        Route::get('/shipping/search', [ShippingController::class, 'searchDestination']);
        Route::post('/shipping/ongkir', [ShippingController::class, 'getOngkir']);
        Route::get('/shipping/couriers', [ShippingController::class, 'couriers']);
    });

    // ── Khusus ADMIN (mirror grup 'admin' di routes/web.php) ────────────────────
    Route::middleware('role:admin')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Monitoring semua order (bukan riwayat pribadi — lihat OrderController::index())
        Route::get('/orders', [OrderController::class, 'index']);

        Route::apiResource('crops', CropController::class);
        Route::apiResource('harvests', HarvestController::class);

        Route::apiResource('livestock-movements', \App\Http\Controllers\Api\LivestockMovementController::class)
            ->only(['index']);
        Route::get('/livestocks', [LivestockController::class, 'index']);
        Route::get('/livestocks/{livestock}', [LivestockController::class, 'show']);
        Route::patch('/livestocks/{livestock}/health', [LivestockController::class, 'updateHealth']);

        // Notifikasi order baru — saat ini hanya dikirim ke admin (lihat NewOrderNotification)
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    });
});
