<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menggunakan seluruh 11 Daftar Pembayaran & Nominal Resmi dari Klien.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::current() ?? AcademicYear::first();
        $admin = User::where('role', 'admin')->first() ?? User::first();

        // 11 Payment Types
        $sppType = PaymentType::where('code', PaymentType::CODE_SPP)->first();
        $pendaftaranBaruType = PaymentType::where('code', PaymentType::CODE_PENDAFTARAN_BARU)->first();
        $daftarUlangGanjilType = PaymentType::where('code', PaymentType::CODE_DAFTAR_ULANG_GANJIL)->first();
        $astsPtsGanjilType = PaymentType::where('code', PaymentType::CODE_ASTS_PTS_GANJIL)->first();
        $asasGanjilType = PaymentType::where('code', PaymentType::CODE_ASAS_GANJIL)->first();
        $ldksType = PaymentType::where('code', PaymentType::CODE_LDKS)->first();
        $daftarUlangGenapType = PaymentType::where('code', PaymentType::CODE_DAFTAR_ULANG_GENAP)->first();
        $astsPtsGenapType = PaymentType::where('code', PaymentType::CODE_ASTS_PTS_GENAP)->first();
        $asataPatType = PaymentType::where('code', PaymentType::CODE_ASATA_PAT)->first();
        $studyTourType = PaymentType::where('code', PaymentType::CODE_STUDY_TOUR)->first();
        $akhirTahunType = PaymentType::where('code', PaymentType::CODE_AKHIR_TAHUN)->first();

        if (! $academicYear || ! $sppType) {
            return;
        }

        $students = Student::where('status', Student::STATUS_ACTIVE)->get();
        if ($students->isEmpty()) {
            return;
        }

        $billCounter = 1;
        $paymentMethods = ['cash', 'bank_transfer', 'midtrans_qris', 'midtrans_gopay', 'midtrans_bank_transfer'];

        // ─── 1. SPP Bulanan (Januari s/d Agustus 2026) - Rp 75.000 / bln ────────
        for ($month = 1; $month <= 8; $month++) {
            $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
            $billingPeriod = "2026-{$monthStr}-01";
            $billingDate = "2026-{$monthStr}-01";
            $dueDate = "2026-{$monthStr}-10";
            $createdAtTime = "2026-{$monthStr}-01 08:00:00";
            $sppAmount = 75000;

            foreach ($students as $student) {
                $cleanNis = str_replace([' ', '-'], '', $student->nis);
                $billNumber = 'INV-'.$cleanNis.'-2026'.$monthStr.'-'.str_pad($billCounter, 4, '0', STR_PAD_LEFT);

                $isPaid = false;
                $isOverdue = false;

                if ($month <= 6) {
                    $isPaid = (rand(1, 100) <= 95);
                } elseif ($month == 7) {
                    $randVal = rand(1, 100);
                    if ($randVal <= 75) {
                        $isPaid = true;
                    } else {
                        $isOverdue = true;
                    }
                } else {
                    $dueDate = '2026-08-10';
                    $isPaid = (rand(1, 100) <= 60);
                }

                $status = Bill::STATUS_UNPAID;
                if ($isPaid) {
                    $status = Bill::STATUS_PAID;
                } elseif ($isOverdue) {
                    $status = Bill::STATUS_OVERDUE;
                }

                $bill = Bill::create([
                    'student_id' => $student->id,
                    'parent_id' => $student->parent_id,
                    'payment_type_id' => $sppType->id,
                    'academic_year_id' => $academicYear->id,
                    'bill_number' => $billNumber,
                    'billing_period' => $billingPeriod,
                    'billing_date' => $billingDate,
                    'due_date' => $dueDate,
                    'amount' => $sppAmount,
                    'paid_amount' => $isPaid ? $sppAmount : 0,
                    'outstanding_amount' => $isPaid ? 0 : $sppAmount,
                    'status' => $status,
                    'created_at' => $createdAtTime,
                    'updated_at' => $createdAtTime,
                ]);

                BillItem::create([
                    'bill_id' => $bill->id,
                    'description' => 'SPP MTs - '.Carbon::parse($billingPeriod)->translatedFormat('F Y'),
                    'quantity' => 1,
                    'unit_price' => $sppAmount,
                    'subtotal' => $sppAmount,
                    'created_at' => $createdAtTime,
                    'updated_at' => $createdAtTime,
                ]);

                if ($isPaid) {
                    $payDay = str_pad(rand(2, 10), 2, '0', STR_PAD_LEFT);
                    $payHour = str_pad(rand(8, 17), 2, '0', STR_PAD_LEFT);
                    $payMinute = str_pad(rand(10, 55), 2, '0', STR_PAD_LEFT);

                    $paidAtTime = "2026-{$monthStr}-{$payDay} {$payHour}:{$payMinute}:00";
                    $methodChoice = $paymentMethods[array_rand($paymentMethods)];
                    $source = str_contains($methodChoice, 'midtrans') ? Payment::SOURCE_MIDTRANS : Payment::SOURCE_MANUAL;
                    $cleanMethod = str_replace('midtrans_', '', $methodChoice);

                    Payment::create([
                        'bill_id' => $bill->id,
                        'student_id' => $student->id,
                        'parent_id' => $student->parent_id,
                        'recorded_by' => $admin->id,
                        'payment_number' => Payment::generatePaymentNumberFromBill($bill, $source),
                        'amount' => $sppAmount,
                        'status' => Payment::STATUS_SUCCESS,
                        'source' => $source,
                        'method' => $cleanMethod,
                        'notes' => 'Pembayaran SPP '.Carbon::parse($billingPeriod)->translatedFormat('F Y'),
                        'paid_at' => $paidAtTime,
                        'created_at' => $paidAtTime,
                        'updated_at' => $paidAtTime,
                    ]);
                }

                $billCounter++;
            }
        }

        // ─── 2. Pendaftaran Siswa Baru (Kelas 7) - Rp 1.190.000 ─────────────────
        if ($pendaftaranBaruType) {
            $class7Students = Student::where('class_level', 7)->get();
            foreach ($class7Students as $c7Student) {
                $cleanNis = str_replace([' ', '-'], '', $c7Student->nis);
                $totalEntry = 1190000;

                $bill = Bill::create([
                    'student_id' => $c7Student->id,
                    'parent_id' => $c7Student->parent_id,
                    'payment_type_id' => $pendaftaranBaruType->id,
                    'academic_year_id' => $academicYear->id,
                    'bill_number' => 'INV-'.$cleanNis.'-PENDAFTARAN',
                    'billing_period' => '2026-07-01',
                    'billing_date' => '2026-07-15',
                    'due_date' => '2026-09-30',
                    'amount' => $totalEntry,
                    'paid_amount' => 800000,
                    'outstanding_amount' => 390000,
                    'status' => Bill::STATUS_UNPAID,
                    'notes' => 'Pendaftaran Siswa Baru MTs (Skema Cicilan)',
                ]);

                BillItem::create([
                    'bill_id' => $bill->id,
                    'description' => 'Pendaftaran Siswa Baru MTs Miftahul Ulum',
                    'quantity' => 1,
                    'unit_price' => $totalEntry,
                    'subtotal' => $totalEntry,
                ]);

                // Cicilan 1 (Juli 2026: Rp 500.000)
                Payment::create([
                    'bill_id' => $bill->id,
                    'student_id' => $c7Student->id,
                    'parent_id' => $c7Student->parent_id,
                    'recorded_by' => $admin->id,
                    'payment_number' => Payment::generatePaymentNumberFromBill($bill, Payment::SOURCE_MANUAL),
                    'amount' => 500000,
                    'status' => Payment::STATUS_SUCCESS,
                    'source' => Payment::SOURCE_MANUAL,
                    'method' => 'bank_transfer',
                    'notes' => 'Pembayaran Cicilan 1 Pendaftaran Siswa Baru',
                    'paid_at' => '2026-07-20 10:00:00',
                    'created_at' => '2026-07-20 10:00:00',
                    'updated_at' => '2026-07-20 10:00:00',
                ]);

                // Cicilan 2 (Agustus 2026: Rp 300.000)
                Payment::create([
                    'bill_id' => $bill->id,
                    'student_id' => $c7Student->id,
                    'parent_id' => $c7Student->parent_id,
                    'recorded_by' => $admin->id,
                    'payment_number' => Payment::generatePaymentNumberFromBill($bill, Payment::SOURCE_MANUAL),
                    'amount' => 300000,
                    'status' => Payment::STATUS_SUCCESS,
                    'source' => Payment::SOURCE_MANUAL,
                    'method' => 'bank_transfer',
                    'notes' => 'Pembayaran Cicilan 2 Pendaftaran Siswa Baru',
                    'paid_at' => '2026-08-20 10:00:00',
                    'created_at' => '2026-08-20 10:00:00',
                    'updated_at' => '2026-08-20 10:00:00',
                ]);
            }
        }

        // ─── 3. Daftar Ulang Tahun Ajaran Baru (Kelas 8 & 9) - Rp 440.000 ───────
        if ($daftarUlangGanjilType) {
            $class89Students = Student::whereIn('class_level', [8, 9])->take(40)->get();
            foreach ($class89Students as $s) {
                $cleanNis = str_replace([' ', '-'], '', $s->nis);
                $duAmount = 440000;

                $bill = Bill::create([
                    'student_id' => $s->id,
                    'parent_id' => $s->parent_id,
                    'payment_type_id' => $daftarUlangGanjilType->id,
                    'academic_year_id' => $academicYear->id,
                    'bill_number' => 'INV-'.$cleanNis.'-DUGANJIL',
                    'billing_period' => '2026-07-01',
                    'billing_date' => '2026-07-10',
                    'due_date' => '2026-08-10',
                    'amount' => $duAmount,
                    'paid_amount' => $duAmount,
                    'outstanding_amount' => 0,
                    'status' => Bill::STATUS_PAID,
                    'notes' => 'Daftar Ulang Tahun Ajaran Baru (Kelas '.$s->class_level.')',
                ]);

                BillItem::create([
                    'bill_id' => $bill->id,
                    'description' => 'Daftar Ulang Tahun Ajaran Baru Siswa Kelas 8 dan 9',
                    'quantity' => 1,
                    'unit_price' => $duAmount,
                    'subtotal' => $duAmount,
                ]);

                Payment::create([
                    'bill_id' => $bill->id,
                    'student_id' => $s->id,
                    'parent_id' => $s->parent_id,
                    'recorded_by' => $admin->id,
                    'payment_number' => Payment::generatePaymentNumberFromBill($bill, Payment::SOURCE_MANUAL),
                    'amount' => $duAmount,
                    'status' => Payment::STATUS_SUCCESS,
                    'source' => Payment::SOURCE_MANUAL,
                    'method' => 'cash',
                    'notes' => 'Lunas Daftar Ulang Tahun Ajaran Baru',
                    'paid_at' => '2026-07-15 09:30:00',
                    'created_at' => '2026-07-15 09:30:00',
                    'updated_at' => '2026-07-15 09:30:00',
                ]);
            }
        }

        // ─── 4. LDKS (Kelas 7) - Rp 450.000 ─────────────────────────────────────
        if ($ldksType) {
            $class7Ldks = Student::where('class_level', 7)->take(30)->get();
            foreach ($class7Ldks as $c7Ldks) {
                $cleanNis = str_replace([' ', '-'], '', $c7Ldks->nis);
                $ldksAmount = 450000;
                $isPaid = rand(1, 100) <= 80;

                $bill = Bill::create([
                    'student_id' => $c7Ldks->id,
                    'parent_id' => $c7Ldks->parent_id,
                    'payment_type_id' => $ldksType->id,
                    'academic_year_id' => $academicYear->id,
                    'bill_number' => 'INV-'.$cleanNis.'-LDKS',
                    'billing_period' => '2026-08-01',
                    'billing_date' => '2026-08-05',
                    'due_date' => '2026-08-25',
                    'amount' => $ldksAmount,
                    'paid_amount' => $isPaid ? $ldksAmount : 0,
                    'outstanding_amount' => $isPaid ? 0 : $ldksAmount,
                    'status' => $isPaid ? Bill::STATUS_PAID : Bill::STATUS_UNPAID,
                    'notes' => 'Kegiatan Latihan Dasar Kepemimpinan Siswa (LDKS)',
                ]);

                BillItem::create([
                    'bill_id' => $bill->id,
                    'description' => 'Biaya Kegiatan LDKS Siswa Kelas 7',
                    'quantity' => 1,
                    'unit_price' => $ldksAmount,
                    'subtotal' => $ldksAmount,
                ]);

                if ($isPaid) {
                    Payment::create([
                        'bill_id' => $bill->id,
                        'student_id' => $c7Ldks->id,
                        'parent_id' => $c7Ldks->parent_id,
                        'recorded_by' => $admin->id,
                        'payment_number' => Payment::generatePaymentNumberFromBill($bill, Payment::SOURCE_MIDTRANS),
                        'amount' => $ldksAmount,
                        'status' => Payment::STATUS_SUCCESS,
                        'source' => Payment::SOURCE_MIDTRANS,
                        'method' => 'qris',
                        'notes' => 'Pembayaran Lunas LDKS Kelas 7',
                        'paid_at' => '2026-08-12 11:20:00',
                        'created_at' => '2026-08-12 11:20:00',
                        'updated_at' => '2026-08-12 11:20:00',
                    ]);
                }
            }
        }

        // ─── 5. Study Tour (Kelas 8) - Rp 450.000 ───────────────────────────────
        if ($studyTourType) {
            $class8Tour = Student::where('class_level', 8)->take(30)->get();
            foreach ($class8Tour as $c8Tour) {
                $cleanNis = str_replace([' ', '-'], '', $c8Tour->nis);
                $stAmount = 450000;
                $isPaid = rand(1, 100) <= 70;

                $bill = Bill::create([
                    'student_id' => $c8Tour->id,
                    'parent_id' => $c8Tour->parent_id,
                    'payment_type_id' => $studyTourType->id,
                    'academic_year_id' => $academicYear->id,
                    'bill_number' => 'INV-'.$cleanNis.'-STUDYTOUR',
                    'billing_period' => '2026-05-01',
                    'billing_date' => '2026-05-05',
                    'due_date' => '2026-05-25',
                    'amount' => $stAmount,
                    'paid_amount' => $isPaid ? $stAmount : 0,
                    'outstanding_amount' => $isPaid ? 0 : $stAmount,
                    'status' => $isPaid ? Bill::STATUS_PAID : Bill::STATUS_UNPAID,
                    'notes' => 'Kegiatan Study Tour Edukatif Kelas 8',
                ]);

                BillItem::create([
                    'bill_id' => $bill->id,
                    'description' => 'Biaya Kegiatan Study Tour Kelas 8',
                    'quantity' => 1,
                    'unit_price' => $stAmount,
                    'subtotal' => $stAmount,
                ]);

                if ($isPaid) {
                    Payment::create([
                        'bill_id' => $bill->id,
                        'student_id' => $c8Tour->id,
                        'parent_id' => $c8Tour->parent_id,
                        'recorded_by' => $admin->id,
                        'payment_number' => Payment::generatePaymentNumberFromBill($bill, Payment::SOURCE_MANUAL),
                        'amount' => $stAmount,
                        'status' => Payment::STATUS_SUCCESS,
                        'source' => Payment::SOURCE_MANUAL,
                        'method' => 'bank_transfer',
                        'notes' => 'Pembayaran Lunas Study Tour Kelas 8',
                        'paid_at' => '2026-05-18 14:00:00',
                        'created_at' => '2026-05-18 14:00:00',
                        'updated_at' => '2026-05-18 14:00:00',
                    ]);
                }
            }
        }

        // ─── 6. Akhir Tahun (Kelas 9) - Rp 2.000.000 (Cicilan 10x) ──────────────
        if ($akhirTahunType) {
            $class9Students = Student::where('class_level', 9)->take(15)->get();
            foreach ($class9Students as $c9Student) {
                $cleanNis = str_replace([' ', '-'], '', $c9Student->nis);
                $totalAt = 2000000;
                $installmentAmount = 200000;

                $bill = Bill::create([
                    'student_id' => $c9Student->id,
                    'parent_id' => $c9Student->parent_id,
                    'payment_type_id' => $akhirTahunType->id,
                    'academic_year_id' => $academicYear->id,
                    'bill_number' => 'INV-'.$cleanNis.'-AKHIRTAHUN',
                    'billing_period' => '2026-01-01',
                    'billing_date' => '2026-01-10',
                    'due_date' => '2026-10-31',
                    'amount' => $totalAt,
                    'paid_amount' => 1200000,
                    'outstanding_amount' => 800000,
                    'status' => Bill::STATUS_UNPAID,
                    'notes' => 'Kegiatan Akhir Tahun, Wisuda & Pelepasan Kelas 9 (Tenor 10x)',
                ]);

                BillItem::create([
                    'bill_id' => $bill->id,
                    'description' => 'Kegiatan Akhir Tahun & Pelepasan Kelas 9',
                    'quantity' => 1,
                    'unit_price' => $totalAt,
                    'subtotal' => $totalAt,
                ]);

                for ($t = 1; $t <= 6; $t++) {
                    $tMonth = str_pad($t, 2, '0', STR_PAD_LEFT);
                    Payment::create([
                        'bill_id' => $bill->id,
                        'student_id' => $c9Student->id,
                        'parent_id' => $c9Student->parent_id,
                        'recorded_by' => $admin->id,
                        'payment_number' => Payment::generatePaymentNumberFromBill($bill, Payment::SOURCE_MIDTRANS),
                        'amount' => $installmentAmount,
                        'status' => Payment::STATUS_SUCCESS,
                        'source' => Payment::SOURCE_MIDTRANS,
                        'method' => 'qris',
                        'notes' => "Pembayaran Cicilan {$t} dari 10 Akhir Tahun",
                        'paid_at' => "2026-{$tMonth}-28 14:30:00",
                        'created_at' => "2026-{$tMonth}-28 14:30:00",
                        'updated_at' => "2026-{$tMonth}-28 14:30:00",
                    ]);
                }
            }
        }

        // ─── 7. ASTS/PTS Ganjil (Rp 75.000) & ASAS Ganjil (Rp 150.000) ───────────
        if ($astsPtsGanjilType) {
            $sampleAsts = Student::take(25)->get();
            foreach ($sampleAsts as $s) {
                $cleanNis = str_replace([' ', '-'], '', $s->nis);
                $astsAmount = 75000;

                $bill = Bill::create([
                    'student_id' => $s->id,
                    'parent_id' => $s->parent_id,
                    'payment_type_id' => $astsPtsGanjilType->id,
                    'academic_year_id' => $academicYear->id,
                    'bill_number' => 'INV-'.$cleanNis.'-ASTSGANJIL',
                    'billing_period' => '2026-09-01',
                    'billing_date' => '2026-09-01',
                    'due_date' => '2026-09-15',
                    'amount' => $astsAmount,
                    'paid_amount' => $astsAmount,
                    'outstanding_amount' => 0,
                    'status' => Bill::STATUS_PAID,
                    'notes' => 'Ujian ASTS/PTS Semester Ganjil',
                ]);

                BillItem::create([
                    'bill_id' => $bill->id,
                    'description' => 'Biaya Ujian ASTS/PTS Ganjil',
                    'quantity' => 1,
                    'unit_price' => $astsAmount,
                    'subtotal' => $astsAmount,
                ]);

                Payment::create([
                    'bill_id' => $bill->id,
                    'student_id' => $s->id,
                    'parent_id' => $s->parent_id,
                    'recorded_by' => $admin->id,
                    'payment_number' => Payment::generatePaymentNumberFromBill($bill, Payment::SOURCE_MANUAL),
                    'amount' => $astsAmount,
                    'status' => Payment::STATUS_SUCCESS,
                    'source' => Payment::SOURCE_MANUAL,
                    'method' => 'cash',
                    'notes' => 'Pembayaran Ujian ASTS/PTS Ganjil',
                    'paid_at' => '2026-09-05 08:45:00',
                    'created_at' => '2026-09-05 08:45:00',
                    'updated_at' => '2026-09-05 08:45:00',
                ]);
            }
        }

        // WhatsApp Logs
        $samplePayments = Payment::where('status', Payment::STATUS_SUCCESS)->take(20)->get();
        $waService = new \App\Services\WhatsAppAutomationService;
        foreach ($samplePayments as $p) {
            $waService->logPaymentSuccess($p);
        }

        $sampleUnpaidBills = Bill::where('status', Bill::STATUS_UNPAID)->take(15)->get();
        foreach ($sampleUnpaidBills as $b) {
            $waService->logDueReminder($b);
        }
    }
}
