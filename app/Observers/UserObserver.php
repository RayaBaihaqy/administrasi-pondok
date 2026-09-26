<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        // Catat jika akun yang dibuat adalah staff (super_admin atau admin)
        if ($user->isInternalStaff()) {
            AuditLog::record(
                action: 'create_admin',
                auditable: $user,
                oldValues: null,
                newValues: [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                ]
            );
        }
    }

    public function updated(User $user): void
    {
        if (! $user->isInternalStaff()) {
            return;
        }

        $dirty = $user->getDirty();
        unset($dirty['updated_at'], $dirty['created_at'], $dirty['password'], $dirty['remember_token']);

        if (empty($dirty)) {
            return;
        }

        $oldValues = [];
        $newValues = [];
        foreach ($dirty as $key => $newValue) {
            $oldValues[$key] = $user->getOriginal($key);
            $newValues[$key] = $newValue;
        }

        AuditLog::record(
            action: 'update_admin',
            auditable: $user,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    public function deleted(User $user): void
    {
        if ($user->isInternalStaff()) {
            AuditLog::record(
                action: 'delete_admin',
                auditable: $user,
                oldValues: [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                newValues: null
            );
        }
    }
}
