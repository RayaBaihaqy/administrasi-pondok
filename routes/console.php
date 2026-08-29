<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Scheduled Commands ─────────────────────────────────────
// Generate tagihan SPP otomatis setiap tanggal 1 bulan
Schedule::command('spp:generate')->monthlyOn(1, '06:00');

// Deteksi tagihan yang melewati jatuh tempo setiap hari
Schedule::command('bills:detect-overdue')->dailyAt('00:05');

// Kirim notifikasi pengingat WhatsApp H-2 jatuh tempo setiap hari pukul 08:00
Schedule::command('bills:send-reminders')->dailyAt('08:00');
