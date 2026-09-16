<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\PaymentType;
use App\Models\PaymentTypePrice;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Sesuai 11 Daftar Pembayaran Resmi dari Klien MTs Miftahul 'Ulum.
     */
    public function run(): void
    {
        $currentYear = AcademicYear::current();

        // 1. SPP - Rp 75.000 (Bulanan)
        $spp = PaymentType::create([
            'code' => PaymentType::CODE_SPP,
            'name' => 'SPP',
            'billing_type' => PaymentType::BILLING_TYPE_MONTHLY,
            'allows_installment' => false,
            'default_amount' => 75000,
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Sumbangan Pembinaan Pendidikan (SPP) bulanan siswa',
        ]);

        // 2. Pendaftaran siswa baru - Rp 1.190.000 (Sekali Bayar / Cicilan)
        $pendaftaranBaru = PaymentType::create([
            'code' => PaymentType::CODE_PENDAFTARAN_BARU,
            'name' => 'Pendaftaran Siswa Baru',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'allows_installment' => true,
            'default_amount' => 1190000,
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Biaya administrasi dan perlengkapan pendaftaran siswa baru (Kelas 7)',
        ]);

        // 3. Daftar ulang tahun ajaran baru siswa kelas 8 dan 9 - Rp 440.000
        $daftarUlangGanjil = PaymentType::create([
            'code' => PaymentType::CODE_DAFTAR_ULANG_GANJIL,
            'name' => 'Daftar Ulang Tahun Ajaran Baru Siswa Kelas 8 dan 9',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'allows_installment' => true,
            'default_amount' => 440000,
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Biaya daftar ulang tahun ajaran baru (Semester Ganjil) untuk siswa Kelas 8 dan 9',
        ]);

        // 4. ASTS/PTS Ganjil - Rp 75.000
        $astsPtsGanjil = PaymentType::create([
            'code' => PaymentType::CODE_ASTS_PTS_GANJIL,
            'name' => 'ASTS/PTS Ganjil',
            'billing_type' => PaymentType::BILLING_TYPE_CUSTOM,
            'allows_installment' => false,
            'default_amount' => 75000,
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Asesmen Sumatif Tengah Semester / Penilaian Tengah Semester (Ganjil)',
        ]);

        // 5. ASAS Ganjil - Rp 150.000
        $asasGanjil = PaymentType::create([
            'code' => PaymentType::CODE_ASAS_GANJIL,
            'name' => 'ASAS Ganjil',
            'billing_type' => PaymentType::BILLING_TYPE_CUSTOM,
            'allows_installment' => false,
            'default_amount' => 150000,
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Asesmen Sumatif Akhir Semester (ASAS) Semester Ganjil',
        ]);

        // 6. LDKS - Rp 450.000
        $ldks = PaymentType::create([
            'code' => PaymentType::CODE_LDKS,
            'name' => 'LDKS',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'allows_installment' => true,
            'default_amount' => 450000,
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Latihan Dasar Kepemimpinan Siswa (LDKS)',
        ]);

        // 7. Daftar ulang Semester Genap - Rp 440.000
        $daftarUlangGenap = PaymentType::create([
            'code' => PaymentType::CODE_DAFTAR_ULANG_GENAP,
            'name' => 'Daftar Ulang Semester Genap',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'allows_installment' => true,
            'default_amount' => 440000,
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Biaya administrasi daftar ulang Semester Genap',
        ]);

        // 8. ASTS/PTS Genap - Rp 75.000
        $astsPtsGenap = PaymentType::create([
            'code' => PaymentType::CODE_ASTS_PTS_GENAP,
            'name' => 'ASTS/PTS Genap',
            'billing_type' => PaymentType::BILLING_TYPE_CUSTOM,
            'allows_installment' => false,
            'default_amount' => 75000,
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Asesmen Sumatif Tengah Semester / Penilaian Tengah Semester (Genap)',
        ]);

        // 9. ASATA/PAT - Rp 150.000
        $asataPat = PaymentType::create([
            'code' => PaymentType::CODE_ASATA_PAT,
            'name' => 'ASATA/PAT',
            'billing_type' => PaymentType::BILLING_TYPE_CUSTOM,
            'allows_installment' => false,
            'default_amount' => 150000,
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Asesmen Sumatif Akhir Tahun Ajaran / Penilaian Akhir Tahun (ASATA/PAT)',
        ]);

        // 10. Study Tour - Rp 450.000
        $studyTour = PaymentType::create([
            'code' => PaymentType::CODE_STUDY_TOUR,
            'name' => 'Study Tour',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'allows_installment' => true,
            'default_amount' => 450000,
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Biaya kegiatan Study Tour edukatif',
        ]);

        // 11. Akhir Tahun - Rp 2.000.000
        $akhirTahun = PaymentType::create([
            'code' => PaymentType::CODE_AKHIR_TAHUN,
            'name' => 'Akhir Tahun',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'allows_installment' => true,
            'default_amount' => 2000000,
            'default_due_day' => 10,
            'is_active' => true,
            'description' => 'Kegiatan Akhir Tahun, Wisuda, Pelepasan & Tour Kelas 9',
        ]);

        if ($currentYear) {
            // Price matrix SPP per jenjang MTs (Rp 75.000)
            foreach ([7, 8, 9] as $level) {
                PaymentTypePrice::create([
                    'payment_type_id' => $spp->id,
                    'academic_year_id' => $currentYear->id,
                    'class_level' => $level,
                    'amount' => 75000,
                ]);
            }

            // Pendaftaran Siswa Baru Khusus Kelas 7 (Rp 1.190.000)
            PaymentTypePrice::create([
                'payment_type_id' => $pendaftaranBaru->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 7,
                'amount' => 1190000,
            ]);

            // Daftar Ulang Tahun Ajaran Baru Khusus Kelas 8 & 9 (Rp 440.000)
            PaymentTypePrice::create([
                'payment_type_id' => $daftarUlangGanjil->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 8,
                'amount' => 440000,
            ]);
            PaymentTypePrice::create([
                'payment_type_id' => $daftarUlangGanjil->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 9,
                'amount' => 440000,
            ]);

            // LDKS Khusus Kelas 7 (Rp 450.000)
            PaymentTypePrice::create([
                'payment_type_id' => $ldks->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 7,
                'amount' => 450000,
            ]);

            // Study Tour Khusus Kelas 8 (Rp 450.000)
            PaymentTypePrice::create([
                'payment_type_id' => $studyTour->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 8,
                'amount' => 450000,
            ]);

            // Akhir Tahun Khusus Kelas 9 (Rp 2.000.000)
            PaymentTypePrice::create([
                'payment_type_id' => $akhirTahun->id,
                'academic_year_id' => $currentYear->id,
                'class_level' => 9,
                'amount' => 2000000,
            ]);
        }
    }
}
