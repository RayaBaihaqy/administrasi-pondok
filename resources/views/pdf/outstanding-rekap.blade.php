<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Tunggakan Tagihan</title>
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
            border-bottom: 2px solid #d32f2f;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #b71c1c;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 12px;
            color: #555555;
        }
        .period-badge {
            display: inline-block;
            background-color: #ffebee;
            color: #c62828;
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
            color: #c62828;
            margin-top: 4px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #b71c1c;
            margin-top: 20px;
            margin-bottom: 8px;
            border-left: 4px solid #d32f2f;
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
            background-color: #ffebee;
            color: #b71c1c;
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
        <p style="margin: 3px 0 0 0; font-weight: bold; color: #2e7d32;">REKAPITULASI LAPORAN TUNGGAKAN TAGIHAN SISWA - MTs. MIFTAHUL 'ULUM</p>
        <div style="font-size: 11px; color: #666; margin-top: 3px;">Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung</div>
        <div class="period-badge" style="margin-top: 5px;">Periode: {{ $periodLabel }}</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td width="50%" style="padding-right: 5px;">
                <div class="stat-card">
                    <div class="stat-title">Total Sisa Tunggakan Aktif</div>
                    <div class="stat-value">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="50%" style="padding-left: 5px;">
                <div class="stat-card">
                    <div class="stat-title">Tunggakan Terlambat (Overdue)</div>
                    <div class="stat-value" style="color: #b71c1c;">Rp {{ number_format($overdueOutstanding, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">1. Daftar Siswa Belum Lunas</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Tagihan</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Jenis Tagihan</th>
                <th>Jatuh Tempo</th>
                <th class="text-right">Sisa Tunggakan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($unpaidBills as $bill)
                <tr>
                    <td>{{ $bill->bill_number }}</td>
                    <td>{{ $bill->student?->full_name ?? '-' }}</td>
                    <td>{{ $bill->student?->class_rombel ?? '-' }}</td>
                    <td>{{ $bill->paymentType?->name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</td>
                    <td class="text-right">Rp {{ number_format($bill->outstanding_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #888;">Tidak ada tunggakan belum lunas pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. Daftar Siswa Terlambat (Melewati Jatuh Tempo)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Tagihan</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Jenis Tagihan</th>
                <th>Jatuh Tempo</th>
                <th class="text-right">Sisa Tunggakan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($overdueBills as $bill)
                <tr>
                    <td>{{ $bill->bill_number }}</td>
                    <td>{{ $bill->student?->full_name ?? '-' }}</td>
                    <td>{{ $bill->student?->class_rombel ?? '-' }}</td>
                    <td>{{ $bill->paymentType?->name ?? '-' }}</td>
                    <td style="color: #c62828; font-weight: bold;">{{ \Carbon\Carbon::parse($bill->due_date)->format('d/m/Y') }}</td>
                    <td class="text-right" style="color: #c62828; font-weight: bold;">Rp {{ number_format($bill->outstanding_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #888;">Tidak ada tagihan terlambat pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sign">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="text-center">
                <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
                <p><strong>Pengelola Administrasi Pondok</strong></p>
                <br><br><br>
                <p>___________________________</p>
            </td>
        </tr>
    </table>

</body>
</html>
