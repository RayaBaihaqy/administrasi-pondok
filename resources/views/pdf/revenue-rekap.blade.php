<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Pemasukan Pondok</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #1b5e20;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 12px;
            color: #555555;
        }
        .period-badge {
            display: inline-block;
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 11px;
            margin-top: 6px;
        }
        .stats-grid {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .stat-card {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }
        .stat-title {
            font-size: 10px;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #2e7d32;
            margin-top: 4px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1b5e20;
            margin-top: 20px;
            margin-bottom: 8px;
            border-left: 4px solid #2e7d32;
            padding-left: 8px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #dddddd;
            padding: 8px 10px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f1f8e9;
            color: #1b5e20;
            font-size: 11px;
            text-transform: uppercase;
        }
        table.data-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer-sign {
            margin-top: 40px;
            width: 100%;
        }
        .footer-sign td {
            border: none;
            padding: 0;
        }
    </style>
</head>
<body>

    <div class="header" style="text-align: center;">
        <img src="{{ public_path('images/logo.png') }}" style="height: 65px; width: auto; margin-bottom: 8px;">
        <h1 style="margin: 0; font-size: 20px;">Yayasan Perguruan Islam Miftahul 'Ulum</h1>
        <p style="margin: 3px 0 0 0; font-weight: bold; color: #2e7d32;">REKAPITULASI LAPORAN PEMASUKAN KEUANGAN - MTs. MIFTAHUL 'ULUM</p>
        <div style="font-size: 11px; color: #666; margin-top: 3px;">Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung</div>
        <div class="period-badge" style="margin-top: 5px;">Periode: {{ $periodLabel }}</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td width="33%" style="padding-right: 5px;">
                <div class="stat-card">
                    <div class="stat-title">Total Pemasukan</div>
                    <div class="stat-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="33%" style="padding: 0 2.5px;">
                <div class="stat-card">
                    <div class="stat-title">Pemasukan Midtrans Online</div>
                    <div class="stat-value" style="color: #0288d1;">Rp {{ number_format($midtransAmount, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="33%" style="padding-left: 5px;">
                <div class="stat-card">
                    <div class="stat-title">Pemasukan Manual (Kasir/Transfer)</div>
                    <div class="stat-value" style="color: #ed6c02;">Rp {{ number_format($manualAmount, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">1. Pemasukan per Jenis Pembayaran</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Jenis Pembayaran</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total Pemasukan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byType as $item)
                <tr>
                    <td>{{ $item->type_name }}</td>
                    <td class="text-center">{{ $item->total_count }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="color: #888;">Tidak ada data pemasukan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. Pemasukan per Metode Pembayaran</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Metode Pembayaran</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total Pemasukan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byMethod as $item)
                <tr>
                    <td>{{ strtoupper($item->method_key ?? 'N/A') }}</td>
                    <td class="text-center">{{ $item->total_count }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="color: #888;">Tidak ada data pemasukan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">3. Rincian Transaksi Pembayaran Lunas</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Waktu Bayar</th>
                <th>Siswa</th>
                <th>Jenis Tagihan</th>
                <th>Metode</th>
                <th class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $payment->student?->full_name ?? '-' }}</td>
                    <td>{{ $payment->bill?->paymentType?->name ?? '-' }}</td>
                    <td>{{ strtoupper($payment->method ?? $payment->source) }}</td>
                    <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #888;">Tidak ada rincian transaksi lunas pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sign">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="text-center">
                <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
                <p><strong>Bendahara / Pengelola Keuangan</strong></p>
                <br><br><br>
                <p>___________________________</p>
            </td>
        </tr>
    </table>

</body>
</html>
