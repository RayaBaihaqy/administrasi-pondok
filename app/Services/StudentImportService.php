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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
        'Tingkat Kelas',
        'Rombel',
        'Tahun Masuk',
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
     * Download file template Excel resmi (.xlsx) dengan format kolom Number (0 desimal) & Text.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $filename = 'template_import_siswa_mts_miftahul_ulum.xlsx';

        $sampleRows = [
            [
                '3144228890',
                '121232160048260099',
                'MUHAMMAD FAIZ AL-FARISI',
                'Laki-laki',
                7,
                '1',
                (int) now()->year,
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
                7,
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
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Template Siswa');

            // 1. Tulis Header
            foreach (self::HEADERS as $colIndex => $headerText) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                $sheet->setCellValue("{$colLetter}1", $headerText);
            }

            // Header Style: Background Hijau Madrasah, Font Putih Bold, Center
            $lastColLetter = Coordinate::stringFromColumnIndex(count(self::HEADERS));
            $headerRange = "A1:{$lastColLetter}1";
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2E7D32'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(26);

            // 2. Tulis Contoh Data
            foreach ($sampleRows as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2;
                foreach ($row as $colIndex => $val) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                    if ($val === '' || $val === null) {
                        $sheet->setCellValue("{$colLetter}{$rowNumber}", '');
                    } elseif (in_array($colIndex, [0, 1, 9, 13], true)) {
                        // NISN, NISM, dan No HP: Explicit String (presisi utuh dan menjaga awalan angka 0)
                        $sheet->setCellValueExplicit("{$colLetter}{$rowNumber}", (string) $val, DataType::TYPE_STRING);
                    } else {
                        $sheet->setCellValue("{$colLetter}{$rowNumber}", $val);
                    }
                }
            }

            // 3. Format Number 0 Desimal untuk Kolom B (NISM), E (Tingkat Kelas), G (Tahun Masuk)
            $sheet->getStyle('B2:B500')->getNumberFormat()->setFormatCode('0');
            $sheet->getStyle('E2:E500')->getNumberFormat()->setFormatCode('0');
            $sheet->getStyle('G2:G500')->getNumberFormat()->setFormatCode('0');

            // Format Text untuk Kolom A (NISN), J (No HP Siswa), N (No HP Wali)
            $sheet->getStyle('A2:A500')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle('J2:J500')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle('N2:N500')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

            // Auto-width columns
            foreach (range(1, count(self::HEADERS)) as $colIndex) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Membersihkan nilai sel dari format Excel, tanda kutip, formula,
     * serta mengonversi notasi ilmiah (misal: 1,21232E+17) menjadi string angka utuh.
     */
    public function cleanCell(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        // 1. Bersihkan formula Excel seperti ="123456" atau ='123456'
        if (preg_match('/^=\s*["\']?(.*?)["\']?$/', $value, $matches)) {
            $value = trim($matches[1]);
        }

        // 2. Bersihkan petik pembuka (awalan text di Excel)
        $value = ltrim($value, "'");

        // 3. Konversi format notasi ilmiah (e.g. 1.21232E+17 atau 1,21232e+17) menjadi string angka bulat
        if (preg_match('/^([0-9]+[.,]?[0-9]*)[eE]\+?([0-9]+)$/i', $value)) {
            $numericVal = (float) str_replace(',', '.', $value);
            $value = sprintf('%.0f', $numericVal);
        }

        return $value !== '' ? $value : null;
    }

    /**
     * Mengekstrak baris-baris data dari file spreadsheet (.xlsx / .xls / .csv / .tsv / .txt).
     */
    public function extractRowsFromSpreadsheet(string $filePath): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            throw new \RuntimeException('File import tidak ditemukan atau tidak dapat dibaca.');
        }

        try {
            // Gunakan PhpSpreadsheet IOFactory yang otomatis mengenali file .xlsx, .xls, .csv, .tsv, etc.
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, false, false);

            return array_values(array_filter($rows, fn ($row) => ! empty(array_filter($row, fn ($c) => trim((string) $c) !== ''))));
        } catch (Throwable $e) {
            // Fallback CSV parsing jika IOFactory gagal
            $handle = fopen($filePath, 'r');
            if (! $handle) {
                throw new \RuntimeException('Gagal membaca file import: '.$e->getMessage());
            }

            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $firstLine = fgets($handle);
            rewind($handle);
            if ($bom === "\xEF\xBB\xBF") {
                fread($handle, 3);
            }

            $delimiter = ',';
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                $delimiter = "\t";
            }

            $rows = [];
            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);

            return $rows;
        }
    }

    /**
     * Memproses file spreadsheet hasil upload (.xls / .csv / .tsv / .txt).
     */
    public function importFromSpreadsheet(string $filePath): array
    {
        $rows = $this->extractRowsFromSpreadsheet($filePath);
        if (empty($rows)) {
            throw new \RuntimeException('File import kosong atau tidak memiliki data.');
        }

        // Lewati baris header (baris pertama)
        array_shift($rows);

        $currentYear = AcademicYear::current() ?? AcademicYear::latest('id')->first();
        $successCount = 0;
        $errors = [];
        $rowNumber = 1;

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $rowNumber++;

                // Skip baris kosong
                if (empty(array_filter($row, fn ($c) => trim((string) $c) !== ''))) {
                    continue;
                }

                // Ambil data berdasarkan urutan kolom
                $nisn = $this->cleanCell($row[0] ?? '') ?? '';
                $nism = $this->cleanCell($row[1] ?? '');
                $fullName = trim($this->cleanCell($row[2] ?? '') ?? '');
                $genderRaw = strtolower(trim($this->cleanCell($row[3] ?? '') ?? ''));
                $classLevelRaw = (int) ($this->cleanCell($row[4] ?? '') ?: 7);
                $rombel = $this->cleanCell($row[5] ?? '') ?: '1';
                $entryYear = (int) ($this->cleanCell($row[6] ?? '') ?: now()->year);
                $birthDateRaw = $this->cleanCell($row[7] ?? '');
                $studentAddress = $this->cleanCell($row[8] ?? '');
                $studentPhone = $this->cleanCell($row[9] ?? '');
                $fatherName = $this->cleanCell($row[10] ?? '');
                $motherName = $this->cleanCell($row[11] ?? '');
                $guardianName = $this->cleanCell($row[12] ?? '');
                $parentPhone = $this->cleanCell($row[13] ?? '') ?: '081234567890';
                $parentJob = $this->cleanCell($row[14] ?? '');
                $parentAddress = $this->cleanCell($row[15] ?? '') ?: $studentAddress;

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
                        'phone' => $parentPhone,
                        'role' => User::ROLE_PARENT,
                        'password' => Hash::make('password'),
                    ]
                );

                if ($parentPhone && empty($parentUser->phone)) {
                    $parentUser->update(['phone' => $parentPhone]);
                }

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

            return [
                'success_count' => $successCount,
                'errors' => $errors,
                'total_rows' => $rowNumber - 1,
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
