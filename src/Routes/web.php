<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;
use Aghaeian\Iyzico\Http\Controllers\IyzicoController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    // Ödeme formunu gösteren rota
    Route::get('iyzico-payment', [
        IyzicoController::class, 'showPaymentForm'])->name('iyzico.payment.form');

    // Ödeme işlemini gerçekleştiren rota
    Route::post('iyzico-payment', [
        IyzicoController::class, 'processPayment'
    ])->withoutMiddleware(VerifyCsrfToken::class)->name('iyzico.payment.process');
});

// Başarı sayfası rotası
Route::get('/checkout/success', 'Webkul\Shop\Http\Controllers\CheckoutController@success')->name('shop.checkout.onepage.success');
