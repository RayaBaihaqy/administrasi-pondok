<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\PaymentType;
use App\Models\Student;
use App\Models\StudentAcademicYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademicYearTransitionService
{
    protected BillingService $billingService;

    public function __construct(?BillingService $billingService = null)
    {
        $this->billingService = $billingService ?? new BillingService;
    }

    /**
     * Memulai tahun ajaran baru, memproses kenaikan kelas massal siswa (dengan dukungan siswa tinggal kelas),
     * dan otomatis menerbitkan tagihan Daftar Ulang untuk seluruh siswa aktif (Kelas 7, 8, dan 9).
     */
    public function startNewAcademicYear(string $name, string $startDate, string $endDate, array $retainedStudentIds = []): array
    {
        return DB::transaction(function () use ($name, $startDate, $endDate, $retainedStudentIds) {
            $previousActiveYear = AcademicYear::current();

            // 1. Nonaktifkan tahun ajaran aktif sebelumnya
            if ($previousActiveYear) {
                $previousActiveYear->is_active = false;
                $previousActiveYear->save();
            }

            // 2. Buat tahun ajaran baru & set aktif
            $newYear = AcademicYear::create([
                'name' => $name,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true,
            ]);

            // 3. Ambil seluruh siswa aktif
            $activeStudents = Student::active()->get();

            $targetBillingStudents = [];
            $promotedCount = 0;
            $graduatedCount = 0;
            $retainedCount = 0;

            // Bersihkan format ID siswa tinggal kelas
            $retainedIdsMap = array_flip(array_map('intval', array_filter($retainedStudentIds)));

            foreach ($activeStudents as $student) {
                // Catat penempatan sejarah di student_academic_years untuk tahun sebelumnya
                if ($previousActiveYear) {
                    StudentAcademicYear::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'academic_year_id' => $previousActiveYear->id,
                        ],
                        [
                            'class_level' => $student->class_level,
                            'rombel' => $student->rombel,
                        ]
                    );
                }

                $isRetained = isset($retainedIdsMap[$student->id]);

                if ($isRetained) {
                    // Siswa Tinggal Kelas: Tingkat kelas tetap, status tetap aktif
                    $retainedCount++;
                    $targetBillingStudents[] = $student;
                } elseif ($student->class_level >= 9) {
                    // Siswa Kelas 9 Normal: Lulus
                    $student->status = Student::STATUS_GRADUATED;
                    $graduatedCount++;
                } else {
                    // Siswa Kelas 7 & 8 Normal: Naik 1 Tingkat
                    $student->class_level = $student->class_level + 1;
                    $promotedCount++;
                    $targetBillingStudents[] = $student;
                }

                $student->save();

                // Catat penempatan di tahun ajaran baru untuk siswa yang masih aktif
                if ($student->status === Student::STATUS_ACTIVE) {
                    StudentAcademicYear::create([
                        'student_id' => $student->id,
                        'academic_year_id' => $newYear->id,
                        'class_level' => $student->class_level,
                        'rombel' => $student->rombel,
                    ]);
                }
            }

            // 4. Otomatis generate tagihan "Daftar Ulang Kenaikan Kelas" untuk seluruh siswa aktif
            $daftarUlangType = PaymentType::where('code', 'DAFTAR_ULANG')
                ->orWhere('name', 'like', '%Daftar Ulang%')
                ->first();

            $billsCreatedCount = 0;
            if ($daftarUlangType) {
                $billingPeriod = Carbon::parse($startDate)->startOfMonth();

                foreach ($targetBillingStudents as $targetStudent) {
                    $note = isset($retainedIdsMap[$targetStudent->id])
                        ? 'Tagihan Daftar Ulang (Tinggal Kelas '.$targetStudent->class_level.') - Tahun Ajaran '.$newYear->name
                        : 'Tagihan Otomatis Daftar Ulang Kenaikan Kelas (Kelas '.$targetStudent->class_level.') - Tahun Ajaran '.$newYear->name;

                    $this->billingService->createBill(
                        student: $targetStudent,
                        paymentType: $daftarUlangType,
                        billingPeriod: $billingPeriod,
                        academicYear: $newYear,
                        notes: $note
                    );
                    $billsCreatedCount++;
                }
            }

            // 5. Record to AuditLog
            AuditLog::record(
                action: 'start_new_academic_year',
                auditable: $newYear,
                newValues: [
                    'academic_year' => $newYear->name,
                    'promoted_count' => $promotedCount,
                    'retained_count' => $retainedCount,
                    'graduated_count' => $graduatedCount,
                    'daftar_ulang_bills_created' => $billsCreatedCount,
                ],
                actor: Auth::user()
            );

            return [
                'new_year' => $newYear,
                'promoted_count' => $promotedCount,
                'retained_count' => $retainedCount,
                'graduated_count' => $graduatedCount,
                'bills_created' => $billsCreatedCount,
            ];
        });
    }
}
