<?php

declare(strict_types=1);

use App\Http\Controllers\Shop\Admin\CategoryController;
use App\Http\Controllers\Shop\Admin\CouponController;
use App\Http\Controllers\Shop\Admin\DashboardController;
use App\Http\Controllers\Shop\Admin\OrderController;
use App\Http\Controllers\Shop\Admin\ProductController;
use App\Http\Controllers\Shop\Admin\ShippingController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\PaymentWebhookController;
use App\Http\Controllers\Shop\ShopAuthController;
use App\Http\Controllers\Shop\StorefrontController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Oficiales — BLANKISOL SHOP (B2C & B2B)
|--------------------------------------------------------------------------
| Módulo comercial completamente autónomo, aislado de BLANKISOL SCGI.
*/

// =========================================================================
// 1. RUTAS PÚBLICAS DE LA TIENDA VIRTUAL (/shop)
// =========================================================================
Route::prefix('shop')->name('shop.')->group(function () {
    // Autenticación Comercial de la Tienda (Estilo Claro / Tuambia)
    Route::get('/login', [ShopAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ShopAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [ShopAuthController::class, 'logout'])->name('logout');

    // Registro de Nuevos Clientes (B2C & B2B)
    Route::get('/register', [ShopAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [ShopAuthController::class, 'register'])->name('register.submit');

    // Recuperación y Restablecimiento de Contraseñas
    Route::get('/forgot-password', [ShopAuthController::class, 'showForgotPasswordForm'])->name('forgot-password');
    Route::post('/forgot-password', [ShopAuthController::class, 'sendResetLink'])->name('forgot-password.submit');
    Route::get('/reset-password/{token}', [ShopAuthController::class, 'showResetPasswordForm'])->name('reset-password');
    Route::post('/reset-password', [ShopAuthController::class, 'resetPassword'])->name('reset-password.submit');

    // Autenticación y Registro con Cuenta Google / Gmail
    Route::get('/auth/google', [ShopAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [ShopAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Portada y Catálogo
    Route::get('/', [StorefrontController::class, 'index'])->name('index');
    Route::get('/products', [StorefrontController::class, 'products'])->name('products.index');
    Route::get('/products/{slug}', [StorefrontController::class, 'show'])->name('products.show');
    Route::get('/categories/{slug}', [StorefrontController::class, 'category'])->name('categories.show');

    // Carrito de Compras
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/items/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/items/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    // Flujo de Checkout Dual (B2C y B2B)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Pasarela y Webhook
    Route::get('/payment/sandbox/simulator', [PaymentWebhookController::class, 'sandboxSimulator'])->name('payment.sandbox.simulator');
    Route::post('/payment/sandbox/process', [PaymentWebhookController::class, 'sandboxProcess'])->name('payment.sandbox.process');
    Route::post('/payment/webhook', [PaymentWebhookController::class, 'webhook'])->name('payment.webhook');
});

// =========================================================================
// 2. PANEL ADMINISTRATIVO COMERCIAL (/shop/admin)
// Protegido por autenticación y el middleware exclusivo shop.admin (Director o Administrador_Tienda)
// =========================================================================
Route::middleware(['auth', 'shop.admin'])->prefix('shop/admin')->name('shop.admin.')->group(function () {
    
    // Dashboard Ejecutivo Comercial
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Gestión del Catálogo: Categorías
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::post('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Gestión del Catálogo: Productos
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::post('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Gestión Comercial: Pedidos
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
    });

    // Gestión Comercial: Promociones y Cupones
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', [CouponController::class, 'index'])->name('index');
        Route::post('/', [CouponController::class, 'store'])->name('store');
        Route::post('/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy');
    });

    // Gestión Logística: Envíos y Zonas
    Route::prefix('shipping')->name('shipping.')->group(function () {
        Route::get('/', [ShippingController::class, 'index'])->name('index');
        Route::post('/zones', [ShippingController::class, 'storeZone'])->name('zones.store');
        Route::post('/rates', [ShippingController::class, 'storeRate'])->name('rates.store');
        Route::delete('/rates/{rate}', [ShippingController::class, 'destroyRate'])->name('rates.destroy');
    });
});
