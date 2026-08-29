<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Webhook Endpoint untuk Callback Notifikasi Midtrans.
     */
    public function handleCallback(Request $request): JsonResponse
    {
        try {
            $payment = $this->paymentService->processMidtransWebhook($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Callback processed successfully',
                'payment_number' => $payment->payment_number,
                'payment_status' => $payment->status,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
