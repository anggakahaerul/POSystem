<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentTermController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\POPaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    return auth()->check() 
        ? redirect()->route('dashboard') 
        : redirect()->route('login');
});

// Guest routes (Authentication)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data - Categories
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Master Data - Products
    Route::resource('products', ProductController::class)->except(['show']);

    // Master Data - Vendors
    Route::resource('vendors', VendorController::class);

    // Master Data - Branches
    Route::resource('branches', BranchController::class)->except(['show']);

    // Master Data - Payment Terms
    Route::resource('payment-terms', PaymentTermController::class)->except(['show']);

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('/purchase-orders/{purchaseOrder}/submit', [PurchaseOrderController::class, 'submit'])->name('purchase-orders.submit');
    Route::post('/purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::post('/purchase-orders/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])->name('purchase-orders.reject');
    Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::get('/purchase-orders/{purchaseOrder}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');
    Route::get('/purchase-orders/{purchaseOrder}/preview', [PurchaseOrderController::class, 'preview'])->name('purchase-orders.preview');

    // PO Payments (Installments)
    Route::post('/purchase-orders/{purchaseOrder}/payments', [POPaymentController::class, 'store'])->name('po-payments.store');
    Route::post('/purchase-orders/{purchaseOrder}/payments/quick', [POPaymentController::class, 'quickCreate'])->name('po-payments.quick');
    Route::post('/po-payments/{payment}/record', [POPaymentController::class, 'recordPayment'])->name('po-payments.record');
    Route::delete('/po-payments/{payment}', [POPaymentController::class, 'destroy'])->name('po-payments.destroy');

    // User Management (Admin only)
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('permissions', PermissionController::class)->except(['show']);

    // API for Products (AJAX)
    Route::get('/api/products/{product}', [ProductController::class, 'getProduct'])->name('api.products.show');
    Route::get('/api/products', [ProductController::class, 'getProducts'])->name('api.products.index');

    // Settings (Admin only)
    Route::get('/settings/email', [SettingController::class, 'email'])->name('settings.email');
    Route::post('/settings/email', [SettingController::class, 'updateEmail'])->name('settings.email.update');
    Route::post('/settings/email/test', [SettingController::class, 'testEmail'])->name('settings.email.test');
});
