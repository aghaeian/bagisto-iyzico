<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;
use Aghaeian\Iyzico\Http\Controllers\IyzicoController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    // Ödeme formunu gösteren rota
    Route::get('iyzico-payment', [
        IyzicoController::class, 'showPaymentForm'
    ])->name('iyzico.payment.form');

    // Ödeme işlemini gerçekleştiren rota
    Route::post('iyzico-payment', [
        IyzicoController::class, 'processPayment'
    ])->withoutMiddleware(VerifyCsrfToken::class)->name('iyzico.payment.process');

    // Ödeme süreci için checkout rotası
    Route::get('iyzico-payment/checkout', [
        IyzicoController::class, 'checkout'
    ])->name('iyzico.payment.checkout'); // Yeni eklenen rota
});
