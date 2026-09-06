<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>INVOICE - {{ $bill->bill_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
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
        .status-unpaid { background-color: #fffde7; color: #f57f17; border: 1px solid #fff59d; }
        .status-paid { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
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
            font-weight: 500;
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

        /* Payment Guide Box */
        .payment-guide-box {
            background-color: #f9fbf9;
            border: 1px solid #c8e6c9;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 11px;
        }
        .payment-guide-title {
            font-weight: bold;
            color: #1b5e20;
            margin-bottom: 5px;
        }
        .bank-details-card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
            padding: 6px 8px;
            margin-top: 4px;
            margin-bottom: 6px;
            font-size: 10.5px;
        }

        /* Amount Calculation Box */
        .calculation-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .calculation-table td {
            padding: 4px 0;
            font-size: 11.5px;
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
            margin-top: 15px;
            border-collapse: collapse;
        }
        .signature-cell {
            text-align: center;
            font-size: 11px;
            vertical-align: bottom;
        }

        /* Footer */
        .doc-footer {
            margin-top: 25px;
            font-size: 10px;
            color: #888888;
            text-align: center;
            border-top: 1px solid #eeeeee;
            padding-top: 8px;
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
                <div class="kop-school">{{ config('school.institution', "MTs. MIFTAHUL 'ULUM CIBITUNG") }}</div>
                <div class="kop-address">{{ config('school.address', 'Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung') }}</div>
            </td>
            <td class="kop-meta">
                <div class="doc-type-title">INVOICE TAGIHAN</div>
                <div class="doc-date">Tanggal: {{ \Carbon\Carbon::parse($bill->billing_date)->translatedFormat('d F Y') }}</div>
                <div>
                    <span class="status-badge status-{{ $bill->status }}">
                        {{ match($bill->status) {
                            'paid' => 'LUNAS',
                            'overdue' => 'TERLAMBAT',
                            'cancelled' => 'DIBATALKAN',
                            default => 'BELUM LUNAS'
                        } }}
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
                <div class="info-box-title">DITAGIHKAN KEPADA:</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Wali Siswa</td>
                        <td>:</td>
                        <td class="info-value"><strong>{{ $bill->parentProfile?->full_name ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">Nama Siswa</td>
                        <td>:</td>
                        <td class="info-value">{{ $bill->student?->full_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">NISN / NIS</td>
                        <td>:</td>
                        <td class="info-value">{{ $bill->student?->nis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Kelas / Rombel</td>
                        <td>:</td>
                        <td class="info-value">Kelas {{ $bill->student?->class_rombel ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </td>
        <td class="info-col" style="padding-left: 8px;">
            <div class="info-box">
                <div class="info-box-title">RINCIAN TAGIHAN:</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">No. Invoice</td>
                        <td>:</td>
                        <td class="info-value">{{ $bill->bill_number }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Jenis Tagihan</td>
                        <td>:</td>
                        <td class="info-value">{{ $bill->paymentType?->name ?? 'Administrasi' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tahun Ajaran</td>
                        <td>:</td>
                        <td class="info-value">{{ $bill->academicYear?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Jatuh Tempo</td>
                        <td>:</td>
                        <td class="info-value"><strong style="color: #c62828;">{{ \Carbon\Carbon::parse($bill->due_date)->translatedFormat('d F Y') }}</strong></td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

<!-- Rincian Item Tagihan -->
<table class="items-table">
    <thead>
        <tr>
            <th width="50%">Deskripsi Tagihan</th>
            <th width="12%" class="text-center">Qty</th>
            <th width="18%" class="text-right">Harga Satuan</th>
            <th width="20%" class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bill->billItems as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td class="text-center">{{ $item->quantity }}</td>
            <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
            <td class="text-right"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
        </tr>
        @empty
        <tr>
            <td>{{ $bill->paymentType?->name ?? 'Tagihan Pendidikan' }}</td>
            <td class="text-center">1</td>
            <td class="text-right">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
            <td class="text-right"><strong>Rp {{ number_format($bill->amount, 0, ',', '.') }}</strong></td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- Bagian Bawah: Petunjuk Pembayaran & Ringkasan / Tanda Tangan -->
<table class="bottom-table">
    <tr>
        <td class="bottom-col-left">
            <div class="payment-guide-box">
                <div class="payment-guide-title">💳 Petunjuk Pembayaran Transfer Bank:</div>
                <div class="bank-details-card">
                    <div>• <strong>Bank:</strong> {{ config('school.bank_name', 'Bank BRI') }}</div>
                    <div>• <strong>No. Rekening:</strong> <span style="font-family: monospace; font-size: 11.5px; font-weight: bold; color: #1b5e20;">{{ config('school.bank_account') }}</span></div>
                    <div>• <strong>Atas Nama:</strong> {{ config('school.bank_holder', 'Madrasah Tsanawiyah Miftahul Ulum') }}</div>
                </div>
                <div style="font-size: 10px; color: #555; line-height: 1.4;">
                    1. Pembayaran dapat melalui transfer m-Banking / ATM atau Portal Online.<br>
                    2. Cantumkan berita transfer: <strong>{{ $bill->bill_number }}</strong>.<br>
                    3. Harap menyelesaikan pembayaran sebelum batas jatuh tempo.
                </div>
            </div>
        </td>
        <td class="bottom-col-right">
            <table class="calculation-table">
                <tr>
                    <td class="text-right" style="color: #666;">Total Tagihan:</td>
                    <td class="text-right" width="45%"><strong>Rp {{ number_format($bill->amount, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td class="text-right" style="color: #666;">Telah Dibayar:</td>
                    <td class="text-right" style="color: #2e7d32;">Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</td>
                </tr>
            </table>

            <div class="highlight-total-box">
                <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Sisa Tagihan yang Harus Dibayar:</div>
                <div style="font-size: 15px; font-weight: bold; margin-top: 2px;">
                    Rp {{ number_format($bill->outstanding_amount, 0, ',', '.') }}
                </div>
            </div>

            <!-- Tanda Tangan -->
            <table class="signature-table">
                <tr>
                    <td class="signature-cell">
                        <div style="color: #666; font-size: 10px;">Cibitung, {{ \Carbon\Carbon::parse($bill->billing_date)->translatedFormat('d F Y') }}</div>
                        <div style="font-weight: bold; margin-top: 2px;">Bendahara MTs Miftahul 'Ulum,</div>
                        @php
                            $sigPath = public_path('images/signature.png');
                            $sigBase64 = file_exists($sigPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($sigPath)) : null;
                        @endphp
                        @if($sigBase64)
                            <img src="{{ $sigBase64 }}" style="height: 48px; margin: 2px 0;" alt="Tanda Tangan"><br>
                        @else
                            <div style="height: 48px;"></div>
                        @endif
                        <strong>{{ config('school.treasurer_name', 'Hj. Titi Nurhayati, S. Pd') }}</strong>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Footer Sistem -->
<div class="doc-footer">
    Dokumen ini diterbitkan secara otomatis dan sah melalui Sistem Informasi Administrasi MTs Miftahul 'Ulum Cibitung.
</div>

</body>
</html>
