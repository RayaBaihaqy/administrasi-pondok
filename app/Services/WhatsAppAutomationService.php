<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\WhatsAppLog;
use Carbon\Carbon;

class WhatsAppAutomationService
{
    /**
     * Format teks pesan notifikasi Pembayaran Sukses (Kuitansi Resmi).
     */
    public function formatPaymentSuccessMessage(Payment $payment): array
    {
        $payment->loadMissing(['student.parentProfile', 'bill.paymentType']);
        $student = $payment->student;
        $parent = $student?->parentProfile ?? $student?->parent;
        $recipientName = $parent?->full_name ?? 'Wali '.($student?->full_name ?? 'Siswa');
        $phone = $parent?->phone ?: ($student?->phone ?: ($parent?->user?->phone ?: '081234567890'));
        $amount = (int) $payment->amount;

        $paymentNumber = $payment->payment_number ?? ('PAY-'.str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT));
        $paidDateStr = $payment->paid_at ? Carbon::parse($payment->paid_at)->translatedFormat('d F Y H:i') : now()->translatedFormat('d F Y H:i');

        $rawMethod = strtoupper($payment->method ?? $payment->source ?? 'TUNAI');
        $sourceLabel = match ($payment->source) {
            Payment::SOURCE_MIDTRANS => 'Online / Midtrans',
            Payment::SOURCE_MANUAL => 'Manual / Offline',
            default => 'Manual / Offline',
        };
        $methodLabel = "{$rawMethod} ({$sourceLabel})";

        $receiptPdfUrl = url('/docs/receipt/'.$paymentNumber);
        $address = config('school.address', 'Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung Kab. Bekasi');

        $studentName = $student?->full_name ?? '-';
        $nisn = $student?->nis ?? '-';
        $classRombel = $student?->class_rombel ?? '-';
        $paymentTypeName = $payment->bill?->paymentType?->name ?? 'SPP';
        $amountStr = 'Rp '.number_format($amount, 0, ',', '.');

        $msg = "==============================\n"
            ."MTs. MIFTAHUL 'ULUM\n"
            ."{$address}\n"
            ."==============================\n\n"
            ."BUKTI PEMBAYARAN PENDIDIKAN (LUNAS)\n"
            ."Assalamu'alaikum Warahmatullahi Wabarakatuh\n\n"
            ."Yth. Bapak/Ibu {$recipientName},\n"
            ."Wali dari ananda {$studentName}\n\n"
            ."Alhamdulillah, pembayaran administrasi pendidikan telah kami terima dan tercatat sah di sistem madrasah:\n\n"
            ."RINCIAN PEMBAYARAN:\n"
            ."* No. Kuitansi: {$paymentNumber}\n"
            ."* Nama Siswa: {$studentName} (NISN: {$nisn})\n"
            ."* Kelas / Rombel: Kelas {$classRombel}\n"
            ."* Jenis Tagihan: {$paymentTypeName}\n"
            ."* Nominal Dibayar: {$amountStr}\n"
            ."* Metode Bayar: {$methodLabel}\n"
            ."* Waktu Transaksi: {$paidDateStr} WIB\n"
            ."* Status: LUNAS\n\n"
            ."📄 Unduh Kuitansi PDF Resmi Berstempel:\n"
            ."{$receiptPdfUrl}\n\n"
            ."Terima kasih atas partisipasi dan kerja sama Bapak/Ibu dalam mendukung pendidikan putra/putri tercinta.\n\n"
            ."Wassalamu'alaikum Warahmatullahi Wabarakatuh\n\n"
            ."Bendahara MTs Miftahul 'Ulum";

        return [
            'parent_id' => $parent?->id,
            'student_id' => $student?->id,
            'recipient_name' => $recipientName,
            'phone_number' => $phone,
            'amount' => $amount,
            'message' => $msg,
        ];
    }

    /**
     * Format dan catat notifikasi Pembayaran Sukses (Kuitansi Resmi) ke Log WhatsApp.
     */
    public function logPaymentSuccess(Payment $payment): WhatsAppLog
    {
        $data = $this->formatPaymentSuccessMessage($payment);

        return WhatsAppLog::create([
            'parent_id' => $data['parent_id'],
            'student_id' => $data['student_id'],
            'recipient_name' => $data['recipient_name'],
            'phone_number' => $data['phone_number'],
            'message_type' => WhatsAppLog::TYPE_PAYMENT_SUCCESS,
            'amount' => $data['amount'],
            'message_content' => $data['message'],
            'status' => WhatsAppLog::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Format teks pesan notifikasi Tagihan Baru (Invoice).
     */
    public function formatNewBillMessage(Bill $bill): array
    {
        $bill->loadMissing(['student.parentProfile', 'paymentType', 'academicYear']);
        $student = $bill->student;
        $parent = $student?->parentProfile ?? $student?->parent;
        $recipientName = $parent?->full_name ?? 'Wali '.($student?->full_name ?? 'Siswa');
        $phone = $parent?->phone ?: ($student?->phone ?: ($parent?->user?->phone ?: '081234567890'));
        $amount = (int) $bill->amount;

        $dueDateStr = $bill->due_date ? Carbon::parse($bill->due_date)->translatedFormat('d F Y') : '-';
        $periodStr = $bill->billing_period ? Carbon::parse($bill->billing_period)->translatedFormat('F Y') : '-';
        $invoicePdfUrl = url('/docs/invoice/'.$bill->bill_number);

        $bankName = config('school.bank_name', 'Bank BRI');
        $bankAccount = config('school.bank_account', '176901000210569');
        $bankHolder = config('school.bank_holder', 'Madrasah Tsanawiyah Miftahul Ulum');
        $address = config('school.address', 'Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung Kab. Bekasi');

        $studentName = $student?->full_name ?? '-';
        $nisn = $student?->nis ?? '-';
        $classRombel = $student?->class_rombel ?? '-';
        $paymentTypeName = $bill->paymentType?->name ?? 'SPP';
        $amountStr = 'Rp '.number_format($amount, 0, ',', '.');
        $statusStr = $bill->status === Bill::STATUS_PAID ? 'LUNAS' : ($bill->status === Bill::STATUS_OVERDUE ? 'TERLAMBAT' : 'BELUM LUNAS');

        $msg = "==============================\n"
            ."MTs. MIFTAHUL 'ULUM\n"
            ."{$address}\n"
            ."==============================\n\n"
            ."PEMBERITAHUAN TAGIHAN PENDIDIKAN\n"
            ."Assalamu'alaikum Warahmatullahi Wabarakatuh\n\n"
            ."Yth. Bapak/Ibu {$recipientName},\n"
            ."Wali dari ananda {$studentName}\n\n"
            ."Berikut kami sampaikan rincian tagihan administrasi pendidikan yang telah diterbitkan:\n\n"
            ."RINCIAN TAGIHAN:\n"
            ."* No. Invoice: {$bill->bill_number}\n"
            ."* Nama Siswa: {$studentName} (NISN: {$nisn})\n"
            ."* Kelas / Rombel: Kelas {$classRombel}\n"
            ."* Jenis Tagihan: {$paymentTypeName}\n"
            ."* Periode Tagihan: {$periodStr}\n"
            ."* Total Tagihan: {$amountStr}\n"
            ."* Batas Jatuh Tempo: {$dueDateStr}\n"
            ."* Status: {$statusStr}\n\n"
            ."PILIHAN PEMBAYARAN TRANSFER BANK:\n"
            ."* Bank: {$bankName}\n"
            ."* No. Rekening: {$bankAccount}\n"
            ."* A/N: {$bankHolder}\n"
            ."(Sertakan berita transfer: {$bill->bill_number})\n\n"
            ."Atau pembayaran dapat dilakukan via Portal Online / Loket Kasir Madrasah.\n\n"
            ."📄 Unduh Invoice Tagihan PDF Resmi:\n"
            ."{$invoicePdfUrl}\n\n"
            ."Mohon untuk melakukan penyelesaian pembayaran sebelum tanggal jatuh tempo. Terima kasih atas perhatian dan kerja sama Bapak/Ibu.\n\n"
            ."Wassalamu'alaikum Warahmatullahi Wabarakatuh\n\n"
            ."Bendahara MTs Miftahul 'Ulum";

        return [
            'parent_id' => $parent?->id,
            'student_id' => $student?->id,
            'recipient_name' => $recipientName,
            'phone_number' => $phone,
            'amount' => $amount,
            'message' => $msg,
        ];
    }

    /**
     * Format dan catat notifikasi Tagihan Baru ke Log WhatsApp.
     */
    public function logNewBill(Bill $bill): WhatsAppLog
    {
        $data = $this->formatNewBillMessage($bill);

        return WhatsAppLog::create([
            'parent_id' => $data['parent_id'],
            'student_id' => $data['student_id'],
            'recipient_name' => $data['recipient_name'],
            'phone_number' => $data['phone_number'],
            'message_type' => WhatsAppLog::TYPE_NEW_BILL,
            'amount' => $data['amount'],
            'message_content' => $data['message'],
            'status' => WhatsAppLog::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Format teks pesan notifikasi Pengingat Jatuh Tempo (Reminder H-2/H-7).
     */
    public function formatDueReminderMessage(Bill $bill): array
    {
        $bill->loadMissing(['student.parentProfile', 'paymentType', 'academicYear']);
        $student = $bill->student;
        $parent = $student?->parentProfile ?? $student?->parent;
        $recipientName = $parent?->full_name ?? 'Wali '.($student?->full_name ?? 'Siswa');
        $phone = $parent?->phone ?: ($student?->phone ?: ($parent?->user?->phone ?: '081234567890'));
        $amount = (int) $bill->outstanding_amount;

        $dueDateStr = $bill->due_date ? Carbon::parse($bill->due_date)->translatedFormat('d F Y') : '-';
        $periodStr = $bill->billing_period ? Carbon::parse($bill->billing_period)->translatedFormat('F Y') : '-';
        $invoicePdfUrl = url('/docs/invoice/'.$bill->bill_number);

        $bankName = config('school.bank_name', 'Bank BRI');
        $bankAccount = config('school.bank_account', '176901000210569');
        $bankHolder = config('school.bank_holder', 'Madrasah Tsanawiyah Miftahul Ulum');
        $address = config('school.address', 'Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung Kab. Bekasi');

        $studentName = $student?->full_name ?? '-';
        $nisn = $student?->nis ?? '-';
        $classRombel = $student?->class_rombel ?? '-';
        $paymentTypeName = $bill->paymentType?->name ?? 'SPP';
        $amountStr = 'Rp '.number_format($amount, 0, ',', '.');
        $statusStr = $bill->status === Bill::STATUS_OVERDUE ? 'TERLAMBAT' : 'BELUM LUNAS';

        $msg = "==============================\n"
            ."MTs. MIFTAHUL 'ULUM\n"
            ."{$address}\n"
            ."==============================\n\n"
            ."PENGINGAT JATUH TEMPO PEMBAYARAN\n"
            ."Assalamu'alaikum Warahmatullahi Wabarakatuh\n\n"
            ."Yth. Bapak/Ibu {$recipientName},\n"
            ."Wali dari ananda {$studentName}\n\n"
            ."Menginfokan bahwa tagihan administrasi pendidikan ananda akan segera melewati batas jatuh tempo:\n\n"
            ."RINCIAN TAGIHAN:\n"
            ."* No. Invoice: {$bill->bill_number}\n"
            ."* Nama Siswa: {$studentName} (NISN: {$nisn})\n"
            ."* Kelas / Rombel: Kelas {$classRombel}\n"
            ."* Jenis Tagihan: {$paymentTypeName}\n"
            ."* Periode Tagihan: {$periodStr}\n"
            ."* Sisa Tagihan: {$amountStr}\n"
            ."* Batas Jatuh Tempo: {$dueDateStr}\n"
            ."* Status: {$statusStr}\n\n"
            ."PILIHAN PEMBAYARAN TRANSFER BANK:\n"
            ."* Bank: {$bankName}\n"
            ."* No. Rekening: {$bankAccount}\n"
            ."* A/N: {$bankHolder}\n"
            ."(Sertakan berita transfer: {$bill->bill_number})\n\n"
            ."Atau pembayaran dapat dilakukan via Portal Online / Loket Kasir Madrasah.\n\n"
            ."📄 Unduh Invoice Tagihan PDF Resmi:\n"
            ."{$invoicePdfUrl}\n\n"
            ."Mohon untuk melakukan penyelesaian pembayaran sebelum tanggal jatuh tempo. Terima kasih atas perhatian dan kerja sama Bapak/Ibu.\n\n"
            ."Wassalamu'alaikum Warahmatullahi Wabarakatuh\n\n"
            ."Bendahara MTs Miftahul 'Ulum";

        return [
            'parent_id' => $parent?->id,
            'student_id' => $student?->id,
            'recipient_name' => $recipientName,
            'phone_number' => $phone,
            'amount' => $amount,
            'message' => $msg,
        ];
    }

    /**
     * Format dan catat notifikasi Pengingat Jatuh Tempo (Reminder H-3/H-7) ke Log WhatsApp.
     */
    public function logDueReminder(Bill $bill): WhatsAppLog
    {
        $data = $this->formatDueReminderMessage($bill);

        return WhatsAppLog::create([
            'parent_id' => $data['parent_id'],
            'student_id' => $data['student_id'],
            'recipient_name' => $data['recipient_name'],
            'phone_number' => $data['phone_number'],
            'message_type' => WhatsAppLog::TYPE_DUE_REMINDER,
            'amount' => $data['amount'],
            'message_content' => $data['message'],
            'status' => WhatsAppLog::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Format direct WhatsApp URL.
     */
    public static function createWhatsAppUrl(string $phoneNumber, string $message): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '8')) {
            $cleanPhone = '62'.$cleanPhone;
        }

        return 'https://wa.me/'.$cleanPhone.'?text='.rawurlencode($message);
    }
}
