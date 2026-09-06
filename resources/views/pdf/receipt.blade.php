<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KUITANSI - {{ $payment->payment_number ?? $receiptNumber }}</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Helvetica', 'Arial', sans-serif !important;
        }
        body, table, tr, td, th, div, span, p, strong, b, h1, h2, h3, h4, em {
            font-family: 'Helvetica', 'Arial', sans-serif !important;
        }
        body {
            font-size: 12px;
            color: #2d3748;
            line-height: 1.45;
            margin: 0;
            padding: 10px 15px;
        }

        /* Kop Surat Resmi */
        .kop-wrapper {
            border-bottom: 3px double #1b5e20;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kop-logo {
            width: 70px;
            vertical-align: middle;
            text-align: left;
        }
        .kop-logo img {
            height: 60px;
            width: auto;
        }
        .kop-institution {
            vertical-align: middle;
            padding-left: 10px;
        }
        .kop-yayasan {
            font-size: 15px;
            font-weight: bold;
            color: #1b5e20;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }
        .kop-school {
            font-size: 13px;
            font-weight: bold;
            color: #2e7d32;
            margin-top: 2px;
            text-transform: uppercase;
        }
        .kop-address {
            font-size: 10px;
            color: #555555;
            margin-top: 3px;
            line-height: 1.3;
        }
        .kop-meta {
            vertical-align: middle;
            text-align: right;
            width: 210px;
        }
        .doc-type-title {
            font-size: 14px;
            font-weight: bold;
            color: #1b5e20;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .doc-number {
            font-size: 12px;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
            color: #1b5e20;
            background: #f1f8e9;
            padding: 2px 6px;
            border-radius: 3px;
            border: 1px solid #c8e6c9;
            display: inline-block;
        }
        .doc-date {
            font-size: 10.5px;
            color: #666666;
            margin-top: 3px;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .status-paid { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .status-unpaid { background-color: #fffde7; color: #f57f17; border: 1px solid #fff59d; }
        .status-overdue { background-color: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
        .status-cancelled { background-color: #f5f5f5; color: #757575; border: 1px solid #e0e0e0; }

        /* Two-Column Info Cards */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .info-col {
            width: 50%;
            vertical-align: top;
        }
        .info-box {
            background-color: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 11px;
        }
        .info-box-title {
            font-size: 11px;
            font-weight: bold;
            color: #1b5e20;
            text-transform: uppercase;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .info-label {
            color: #666666;
            width: 38%;
        }
        .info-value {
            color: #2d3748;
            font-size: 11px;
        }

        /* Items / Data Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .items-table th {
            background-color: #e8f5e9;
            color: #1b5e20;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            text-transform: uppercase;
            border-top: 1px solid #c8e6c9;
            border-bottom: 2px solid #2e7d32;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eeeeee;
            font-size: 11.5px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Summary & Action Section */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .bottom-col-left {
            width: 55%;
            vertical-align: top;
            padding-right: 15px;
        }
        .bottom-col-right {
            width: 45%;
            vertical-align: top;
        }

        /* Bill Status Overview Box */
        .bill-status-box {
            background-color: #f9fbf9;
            border: 1px solid #c8e6c9;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 11px;
        }
        .bill-status-title {
            font-weight: bold;
            color: #1b5e20;
            margin-bottom: 5px;
        }

        /* Amount Calculation Box */
        .calculation-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .calculation-table td {
            padding: 3px 0;
            font-size: 11px;
        }
        .highlight-total-box {
            background-color: #1b5e20;
            color: #ffffff;
            padding: 8px 12px;
            border-radius: 4px;
            text-align: right;
            margin-top: 6px;
        }

        /* Signature Section */
        .signature-table {
            width: 100%;
            margin-top: 28px;
            border-collapse: collapse;
        }
        .signature-cell {
            text-align: center;
            font-size: 11px;
            vertical-align: bottom;
        }
    </style>
</head>
<body>

<!-- Kop Surat Resmi -->
<div class="kop-wrapper">
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo MTs">
            </td>
            <td class="kop-institution">
                <div class="kop-yayasan">{{ config('school.name', "YAYASAN PERGURUAN ISLAM MIFTAHUL 'ULUM") }}</div>
                <div class="kop-school">{{ config('school.institution', "MTs. MIFTAHUL 'ULUM") }}</div>
                <div class="kop-address">{{ config('school.address', 'Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung') }}</div>
            </td>
            <td class="kop-meta">
                <div class="doc-type-title">KUITANSI PEMBAYARAN</div>
                <div>
                    <span class="status-badge status-paid">
                        LUNAS
                    </span>
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- Informasi Siswa & Dokumen -->
<table class="info-grid">
    <tr>
        <td class="info-col" style="padding-right: 8px;">
            <div class="info-box">
                <div class="info-box-title">TELAH DITERIMA DARI:</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Wali Siswa</td>
                        <td>:</td>
                        <td class="info-value">{{ $payment->parentProfile?->full_name ?? $payment->student?->parentProfile?->full_name ?? 'Wali Siswa' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Nama Siswa</td>
                        <td>:</td>
                        <td class="info-value">{{ $payment->student?->full_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">NISN / NIS</td>
                        <td>:</td>
                        <td class="info-value">{{ $payment->student?->nis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Kelas / Rombel</td>
                        <td>:</td>
                        <td class="info-value">Kelas {{ $payment->student?->class_rombel ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </td>
        <td class="info-col" style="padding-left: 8px;">
            <div class="info-box">
                <div class="info-box-title">RINCIAN TRANSAKSI:</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">No. Kuitansi</td>
                        <td>:</td>
                        <td class="info-value">{{ $payment->payment_number ?? $receiptNumber }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">No. Tagihan</td>
                        <td>:</td>
                        <td class="info-value">{{ $payment->bill?->bill_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Metode Bayar</td>
                        <td>:</td>
                        <td class="info-value">{{ strtoupper($payment->method ?? $payment->source ?? 'TUNAI') }} ({{ \App\Models\Payment::SOURCES[$payment->source] ?? 'Loket Kasir' }})</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal Bayar</td>
                        <td>:</td>
                        <td class="info-value">{{ \Carbon\Carbon::parse($payment->paid_at ?? now())->translatedFormat('d F Y H:i:s') }} WIB</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

<!-- Rincian Alokasi Pembayaran -->
<table class="items-table">
    <thead>
        <tr>
            <th width="50%">Deskripsi Pembayaran</th>
            <th width="15%" class="text-center">Tahun Ajaran</th>
            <th width="15%" class="text-center">Status</th>
            <th width="20%" class="text-right">Nominal Diterima</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <strong>{{ $payment->bill?->paymentType?->name ?? 'Pembayaran Administrasi' }}</strong>
                @if($payment->notes)
                    <div style="font-size: 10px; color: #666; margin-top: 2px;">Catatan: {{ $payment->notes }}</div>
                @endif
            </td>
            <td class="text-center">{{ $payment->bill?->academicYear?->name ?? '-' }}</td>
            <td class="text-center">
                <span class="status-badge status-paid" style="font-size: 9px;">BERHASIL</span>
            </td>
            <td class="text-right"><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
        </tr>
    </tbody>
</table>

<!-- Bagian Bawah: Status Tagihan & Total / Tanda Tangan -->
<table class="bottom-table">
    <tr>
        <td class="bottom-col-left">
            <div class="bill-status-box">
                <div class="bill-status-title">Status Tagihan Terkait:</div>
                @php
                    $totalBillAmount = (int) ($payment->bill?->amount ?? $payment->amount);
                    if ($payment->bill) {
                        $accumulatedPaid = (int) $payment->bill->payments()
                            ->where('status', \App\Models\Payment::STATUS_SUCCESS)
                            ->where(function($q) use ($payment) {
                                $q->where('paid_at', '<', $payment->paid_at)
                                  ->orWhere(function($sub) use ($payment) {
                                      $sub->where('paid_at', '=', $payment->paid_at)
                                          ->where('id', '<=', $payment->id);
                                  });
                            })
                            ->sum('amount');
                        if ($accumulatedPaid == 0) {
                            $accumulatedPaid = (int) $payment->amount;
                        }
                    } else {
                        $accumulatedPaid = (int) $payment->amount;
                    }
                    $remainingOutstanding = max(0, $totalBillAmount - $accumulatedPaid);
                @endphp
                <table class="calculation-table">
                    <tr>
                        <td width="55%" style="color: #666;">Total Tagihan:</td>
                        <td width="45%" class="text-right">Rp {{ number_format($totalBillAmount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="color: #666;">Akumulasi Pembayaran:</td>
                        <td class="text-right" style="color: #2e7d32; font-weight: bold;">Rp {{ number_format($accumulatedPaid, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="color: #666;">Sisa Tagihan:</td>
                        <td class="text-right" style="color: {{ $remainingOutstanding > 0 ? '#c62828' : '#2e7d32' }}; font-weight: bold;">Rp {{ number_format($remainingOutstanding, 0, ',', '.') }}</td>
                    </tr>
                </table>
                <div style="font-size: 10px; color: #555; border-top: 1px dashed #c8e6c9; padding-top: 4px; margin-top: 4px;">
                    Kuitansi ini merupakan bukti pelunasan/pembayaran yang sah dan diakui madrasah.
                </div>
            </div>
        </td>
        <td class="bottom-col-right">
            <div class="highlight-total-box">
                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Jumlah yang Telah Dibayarkan:</div>
                <div style="font-size: 16px; font-weight: bold; margin-top: 2px;">
                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                </div>
            </div>

            <!-- Tanda Tangan -->
            <table class="signature-table">
                <tr>
                    <td class="signature-cell">
                        <div style="color: #666; font-size: 10.5px;">Cibitung, {{ \Carbon\Carbon::parse($payment->paid_at ?? now())->translatedFormat('d F Y') }}</div>
                        <div style="font-weight: bold; margin-top: 3px;">Bendahara MTs Miftahul 'Ulum,</div>
                        @php
                            $sigPath = public_path('images/signature.png');
                            $sigBase64 = file_exists($sigPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($sigPath)) : null;
                        @endphp
                        @if($sigBase64)
                            <img src="{{ $sigBase64 }}" style="height: 62px; margin: 4px 0;" alt="Tanda Tangan"><br>
                        @else
                            <div style="height: 62px;"></div>
                        @endif
                        <strong>{{ $payment->recorder?->name ?? config('school.treasurer_name', 'Hj. Titi Nurhayati, S. Pd') }}</strong>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
