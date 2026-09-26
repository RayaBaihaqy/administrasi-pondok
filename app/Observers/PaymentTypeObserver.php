<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\PaymentType;

class PaymentTypeObserver
{
    public function created(PaymentType $type): void
    {
        AuditLog::record(
            action: 'create_payment_type',
            auditable: $type,
            oldValues: null,
            newValues: [
                'code' => $type->code,
                'name' => $type->name,
                'billing_type' => $type->billing_type,
                'default_amount' => $type->default_amount,
                'is_active' => $type->is_active,
            ]
        );
    }

    public function updated(PaymentType $type): void
    {
        $dirty = $type->getDirty();
        unset($dirty['updated_at'], $dirty['created_at']);

        if (empty($dirty)) {
            return;
        }

        $oldValues = [];
        $newValues = [];
        foreach ($dirty as $key => $newValue) {
            $oldValues[$key] = $type->getOriginal($key);
            $newValues[$key] = $newValue;
        }

        AuditLog::record(
            action: 'update_payment_type',
            auditable: $type,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    public function deleted(PaymentType $type): void
    {
        AuditLog::record(
            action: 'delete_payment_type',
            auditable: $type,
            oldValues: [
                'code' => $type->code,
                'name' => $type->name,
            ],
            newValues: null
        );
    }
}
