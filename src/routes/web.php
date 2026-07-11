<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\MidtransPaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

/* NOTE: Do Not Remove
/ Livewire asset handling if using sub folder in domain
*/

Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix') . '/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix') . '/livewire/livewire.js', $handle);
});
/*
/ END
*/

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/layanan', [PublicController::class, 'services'])->name('services.index');
Route::get('/layanan/{layananLaundry:slug}', [PublicController::class, 'serviceDetail'])->name('services.show');
Route::get('/cek-status', [PublicController::class, 'status'])->name('status.form');
Route::post('/cek-status', [PublicController::class, 'checkStatus'])->name('status.check');
Route::get('/kontak', [PublicController::class, 'contact'])->name('contact');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/registrasi', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registrasi', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')
    ->prefix('dashboard')
    ->name('customer.')
    ->group(function (): void {
        Route::get('/', [CustomerDashboardController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/pesanan', [CustomerDashboardController::class, 'orders'])
            ->name('orders.index');

        Route::get('/pesanan/buat', [OrderController::class, 'create'])
            ->name('orders.create');

        Route::post('/pesanan', [OrderController::class, 'store'])
            ->name('orders.store');

        Route::get('/pesanan/{pesanan}', [CustomerDashboardController::class, 'orderDetail'])
            ->name('orders.show');

        Route::post('/pesanan/{pesanan}/bayar', [CustomerDashboardController::class, 'payOrder'])
            ->name('orders.pay');

        Route::get('/pembayaran/{pembayaran}/midtrans', [MidtransPaymentController::class, 'pay'])
            ->name('payments.midtrans');

        Route::get('/pembayaran/{pembayaran}/midtrans/callback', [MidtransPaymentController::class, 'callback'])
            ->name('payments.midtrans.callback');

        Route::get('/profil', [CustomerDashboardController::class, 'profile'])
            ->name('profile');

        Route::put('/profil', [CustomerDashboardController::class, 'updateProfile'])
            ->name('profile.update');
    });