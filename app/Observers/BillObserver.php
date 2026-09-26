<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Bill;

class BillObserver
{
    public function created(Bill $bill): void
    {
        AuditLog::record(
            action: 'create_bill',
            auditable: $bill,
            oldValues: null,
            newValues: [
                'bill_number' => $bill->bill_number,
                'student_id' => $bill->student_id,
                'student_name' => $bill->student?->full_name,
                'payment_type' => $bill->paymentType?->name,
                'amount' => $bill->amount,
                'billing_period' => $bill->billing_period,
                'due_date' => $bill->due_date,
                'status' => $bill->status,
            ]
        );
    }

    public function updated(Bill $bill): void
    {
        $dirty = $bill->getDirty();
        unset($dirty['updated_at'], $dirty['created_at']);

        if (empty($dirty)) {
            return;
        }

        $action = 'update_bill';
        if (isset($dirty['status']) && $dirty['status'] === Bill::STATUS_CANCELLED) {
            $action = 'cancel_bill';
        }

        $oldValues = [];
        $newValues = [];
        foreach ($dirty as $key => $newValue) {
            $oldValues[$key] = $bill->getOriginal($key);
            $newValues[$key] = $newValue;
        }

        AuditLog::record(
            action: $action,
            auditable: $bill,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    public function deleted(Bill $bill): void
    {
        AuditLog::record(
            action: 'delete_bill',
            auditable: $bill,
            oldValues: [
                'bill_number' => $bill->bill_number,
                'student_id' => $bill->student_id,
                'amount' => $bill->amount,
            ],
            newValues: null
        );
    }
}
