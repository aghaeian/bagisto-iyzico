<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;
use Aghaeian\Iyzico\Http\Controllers\IyzicoController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    Route::get('iyzico-payment', [
        IyzicoController::class, 'showPaymentForm'])->name('iyzico.payment.form');

    Route::post('iyzico-payment', [
        IyzicoController::class, 'processPayment'
    ])->withoutMiddleware(VerifyCsrfToken::class)->name('iyzico.payment.process');
});

});
