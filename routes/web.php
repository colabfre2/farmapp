<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('about');
})->name('about');

// Halaman kalkulator ongkir umum (kalau memang dipakai di luar flow buyer)
Route::get('/rajaongkir', [\App\Http\Controllers\RajaOngkirController::class, 'index'])->name('rajaongkir.index');
Route::get('/rajaongkir/search', [\App\Http\Controllers\RajaOngkirController::class, 'searchDestination'])->name('rajaongkir.search');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Buyer Routes
Route::middleware(['auth', 'role:buyer'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/home', function () {
        return view('buyer.home');
    })->name('home');

    Route::get('/marketplace', [\App\Http\Controllers\Buyer\MarketplaceController::class, 'index'])->name('marketplace');
    Route::get('/marketplace/{product}', [\App\Http\Controllers\Buyer\MarketplaceController::class, 'show'])->name('marketplace.show');

    Route::get('/cart', [\App\Http\Controllers\Buyer\CartController::class, 'index'])->name('cart');
    Route::post('/cart/{product}/add', [\App\Http\Controllers\Buyer\CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{id}/update', [\App\Http\Controllers\Buyer\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}/remove', [\App\Http\Controllers\Buyer\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [\App\Http\Controllers\Buyer\CartController::class, 'clear'])->name('cart.clear');

    Route::get('/checkout', [\App\Http\Controllers\Buyer\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [\App\Http\Controllers\Buyer\CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/orders', [\App\Http\Controllers\Buyer\OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [\App\Http\Controllers\Buyer\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [\App\Http\Controllers\Buyer\OrderController::class, 'cancel'])->name('orders.cancel');

    // Alamat Tersimpan (Addresses)
    Route::get('/addresses', [\App\Http\Controllers\Buyer\AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [\App\Http\Controllers\Buyer\AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [\App\Http\Controllers\Buyer\AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [\App\Http\Controllers\Buyer\AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [\App\Http\Controllers\Buyer\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [\App\Http\Controllers\Buyer\AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{address}/set-default', [\App\Http\Controllers\Buyer\AddressController::class, 'setDefault'])->name('addresses.set-default');

    // Shipping & RajaOngkir (API v1 Komerce — search destination, bukan province/city terpisah lagi)
    Route::get('/shipping/search', [\App\Http\Controllers\Buyer\ShippingController::class, 'searchDestination'])->name('shipping.search');
    Route::post('/shipping/ongkir', [\App\Http\Controllers\Buyer\ShippingController::class, 'getOngkir'])->name('shipping.ongkir');

    Route::get('/orders/{order}/review/{productId}', [\App\Http\Controllers\Buyer\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/orders/{order}/review/{productId}', [\App\Http\Controllers\Buyer\ReviewController::class, 'store'])->name('reviews.store');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('units', \App\Http\Controllers\UnitController::class);
    Route::resource('crop-types', \App\Http\Controllers\CropTypeController::class);
    Route::resource('livestock-types', \App\Http\Controllers\LivestockTypeController::class);
    Route::resource('expense-categories', \App\Http\Controllers\ExpenseCategoryController::class);
    Route::resource('crop-varieties', \App\Http\Controllers\Admin\CropVarietyController::class);

    // Harvest
    Route::resource('harvests', \App\Http\Controllers\Admin\HarvestController::class);
    Route::get('/harvests-export', [\App\Http\Controllers\Admin\HarvestController::class, 'exportExcel'])->name('harvests.export');

    // Crops
    Route::post('/crops/bulk', [\App\Http\Controllers\Admin\CropController::class, 'storeBulk'])->name('crops.store-bulk');
    Route::resource('crops', \App\Http\Controllers\Admin\CropController::class);
    Route::get('/crops-trash', [\App\Http\Controllers\Admin\CropController::class, 'trash'])->name('crops.trash');
    Route::patch('/crops/{id}/restore', [\App\Http\Controllers\Admin\CropController::class, 'restore'])->name('crops.restore');
    Route::delete('/crops/{id}/force-delete', [\App\Http\Controllers\Admin\CropController::class, 'forceDelete'])->name('crops.force-delete');

    // Livestock
    Route::post('/livestock/store-bulk', [\App\Http\Controllers\Admin\LivestockController::class, 'storeBulk'])->name('livestock.store-bulk');
    Route::resource('livestock', \App\Http\Controllers\Admin\LivestockController::class);
    Route::get('/livestock-trash', [\App\Http\Controllers\Admin\LivestockController::class, 'trash'])->name('livestock-trash'); // disesuaikan dikit biar rapi
    Route::patch('/livestock/{id}/restore', [\App\Http\Controllers\Admin\LivestockController::class, 'restore'])->name('livestock.restore');
    Route::delete('/livestock/{id}/force-delete', [\App\Http\Controllers\Admin\LivestockController::class, 'forceDelete'])->name('livestock.force-delete');
    
    // Product
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::get('/products-trash', [\App\Http\Controllers\Admin\ProductController::class, 'trash'])->name('products.trash');
    Route::patch('/products/{id}/restore', [\App\Http\Controllers\Admin\ProductController::class, 'restore'])->name('products.restore');
    Route::delete('/products/{id}/force-delete', [\App\Http\Controllers\Admin\ProductController::class, 'forceDelete'])->name('products.force-delete');
    Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'index'])->name('search');

    // Stock - Barang Masuk
    Route::get('/stock/in', [\App\Http\Controllers\Admin\StockMovementController::class, 'inIndex'])->name('stock.in.index');
    Route::get('/stock/in/create', [\App\Http\Controllers\Admin\StockMovementController::class, 'inCreate'])->name('stock.in.create');
    Route::post('/stock/in', [\App\Http\Controllers\Admin\StockMovementController::class, 'inStore'])->name('stock.in.store');
    Route::get('/stock/in-export', [\App\Http\Controllers\Admin\StockMovementController::class, 'exportInExcel'])->name('stock.in.export');

    // Stock - Barang Keluar
    Route::get('/stock/out', [\App\Http\Controllers\Admin\StockMovementController::class, 'outIndex'])->name('stock.out.index');
    Route::get('/stock/out/create', [\App\Http\Controllers\Admin\StockMovementController::class, 'outCreate'])->name('stock.out.create');
    Route::post('/stock/out', [\App\Http\Controllers\Admin\StockMovementController::class, 'outStore'])->name('stock.out.store');
    Route::get('/stock/out-export', [\App\Http\Controllers\Admin\StockMovementController::class, 'exportOutExcel'])->name('stock.out.export');

    // Transactions
    Route::get('/transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{order}', [\App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('transactions.show');
    Route::patch('/transactions/{order}/status', [\App\Http\Controllers\Admin\TransactionController::class, 'updateStatus'])->name('transactions.update-status');
    Route::get('/transactions-export', [\App\Http\Controllers\Admin\TransactionController::class, 'exportExcel'])->name('transactions.export');

    // Finance - Income
    Route::get('/finance/income', [\App\Http\Controllers\Admin\FinanceController::class, 'incomeIndex'])->name('finance.income.index');
    Route::get('/finance/income/create', [\App\Http\Controllers\Admin\FinanceController::class, 'incomeCreate'])->name('finance.income.create');
    Route::post('/finance/income', [\App\Http\Controllers\Admin\FinanceController::class, 'incomeStore'])->name('finance.income.store');
    Route::get('/finance/income/{income}/edit', [\App\Http\Controllers\Admin\FinanceController::class, 'incomeEdit'])->name('finance.income.edit');
    Route::put('/finance/income/{income}', [\App\Http\Controllers\Admin\FinanceController::class, 'incomeUpdate'])->name('finance.income.update');
    Route::delete('/finance/income/{income}', [\App\Http\Controllers\Admin\FinanceController::class, 'incomeDestroy'])->name('finance.income.destroy');

    // Finance - Expense
    Route::get('/finance/expense', [\App\Http\Controllers\Admin\FinanceController::class, 'expenseIndex'])->name('finance.expense.index');
    Route::get('/finance/expense/create', [\App\Http\Controllers\Admin\FinanceController::class, 'expenseCreate'])->name('finance.expense.create');
    Route::post('/finance/expense', [\App\Http\Controllers\Admin\FinanceController::class, 'expenseStore'])->name('finance.expense.store');
    Route::get('/finance/expense/{expense}/edit', [\App\Http\Controllers\Admin\FinanceController::class, 'expenseEdit'])->name('finance.expense.edit');
    Route::put('/finance/expense/{expense}', [\App\Http\Controllers\Admin\FinanceController::class, 'expenseUpdate'])->name('finance.expense.update');
    Route::delete('/finance/expense/{expense}', [\App\Http\Controllers\Admin\FinanceController::class, 'expenseDestroy'])->name('finance.expense.destroy');

    // Finance - Profit & Loss
    Route::get('/finance/profit-loss', [\App\Http\Controllers\Admin\FinanceController::class, 'profitLoss'])->name('finance.profit-loss');
    Route::get('/finance/profit-loss/export-pdf', [\App\Http\Controllers\Admin\FinanceController::class, 'profitLossExportPdf'])->name('finance.profit-loss.export-pdf');

    // Obat Ternak
    Route::resource('medicines', \App\Http\Controllers\Admin\MedicineController::class);
    Route::resource('medicine-logs', \App\Http\Controllers\Admin\MedicineLogController::class)->only(['index', 'create', 'store']);

    // Pakan Ternak
    Route::resource('feeds', \App\Http\Controllers\Admin\FeedController::class);
    Route::resource('feed-logs', \App\Http\Controllers\Admin\FeedLogController::class)->only(['index', 'create', 'store']);

    // Perawatan Tanaman
    Route::resource('plant-cares', \App\Http\Controllers\Admin\PlantCareController::class);
    Route::resource('plant-care-logs', \App\Http\Controllers\Admin\PlantCareLogController::class)->only(['index', 'create', 'store']);

    Route::resource('income-sources', \App\Http\Controllers\Admin\IncomeSourceController::class);

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Ternak Masuk
    Route::get('/livestock-movements/in', [\App\Http\Controllers\Admin\LivestockMovementController::class, 'inIndex'])->name('livestock-movements.in.index');
    Route::get('/livestock-movements/in/create', [\App\Http\Controllers\Admin\LivestockMovementController::class, 'inCreate'])->name('livestock-movements.in.create');
    Route::post('/livestock-movements/in', [\App\Http\Controllers\Admin\LivestockMovementController::class, 'inStore'])->name('livestock-movements.in.store');

    // Ternak Keluar
    Route::get('/livestock-movements/out', [\App\Http\Controllers\Admin\LivestockController::class, 'outIndex'] ?? \App\Http\Controllers\Admin\LivestockMovementController::class)->name('livestock-movements.out.index');
    Route::get('/livestock-movements/out/create', [\App\Http\Controllers\Admin\LivestockMovementController::class, 'outCreate'])->name('livestock-movements.out.create');
    Route::post('/livestock-movements/out', [\App\Http\Controllers\Admin\LivestockMovementController::class, 'outStore'])->name('livestock-movements.out.store');

    Route::resource('kandangs', \App\Http\Controllers\Admin\KandangController::class);
    Route::resource('farms', \App\Http\Controllers\Admin\FarmController::class);
});

Route::get('/cek-id-kota/{id}', function ($id) {
    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
        ->withHeaders(['key' => config('rajaongkir.api_key')])
        ->get(config('rajaongkir.base_url') . '/destination/domestic-destination', [
            'search' => 'Curug', // Coba pakai keyword kecamatan Curug atau Tangerang
            'limit'  => 50,
        ]);

    if ($response->successful()) {
        return response()->json([
            'status' => 'sukses',
            'raw_data' => $response->json()['data'] ?? []
        ]);
    }

    return response()->json(['error' => 'Gagal konek ke API Komerce'], 500);
});
Route::get('/test-komerce', function () {
    $apiKey = config('rajaongkir.api_key');
    $baseUrl = config('rajaongkir.base_url');
    
    if (empty($apiKey)) {
        return "API KEY KOSONG! Cek file .env lu bro.";
    }

    try {
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->timeout(10)
            ->withHeaders(['key' => $apiKey])
            ->get("{$baseUrl}/destination/domestic-destination", [
                'search' => 'Curug',
                'limit' => 1
            ]);
            
        if ($response->successful()) {
            return response()->json([
                'status' => 'MANTAP BERHASIL KONEK BRO!',
                'data' => $response->json()
            ]);
        }
        
        return response()->json([
            'status' => 'GAGAL KONEK KE SERVER KOMERCE!',
            'alasan_dari_server' => $response->body()
        ]);
        
    } catch (\Exception $e) {
        return "ERROR KONEKSI LAPTOP LU (CURL): " . $e->getMessage();
    }
});

require __DIR__.'/auth.php';