<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/payment/success', function () {
    return response()->view('payment.status', ['status' => 'success']);
})->name('payment.success');

Route::get('/payment/pending', function () {
    return response()->view('payment.status', ['status' => 'pending']);
})->name('payment.pending');

Route::get('/payment/failed', function () {
    return response()->view('payment.status', ['status' => 'failed']);
})->name('payment.failed');

Route::get('/parent', function () {
    return redirect('/portal');
});

Route::post('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
