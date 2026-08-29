<?php

namespace App\Filament\Widgets;

use App\Models\Bill;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class OutstandingBillsWidget extends BaseWidget
{
    protected static ?string $heading = 'Daftar Siswa Menunggak Tagihan';

    protected int|string|array $columnSpan = 'full';

    public ?string $from_date = null;

    public ?string $until_date = null;

    #[On('updateOutstandingFilter')]
    public function updateOutstandingFilter(?string $from_date = null, ?string $until_date = null): void
    {
        $this->from_date = $from_date;
        $this->until_date = $until_date;
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        $query = Bill::query()
            ->whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])
            ->whereNull('deleted_at')
            ->with(['student', 'paymentType']);

        if ($this->from_date) {
            $query->whereDate('due_date', '>=', $this->from_date);
        }
        if ($this->until_date) {
            $query->whereDate('due_date', '<=', $this->until_date);
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('student.class_rombel')
                    ->label('Kelas')
                    ->getStateUsing(fn (Bill $record) => $record->student?->class_rombel)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('student', function ($q) use ($search) {
                            $q->where('class_level', 'like', "%{$search}%")
                                ->orWhere('rombel', 'like', "%{$search}%");
                        });
                    }),

                Tables\Columns\TextColumn::make('paymentType.name')
                    ->label('Jenis Tagihan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_keterangan')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (Bill $record) {
                        $isOverdue = $record->status === Bill::STATUS_OVERDUE
                            || ($record->due_date && \Carbon\Carbon::parse($record->due_date)->startOfDay()->lessThan(now()->startOfDay()));

                        return $isOverdue ? 'Terlambat' : 'Belum Lunas';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Terlambat' => 'danger',
                        'Belum Lunas' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('outstanding_amount')
                    ->label('Sisa Tunggakan')
                    ->money('IDR', locale: 'id_ID')
                    ->color('warning')
                    ->weight('bold')
                    ->sortable()
                    ->alignEnd(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_type')
                    ->label('Status Tunggakan')
                    ->options([
                        'unpaid' => 'Belum Lunas (Belum Jatuh Tempo)',
                        'overdue' => 'Terlambat (Melewati Jatuh Tempo)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'unpaid') {
                            $query->where(function ($q) {
                                $q->where('status', Bill::STATUS_UNPAID)
                                    ->whereDate('due_date', '>=', now()->startOfDay());
                            });
                        } elseif ($data['value'] === 'overdue') {
                            $query->where(function ($q) {
                                $q->where('status', Bill::STATUS_OVERDUE)
                                    ->orWhere(function ($sq) {
                                        $sq->where('status', Bill::STATUS_UNPAID)
                                            ->whereDate('due_date', '<', now()->startOfDay());
                                    });
                            });
                        }
                    }),
            ])
            ->defaultSort('due_date', 'asc')
            ->paginated([5, 10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}
