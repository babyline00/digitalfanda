<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PayoutController as AdminPayoutController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\LibraryController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\PurchaseController;
use App\Http\Controllers\Seller\CouponController as SellerCouponController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\PayoutController as SellerPayoutController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\SalesController as SellerSalesController;
use App\Http\Controllers\Seller\SettingsController as SellerSettingsController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CatalogController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\DownloadController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\WishlistController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Storefront
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/shop/category/{category:slug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product/{product:slug}/review', [ProductController::class, 'storeReview'])->name('product.review')->middleware('auth');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{productId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{productId}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

// Wishlist
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');
});

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success/{number}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');

// Downloads
Route::get('/download/{token}', [DownloadController::class, 'download'])->name('download.file');
Route::get('/stream/{token}', [DownloadController::class, 'stream'])->name('download.stream');

// Auth (Breeze)
require __DIR__.'/auth.php';

// Role-based dashboard redirect
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return redirect()->route(match (auth()->user()->role) {
        'admin', 'superadmin' => 'admin.dashboard',
        'seller' => 'seller.dashboard',
        'cashier' => 'admin.pos.registers',
        default => 'account.dashboard',
    });
})->name('dashboard');

// Customer Dashboard
Route::middleware(['auth', 'verified', 'role:customer,seller,admin,superadmin'])->prefix('account')->name('account.')->group(function () {
    Route::get('/', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/library', [LibraryController::class, 'index'])->name('library');
    Route::get('/library/{product:slug}', [LibraryController::class, 'show'])->name('library.show');
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases');
    Route::get('/purchases/{order}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/{order}/download/{itemId}', [PurchaseController::class, 'download'])->name('purchases.download');
    Route::post('/purchases/{order}/refund', [PurchaseController::class, 'requestRefund'])->name('purchases.refund');
    Route::get('/profile', [CustomerProfileController::class, 'index'])->name('profile');
    Route::patch('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [CustomerProfileController::class, 'updatePassword'])->name('profile.password');
    Route::patch('/profile/avatar', [CustomerProfileController::class, 'updateAvatar'])->name('profile.avatar');
});

// Seller Dashboard
Route::middleware(['auth', 'verified', 'role:seller,admin,superadmin'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('products', SellerProductController::class)->except(['show']);
    Route::post('products/{product}/files', [SellerProductController::class, 'addFile'])->name('products.add-file');
    Route::delete('products/{product}/files/{file}', [SellerProductController::class, 'removeFile'])->name('products.remove-file');
    Route::post('products/{product}/lessons', [SellerProductController::class, 'addLesson'])->name('products.add-lesson');
    Route::put('products/{product}/lessons/{lesson}', [SellerProductController::class, 'updateLesson'])->name('products.update-lesson');
    Route::delete('products/{product}/lessons/{lesson}', [SellerProductController::class, 'deleteLesson'])->name('products.delete-lesson');
    
    Route::get('sales', [SellerSalesController::class, 'index'])->name('sales.index');
    Route::get('sales/{sale}', [SellerSalesController::class, 'show'])->name('sales.show');
    Route::get('sales/analytics', [SellerSalesController::class, 'analytics'])->name('sales.analytics');
    Route::get('sales/export', [SellerSalesController::class, 'export'])->name('sales.export');
    
    Route::get('payouts', [SellerPayoutController::class, 'index'])->name('payouts.index');
    Route::post('payouts/request', [SellerPayoutController::class, 'request'])->name('payouts.request');
    Route::get('payouts/{payout}', [SellerPayoutController::class, 'show'])->name('payouts.show');
    Route::get('payouts/settings', [SellerPayoutController::class, 'settings'])->name('payouts.settings');
    Route::patch('payouts/settings', [SellerPayoutController::class, 'updateSettings'])->name('payouts.settings.update');
    
    Route::resource('coupons', SellerCouponController::class);
    Route::post('coupons/generate-code', [SellerCouponController::class, 'generateCode'])->name('coupons.generate-code');
    
    Route::get('settings', [SellerSettingsController::class, 'index'])->name('settings');
    Route::patch('settings', [SellerSettingsController::class, 'update'])->name('settings.update');
});

// Admin Panel
Route::middleware(['auth', 'verified', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('users', AdminUserController::class)->except(['create', 'store']);
    Route::post('users/{user}/impersonate', [AdminUserController::class, 'impersonate'])->name('users.impersonate');
    Route::post('impersonate/stop', [AdminUserController::class, 'stopImpersonate'])->name('impersonate.stop');
    
    Route::resource('sellers', AdminSellerController::class)->only(['index', 'show']);
    Route::post('sellers/{seller}/approve', [AdminSellerController::class, 'approve'])->name('sellers.approve');
    Route::post('sellers/{seller}/reject', [AdminSellerController::class, 'reject'])->name('sellers.reject');
    Route::post('sellers/{seller}/suspend', [AdminSellerController::class, 'suspend'])->name('sellers.suspend');
    Route::post('sellers/{seller}/activate', [AdminSellerController::class, 'activate'])->name('sellers.activate');
    Route::patch('sellers/{seller}/commission', [AdminSellerController::class, 'updateCommission'])->name('sellers.commission');
    
    Route::resource('products', AdminProductController::class)->only(['index', 'show']);
    Route::post('products/{product}/approve', [AdminProductController::class, 'approve'])->name('products.approve');
    Route::post('products/{product}/reject', [AdminProductController::class, 'reject'])->name('products.reject');
    Route::post('products/{product}/feature', [AdminProductController::class, 'feature'])->name('products.feature');
    Route::post('products/{product}/archive', [AdminProductController::class, 'archive'])->name('products.archive');
    Route::post('products/bulk', [AdminProductController::class, 'bulkAction'])->name('products.bulk');
    
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
    Route::post('orders/{order}/mark-paid', [AdminOrderController::class, 'markPaid'])->name('orders.mark-paid');
    Route::post('orders/{order}/refund', [AdminOrderController::class, 'refund'])->name('orders.refund');
    Route::post('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    
    Route::resource('payouts', AdminPayoutController::class)->only(['index', 'show']);
    Route::post('payouts/{payout}/process', [AdminPayoutController::class, 'process'])->name('payouts.process');
    Route::post('payouts/{payout}/fail', [AdminPayoutController::class, 'fail'])->name('payouts.fail');
    Route::post('payouts/bulk-generate', [AdminPayoutController::class, 'bulkGenerate'])->name('payouts.bulk-generate');
    
    Route::resource('categories', CategoryController::class);
    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    
    Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings');
    Route::patch('settings', [AdminSettingsController::class, 'update'])->name('settings.update');
    
    // POS
    Route::get('pos/registers', [PosController::class, 'registers'])->name('pos.registers');
    Route::post('pos/registers', [PosController::class, 'openRegister'])->name('pos.registers.open');
    Route::get('pos/register/{register}', [PosController::class, 'register'])->name('pos.register');
    Route::post('pos/register/{register}/cart/add', [PosController::class, 'addToCart'])->name('pos.cart.add');
    Route::patch('pos/register/{register}/cart/{productId}', [PosController::class, 'updateCart'])->name('pos.cart.update');
    Route::delete('pos/register/{register}/cart', [PosController::class, 'clearCart'])->name('pos.cart.clear');
    Route::post('pos/register/{register}/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('pos/receipt/{order}', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::post('pos/register/{register}/close', [PosController::class, 'closeRegister'])->name('pos.register.close');
    Route::get('pos/register/{register}/report', [PosController::class, 'salesReport'])->name('pos.report');
});

// Seller application
Route::middleware('auth')->group(function () {
    Route::get('/become-seller', function () {
        $user = auth()->user();
        if ($user->seller) {
            return redirect()->route('seller.dashboard');
        }
        return view('seller.apply');
    })->name('seller.apply');
    
    Route::post('/become-seller', function (Request $request) {
        $user = auth()->user();
        abort_if($user->seller, 403, 'Already a seller');
        
        $request->validate([
            'store_name' => 'required|string|max:255|unique:sellers,store_name',
            'bio' => 'nullable|string',
            'website' => 'nullable|url',
            'payout_email' => 'nullable|email',
        ]);
        
        $user->seller()->create([
            'store_name' => $request->store_name,
            'slug' => \Illuminate\Support\Str::slug($request->store_name) . '-' . \Illuminate\Support\Str::random(4),
            'bio' => $request->bio,
            'website' => $request->website,
            'payout_email' => $request->payout_email ?? $user->email,
            'status' => 'pending',
        ]);
        
        $user->update(['role' => 'seller']);
        
        return redirect()->route('seller.dashboard')->with('success', 'Seller application submitted for review');
    })->name('seller.apply.submit');
});

// Affiliate tracking
Route::get('/ref/{code}', function (string $code) {
    $affiliate = \App\Models\Affiliate::where('code', $code)->first();
    if ($affiliate) {
        $affiliate->clicks()->create([
            'ip_hash' => hash('sha256', request()->ip()),
            'referrer' => request()->headers->get('referer'),
            'landing_page' => request()->fullUrl(),
        ]);
        session(['affiliate_code' => $code]);
    }
    return redirect('/shop');
})->name('affiliate.track');

// Newsletter
Route::post('/newsletter/subscribe', function (Request $request) {
    $request->validate(['email' => 'required|email', 'name' => 'nullable|string']);
    
    \App\Models\Subscriber::updateOrCreate(
        ['email' => $request->email],
        ['name' => $request->name, 'status' => 'subscribed', 'source' => $request->source ?? 'homepage', 'subscribed_at' => now()]
    );
    
    return response()->json(['success' => true, 'message' => 'Subscribed!']);
})->name('newsletter.subscribe');