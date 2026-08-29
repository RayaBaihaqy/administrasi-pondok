<?php

namespace App\Services;

use App\Models\PaymentDueDateOverride;
use App\Models\PaymentType;
use App\Models\Student;
use Carbon\Carbon;
use Throwable;

class DueDateResolutionService
{
    /**
     * Resolve effective due date for a bill.
     *
     * @param  Carbon|string|null  $billingPeriod  Date or period string (e.g. "2026-08-01")
     */
    public function resolve(Student $student, PaymentType $paymentType, Carbon|string|null $billingPeriod = null): Carbon
    {
        $baseDate = $billingPeriod ? Carbon::parse($billingPeriod) : Carbon::now();

        try {
            if ($paymentType->id) {
                // 1. Cek override spesifik untuk siswa ini
                if ($student->id) {
                    $studentOverride = PaymentDueDateOverride::where('payment_type_id', $paymentType->id)
                        ->where('student_id', $student->id)
                        ->first();

                    if ($studentOverride) {
                        if ($studentOverride->due_date) {
                            return Carbon::parse($studentOverride->due_date);
                        }
                        if ($studentOverride->due_day) {
                            return $baseDate->copy()->day(min($studentOverride->due_day, $baseDate->daysInMonth));
                        }
                    }
                }

                // 2. Cek override untuk jenis pembayaran (semua siswa)
                $typeOverride = PaymentDueDateOverride::where('payment_type_id', $paymentType->id)
                    ->whereNull('student_id')
                    ->first();

                if ($typeOverride) {
                    if ($typeOverride->due_date) {
                        return Carbon::parse($typeOverride->due_date);
                    }
                    if ($typeOverride->due_day) {
                        return $baseDate->copy()->day(min($typeOverride->due_day, $baseDate->daysInMonth));
                    }
                }
            }
        } catch (Throwable $e) {
            // Log / fallback gracefully if database is not available
        }

        // 3. Fallback ke default_due_day pada PaymentType (misal tanggal 1 atau tanggal 10)
        $dueDay = $paymentType->default_due_day ?? 1;

        return $baseDate->copy()->day(min($dueDay, $baseDate->daysInMonth));
    }
}
