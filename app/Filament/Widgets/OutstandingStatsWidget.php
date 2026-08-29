<?php

namespace App\Filament\Widgets;

use App\Models\Bill;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class OutstandingStatsWidget extends BaseWidget
{
    public ?string $from_date = null;

    public ?string $until_date = null;

    #[On('updateOutstandingFilter')]
    public function updateOutstandingFilter(?string $from_date = null, ?string $until_date = null): void
    {
        $this->from_date = $from_date;
        $this->until_date = $until_date;
    }

    protected function getStats(): array
    {
        $query = Bill::query()
            ->whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])
            ->whereNull('deleted_at');

        if ($this->from_date) {
            $query->whereDate('due_date', '>=', $this->from_date);
        }
        if ($this->until_date) {
            $query->whereDate('due_date', '<=', $this->until_date);
        }

        $totalCount = (clone $query)->count();
        $totalAmount = (clone $query)->sum('outstanding_amount');

        $cutoffDate = $this->until_date ? $this->until_date : now()->format('Y-m-d');

        $overdueQuery = Bill::query()
            ->whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])
            ->whereDate('due_date', '<', $cutoffDate)
            ->whereNull('deleted_at');

        if ($this->from_date) {
            $overdueQuery->whereDate('due_date', '>=', $this->from_date);
        }

        $overdueCount = (clone $overdueQuery)->count();
        $overdueAmount = (clone $overdueQuery)->sum('outstanding_amount');

        $prioritasLabel = 'Monitoring Active';
        if ($this->from_date && $this->until_date) {
            $prioritasLabel = Carbon::parse($this->from_date)->format('d/m/Y').' - '.Carbon::parse($this->until_date)->format('d/m/Y');
        } elseif ($this->until_date) {
            $prioritasLabel = 's/d '.Carbon::parse($this->until_date)->format('d M Y');
        } elseif ($this->from_date) {
            $prioritasLabel = 'Mulai '.Carbon::parse($this->from_date)->format('d M Y');
        }

        return [
            Stat::make('Total Tunggakan Aktif', 'Rp '.number_format($totalAmount, 0, ',', '.'))
                ->description($totalCount.' Tagihan Belum Lunas')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Tunggakan Terlambat (Overdue)', 'Rp '.number_format($overdueAmount, 0, ',', '.'))
                ->description($overdueCount.' Tagihan Melewati Jatuh Tempo')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Periode Jatuh Tempo', $prioritasLabel)
                ->description('Filter Tanggal Berlangsung')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
        ];
    }
}
