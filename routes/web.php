<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Admin\BannerController;
// ── Controller Utama ─────────────────────────────────────────
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RajaOngkirController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\CropTypeController;
use App\Http\Controllers\LivestockTypeController;
use App\Http\Controllers\ExpenseCategoryController;

// ── Controller Buyer ─────────────────────────────────────────
use App\Http\Controllers\Buyer\MarketplaceController;
use App\Http\Controllers\Buyer\CartController;
use App\Http\Controllers\Buyer\CheckoutController;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\Buyer\AddressController;
use App\Http\Controllers\Buyer\ShippingController;
use App\Http\Controllers\Buyer\ReviewController;
use App\Http\Controllers\PaymentController;

// ── Controller Admin ─────────────────────────────────────────
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CropVarietyController;
use App\Http\Controllers\Admin\HarvestController;
use App\Http\Controllers\Admin\CropController;
use App\Http\Controllers\Admin\LivestockController;
use App\Http\Controllers\Admin\LivestockMovementController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\MedicineController;
use App\Http\Controllers\Admin\MedicineLogController;
use App\Http\Controllers\Admin\FeedController;
use App\Http\Controllers\Admin\FeedLogController;
use App\Http\Controllers\Admin\PlantCareController;
use App\Http\Controllers\Admin\PlantCareLogController;
use App\Http\Controllers\Admin\IncomeSourceController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\KandangController;
use App\Http\Controllers\Admin\FarmController;
use App\Http\Controllers\Admin\FeedScheduleController;

Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');


Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('about');
})->name('about');

// Halaman kalkulator ongkir umum
Route::get('/rajaongkir', [RajaOngkirController::class, 'index'])->name('rajaongkir.index');
Route::get('/rajaongkir/search', [RajaOngkirController::class, 'searchDestination'])->name('rajaongkir.search');

// Midtrans Notification Webhook (Harus bebas dari middleware auth agar bisa diakses server Midtrans)
Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ── BUYER ROUTES ─────────────────────────────────────────────
// ── BUYER ROUTES ─────────────────────────────────────────────
Route::middleware(['auth', 'role:buyer'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/home', function () {
        return view('buyer.home');
    })->name('home');

    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace');
    Route::get('/marketplace/{product}', [MarketplaceController::class, 'show'])->name('marketplace.show');

    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/{product}/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{id}/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // 🚀 FIX: checkout sekarang POST (bukan GET) karena butuh kirim selected_ids[]
    // dari checkbox di halaman cart. Dipecah jadi 2 path beda biar tidak bentrok.
    Route::post('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('checkout.store');

    // ── Rute Midtrans Snap Token ──
    Route::get('/payment/snap-token/{order}', [PaymentController::class, 'getSnapToken'])->name('payment.snap-token');

    // Daftar pesanan
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');

    // 🚀 Detail pesanan pakai order_number (route model binding)
    Route::get('/orders/{order:order_number}', [OrderController::class, 'show'])->name('orders.show');

    // Batalin pesanan tetep pakai {id} numerik biar gampang nembak form
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Alamat Tersimpan
    Route::resource('addresses', AddressController::class);
    Route::patch('/addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('addresses.set-default');

    // Shipping & RajaOngkir (API v1 Komerce)
    Route::get('/shipping/search', [ShippingController::class, 'searchDestination'])->name('shipping.search');
    Route::post('/shipping/ongkir', [ShippingController::class, 'getOngkir'])->name('shipping.ongkir');

    // Reviews
    Route::get('/orders/{order}/review/{productId}', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/orders/{order}/review/{productId}', [ReviewController::class, 'store'])->name('reviews.store');
});

// ── ADMIN ROUTES ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    //home banner
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
Route::get('/banners/create', [BannerController::class, 'create'])->name('banners.create');
Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
Route::get('/banners/{banner}/edit', [BannerController::class, 'edit'])->name('banners.edit');
Route::put('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');
Route::patch('/banners/{banner}/toggle', [BannerController::class, 'toggleActive'])->name('banners.toggle');

    // Master Data
    Route::resource('categories', CategoryController::class);
    Route::resource('units', UnitController::class);
    Route::resource('crop-types', CropTypeController::class);
    Route::resource('livestock-types', LivestockTypeController::class);
    Route::resource('expense-categories', ExpenseCategoryController::class);
    Route::resource('crop-varieties', CropVarietyController::class);
    Route::resource('income-sources', IncomeSourceController::class);
    Route::resource('kandangs', KandangController::class);
    Route::resource('farms', FarmController::class);

    // Harvest
    Route::resource('harvests', HarvestController::class);
    Route::get('/harvests-export', [HarvestController::class, 'exportExcel'])->name('harvests.export');

    // Crops
    Route::post('/crops/bulk', [CropController::class, 'storeBulk'])->name('crops.store-bulk');
    Route::get('/crops-trash', [CropController::class, 'trash'])->name('crops.trash');
    Route::patch('/crops/{id}/restore', [CropController::class, 'restore'])->name('crops.restore');
    Route::delete('/crops/{id}/force-delete', [CropController::class, 'forceDelete'])->name('crops.force-delete');
    Route::resource('crops', CropController::class);

    // Livestock
    Route::get('/livestock-trash', [LivestockController::class, 'trash'])->name('livestock.trash');
    Route::patch('/livestock/{id}/restore', [LivestockController::class, 'restore'])->name('livestock.restore');
    Route::delete('/livestock/{id}/force-delete', [LivestockController::class, 'forceDelete'])->name('livestock.force-delete');
    Route::post('/livestock/store-bulk', [LivestockController::class, 'storeBulk'])->name('livestock.store-bulk');
    Route::resource('livestock', LivestockController::class);
    
    // ── Mutasi Ternak (Masuk & Keluar) ───────────────────────
    Route::get('livestock-movements/in', [LivestockMovementController::class, 'inIndex'])->name('livestock-movements.in.index');
    Route::get('livestock-movements/in/create', [LivestockMovementController::class, 'inCreate'])->name('livestock-movements.in.create');
    Route::post('livestock-movements/in', [LivestockMovementController::class, 'inStore'])->name('livestock-movements.in.store');
    Route::post('livestock-movements/in/bulk', [LivestockMovementController::class, 'inStoreBulk'])->name('livestock-movements.in.store-bulk');

    Route::get('livestock-movements/out', [LivestockMovementController::class, 'outIndex'])->name('livestock-movements.out.index');
    Route::get('livestock-movements/out/create', [LivestockMovementController::class, 'outCreate'])->name('livestock-movements.out.create');
    Route::post('livestock-movements/out', [LivestockMovementController::class, 'outStore'])->name('livestock-movements.out.store');
    Route::post('livestock-movements/out/bulk', [LivestockMovementController::class, 'outStoreBulk'])->name('livestock-movements.out.store-bulk');

    // Product
    Route::get('/products-trash', [ProductController::class, 'trash'])->name('products.trash');
    Route::patch('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');
    Route::resource('products', ProductController::class);

    // Stock Barang Masuk & Keluar
    
    Route::post('/stock/in/store-bulk', [StockMovementController::class, 'inStoreBulk'])->name('stock.in.store-bulk');
    Route::get('/stock/in', [StockMovementController::class, 'inIndex'])->name('stock.in.index');
    Route::get('/stock/in/create', [StockMovementController::class, 'inCreate'])->name('stock.in.create');
    Route::post('/stock/in', [StockMovementController::class, 'inStore'])->name('stock.in.store');
    Route::get('/stock/in-export', [StockMovementController::class, 'exportInExcel'])->name('stock.in.export');
    
    Route::post('/stock/out/store-bulk', [StockMovementController::class, 'outStoreBulk'])->name('stock.out.store-bulk');
    Route::get('/stock/out', [StockMovementController::class, 'outIndex'])->name('stock.out.index');
    Route::get('/stock/out/create', [StockMovementController::class, 'outCreate'])->name('stock.out.create');
    Route::post('/stock/out', [StockMovementController::class, 'outStore'])->name('stock.out.store');
    Route::get('/stock/out-export', [StockMovementController::class, 'exportOutExcel'])->name('stock.out.export');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{order}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::patch('/transactions/{order}/status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');
    Route::patch('/transactions/{order}/tracking', [TransactionController::class, 'updateTracking'])->name('transactions.update-tracking');
    
    Route::get('/transactions-export', [TransactionController::class, 'exportExcel'])->name('transactions.export');

    // Finance (Income & Expense)
    Route::get('/finance/income', [FinanceController::class, 'incomeIndex'])->name('finance.income.index');
    Route::get('/finance/income/create', [FinanceController::class, 'incomeCreate'])->name('finance.income.create');
    Route::post('/finance/income', [FinanceController::class, 'incomeStore'])->name('finance.income.store');
    Route::get('/finance/income/{income}/edit', [FinanceController::class, 'incomeEdit'])->name('finance.income.edit');
    Route::put('/finance/income/{income}', [FinanceController::class, 'incomeUpdate'])->name('finance.income.update');
    Route::delete('/finance/income/{income}', [FinanceController::class, 'incomeDestroy'])->name('finance.income.destroy');

    Route::get('/finance/expense', [FinanceController::class, 'expenseIndex'])->name('finance.expense.index');
    Route::get('/finance/expense/create', [FinanceController::class, 'expenseCreate'])->name('finance.expense.create');
    Route::post('/finance/expense', [FinanceController::class, 'expenseStore'])->name('finance.expense.store');
    Route::get('/finance/expense/{expense}/edit', [FinanceController::class, 'expenseEdit'])->name('finance.expense.edit');
    Route::put('/finance/expense/{expense}', [FinanceController::class, 'expenseUpdate'])->name('finance.expense.update');
    Route::delete('/finance/expense/{expense}', [FinanceController::class, 'expenseDestroy'])->name('finance.expense.destroy');

    Route::get('/finance/profit-loss', [FinanceController::class, 'profitLoss'])->name('finance.profit-loss');
    Route::get('/finance/profit-loss/export-pdf', [FinanceController::class, 'profitLossExportPdf'])->name('finance.profit-loss.export-pdf');

    // Modul Operasional Ternak & Tanaman
    Route::resource('medicines', MedicineController::class);
    Route::resource('medicine-logs', MedicineLogController::class)->only(['index', 'create', 'store']);
    Route::resource('feeds', FeedController::class);
    Route::resource('feed-logs', FeedLogController::class)->only(['index', 'create', 'store']);
    Route::resource('plant-cares', PlantCareController::class);
    Route::resource('plant-care-logs', PlantCareLogController::class)->only(['index', 'create', 'store']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    
Route::get('/feed-schedules', [FeedScheduleController::class, 'index'])->name('feed-schedules.index');
Route::post('/feed-schedules', [FeedScheduleController::class, 'store'])->name('feed-schedules.store');
Route::patch('/feed-schedules/{feedSchedule}/toggle', [FeedScheduleController::class, 'toggleActive'])->name('feed-schedules.toggle');
Route::delete('/feed-schedules/{feedSchedule}', [FeedScheduleController::class, 'destroy'])->name('feed-schedules.destroy');


});

// ── API / Testing Routes ─────────────────────────────────────
Route::get('/cek-id-kota/{id}', function ($id) {
    $response = Http::withoutVerifying()
        ->withHeaders(['key' => config('rajaongkir.api_key')])
        ->get(config('rajaongkir.base_url') . '/destination/domestic-destination', [
            'search' => 'Curug',
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
        $response = Http::withoutVerifying()
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