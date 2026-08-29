<?php

namespace App\Filament\Widgets;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalSiswa = Student::active()->count();

        $totalTagihan = Bill::whereMonth('billing_period', now()->month)
            ->whereYear('billing_period', now()->year)
            ->sum('amount');

        $sudahDibayar = Payment::where('status', Payment::STATUS_SUCCESS)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $belumDibayar = Bill::whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])
            ->sum('outstanding_amount');

        return [
            Stat::make('Total Siswa Aktif', number_format($totalSiswa, 0, ',', '.'))
                ->description('Siswa aktif terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Total Tagihan', 'Rp '.number_format($totalTagihan, 0, ',', '.'))
                ->description('Tagihan bulan ini')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Sudah Dibayar', 'Rp '.number_format($sudahDibayar, 0, ',', '.'))
                ->description('Pembayaran bulan ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Tunggakan (Belum Lunas)', 'Rp '.number_format($belumDibayar, 0, ',', '.'))
                ->description('Total sisa tunggakan siswa')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),
        ];
    }
}
