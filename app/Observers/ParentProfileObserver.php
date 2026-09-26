<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\ParentProfile;

class ParentProfileObserver
{
    public function created(ParentProfile $parent): void
    {
        AuditLog::record(
            action: 'create_parent',
            auditable: $parent,
            oldValues: null,
            newValues: [
                'full_name' => $parent->full_name,
                'phone' => $parent->phone,
                'contact_email' => $parent->contact_email,
                'address' => $parent->address,
                'user_id' => $parent->user_id,
            ]
        );
    }

    public function updated(ParentProfile $parent): void
    {
        $dirty = $parent->getDirty();
        unset($dirty['updated_at'], $dirty['created_at']);

        if (empty($dirty)) {
            return;
        }

        $oldValues = [];
        $newValues = [];
        foreach ($dirty as $key => $newValue) {
            $oldValues[$key] = $parent->getOriginal($key);
            $newValues[$key] = $newValue;
        }

        AuditLog::record(
            action: 'update_parent',
            auditable: $parent,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    public function deleted(ParentProfile $parent): void
    {
        AuditLog::record(
            action: 'delete_parent',
            auditable: $parent,
            oldValues: [
                'full_name' => $parent->full_name,
                'phone' => $parent->phone,
            ],
            newValues: null
        );
    }
}
