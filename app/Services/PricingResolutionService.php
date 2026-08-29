<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\PaymentType;
use App\Models\PaymentTypePrice;
use App\Models\Student;
use Throwable;

class PricingResolutionService
{
    /**
     * Resolve final nominal amount (in IDR) for a given student and payment type.
     *
     * Hierarchy:
     * 1. Class Level + Rombel specific price in Academic Year
     * 2. Class Level specific price (all rombels) in Academic Year
     * 3. Academic Year default price (all classes)
     * 4. PaymentType default_amount
     */
    public function resolve(Student $student, PaymentType $paymentType, ?AcademicYear $academicYear = null): int
    {
        try {
            $academicYear = $academicYear ?? AcademicYear::current();

            if ($academicYear && $student->id && $paymentType->id) {
                // 1. Cek harga per Kelas + Rombel
                $classRombelPrice = PaymentTypePrice::where('payment_type_id', $paymentType->id)
                    ->where('academic_year_id', $academicYear->id)
                    ->where('class_level', $student->class_level)
                    ->where('rombel', $student->rombel)
                    ->first();

                if ($classRombelPrice) {
                    return (int) $classRombelPrice->amount;
                }

                // 3. Cek harga per Kelas (semua rombel)
                $classPrice = PaymentTypePrice::where('payment_type_id', $paymentType->id)
                    ->where('academic_year_id', $academicYear->id)
                    ->where('class_level', $student->class_level)
                    ->whereNull('rombel')
                    ->first();

                if ($classPrice) {
                    return (int) $classPrice->amount;
                }

                // 4. Cek harga tahun ajaran (semua kelas)
                $academicYearPrice = PaymentTypePrice::where('payment_type_id', $paymentType->id)
                    ->where('academic_year_id', $academicYear->id)
                    ->whereNull('class_level')
                    ->whereNull('rombel')
                    ->first();

                if ($academicYearPrice) {
                    return (int) $academicYearPrice->amount;
                }
            }
        } catch (Throwable $e) {
            // Log / fallback gracefully if database is not available
        }

        // 5. Fallback ke default_amount pada master PaymentType
        return (int) ($paymentType->default_amount ?? 0);
    }
}
