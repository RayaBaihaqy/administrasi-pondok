<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentEvidence;
use App\Models\PaymentGatewayTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap as MidtransSnap;
use RuntimeException;
use Throwable;

class PaymentService
{
    /**
     * Konfigurasi SDK Midtrans.
     */
    protected function setupMidtrans(): void
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('midtrans.is_production', false);
        MidtransConfig::$isSanitized = true;
        MidtransConfig::$is3ds = (bool) config('midtrans.is_3ds', true);
    }

    /**
     * Catat pembayaran manual (offline / cash / transfer bank manual) oleh admin.
     */
    public function recordManualPayment(
        Bill $bill,
        int $amount,
        string $method = 'cash',
        ?string $notes = null,
        ?User $recorder = null,
        ?array $evidenceData = null
    ): Payment {
        if ($amount <= 0) {
            throw new RuntimeException('Nominal pembayaran harus lebih dari 0.');
        }

        if ($amount > $bill->outstanding_amount) {
            throw new RuntimeException('Nominal pembayaran (Rp '.number_format($amount, 0, ',', '.').') melebihi sisa tagihan (Rp '.number_format($bill->outstanding_amount, 0, ',', '.').').');
        }

        return DB::transaction(function () use ($bill, $amount, $method, $notes, $recorder, $evidenceData) {
            $payment = Payment::create([
                'bill_id' => $bill->id,
                'student_id' => $bill->student_id,
                'parent_id' => $bill->parent_id,
                'payment_number' => Payment::generatePaymentNumberFromBill($bill, Payment::SOURCE_MANUAL),
                'amount' => $amount,
                'source' => Payment::SOURCE_MANUAL,
                'method' => $method,
                'status' => Payment::STATUS_SUCCESS,
                'paid_at' => now(),
                'notes' => $notes,
                'recorded_by' => $recorder?->id,
            ]);

            // Jika ada bukti pembayaran yang diupload
            if ($evidenceData && isset($evidenceData['file_path'])) {
                PaymentEvidence::create([
                    'payment_id' => $payment->id,
                    'file_name' => $evidenceData['file_name'] ?? basename($evidenceData['file_path']),
                    'file_path' => $evidenceData['file_path'],
                    'mime_type' => $evidenceData['mime_type'] ?? null,
                    'file_size' => $evidenceData['file_size'] ?? null,
                    'uploaded_by' => $recorder?->id,
                ]);
            }

            // Recalculate bill amounts and status
            $bill->recalculateStatusAndAmounts();

            // Auto-log payment receipt WhatsApp notification
            try {
                (new WhatsAppAutomationService)->logPaymentSuccess($payment);
            } catch (Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to log payment success WhatsApp: '.$e->getMessage());
            }

            // Record to AuditLog
            \App\Models\AuditLog::record(
                action: 'record_manual_payment',
                auditable: $payment,
                newValues: [
                    'bill_number' => $bill->bill_number,
                    'amount' => $amount,
                    'method' => $method,
                ],
                actor: $recorder
            );

            return $payment;
        });
    }

    /**
     * Buat transaksi pembayaran online via Midtrans Snap Token.
     */
    public function createMidtransPayment(Bill $bill, ?int $customAmount = null): Payment
    {
        $amount = $customAmount ?? $bill->outstanding_amount;

        if ($amount <= 0) {
            throw new RuntimeException('Sisa tagihan sudah lunas.');
        }

        if ($amount > $bill->outstanding_amount) {
            throw new RuntimeException('Nominal pembayaran melebihi sisa tagihan.');
        }

        $this->setupMidtrans();

        $paymentNumber = Payment::generatePaymentNumberFromBill($bill, Payment::SOURCE_MIDTRANS);

        $student = $bill->student;
        $parentUser = $bill->parentProfile?->user;

        $params = [
            'transaction_details' => [
                'order_id' => $paymentNumber,
                'gross_amount' => $amount,
            ],
            'item_details' => [
                [
                    'id' => $bill->bill_number,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => mb_strimwidth($bill->paymentType?->name.' - '.$student?->full_name, 0, 50, '...'),
                ],
            ],
            'customer_details' => [
                'first_name' => $student?->full_name ?? 'Siswa',
                'email' => $parentUser?->email ?? 'wali@pondok.test',
                'phone' => $bill->parentProfile?->phone ?? '08123456789',
            ],
            'callbacks' => [
                'finish' => url('/payment/success'),
            ],
        ];

        try {
            $snapToken = MidtransSnap::getSnapToken($params);
        } catch (Throwable $e) {
            throw new RuntimeException('Gagal menghubungi Midtrans Gateway: '.$e->getMessage());
        }

        return Payment::create([
            'bill_id' => $bill->id,
            'student_id' => $bill->student_id,
            'parent_id' => $bill->parent_id,
            'payment_number' => $paymentNumber,
            'amount' => $amount,
            'source' => Payment::SOURCE_MIDTRANS,
            'status' => Payment::STATUS_PENDING,
            'snap_token' => $snapToken,
            'transaction_reference' => $paymentNumber,
        ]);
    }

    /**
     * Memproses Webhook Notification dari Midtrans dengan SHA512 signature verification & Idempotency.
     */
    public function processMidtransWebhook(array $payload): Payment
    {
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $serverKey = config('midtrans.server_key');

        if (! $orderId || ! $statusCode || ! $grossAmount || ! $signatureKey) {
            throw new RuntimeException('Payload webhook Midtrans tidak valid.');
        }

        // 1. SHA512 Signature Verification (Timing-safe comparison)
        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);
        if (! hash_equals($expectedSignature, $signatureKey)) {
            throw new RuntimeException('Midtrans SHA512 signature mismatch / untrusted webhook request.');
        }

        // 2. Cari data Payment berdasarkan transaction_reference / payment_number
        $payment = Payment::where('transaction_reference', $orderId)
            ->orWhere('payment_number', $orderId)
            ->first();

        if (! $payment) {
            throw new RuntimeException("Transaksi pembayaran dengan referensi [$orderId] tidak ditemukan.");
        }

        // 3. Idempotency Check: Jika payment sudah sukses, abaikan callback duplikat
        if ($payment->status === Payment::STATUS_SUCCESS && in_array($transactionStatus, ['capture', 'settlement'])) {
            return $payment;
        }

        // 4. Map Midtrans transaction status
        $newPaymentStatus = match ($transactionStatus) {
            'capture' => ($fraudStatus === 'challenge') ? Payment::STATUS_PENDING : Payment::STATUS_SUCCESS,
            'settlement' => Payment::STATUS_SUCCESS,
            'pending' => Payment::STATUS_PENDING,
            'deny', 'cancel' => Payment::STATUS_FAILED,
            'expire' => Payment::STATUS_EXPIRED,
            default => Payment::STATUS_FAILED,
        };

        return DB::transaction(function () use ($payment, $newPaymentStatus, $payload, $orderId, $transactionStatus, $grossAmount) {
            $payment->status = $newPaymentStatus;

            if ($newPaymentStatus === Payment::STATUS_SUCCESS && ! $payment->paid_at) {
                $payment->paid_at = now();
            }

            if (! empty($payload['payment_type'])) {
                $payment->method = $payload['payment_type'];
            }

            $payment->save();

            // Catat ke log tabel payment_gateway_transactions
            PaymentGatewayTransaction::create([
                'payment_id' => $payment->id,
                'provider' => 'midtrans',
                'order_id' => $orderId,
                'transaction_id' => $payload['transaction_id'] ?? null,
                'transaction_status' => $transactionStatus,
                'gross_amount' => (int) $grossAmount,
                'payment_type' => $payload['payment_type'] ?? null,
                'signature_key' => $payload['signature_key'] ?? null,
                'raw_response' => $payload,
            ]);

            // Re-calculate Bill status and outstanding amounts
            $payment->bill?->recalculateStatusAndAmounts();

            // Auto-log payment receipt WhatsApp notification
            if ($newPaymentStatus === Payment::STATUS_SUCCESS) {
                try {
                    (new WhatsAppAutomationService)->logPaymentSuccess($payment);
                } catch (Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to log payment success WhatsApp: '.$e->getMessage());
                }
            }

            return $payment;
        });
    }
}
