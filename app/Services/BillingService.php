<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\PaymentType;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class BillingService
{
    protected PricingResolutionService $pricingService;

    protected DueDateResolutionService $dueDateService;

    public function __construct(
        ?PricingResolutionService $pricingService = null,
        ?DueDateResolutionService $dueDateService = null
    ) {
        $this->pricingService = $pricingService ?? new PricingResolutionService;
        $this->dueDateService = $dueDateService ?? new DueDateResolutionService;
    }

    /**
     * Membuat tagihan baru untuk seorang siswa secara atomic.
     */
    public function createBill(
        Student $student,
        PaymentType $paymentType,
        Carbon|string|null $billingPeriod = null,
        ?AcademicYear $academicYear = null,
        ?int $customAmount = null,
        ?string $notes = null
    ): Bill {
        $academicYear = $academicYear ?? AcademicYear::current();
        if (! $academicYear) {
            throw new \RuntimeException('Tidak ada tahun ajaran aktif yang diset.');
        }

        $periodDate = $billingPeriod ? Carbon::parse($billingPeriod)->startOfMonth() : Carbon::now()->startOfMonth();
        $billingDate = Carbon::now();
        $dueDate = $this->dueDateService->resolve($student, $paymentType, $periodDate);
        $amount = $customAmount ?? $this->pricingService->resolve($student, $paymentType, $academicYear);

        return DB::transaction(function () use ($student, $paymentType, $academicYear, $periodDate, $billingDate, $dueDate, $amount, $notes) {
            $bill = Bill::create([
                'student_id' => $student->id,
                'parent_id' => $student->parent_id,
                'payment_type_id' => $paymentType->id,
                'academic_year_id' => $academicYear->id,
                'billing_period' => $periodDate->format('Y-m-d'),
                'billing_date' => $billingDate->format('Y-m-d'),
                'due_date' => $dueDate->format('Y-m-d'),
                'amount' => $amount,
                'paid_amount' => 0,
                'outstanding_amount' => $amount,
                'status' => Carbon::now()->startOfDay()->greaterThan($dueDate->startOfDay()) ? Bill::STATUS_OVERDUE : Bill::STATUS_UNPAID,
                'notes' => $notes,
            ]);

            BillItem::create([
                'bill_id' => $bill->id,
                'description' => $paymentType->name.' - Periode '.$periodDate->format('F Y'),
                'quantity' => 1,
                'unit_price' => $amount,
                'subtotal' => $amount,
            ]);

            return $bill;
        });
    }

    /**
     * Generate tagihan massal untuk siswa aktif berdasarkan filter target:
     * - Semua siswa aktif
     * - Tingkat kelas tertentu (e.g. Kelas 7, 8, atau 9)
     * - Rombel spesifik (e.g. Kelas 7.1, 8.2)
     *
     * Mendukung penagihan reguler dan penagihan skema cicilan (tenor).
     */
    public function generateBulkBills(
        PaymentType $paymentType,
        ?AcademicYear $academicYear = null,
        Carbon|string|null $billingPeriod = null,
        ?int $classLevel = null,
        ?string $rombel = null,
        ?int $customAmount = null,
        Carbon|string|null $customDueDate = null,
        bool $isInstallment = false,
        int $tenorCount = 1,
        ?string $notes = null
    ): array {
        $academicYear = $academicYear ?? AcademicYear::current();
        if (! $academicYear) {
            return ['created' => 0, 'skipped' => 0, 'errors' => 0, 'students_count' => 0, 'message' => 'Tahun ajaran aktif tidak ditemukan.'];
        }

        $periodDate = $billingPeriod ? Carbon::parse($billingPeriod)->startOfMonth() : Carbon::now()->startOfMonth();

        $studentsQuery = Student::active();
        if ($classLevel) {
            $studentsQuery->where('class_level', $classLevel);
        }
        if ($rombel) {
            $studentsQuery->where('rombel', $rombel);
        }

        $targetStudents = $studentsQuery->get();
        $totalStudents = $targetStudents->count();

        $createdCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($targetStudents as $student) {
            try {
                if ($isInstallment && $tenorCount > 1) {
                    $resolvedAmount = $customAmount ?? $this->pricingService->resolve($student, $paymentType, $academicYear);
                    if ($resolvedAmount <= 0) {
                        $skippedCount++;

                        continue;
                    }

                    $bills = $this->createInstallmentBills(
                        student: $student,
                        paymentType: $paymentType,
                        totalAmount: $resolvedAmount,
                        tenorCount: $tenorCount,
                        startBillingPeriod: $periodDate,
                        initialDueDate: $customDueDate,
                        academicYear: $academicYear,
                        notes: $notes
                    );

                    if (! empty($bills)) {
                        $createdCount += count($bills);
                    } else {
                        $skippedCount++;
                    }
                } else {
                    // Idempotency Check: Cek apakah bill untuk siswa + jenis + tahun ajaran + periode ini sudah ada
                    $exists = Bill::where('student_id', $student->id)
                        ->where('payment_type_id', $paymentType->id)
                        ->where('academic_year_id', $academicYear->id)
                        ->where('billing_period', $periodDate->format('Y-m-d'))
                        ->exists();

                    if ($exists) {
                        $skippedCount++;

                        continue;
                    }

                    $resolvedAmount = $customAmount ?? $this->pricingService->resolve($student, $paymentType, $academicYear);
                    $effectiveDueDate = $customDueDate ? Carbon::parse($customDueDate) : $this->dueDateService->resolve($student, $paymentType, $periodDate);
                    $billingDate = Carbon::now();

                    DB::transaction(function () use ($student, $paymentType, $academicYear, $periodDate, $billingDate, $effectiveDueDate, $resolvedAmount, $notes) {
                        $bill = Bill::create([
                            'student_id' => $student->id,
                            'parent_id' => $student->parent_id,
                            'payment_type_id' => $paymentType->id,
                            'academic_year_id' => $academicYear->id,
                            'billing_period' => $periodDate->format('Y-m-d'),
                            'billing_date' => $billingDate->format('Y-m-d'),
                            'due_date' => $effectiveDueDate->format('Y-m-d'),
                            'amount' => $resolvedAmount,
                            'paid_amount' => 0,
                            'outstanding_amount' => $resolvedAmount,
                            'status' => Carbon::now()->startOfDay()->greaterThan($effectiveDueDate->startOfDay()) ? Bill::STATUS_OVERDUE : Bill::STATUS_UNPAID,
                            'notes' => $notes,
                        ]);

                        BillItem::create([
                            'bill_id' => $bill->id,
                            'description' => $paymentType->name.' - Periode '.$periodDate->format('F Y'),
                            'quantity' => 1,
                            'unit_price' => $resolvedAmount,
                            'subtotal' => $resolvedAmount,
                        ]);
                    });

                    $createdCount++;
                }
            } catch (Throwable $e) {
                $errorCount++;
            }
        }

        return [
            'created' => $createdCount,
            'skipped' => $skippedCount,
            'errors' => $errorCount,
            'students_count' => $totalStudents,
            'period' => $periodDate->format('F Y'),
        ];
    }

    /**
     * Generate tagihan bulanan (SPP) secara otomatis & idempotent untuk SEMUA siswa aktif.
     */
    public function generateMonthlyBills(
        ?PaymentType $paymentType = null,
        ?AcademicYear $academicYear = null,
        Carbon|string|null $billingPeriod = null
    ): array {
        $academicYear = $academicYear ?? AcademicYear::current();
        if (! $academicYear) {
            return ['created' => 0, 'skipped' => 0, 'errors' => 0, 'students_count' => 0, 'message' => 'Tahun ajaran aktif tidak ditemukan.'];
        }

        $paymentType = $paymentType ?? PaymentType::where('code', 'SPP')->first();
        if (! $paymentType) {
            return ['created' => 0, 'skipped' => 0, 'errors' => 0, 'students_count' => 0, 'message' => 'Jenis pembayaran SPP tidak ditemukan.'];
        }

        return $this->generateBulkBills(
            paymentType: $paymentType,
            academicYear: $academicYear,
            billingPeriod: $billingPeriod
        );
    }

    /**
     * Generate skema tagihan cicilan (tenor) untuk seorang siswa secara atomic.
     */
    public function createInstallmentBills(
        Student $student,
        PaymentType $paymentType,
        int $totalAmount,
        int $tenorCount,
        Carbon|string|null $startBillingPeriod = null,
        Carbon|string|null $initialDueDate = null,
        ?AcademicYear $academicYear = null,
        ?string $notes = null
    ): array {
        if ($tenorCount <= 0) {
            throw new \InvalidArgumentException('Jumlah cicilan (tenor) harus lebih dari 0.');
        }

        if ($totalAmount <= 0) {
            throw new \InvalidArgumentException('Total nominal tagihan harus lebih dari 0.');
        }

        $academicYear = $academicYear ?? AcademicYear::current();
        if (! $academicYear) {
            throw new \RuntimeException('Tidak ada tahun ajaran aktif yang diset.');
        }

        $startPeriodDate = $startBillingPeriod ? Carbon::parse($startBillingPeriod)->startOfMonth() : Carbon::now()->startOfMonth();
        $billingDate = Carbon::now();

        $cleanNis = str_replace([' ', '-'], '', $student->nis);
        $baseAmount = intdiv($totalAmount, $tenorCount);
        $remainder = $totalAmount % $tenorCount;

        return DB::transaction(function () use ($student, $paymentType, $academicYear, $startPeriodDate, $billingDate, $initialDueDate, $tenorCount, $baseAmount, $remainder, $notes, $cleanNis) {
            $createdBills = [];

            for ($i = 1; $i <= $tenorCount; $i++) {
                $periodDate = $startPeriodDate->copy()->addMonths($i - 1)->startOfMonth();

                // Jatuh tempo cicilan otomatis di akhir bulan setiap periode (Sesuai Konfirmasi Client)
                $dueDate = ($initialDueDate && $i === 1)
                    ? Carbon::parse($initialDueDate)
                    : $periodDate->copy()->endOfMonth();

                // Format nomor tagihan cicilan: INV-{NIS}-{KODE_JENIS}-{INDEX}
                $billNumber = 'INV-'.$cleanNis.'-'.$paymentType->code.'-'.str_pad($i, 2, '0', STR_PAD_LEFT);

                // Tambahkan sisa pembulatan pada cicilan pertama
                $installmentAmount = ($i === 1) ? ($baseAmount + $remainder) : $baseAmount;

                $installmentNotes = trim(($notes ? $notes.' - ' : '')."Cicilan {$i} dari {$tenorCount}");

                // Idempotency check: Jangan buat duplikat jika tagihan siswa + jenis + periode ini sudah ada
                $existingBill = Bill::where('student_id', $student->id)
                    ->where('payment_type_id', $paymentType->id)
                    ->where('academic_year_id', $academicYear->id)
                    ->where('billing_period', $periodDate->format('Y-m-d'))
                    ->first();

                if ($existingBill) {
                    continue;
                }

                $bill = Bill::create([
                    'student_id' => $student->id,
                    'parent_id' => $student->parent_id,
                    'payment_type_id' => $paymentType->id,
                    'academic_year_id' => $academicYear->id,
                    'bill_number' => $billNumber,
                    'billing_period' => $periodDate->format('Y-m-d'),
                    'billing_date' => $billingDate->format('Y-m-d'),
                    'due_date' => $dueDate->format('Y-m-d'),
                    'amount' => $installmentAmount,
                    'paid_amount' => 0,
                    'outstanding_amount' => $installmentAmount,
                    'status' => Carbon::now()->startOfDay()->greaterThan($dueDate->startOfDay()) ? Bill::STATUS_OVERDUE : Bill::STATUS_UNPAID,
                    'notes' => $installmentNotes,
                ]);

                BillItem::create([
                    'bill_id' => $bill->id,
                    'description' => $paymentType->name." (Cicilan {$i}/{$tenorCount}) - Periode ".$periodDate->format('F Y'),
                    'quantity' => 1,
                    'unit_price' => $installmentAmount,
                    'subtotal' => $installmentAmount,
                ]);

                $createdBills[] = $bill;
            }

            return $createdBills;
        });
    }
}
