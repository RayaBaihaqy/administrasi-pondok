<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class RevenueByMethodWidget extends BaseWidget
{
    protected static ?string $heading = 'Pemasukan per Metode Pembayaran';

    protected int|string|array $columnSpan = 1;

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
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        $subQuery = DB::table('payments')
            ->where('payments.status', Payment::STATUS_SUCCESS)
            ->whereNull('payments.deleted_at');

        if ($this->filter_type === 'custom_date' || ($this->from_date || $this->until_date)) {
            if ($this->from_date) {
                $subQuery->whereDate('payments.paid_at', '>=', $this->from_date);
            }
            if ($this->until_date) {
                $subQuery->whereDate('payments.paid_at', '<=', $this->until_date);
            }
        } else {
            match ($this->period) {
                'this_month' => $subQuery->whereMonth('payments.paid_at', now()->month)->whereYear('payments.paid_at', now()->year),
                'last_month' => $subQuery->whereMonth('payments.paid_at', now()->subMonth()->month)->whereYear('payments.paid_at', now()->subMonth()->year),
                '3_months' => $subQuery->where('payments.paid_at', '>=', now()->subMonths(3)->startOfDay()),
                '6_months' => $subQuery->where('payments.paid_at', '>=', now()->subMonths(6)->startOfDay()),
                'this_year' => $subQuery->whereYear('payments.paid_at', now()->year),
                default => $subQuery->whereMonth('payments.paid_at', now()->month)->whereYear('payments.paid_at', now()->year),
            };
        }

        $subQuery->select(
            DB::raw('MIN(payments.id) as id'),
            DB::raw('COALESCE(method, source) as method_key'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(id) as total_count')
        )
            ->groupBy(DB::raw('COALESCE(method, source)'));

        return $table
            ->query(
                Payment::query()->withTrashed()->fromSub($subQuery, 'payments')
            )
            ->columns([
                Tables\Columns\TextColumn::make('method_key')
                    ->label('Metode')
                    ->formatStateUsing(fn (?string $state) => strtoupper($state ?? 'N/A'))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('total_count')
                    ->label('Jumlah Transaksi')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Pemasukan')
                    ->money('IDR', locale: 'id_ID')
                    ->alignEnd()
                    ->color('info')
                    ->weight('bold'),
            ])
            ->defaultSort('total_amount', 'desc')
            ->paginated(false);
    }
}
