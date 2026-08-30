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

Route::get('/docs/invoice/{billNumber}', [\App\Http\Controllers\DocumentController::class, 'downloadInvoice'])->name('public.invoice');
Route::get('/docs/receipt/{paymentNumber}', [\App\Http\Controllers\DocumentController::class, 'downloadReceipt'])->name('public.receipt');

Route::post('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
