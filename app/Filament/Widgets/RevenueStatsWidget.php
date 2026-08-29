<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class RevenueStatsWidget extends BaseWidget
{
    public ?string $filter_type = 'period';

    public ?string $period = 'this_month';

    public ?string $from_date = null;

    public ?string $until_date = null;

    #[On('updateRevenueFilter')]
    public function updateRevenueFilter(?string $filter_type = 'period', ?string $period = 'this_month', ?string $from_date = null, ?string $until_date = null): void
    {
        $this->filter_type = $filter_type;
        $this->period = $period;
        $this->from_date = $from_date;
        $this->until_date = $until_date;
    }

    protected function getStats(): array
    {
        $query = Payment::query()
            ->where('status', Payment::STATUS_SUCCESS)
            ->whereNull('deleted_at');

        if ($this->filter_type === 'custom_date' || ($this->from_date || $this->until_date)) {
            if ($this->from_date) {
                $query->whereDate('paid_at', '>=', $this->from_date);
            }
            if ($this->until_date) {
                $query->whereDate('paid_at', '<=', $this->until_date);
            }
        } else {
            match ($this->period) {
                'this_month' => $query->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year),
                'last_month' => $query->whereMonth('paid_at', now()->subMonth()->month)->whereYear('paid_at', now()->subMonth()->year),
                '3_months' => $query->where('paid_at', '>=', now()->subMonths(3)->startOfDay()),
                '6_months' => $query->where('paid_at', '>=', now()->subMonths(6)->startOfDay()),
                'this_year' => $query->whereYear('paid_at', now()->year),
                default => $query->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year),
            };
        }

        $totalCount = (clone $query)->count();
        $totalAmount = (clone $query)->sum('amount');
        $midtransAmount = (clone $query)->where('source', Payment::SOURCE_MIDTRANS)->sum('amount');
        $manualAmount = (clone $query)->where('source', Payment::SOURCE_MANUAL)->sum('amount');

        return [
            Stat::make('Total Pemasukan (Lunas)', 'Rp '.number_format($totalAmount, 0, ',', '.'))
                ->description($totalCount.' Transaksi Lunas')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Pemasukan Midtrans Online', 'Rp '.number_format($midtransAmount, 0, ',', '.'))
                ->description('Payment Gateway Online')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),

            Stat::make('Pemasukan Manual / Offline', 'Rp '.number_format($manualAmount, 0, ',', '.'))
                ->description('Kasir / Transfer Manual')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}
