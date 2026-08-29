<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ParentOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $parentId = Auth::user()?->parentProfile?->id;

        if (! $parentId) {
            return [];
        }

        // 1. Total Tunggakan Belum Lunas
        $totalUnpaid = (int) Bill::where('parent_id', $parentId)
            ->whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])
            ->sum('outstanding_amount');

        // 2. Jumlah Anak Terdaftar
        $childrenCount = Student::where('parent_id', $parentId)->count();

        return [
            Stat::make('Total Tunggakan Tagihan', 'Rp '.number_format($totalUnpaid, 0, ',', '.'))
                ->description($totalUnpaid > 0 ? 'Tagihan yang belum diselesaikan' : 'Semua tagihan lunas')
                ->descriptionIcon($totalUnpaid > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($totalUnpaid > 0 ? 'danger' : 'success'),

            Stat::make('Anak Terdaftar', $childrenCount.' Siswa')
                ->description('Putra/Putri aktif di MTs Miftahul \'Ulum')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }
}
