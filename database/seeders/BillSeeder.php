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
use Illuminate\Support\Str;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::current() ?? AcademicYear::first();
        $sppType = PaymentType::where('code', PaymentType::CODE_SPP)->first();
        $daftarUlangType = PaymentType::where('code', PaymentType::CODE_DAFTAR_ULANG)->first();
        $atType = PaymentType::where('code', PaymentType::CODE_AT)->first();
        $pendaftaranBaruType = PaymentType::where('code', PaymentType::CODE_PENDAFTARAN_BARU)->first();
        $admin = User::where('role', 'admin')->first() ?? User::first();

        if (! $academicYear || ! $sppType) {
            return;
        }

        $students = Student::where('status', Student::STATUS_ACTIVE)->get();
        if ($students->isEmpty()) {
            return;
        }

        $billCounter = 1;
        $paymentMethods = ['cash', 'bank_transfer', 'midtrans_qris', 'midtrans_gopay', 'midtrans_bank_transfer'];

        // Loop untuk 8 Bulan di Tahun 2026 (Januari s/d Agustus 2026)
        for ($month = 1; $month <= 8; $month++) {
            $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
            $billingPeriod = "2026-{$monthStr}-01";
            $billingDate = "2026-{$monthStr}-01";
            $dueDate = "2026-{$monthStr}-10"; // Sesuai Konfirmasi Client: Jatuh tempo tgl 10
            $createdAtTime = "2026-{$monthStr}-01 08:00:00";

            // Setiap siswa aktif dibuatkan SPP Bulanan
            foreach ($students as $student) {
                // Harga SPP MTs berdasarkan tingkat kelas
                $basePrice = match ($student->class_level) {
                    7 => 350000,
                    8 => 375000,
                    9 => 400000,
                    default => 350000,
                };

                $cleanNis = str_replace([' ', '-'], '', $student->nis);
                $billNumber = 'INV-'.$cleanNis.'-2026'.$monthStr.'-'.str_pad($billCounter, 4, '0', STR_PAD_LEFT);

                // Tentukan status pembayaran per bulan
                $isPaid = false;
                $isOverdue = false;

                if ($month <= 6) {
                    // Bulan Jan - Jun: 95% Lunas
                    $isPaid = (rand(1, 100) <= 95);
                } elseif ($month == 7) {
                    // Bulan Juli: 75% Lunas, 25% Terlambat (Overdue)
                    $randVal = rand(1, 100);
                    if ($randVal <= 75) {
                        $isPaid = true;
                    } else {
                        $isOverdue = true;
                    }
                } else {
                    // Bulan Agustus (Bulan Ini): 60% Lunas, 40% Belum Lunas (Jatuh tempo 10 Aug)
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
                    'amount' => $basePrice,
                    'paid_amount' => $isPaid ? $basePrice : 0,
                    'outstanding_amount' => $isPaid ? 0 : $basePrice,
                    'status' => $status,
                    'created_at' => $createdAtTime,
                    'updated_at' => $createdAtTime,
                ]);

                BillItem::create([
                    'bill_id' => $bill->id,
                    'description' => 'SPP Bulanan MTs - '.Carbon::parse($billingPeriod)->translatedFormat('F Y'),
                    'quantity' => 1,
                    'unit_price' => $basePrice,
                    'subtotal' => $basePrice,
                    'created_at' => $createdAtTime,
                    'updated_at' => $createdAtTime,
                ]);

                // Jika tagihan lunas, catat pembayaran dengan variasi tanggal bayar & metode
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
                        'amount' => $basePrice,
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

        // Tagihan Pendaftaran Siswa Baru (Uang Masuk Kelas 7 - Cicilan 3x)
        if ($pendaftaranBaruType) {
            $class7Students = Student::where('class_level', 7)->get();
            foreach ($class7Students as $c7Student) {
                $cleanNis = str_replace([' ', '-'], '', $c7Student->nis);
                $totalEntry = 1500000;
                $installmentPerMonth = 500000; // 3x cicilan

                for ($t = 1; $t <= 3; $t++) {
                    $tMonth = 6 + $t; // Juli, Agustus, September
                    $tMonthStr = str_pad($tMonth, 2, '0', STR_PAD_LEFT);
                    $tPeriod = "2026-{$tMonthStr}-01";
                    $tDueDate = Carbon::parse($tPeriod)->endOfMonth()->format('Y-m-d'); // Akhir bulan
                    $isTPaid = ($t <= 2); // Bulan 1 & 2 Lunas

                    $bill = Bill::create([
                        'student_id' => $c7Student->id,
                        'parent_id' => $c7Student->parent_id,
                        'payment_type_id' => $pendaftaranBaruType->id,
                        'academic_year_id' => $academicYear->id,
                        'bill_number' => 'INV-'.$cleanNis.'-PENDAFTARAN-'.str_pad($t, 2, '0', STR_PAD_LEFT),
                        'billing_period' => $tPeriod,
                        'billing_date' => '2026-07-15',
                        'due_date' => $tDueDate,
                        'amount' => $installmentPerMonth,
                        'paid_amount' => $isTPaid ? $installmentPerMonth : 0,
                        'outstanding_amount' => $isTPaid ? 0 : $installmentPerMonth,
                        'status' => $isTPaid ? Bill::STATUS_PAID : Bill::STATUS_UNPAID,
                        'notes' => "Cicilan {$t} dari 3 - Pendaftaran Siswa Baru MTs",
                    ]);

                    BillItem::create([
                        'bill_id' => $bill->id,
                        'description' => "Pendaftaran Siswa Baru (Cicilan {$t}/3)",
                        'quantity' => 1,
                        'unit_price' => $installmentPerMonth,
                        'subtotal' => $installmentPerMonth,
                    ]);

                    if ($isTPaid) {
                        Payment::create([
                            'bill_id' => $bill->id,
                            'student_id' => $c7Student->id,
                            'parent_id' => $c7Student->parent_id,
                            'recorded_by' => $admin->id,
                            'payment_number' => Payment::generatePaymentNumberFromBill($bill, Payment::SOURCE_MANUAL),
                            'amount' => $installmentPerMonth,
                            'status' => Payment::STATUS_SUCCESS,
                            'source' => Payment::SOURCE_MANUAL,
                            'method' => 'bank_transfer',
                            'notes' => "Pembayaran Cicilan {$t} dari 3 Pendaftaran Siswa Baru",
                            'paid_at' => "2026-{$tMonthStr}-20 10:00:00",
                            'created_at' => "2026-{$tMonthStr}-20 10:00:00",
                            'updated_at' => "2026-{$tMonthStr}-20 10:00:00",
                        ]);
                    }
                }
            }
        }

        // Tagihan Akhir Tahun Kelas 9 (AT - Cicilan 10x)
        if ($atType) {
            $class9Students = Student::where('class_level', 9)->take(5)->get();
            foreach ($class9Students as $c9Student) {
                $cleanNis = str_replace([' ', '-'], '', $c9Student->nis);
                $installmentAmount = 200000; // 10x cicilan dari Rp 2.000.000

                for ($t = 1; $t <= 10; $t++) {
                    $tMonth = (($t - 1) % 12) + 1;
                    $tYear = ($t <= 6) ? 2026 : 2026;
                    $tMonthStr = str_pad($tMonth, 2, '0', STR_PAD_LEFT);
                    $tPeriod = "{$tYear}-{$tMonthStr}-01";
                    $tDueDate = Carbon::parse($tPeriod)->endOfMonth()->format('Y-m-d');
                    $isTPaid = ($t <= 6); // Cicilan 1-6 sudah terbayar

                    $bill = Bill::create([
                        'student_id' => $c9Student->id,
                        'parent_id' => $c9Student->parent_id,
                        'payment_type_id' => $atType->id,
                        'academic_year_id' => $academicYear->id,
                        'bill_number' => 'INV-'.$cleanNis.'-AT-'.str_pad($t, 2, '0', STR_PAD_LEFT),
                        'billing_period' => $tPeriod,
                        'billing_date' => '2026-01-10',
                        'due_date' => $tDueDate,
                        'amount' => $installmentAmount,
                        'paid_amount' => $isTPaid ? $installmentAmount : 0,
                        'outstanding_amount' => $isTPaid ? 0 : $installmentAmount,
                        'status' => $isTPaid ? Bill::STATUS_PAID : Bill::STATUS_UNPAID,
                        'notes' => "Cicilan {$t} dari 10 - Kegiatan Akhir Tahun Kelas 9",
                    ]);

                    BillItem::create([
                        'bill_id' => $bill->id,
                        'description' => "Kegiatan Akhir Tahun & Tour Kelas 9 (Cicilan {$t}/10)",
                        'quantity' => 1,
                        'unit_price' => $installmentAmount,
                        'subtotal' => $installmentAmount,
                    ]);

                    if ($isTPaid) {
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
                            'paid_at' => "2026-{$tMonthStr}-28 14:30:00",
                            'created_at' => "2026-{$tMonthStr}-28 14:30:00",
                            'updated_at' => "2026-{$tMonthStr}-28 14:30:00",
                        ]);
                    }
                }
            }
        }

        // Generate Sample WhatsApp Logs untuk Dashboard & Log WhatsApp
        $samplePayments = Payment::where('status', Payment::STATUS_SUCCESS)->take(15)->get();
        $waService = new \App\Services\WhatsAppAutomationService;
        foreach ($samplePayments as $p) {
            $waService->logPaymentSuccess($p);
        }

        $sampleUnpaidBills = Bill::where('status', Bill::STATUS_UNPAID)->take(10)->get();
        foreach ($sampleUnpaidBills as $b) {
            $waService->logDueReminder($b);
        }
    }
}
