<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $bill->bill_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.5; }
        .header { border-bottom: 2px solid #2e7d32; padding-bottom: 15px; margin-bottom: 20px; }
        .header table { width: 100%; }
        .title { font-size: 24px; font-weight: bold; color: #2e7d32; text-transform: uppercase; }
        .subtitle { font-size: 14px; color: #666; }
        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 5px; vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .items-table th { background-color: #f2f4f6; color: #333; text-align: left; padding: 10px; border-bottom: 2px solid #ddd; }
        .items-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .total-box { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 10px; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 3px; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        .status-unpaid { background-color: #ffebee; color: #c62828; }
        .status-paid { background-color: #e8f5e9; color: #2e7d32; }
        .status-overdue { background-color: #fff3e0; color: #ef6c00; }
        .footer { margin-top: 40px; font-size: 11px; color: #888; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
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
                <div class="subtitle">INVOICE TAGIHAN PEMBAYARAN - MTs. MIFTAHUL 'ULUM</div>
                <div style="font-size: 11px; color: #666;">Jl. Raya Setu Kp. Cibuntu RT. 002/007 Desa Cibuntu Kec. Cibitung</div>
            </td>
            <td class="text-right">
                <div style="font-size: 14px; font-weight: bold; color: #2e7d32; word-break: break-all;">{{ $bill->bill_number }}</div>
                <div style="font-size: 11px; color: #666; margin-top: 3px;">Tanggal: {{ \Carbon\Carbon::parse($bill->billing_date)->translatedFormat('d F Y') }}</div>
                <div style="margin-top: 5px;">
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

<table class="info-table">
    <tr>
        <td width="55%">
            <strong>DITAGIHKAN KEPADA:</strong><br>
            <strong>Wali Siswa:</strong> {{ $bill->parentProfile?->full_name ?? '-' }}<br>
            <strong>Nama Siswa:</strong> {{ $bill->student?->full_name ?? '-' }} (NISN: {{ $bill->student?->nis }})<br>
            <strong>Kelas / Rombel:</strong> {{ $bill->student?->class_rombel }}<br>
        </td>
        <td width="45%">
            <strong>RINCIAN TAGIHAN:</strong><br>
            <strong>No. Tagihan:</strong> {{ $bill->bill_number }}<br>
            <strong>Tanggal Diterbitkan:</strong> {{ \Carbon\Carbon::parse($bill->billing_date)->translatedFormat('d F Y') }}<br>
            <strong>Jatuh Tempo:</strong> <span style="color: #c62828; font-weight: bold;">{{ \Carbon\Carbon::parse($bill->due_date)->translatedFormat('d F Y') }}</span><br>
            <strong>Tahun Ajaran:</strong> {{ $bill->academicYear?->name }}<br>
        </td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th>Deskripsi Tagihan</th>
            <th class="text-right">Jumlah</th>
            <th class="text-right">Harga Satuan</th>
            <th class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bill->billItems as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td class="text-right">{{ $item->quantity }}</td>
            <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table width="100%">
    <tr>
        <td width="50%" style="vertical-align: top;">
            <div style="background-color: #f5f5f5; padding: 12px; border-radius: 4px; font-size: 11px;">
                <strong>Petunjuk Pembayaran:</strong>
                <ol style="margin-left: 15px; margin-top: 5px; padding-left: 0;">
                    <li>Pembayaran dapat dilakukan melalui portal online (Midtrans).</li>
                    <li>Atau secara tunai / transfer via kantor bendahara pondok.</li>
                    <li>Harap melakukan pembayaran sebelum tanggal jatuh tempo.</li>
                </ol>
            </div>
        </td>
        <td width="50%" class="text-right" style="vertical-align: top;">
            <table width="100%">
                <tr>
                    <td class="text-right"><strong>Total Tagihan:</strong></td>
                    <td class="text-right" width="40%">Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-right"><strong>Telah Dibayar:</strong></td>
                    <td class="text-right" style="color: #2e7d32;">Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</td>
                </tr>
                <tr style="font-size: 16px; font-weight: bold;">
                    <td class="text-right" style="padding-top: 10px;">Sisa Tagihan:</td>
                    <td class="text-right" style="padding-top: 10px; color: #c62828;">Rp {{ number_format($bill->outstanding_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>



</body>
</html>
