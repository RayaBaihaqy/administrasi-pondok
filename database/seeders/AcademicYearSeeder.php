<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentAcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tahun ajaran aktif
        $currentYear = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        // Tahun ajaran sebelumnya (historis)
        AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);

        // Daftarkan semua siswa aktif ke tahun ajaran aktif
        $activeStudents = Student::active()->get();

        foreach ($activeStudents as $student) {
            StudentAcademicYear::create([
                'student_id' => $student->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => $student->class_level,
                'rombel' => $student->rombel,
            ]);
        }
    }
}
