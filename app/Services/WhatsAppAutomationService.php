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
        $payment->loadMissing(['student.parent', 'bill.paymentType']);
        $student = $payment->student;
        $parent = $student?->parent;
        $recipientName = $parent?->full_name ?? 'Wali '.($student?->full_name ?? 'Siswa');
        $phone = $parent?->phone ?? '081234567890';
        $amount = (int) $payment->amount;

        $paymentNumber = $payment->payment_number ?? ('PAY-'.str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT));
        $paidDateStr = $payment->paid_at ? Carbon::parse($payment->paid_at)->translatedFormat('d F Y H:i') : now()->translatedFormat('d F Y H:i');

        $msg = "📢 *BUKTI PEMBAYARAN SAH - MTs MIFTAHUL 'ULUM*\n\n"
            ."Assalamu'alaikum Wr. Wb.\n"
            ."Yth. Bapak/Ibu *{$recipientName}*,\n\n"
            ."Alhamdulillah, pembayaran administrasi pendidikan telah kami terima dengan rincian sbb:\n\n"
            ."📄 *No. Kuitansi:* {$paymentNumber}\n"
            .'👤 *Nama Siswa:* '.($student?->full_name ?? '-').' (NISN: '.($student?->nis ?? '-').")\n"
            .'🏫 *Kelas:* '.($student?->class_rombel ?? '-')."\n"
            .'💳 *Pembayaran:* '.($payment->bill?->paymentType?->name ?? 'SPP/Administrasi')."\n"
            .'💰 *Nominal:* Rp '.number_format($amount, 0, ',', '.')."\n"
            ."📅 *Tanggal Bayar:* {$paidDateStr} WIB\n"
            ."✅ *Status:* LUNAS (Sah)\n\n"
            ."Terima kasih atas kerja sama dan kepercayaan Bapak/Ibu.\n\n"
            ."_Wassalamu'alaikum Wr. Wb._\n"
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
        $bill->loadMissing(['student.parent', 'paymentType']);
        $student = $bill->student;
        $parent = $student?->parent;
        $recipientName = $parent?->full_name ?? 'Wali '.($student?->full_name ?? 'Siswa');
        $phone = $parent?->phone ?? '081234567890';
        $amount = (int) $bill->outstanding_amount;

        $dueDateStr = $bill->due_date ? Carbon::parse($bill->due_date)->translatedFormat('d F Y') : '-';

        $msg = "🔔 *PENGINGAT JATUH TEMPO PEMBAYARAN - MTs MIFTAHUL 'ULUM*\n\n"
            ."Assalamu'alaikum Wr. Wb.\n"
            ."Yth. Bapak/Ibu *{$recipientName}*,\n\n"
            ."Menginfokan bahwa tagihan administrasi sekolah atas nama:\n"
            .'👤 *Siswa:* '.($student?->full_name ?? '-').' (Kelas: '.($student?->class_rombel ?? '-').")\n"
            .'📌 *Tagihan:* '.($bill->paymentType?->name ?? 'Tagihan Sekolah')."\n"
            .'💰 *Sisa Tagihan:* Rp '.number_format($amount, 0, ',', '.')."\n"
            ."⏰ *Jatuh Tempo:* {$dueDateStr}\n\n"
            ."Pembayaran dapat dilakukan melalui Portal Online atau langsung ke loket kantor bendahara.\n\n"
            ."Terima kasih atas perhatiannya.\n"
            ."_Wassalamu'alaikum Wr. Wb._";

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
        $bill->loadMissing(['student.parent', 'paymentType']);
        $student = $bill->student;
        $parent = $student?->parent;
        $recipientName = $parent?->full_name ?? 'Wali '.($student?->full_name ?? 'Siswa');
        $phone = $parent?->phone ?? '081234567890';
        $amount = (int) $bill->amount;

        $dueDateStr = $bill->due_date ? Carbon::parse($bill->due_date)->translatedFormat('d F Y') : '-';

        $msg = "📄 *TAGIHAN BARU DITERBITKAN - MTs MIFTAHUL 'ULUM*\n\n"
            ."Assalamu'alaikum Wr. Wb.\n"
            ."Yth. Bapak/Ibu *{$recipientName}*,\n\n"
            ."Tagihan baru telah diterbitkan untuk ananda:\n"
            .'👤 *Siswa:* '.($student?->full_name ?? '-').' (Kelas: '.($student?->class_rombel ?? '-').")\n"
            .'📌 *Jenis Pembayaran:* '.($bill->paymentType?->name ?? 'Tagihan Sekolah')."\n"
            .'💰 *Total Tagihan:* Rp '.number_format($amount, 0, ',', '.')."\n"
            ."⏰ *Jatuh Tempo:* {$dueDateStr}\n\n"
            ."Silakan cek rinciannya melalui Portal Wali Siswa.\n\n"
            ."_Wassalamu'alaikum Wr. Wb._";

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
        }

        return 'https://wa.me/'.$cleanPhone.'?text='.rawurlencode($message);
    }
}
