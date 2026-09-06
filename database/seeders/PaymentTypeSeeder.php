<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\PaymentType;
use App\Models\PaymentTypePrice;
use App\Models\Student;
use App\Models\StudentPaymentOverride;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = AcademicYear::current();

        // 1. SPP Bulanan (Jatuh tempo tgl 10 setiap bulan)
        $spp = PaymentType::create([
            'code' => PaymentType::CODE_SPP,
            'name' => 'SPP Bulanan',
            'billing_type' => PaymentType::BILLING_TYPE_MONTHLY,
            'allows_installment' => false,
            'default_amount' => 350000, // Rp 350.000
            'default_due_day' => 10,     // Jatuh tempo tanggal 10 tiap bulan (Sesuai Konfirmasi Client)
            'is_active' => true,
            'description' => 'Sumbangan Pembinaan Pendidikan bulanan MTs',
        ]);

        // 2. PTS (Penilaian Tengah Semester)
        $pts = PaymentType::create([
            'code' => PaymentType::CODE_PTS,
            'name' => 'PTS (Penilaian Tengah Semester)',
            'billing_type' => PaymentType::BILLING_TYPE_CUSTOM,
            'allows_installment' => false,
            'default_amount' => 100000, // Rp 100.000
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Biaya ujian Penilaian Tengah Semester (PTS)',
        ]);

        // 3. PAS (Penilaian Akhir Semester - Ganjil)
        $pas = PaymentType::create([
            'code' => PaymentType::CODE_PAS,
            'name' => 'PAS (Penilaian Akhir Semester Ganjil)',
            'billing_type' => PaymentType::BILLING_TYPE_CUSTOM,
            'allows_installment' => false,
            'default_amount' => 150000, // Rp 150.000
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Biaya ujian Penilaian Akhir Semester (PAS) Semester Ganjil',
        ]);

        // 4. PAT (Penilaian Akhir Tahun - Genap)
        $pat = PaymentType::create([
            'code' => PaymentType::CODE_PAT,
            'name' => 'PAT (Penilaian Akhir Tahun Genap)',
            'billing_type' => PaymentType::BILLING_TYPE_CUSTOM,
            'allows_installment' => false,
            'default_amount' => 150000, // Rp 150.000
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Biaya ujian Penilaian Akhir Tahun (PAT) Semester Genap',
        ]);

        // 5. LDKS (Latihan Dasar Kepemimpinan Siswa - Khusus Kelas 7)
        $ldks = PaymentType::create([
            'code' => PaymentType::CODE_LDKS,
            'name' => 'LDKS (Latihan Dasar Kepemimpinan Siswa)',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'allows_installment' => false,
            'default_amount' => 300000, // Rp 300.000
            'default_due_day' => 15,
            'is_active' => true,
            'description' => 'Kegiatan LDKS khusus siswa baru Kelas 7',
        ]);

        // 6. ST (Studi Tour - Khusus Kelas 8)
        $st = PaymentType::create([
            'code' => PaymentType::CODE_ST,
            'name' => 'ST (Studi Tour Kelas 8)',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'allows_installment' => true, // Boleh dicicil
            'default_amount' => 750000,   // Rp 750.000
            'default_due_day' => 20,
            'is_active' => true,
            'description' => 'Biaya kegiatan Studi Tour edukatif khusus siswa Kelas 8',
        ]);

        // 7. AT (Kegiatan Akhir Tahun - Khusus Kelas 9, Cicilan 10x)
        $at = PaymentType::create([
            'code' => PaymentType::CODE_AT,
            'name' => 'AT (Kegiatan Akhir Tahun & Perpisahan Kelas 9)',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'allows_installment' => true, // Boleh dicicil 10x (Sesuai Konfirmasi Client)
            'default_amount' => 2000000,  // Rp 2.000.000
            'default_due_day' => 25,
            'is_active' => true,
            'description' => 'Biaya kegiatan akhir tahun, wisuda, perpisahan dan tour Kelas 9 (Tenor 10x)',
        ]);

        // 8. Daftar Ulang (Kenaikan Kelas 8 & 9)
        $daftarUlang = PaymentType::create([
            'code' => PaymentType::CODE_DAFTAR_ULANG,
            'name' => 'Daftar Ulang Kenaikan Kelas',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'allows_installment' => true, // Boleh dicicil
            'default_amount' => 500000,   // Rp 500.000
            'default_due_day' => 20,
            'is_active' => true,
            'description' => 'Biaya administrasi daftar ulang buku dan modul kenaikan kelas',
        ]);

        // 9. Pendaftaran Siswa Baru (Kelas 7, Cicilan 3x)
        $pendaftaranBaru = PaymentType::create([
            'code' => PaymentType::CODE_PENDAFTARAN_BARU,
            'name' => 'Pendaftaran Siswa Baru',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'allows_installment' => true, // Boleh dicicil 3x (Sesuai Konfirmasi Client)
            'default_amount' => 1500000,  // Rp 1.500.000
            'default_due_day' => 20,
            'is_active' => true,
            'description' => 'Biaya pendaftaran dan seragam perlengkapan awal masuk MTs (Tenor 3x)',
        ]);

        if ($currentYear) {
            // Price matrix SPP per jenjang MTs:
            // Kelas 7: Rp 350.000
            PaymentTypePrice::create([
                'payment_type_id' => $spp->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 7,
                'amount' => 350000,
            ]);

            // Kelas 8: Rp 375.000
            PaymentTypePrice::create([
                'payment_type_id' => $spp->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 8,
                'amount' => 375000,
            ]);

            // Kelas 9: Rp 400.000
            PaymentTypePrice::create([
                'payment_type_id' => $spp->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 9,
                'amount' => 400000,
            ]);

            // Price matrix untuk kegiatan bertingkat:
            // LDKS Khusus Kelas 7
            PaymentTypePrice::create([
                'payment_type_id' => $ldks->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 7,
                'amount' => 300000,
            ]);

            // ST Khusus Kelas 8
            PaymentTypePrice::create([
                'payment_type_id' => $st->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 8,
                'amount' => 750000,
            ]);

            // AT Khusus Kelas 9
            PaymentTypePrice::create([
                'payment_type_id' => $at->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 9,
                'amount' => 2000000,
            ]);

            // Pendaftaran Baru Khusus Kelas 7
            PaymentTypePrice::create([
                'payment_type_id' => $pendaftaranBaru->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 7,
                'amount' => 1500000,
            ]);

            // Daftar Ulang Khusus Kelas 8 & 9
            PaymentTypePrice::create([
                'payment_type_id' => $daftarUlang->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 8,
                'amount' => 500000,
            ]);
            PaymentTypePrice::create([
                'payment_type_id' => $daftarUlang->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 9,
                'amount' => 500000,
            ]);

            // Data Sampel Beasiswa Tunai Siswa
            $studentSample = Student::first();
            if ($studentSample) {
                StudentPaymentOverride::create([
                    'student_id' => $studentSample->id,
                    'academic_year_id' => $currentYear->id,
                    'disbursed_at' => '2026-08-15',
                    'amount' => 500000,
                    'reason' => 'Beasiswa Prestasi Tahfidz Al-Qur\'an (Uang Tunai)',
                ]);
            }
        }
    }
}
