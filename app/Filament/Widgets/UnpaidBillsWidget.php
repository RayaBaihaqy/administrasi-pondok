<?php

namespace App\Filament\Widgets;

use App\Models\Bill;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UnpaidBillsWidget extends TableWidget
{
    protected static ?string $heading = 'Tagihan Belum Lunas (Tunggakan)';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Bill::query()
                    ->with(['student', 'paymentType'])
                    ->where('outstanding_amount', '>', 0)
                    ->where('status', '!=', Bill::STATUS_CANCELLED)
                    ->orderBy('due_date', 'asc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('bill_number')
                    ->label('No. Tagihan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable(),

                TextColumn::make('paymentType.name')
                    ->label('Jenis Tagihan'),

                TextColumn::make('amount')
                    ->label('Total Tagihan')
                    ->money('IDR', locale: 'id_ID'),

                TextColumn::make('outstanding_amount')
                    ->label('Sisa Tunggakan')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->label('Status Tagihan')
                    ->color(fn (string $state): string => match ($state) {
                        Bill::STATUS_OVERDUE => 'danger',
                        Bill::STATUS_UNPAID => 'warning',
                        Bill::STATUS_PAID => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state) => Bill::STATUSES[$state] ?? $state),
            ])
            ->paginated(false);
    }
}
