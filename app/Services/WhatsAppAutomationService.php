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
        $phone = $parent?->phone ?? '081234567890';
        $amount = (int) $payment->amount;

        $paymentNumber = $payment->payment_number ?? ('PAY-'.str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT));
        $paidDateStr = $payment->paid_at ? Carbon::parse($payment->paid_at)->translatedFormat('d F Y H:i') : now()->translatedFormat('d F Y H:i');
        $methodLabel = strtoupper($payment->method ?? $payment->source ?? 'TUNAI');
        $sourceLabel = Payment::SOURCES[$payment->source] ?? 'Manual / Kasir';

        $receiptPdfUrl = url('/docs/receipt/'.$paymentNumber);

        $msg = "==============================\n"
            ."*YAYASAN PERGURUAN ISLAM MIFTAHUL 'ULUM*\n"
            ."*MTs. MIFTAHUL 'ULUM CIBITUNG*\n"
            ."==============================\n\n"
            ."*BUKTI PEMBAYARAN PENDIDIKAN (LUNAS)*\n"
            ."_Assalamu'alaikum Warahmatullahi Wabarakatuh_\n\n"
            ."Yth. Bapak/Ibu *{$recipientName}*,\n"
            ."Wali dari ananda *".($student?->full_name ?? '-')."*\n\n"
            ."Alhamdulillah, pembayaran administrasi pendidikan telah kami terima dan tercatat sah di sistem madrasah:\n\n"
            ."*RINCIAN PEMBAYARAN:*\n"
            ."• *No. Kuitansi:* `{$paymentNumber}`\n"
            ."• *Nama Siswa:* ".($student?->full_name ?? '-')." (NISN: ".($student?->nis ?? '-').")\n"
            ."• *Kelas / Rombel:* Kelas ".($student?->class_rombel ?? '-')."\n"
            ."• *Jenis Tagihan:* ".($payment->bill?->paymentType?->name ?? 'SPP / Administrasi')."\n"
            ."• *Nominal Dibayar:* *Rp ".number_format($amount, 0, ',', '.')."*\n"
            ."• *Metode Bayar:* {$methodLabel} ({$sourceLabel})\n"
            ."• *Waktu Transaksi:* {$paidDateStr} WIB\n"
            ."• *Status:* *LUNAS (SAH)*\n\n"
            ."📄 *Unduh Kuitansi PDF Resmi Berstempel:*\n"
            ."{$receiptPdfUrl}\n\n"
            ."Terima kasih atas partisipasi dan kerja sama Bapak/Ibu dalam mendukung pendidikan putra/putri tercinta.\n\n"
            ."_Jazakumullah Khairan Katsiran_\n"
            ."_Wassalamu'alaikum Warahmatullahi Wabarakatuh_\n\n"
            ."*Bendahara MTs Miftahul 'Ulum*";

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
     * Format teks pesan notifikasi Pengingat Jatuh Tempo (Reminder H-2/H-7).
     */
    public function formatDueReminderMessage(Bill $bill): array
    {
        $bill->loadMissing(['student.parentProfile', 'paymentType', 'academicYear']);
        $student = $bill->student;
        $parent = $student?->parentProfile ?? $student?->parent;
        $recipientName = $parent?->full_name ?? 'Wali '.($student?->full_name ?? 'Siswa');
        $phone = $parent?->phone ?? '081234567890';
        $amount = (int) $bill->outstanding_amount;

        $dueDateStr = $bill->due_date ? Carbon::parse($bill->due_date)->translatedFormat('d F Y') : '-';
        $periodStr = $bill->billing_period ? Carbon::parse($bill->billing_period)->translatedFormat('F Y') : '-';
        $invoicePdfUrl = url('/docs/invoice/'.$bill->bill_number);

        $msg = "==============================\n"
            ."*YAYASAN PERGURUAN ISLAM MIFTAHUL 'ULUM*\n"
            ."*MTs. MIFTAHUL 'ULUM CIBITUNG*\n"
            ."==============================\n\n"
            ."*PENGINGAT JATUH TEMPO PEMBAYARAN*\n"
            ."_Assalamu'alaikum Warahmatullahi Wabarakatuh_\n\n"
            ."Yth. Bapak/Ibu *{$recipientName}*,\n"
            ."Wali dari ananda *".($student?->full_name ?? '-')."*\n\n"
            ."Menginfokan bahwa tagihan administrasi pendidikan berikut akan segera melewati batas jatuh tempo:\n\n"
            ."*RINCIAN TAGIHAN:*\n"
            ."• *No. Invoice:* `{$bill->bill_number}`\n"
            ."• *Nama Siswa:* ".($student?->full_name ?? '-')." (NISN: ".($student?->nis ?? '-').")\n"
            ."• *Kelas / Rombel:* Kelas ".($student?->class_rombel ?? '-')."\n"
            ."• *Jenis Tagihan:* ".($bill->paymentType?->name ?? 'Tagihan Sekolah')."\n"
            ."• *Periode:* {$periodStr}\n"
            ."• *Sisa Tagihan:* *Rp ".number_format($amount, 0, ',', '.')."*\n"
            ."• *Batas Jatuh Tempo:* *{$dueDateStr}*\n\n"
            ."💳 *Cara Pembayaran:* \n"
            ."1. Online via Portal Wali Siswa (Midtrans / QRIS / Transfer).\n"
            ."2. Langsung melalui loket kantor Bendahara MTs Miftahul 'Ulum.\n\n"
            ."📄 *Unduh Invoice Tagihan PDF Resmi:*\n"
            ."{$invoicePdfUrl}\n\n"
            ."Mohon untuk melakukan pembayaran sebelum tanggal jatuh tempo. Terima kasih atas perhatian Bapak/Ibu.\n\n"
            ."_Wassalamu'alaikum Warahmatullahi Wabarakatuh_\n\n"
            ."*Bendahara MTs Miftahul 'Ulum*";

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
     * Format teks pesan notifikasi Tagihan Baru.
     */
    public function formatNewBillMessage(Bill $bill): array
    {
        $bill->loadMissing(['student.parentProfile', 'paymentType', 'academicYear']);
        $student = $bill->student;
        $parent = $student?->parentProfile ?? $student?->parent;
        $recipientName = $parent?->full_name ?? 'Wali '.($student?->full_name ?? 'Siswa');
        $phone = $parent?->phone ?? '081234567890';
        $amount = (int) $bill->amount;

        $dueDateStr = $bill->due_date ? Carbon::parse($bill->due_date)->translatedFormat('d F Y') : '-';
        $periodStr = $bill->billing_period ? Carbon::parse($bill->billing_period)->translatedFormat('F Y') : '-';
        $invoicePdfUrl = url('/docs/invoice/'.$bill->bill_number);

        $msg = "==============================\n"
            ."*YAYASAN PERGURUAN ISLAM MIFTAHUL 'ULUM*\n"
            ."*MTs. MIFTAHUL 'ULUM CIBITUNG*\n"
            ."==============================\n\n"
            ."*PEMBERITAHUAN TAGIHAN PENDIDIKAN*\n"
            ."_Assalamu'alaikum Warahmatullahi Wabarakatuh_\n\n"
            ."Yth. Bapak/Ibu *{$recipientName}*,\n"
            ."Wali dari ananda *".($student?->full_name ?? '-')."*\n\n"
            ."Berikut kami sampaikan rincian tagihan pendidikan yang telah diterbitkan:\n\n"
            ."*RINCIAN TAGIHAN:*\n"
            ."• *No. Invoice:* `{$bill->bill_number}`\n"
            ."• *Nama Siswa:* ".($student?->full_name ?? '-')." (NISN: ".($student?->nis ?? '-').")\n"
            ."• *Kelas / Rombel:* Kelas ".($student?->class_rombel ?? '-')."\n"
            ."• *Jenis Tagihan:* ".($bill->paymentType?->name ?? 'Tagihan Sekolah')."\n"
            ."• *Periode Tagihan:* {$periodStr}\n"
            ."• *Total Tagihan:* *Rp ".number_format($amount, 0, ',', '.')."*\n"
            ."• *Batas Jatuh Tempo:* *{$dueDateStr}*\n\n"
            ."💳 *Pilihan Metode Pembayaran:*\n"
            ."1. *Online:* Login ke Portal Wali Siswa untuk bayar instan via Midtrans (QRIS / Transfer Bank / E-Wallet).\n"
            ."2. *Offline:* Melalui loket kantor Bendahara MTs Miftahul 'Ulum.\n\n"
            ."📄 *Unduh Invoice Tagihan PDF Resmi:*\n"
            ."{$invoicePdfUrl}\n\n"
            ."_Mohon untuk melakukan penyelesaian sebelum tanggal jatuh tempo. Terima kasih atas perhatian dan kerja sama Bapak/Ibu._\n\n"
            ."_Wassalamu'alaikum Warahmatullahi Wabarakatuh_\n\n"
            ."*Bendahara MTs Miftahul 'Ulum*";

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
