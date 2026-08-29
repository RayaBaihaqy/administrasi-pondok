<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\MyBillsResource\Pages;
use App\Models\Bill;
use App\Services\DocumentService;
use App\Services\PaymentService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyBillsResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Tagihan Saya';

    protected static ?string $modelLabel = 'Tagihan';

    protected static ?string $pluralModelLabel = 'Daftar Tagihan';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $parentId = Auth::user()?->parentProfile?->id;

        return parent::getEloquentQuery()
            ->where('parent_id', $parentId);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('bill_number')->label('No. Tagihan')->disabled(),
                Forms\Components\TextInput::make('amount')->label('Total Tagihan')->prefix('Rp')->disabled(),
                Forms\Components\TextInput::make('outstanding_amount')->label('Sisa Tagihan')->prefix('Rp')->disabled(),
                Forms\Components\TextInput::make('due_date')->label('Jatuh Tempo')->disabled(),
                Forms\Components\TextInput::make('status')->label('Status')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bill_number')
                    ->label('No. Tagihan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('paymentType.name')
                    ->label('Jenis Pembayaran'),

                Tables\Columns\TextColumn::make('billing_period')
                    ->label('Periode')
                    ->date('M Y'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Total')
                    ->money('IDR', locale: 'id_ID'),

                Tables\Columns\TextColumn::make('outstanding_amount')
                    ->label('Sisa')
                    ->money('IDR', locale: 'id_ID'),

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
                // Action Bayar Online Midtrans
                Actions\Action::make('payOnline')
                    ->label('Bayar Online')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->visible(fn (Bill $record) => $record->outstanding_amount > 0 && $record->status !== Bill::STATUS_CANCELLED)
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
            ->defaultSort('due_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyBills::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
