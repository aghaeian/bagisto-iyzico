<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;
use Aghaeian\Iyzico\Http\Controllers\IyzicoController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    // Ödeme formunu gösteren rota (yeni rota ismiyle)
    Route::get('iyzico-payment', [
        IyzicoController::class, 'showPaymentForm'])->name('iyzico.payment.form');

    // Ödeme işlemini gerçekleştiren rota
    Route::post('iyzico-payment', [
        IyzicoController::class, 'processPayment'
    ])->withoutMiddleware(VerifyCsrfToken::class)->name('iyzico.payment.process');

    // Eski rota ismiyle ödeme formunu gösteren rota (hata alan kodlar için)
    Route::get('iyzico-payment-checkout', [
        IyzicoController::class, 'showPaymentForm'])->name('iyzico.payment.checkout');
});
