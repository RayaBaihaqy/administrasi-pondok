<?php

namespace App\Observers;

use App\Models\AcademicYear;
use App\Models\AuditLog;

class AcademicYearObserver
{
    public function created(AcademicYear $year): void
    {
        AuditLog::record(
            action: 'create_academic_year',
            auditable: $year,
            oldValues: null,
            newValues: [
                'name' => $year->name,
                'start_date' => $year->start_date,
                'end_date' => $year->end_date,
                'is_active' => $year->is_active,
            ]
        );
    }

    public function updated(AcademicYear $year): void
    {
        $dirty = $year->getDirty();
        unset($dirty['updated_at'], $dirty['created_at']);

        if (empty($dirty)) {
            return;
        }

        $action = (isset($dirty['is_active']) && $dirty['is_active']) ? 'activate_academic_year' : 'update_academic_year';

        $oldValues = [];
        $newValues = [];
        foreach ($dirty as $key => $newValue) {
            $oldValues[$key] = $year->getOriginal($key);
            $newValues[$key] = $newValue;
        }

        AuditLog::record(
            action: $action,
            auditable: $year,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    public function deleted(AcademicYear $year): void
    {
        AuditLog::record(
            action: 'delete_academic_year',
            auditable: $year,
            oldValues: [
                'name' => $year->name,
            ],
            newValues: null
        );
    }
}
