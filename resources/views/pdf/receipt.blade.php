<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $payment->payment_number ?? $receiptNumber }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.5; }
        .header { border-bottom: 2px solid #2e7d32; padding-bottom: 15px; margin-bottom: 20px; }
        .header table { width: 100%; }
        .title { font-size: 24px; font-weight: bold; color: #2e7d32; text-transform: uppercase; }
        .subtitle { font-size: 14px; color: #666; }
        .receipt-box { border: 2px solid #2e7d32; padding: 20px; border-radius: 5px; margin-bottom: 20px; background-color: #fafafa; }
        .table-details { width: 100%; border-collapse: collapse; }
        .table-details td { padding: 8px 5px; vertical-align: top; }
        .amount-banner { background-color: #2e7d32; color: #ffffff; padding: 12px 20px; font-size: 20px; font-weight: bold; display: inline-block; border-radius: 4px; margin-top: 15px; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; font-size: 11px; color: #888; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
        .sign-table { width: 100%; margin-top: 30px; }
        .sign-table td { text-align: center; vertical-align: bottom; }
    </style>
</head>
<body>

<div class="header">
    <table>
        <tr>
            <td width="70">
                <img src="{{ public_path('images/logo.png') }}" style="height: 60px; width: auto; margin-right: 15px;">
            </td>
            <td>
                <div class="title" style="font-size: 18px;">YAYASAN PERGURUAN ISLAM MIFTAHUL 'ULUM</div>
                <div class="subtitle">KUITANSI BUKTI PEMBAYARAN SAH - MTs. MIFTAHUL 'ULUM</div>
                <div style="font-size: 11px; color: #666;">Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung</div>
            </td>
            <td class="text-right">
                <div style="font-size: 14px; font-weight: bold; color: #2e7d32; word-break: break-all;">{{ $payment->payment_number }}</div>
                <div style="font-size: 11px; color: #666; margin-top: 3px;">Tanggal: {{ \Carbon\Carbon::parse($payment->paid_at)->translatedFormat('d F Y H:i') }} WIB</div>
            </td>
        </tr>
    </table>
</div>

<div class="receipt-box">
    <table class="table-details">
        <tr>
            <td width="25%"><strong>Telah Diterima Dari:</strong></td>
            <td width="75%">{{ $payment->parentProfile?->full_name ?? $payment->student?->full_name }}</td>
        </tr>
        <tr>
            <td><strong>Nama Siswa:</strong></td>
            <td>{{ $payment->student?->full_name }} (NISN: {{ $payment->student?->nis }}) - Kelas {{ $payment->student?->class_rombel }}</td>
        </tr>
        <tr>
            <td><strong>Untuk Pembayaran:</strong></td>
            <td>{{ $payment->bill?->paymentType?->name }} (No. Tagihan: {{ $payment->bill?->bill_number }})</td>
        </tr>
        <tr>
            <td><strong>Metode Pembayaran:</strong></td>
            <td>{{ strtoupper($payment->method ?? $payment->source) }} ({{ \App\Models\Payment::SOURCES[$payment->source] ?? $payment->source }})</td>
        </tr>
        @if($payment->notes)
        <tr>
            <td><strong>Catatan:</strong></td>
            <td>{{ $payment->notes }}</td>
        </tr>
        @endif
    </table>

    <div style="margin-top: 15px;">
        <span style="font-size: 12px; color: #555;">Jumlah Pembayaran:</span><br>
        <div class="amount-banner">
            Rp {{ number_format($payment->amount, 0, ',', '.') }}
        </div>
    </div>
</div>

<table width="100%">
    <tr>
        <td width="60%" style="font-size: 11px; color: #666; vertical-align: top;">
            <strong>Status Tagihan Setelah Pembayaran:</strong><br>
            Total Tagihan: Rp {{ number_format($payment->bill?->amount, 0, ',', '.') }}<br>
            Total Dibayar: Rp {{ number_format($payment->bill?->paid_amount, 0, ',', '.') }}<br>
            Sisa Tagihan: <strong>Rp {{ number_format($payment->bill?->outstanding_amount, 0, ',', '.') }}</strong>
        </td>
        <td width="40%" class="text-right" style="vertical-align: top;">
            <div style="font-size: 11px; text-align: center;">
                Bendahara / Kasir,<br>
                @php
                    $sigPath = public_path('images/signature.png');
                    $sigBase64 = file_exists($sigPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($sigPath)) : null;
                @endphp
                @if($sigBase64)
                    <img src="{{ $sigBase64 }}" style="height: 60px; margin: 2px 0;" alt="Tanda Tangan"><br>
                @else
                    <br><br><br><br>
                @endif
                <strong>{{ $payment->recorder?->name ?? 'Hj. Titi Nurhayati, S. Pd' }}</strong>
            </div>
        </td>
    </tr>
</table>



</body>
</html>
