<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Payment;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        // Hindari duplikasi jika sudah dicatat via PaymentService::recordManualPayment
        if (request()->has('_skip_payment_observer_audit')) {
            return;
        }

        AuditLog::record(
            action: $payment->source === Payment::SOURCE_MANUAL ? 'record_manual_payment' : 'create_payment',
            auditable: $payment,
            oldValues: null,
            newValues: [
                'payment_number' => $payment->payment_number,
                'bill_id' => $payment->bill_id,
                'student_id' => $payment->student_id,
                'amount' => $payment->amount,
                'source' => $payment->source,
                'method' => $payment->method,
                'status' => $payment->status,
                'paid_at' => $payment->paid_at,
            ]
        );
    }

    public function updated(Payment $payment): void
    {
        $dirty = $payment->getDirty();
        unset($dirty['updated_at'], $dirty['created_at']);

        if (empty($dirty)) {
            return;
        }

        $oldValues = [];
        $newValues = [];
        foreach ($dirty as $key => $newValue) {
            $oldValues[$key] = $payment->getOriginal($key);
            $newValues[$key] = $newValue;
        }

        AuditLog::record(
            action: 'update_payment',
            auditable: $payment,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }
}
