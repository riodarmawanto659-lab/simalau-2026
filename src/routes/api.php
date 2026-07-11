<?php

use App\Http\Controllers\MidtransPaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/midtrans/notification', [MidtransPaymentController::class, 'notification'])
    ->name('midtrans.notification');