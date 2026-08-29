<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Bill;
use App\Services\DocumentService;
use App\Services\PaymentService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UnpaidBillsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $parentId = Auth::user()?->parentProfile?->id;

        return $table
            ->heading('Tagihan Yang Belum Dibayar / Tunggakan')
            ->description('Berikut daftar tagihan administrasi pendidikan yang perlu diselesaikan. Anda dapat langsung membayar secara online atau mengunduh invoice.')
            ->query(
                Bill::query()
                    ->where('parent_id', $parentId)
                    ->whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])
                    ->with(['student', 'paymentType'])
                    ->latest('due_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->description(fn (Bill $record) => 'Kelas: '.($record->student?->class_rombel ?? '-').' | NISN: '.($record->student?->nis ?? '-')),

                Tables\Columns\TextColumn::make('paymentType.name')
                    ->label('Jenis Pembayaran')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('billing_period')
                    ->label('Periode')
                    ->date('M Y'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->color(fn (Bill $record) => $record->isOverdue() ? 'danger' : 'gray'),

                Tables\Columns\TextColumn::make('outstanding_amount')
                    ->label('Sisa Tagihan')
                    ->money('IDR', locale: 'id_ID')
                    ->weight('bold')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        Bill::STATUS_OVERDUE => 'danger',
                        Bill::STATUS_UNPAID => 'warning',
                        Bill::STATUS_PAID => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state) => Bill::STATUSES[$state] ?? $state),
            ])
            ->actions([
                // Action Bayar Online Midtrans langsung dari Dashboard
                Actions\Action::make('payOnline')
                    ->label('Bayar Online')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->action(function (Bill $record, $livewire) {
                        try {
                            $paymentService = new PaymentService;
                            $payment = $paymentService->createMidtransPayment($record);

                            $livewire->dispatch('open-midtrans-snap', snapToken: $payment->snap_token);
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Gagal Memproses Pembayaran')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                // Action Download Invoice PDF
                Actions\Action::make('downloadInvoice')
                    ->label('Invoice PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->action(function (Bill $record) {
                        $documentService = new DocumentService;

                        return $documentService->downloadBillInvoice($record);
                    }),
            ])
            ->emptyStateHeading('Alhamdulillah! Tidak Ada Tunggakan')
            ->emptyStateDescription('Semua tagihan administrasi ananda telah lunas dan tidak ada pembayaran yang tertunda.')
            ->emptyStateIcon('heroicon-o-check-badge');
    }
}
