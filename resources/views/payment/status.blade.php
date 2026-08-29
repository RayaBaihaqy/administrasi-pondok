<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran - Yayasan Perguruan Islam Miftahul 'Ulum</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f6f9;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            max-width: 480px;
            width: 100%;
            padding: 40px 30px;
            text-align: center;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        .success { background: #e8f5e9; color: #2e7d32; }
        .pending { background: #fff3e0; color: #ef6c00; }
        .failed { background: #ffebee; color: #c62828; }

        h1 { font-size: 22px; margin-bottom: 10px; color: #1a202c; }
        p { font-size: 14px; color: #718096; line-height: 1.6; margin-bottom: 30px; }
        
        .btn {
            display: inline-block;
            background-color: #2e7d32;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        .btn:hover { background-color: #1b5e20; }
    </style>
</head>
<body>
    <div class="card">
        @if(($status ?? 'pending') === 'success')
            <div class="icon-box success">✓</div>
            <h1>Pembayaran Berhasil!</h1>
            <p>Terima kasih. Pembayaran Anda telah kami terima dan tercatat di sistem administrasi pondok.</p>
        @elseif(($status ?? 'pending') === 'pending')
            <div class="icon-box pending">⏳</div>
            <h1>Menunggu Pembayaran</h1>
            <p>Transaksi Anda sedang diproses. Silakan selesaikan pembayaran sesuai petunjuk yang diberikan.</p>
        @else
            <div class="icon-box failed">✕</div>
            <h1>Pembayaran Gagal</h1>
            <p>Mohon maaf, proses pembayaran tidak dapat diselesaikan atau telah dibatalkan. Silakan coba kembali.</p>
        @endif

        <a href="/parent" class="btn">Kembali ke Portal Wali Siswa</a>
    </div>
</body>
</html>
