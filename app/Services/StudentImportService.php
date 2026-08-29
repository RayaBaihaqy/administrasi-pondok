<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\StudentAcademicYear;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class StudentImportService
{
    /**
     * Header standar kolom template import.
     */
    public const HEADERS = [
        'NISN (Wajib)',
        'NISM (Opsional)',
        'Nama Lengkap Siswa (Wajib)',
        'Jenis Kelamin (L/P atau Laki-laki/Perempuan)',
        'Tingkat Kelas (7/8/9)',
        'Rombel (1/2/3/A/B/C)',
        'Tahun Masuk (Contoh: 2026)',
        'Tanggal Lahir (YYYY-MM-DD)',
        'Alamat Siswa',
        'No HP Siswa',
        'Nama Ayah',
        'Nama Ibu',
        'Nama Wali',
        'No HP / WA Wali Murid (Wajib)',
        'Pekerjaan Orang Tua',
        'Alamat Orang Tua',
    ];

    /**
     * Download file template Excel CSV dengan format UTF-8 BOM untuk Microsoft Excel.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $filename = 'template_import_siswa_mts_miftahul_ulum.csv';

        $sampleRows = [
            [
                '3144228890',
                '121232160048260099',
                'MUHAMMAD FAIZ AL-FARISI',
                'Laki-laki',
                '7',
                '1',
                (string) now()->year,
                '2014-06-15',
                'Jl. Pesantren No. 12, Desa Sindang',
                '081234567891',
                'Ahmad Subagyo',
                'Siti Aminah',
                'Ahmad Subagyo',
                '081298765432',
                'Wiraswasta',
                'Jl. Pesantren No. 12, Desa Sindang',
            ],
            [
                '3144228891',
                '',
                'NURUL AISYAH AZ-ZAHRA',
                'Perempuan',
                '7',
                '2',
                (string) now()->year,
                '2014-09-21',
                'Dusun Krajan RT 03/02, Desa Sindang',
                '',
                'H. Mahmud Ridwan',
                'Hj. Fatimah Zahro',
                'H. Mahmud Ridwan',
                '085612345678',
                'PNS / Guru',
                'Dusun Krajan RT 03/02, Desa Sindang',
            ],
        ];

        return response()->streamDownload(function () use ($sampleRows) {
            $handle = fopen('php://output', 'w');

            // Write UTF-8 BOM agar Excel di Windows membuka file dengan encoding yang benar
            fputs($handle, "\xEF\xBB\xBF");

            // Tulis Header
            fputcsv($handle, self::HEADERS);

            // Tulis Contoh Data
            foreach ($sampleRows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Memproses file spreadsheet hasil upload (CSV/TSV/Text).
     */
    public function importFromSpreadsheet(string $filePath): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            throw new \RuntimeException('File import tidak ditemukan atau tidak dapat dibaca.');
        }

        $handle = fopen($filePath, 'r');
        if (! $handle) {
            throw new \RuntimeException('Gagal membuka file import.');
        }

        // Baca baris pertama untuk deteksi delimiter (, atau ; atau \t)
        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
            $delimiter = "\t";
        }

        // Lewati BOM jika ada
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Baca header baris 1
        $header = fgetcsv($handle, 0, $delimiter);

        $currentYear = AcademicYear::current() ?? AcademicYear::latest('id')->first();
        $successCount = 0;
        $errors = [];
        $rowNumber = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;

                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                // Ambil data berdasarkan urutan kolom
                $nisn = trim($row[0] ?? '');
                $nism = trim($row[1] ?? '') ?: null;
                $fullName = trim($row[2] ?? '');
                $genderRaw = strtolower(trim($row[3] ?? ''));
                $classLevelRaw = (int) trim($row[4] ?? 7);
                $rombel = trim($row[5] ?? '1') ?: '1';
                $entryYear = (int) (trim($row[6] ?? '') ?: now()->year);
                $birthDateRaw = trim($row[7] ?? '') ?: null;
                $studentAddress = trim($row[8] ?? '') ?: null;
                $studentPhone = trim($row[9] ?? '') ?: null;
                $fatherName = trim($row[10] ?? '') ?: null;
                $motherName = trim($row[11] ?? '') ?: null;
                $guardianName = trim($row[12] ?? '') ?: null;
                $parentPhone = trim($row[13] ?? '') ?: '081234567890';
                $parentJob = trim($row[14] ?? '') ?: null;
                $parentAddress = trim($row[15] ?? '') ?: $studentAddress;

                // Validasi data penting
                if (empty($nisn)) {
                    $errors[] = "Baris {$rowNumber}: NISN tidak boleh kosong.";

                    continue;
                }

                if (empty($fullName)) {
                    $errors[] = "Baris {$rowNumber}: Nama Lengkap Siswa tidak boleh kosong (NISN: {$nisn}).";

                    continue;
                }

                // Normalisasi Gender
                $gender = (str_starts_with($genderRaw, 'p') || str_starts_with($genderRaw, 'f') || str_contains($genderRaw, 'perempuan'))
                    ? 'female'
                    : 'male';

                // Normalisasi Class Level
                $classLevel = in_array($classLevelRaw, [7, 8, 9]) ? $classLevelRaw : 7;

                // Normalisasi Tanggal Lahir
                $birthDate = null;
                if ($birthDateRaw) {
                    try {
                        $birthDate = Carbon::parse($birthDateRaw)->format('Y-m-d');
                    } catch (Throwable $e) {
                        $birthDate = null;
                    }
                }

                // Normalisasi Nama Wali
                $primaryGuardianName = $guardianName ?: ($fatherName ?: ($motherName ?: "Wali {$fullName}"));

                // 1. Cari atau buat User Wali Siswa
                $cleanNisn = preg_replace('/[^A-Za-z0-9]/', '', $nisn);
                $parentEmail = "wali_{$cleanNisn}@parent.test";

                $parentUser = User::firstOrCreate(
                    ['email' => $parentEmail],
                    [
                        'name' => $primaryGuardianName,
                        'role' => User::ROLE_PARENT,
                        'password' => Hash::make('password'),
                    ]
                );

                // 2. Cari atau buat Profil Orang Tua / Wali
                $parentProfile = ParentProfile::firstOrCreate(
                    ['user_id' => $parentUser->id],
                    [
                        'full_name' => $primaryGuardianName,
                        'phone' => $parentPhone,
                        'address' => $parentAddress,
                    ]
                );

                // 3. Buat atau perbarui data Siswa
                $student = Student::updateOrCreate(
                    ['nis' => $nisn],
                    [
                        'parent_id' => $parentProfile->id,
                        'nism' => $nism,
                        'full_name' => $fullName,
                        'gender' => $gender,
                        'class_level' => $classLevel,
                        'rombel' => $rombel,
                        'entry_year' => $entryYear,
                        'birth_date' => $birthDate,
                        'address' => $studentAddress,
                        'phone' => $studentPhone,
                        'status' => Student::STATUS_ACTIVE,
                    ]
                );

                // 4. Catat histori penempatan di tahun ajaran aktif
                if ($currentYear) {
                    StudentAcademicYear::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'academic_year_id' => $currentYear->id,
                        ],
                        [
                            'class_level' => $student->class_level,
                            'rombel' => $student->rombel,
                        ]
                    );
                }

                $successCount++;
            }

            // Catat ke AuditLog
            AuditLog::record(
                action: 'bulk_import_students',
                auditable: null,
                newValues: [
                    'imported_count' => $successCount,
                    'file_name' => basename($filePath),
                ],
                actor: Auth::user()
            );

            DB::commit();
            fclose($handle);

            return [
                'success_count' => $successCount,
                'errors' => $errors,
                'total_rows' => $rowNumber - 1,
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }
    }
}
