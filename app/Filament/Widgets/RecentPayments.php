<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentPayments extends TableWidget
{
    protected static ?string $heading = 'Pembayaran Terbaru';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Payment::query()
                    ->with(['student', 'bill.paymentType'])
                    ->latest('created_at')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('payment_number')
                    ->label('No. Transaksi')
                    ->sortable(),

                TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable(),

                TextColumn::make('bill.paymentType.name')
                    ->label('Jenis Tagihan'),

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                TextColumn::make('source')
                    ->badge()
                    ->label('Sumber')
                    ->color(fn (string $state): string => match ($state) {
                        Payment::SOURCE_MIDTRANS => 'primary',
                        Payment::SOURCE_MANUAL => 'success',
                        default => 'secondary',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        Payment::STATUS_SUCCESS => 'success',
                        Payment::STATUS_PENDING => 'warning',
                        Payment::STATUS_FAILED => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state) => Payment::STATUSES[$state] ?? $state),

                TextColumn::make('paid_at')
                    ->label('Tanggal Pembayaran')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
