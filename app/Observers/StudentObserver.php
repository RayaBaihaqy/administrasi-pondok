<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Student;

class StudentObserver
{
    public function created(Student $student): void
    {
        AuditLog::record(
            action: 'create_student',
            auditable: $student,
            oldValues: null,
            newValues: [
                'nis' => $student->nis,
                'nism' => $student->nism,
                'full_name' => $student->full_name,
                'gender' => $student->gender,
                'class_level' => $student->class_level,
                'rombel' => $student->rombel,
                'status' => $student->status,
                'parent_id' => $student->parent_id,
            ]
        );
    }

    public function updated(Student $student): void
    {
        $dirty = $student->getDirty();
        unset($dirty['updated_at'], $dirty['created_at']);

        if (empty($dirty)) {
            return;
        }

        // Jika mutasi sudah dicatat di method mutateOut / revertMutation, hindari duplikasi log
        $action = 'update_student';
        if (isset($dirty['status'])) {
            if ($dirty['status'] === Student::STATUS_WITHDRAWN) {
                $action = 'student_mutate_out';
            } elseif ($dirty['status'] === Student::STATUS_ACTIVE && $student->getOriginal('status') === Student::STATUS_WITHDRAWN) {
                $action = 'student_revert_mutation';
            }
        }

        $oldValues = [];
        $newValues = [];
        foreach ($dirty as $key => $newValue) {
            $oldValues[$key] = $student->getOriginal($key);
            $newValues[$key] = $newValue;
        }

        AuditLog::record(
            action: $action,
            auditable: $student,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }

    public function deleted(Student $student): void
    {
        AuditLog::record(
            action: 'delete_student',
            auditable: $student,
            oldValues: [
                'nis' => $student->nis,
                'full_name' => $student->full_name,
                'class_level' => $student->class_level,
                'rombel' => $student->rombel,
            ],
            newValues: null
        );
    }
}
