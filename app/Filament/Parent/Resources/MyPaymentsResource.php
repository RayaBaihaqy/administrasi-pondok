<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\MyPaymentsResource\Pages;
use App\Models\Payment;
use App\Services\DocumentService;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyPaymentsResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Riwayat Pembayaran';

    protected static ?string $modelLabel = 'Pembayaran';

    protected static ?string $pluralModelLabel = 'Riwayat Pembayaran Saya';

    protected static ?int $navigationSort = 3;

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
                Forms\Components\TextInput::make('payment_number')->label('No. Pembayaran')->disabled(),
                Forms\Components\TextInput::make('amount')->label('Nominal')->prefix('Rp')->disabled(),
                Forms\Components\TextInput::make('status')->label('Status')->disabled(),
                Forms\Components\TextInput::make('paid_at')->label('Waktu Bayar')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
                    ->label('No. Transaksi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('bill.paymentType.name')
                    ->label('Jenis Tagihan'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id_ID'),

                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->label('Sumber')
                    ->color(fn (string $state): string => match ($state) {
                        Payment::SOURCE_MIDTRANS => 'primary',
                        Payment::SOURCE_MANUAL => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state) => Payment::SOURCES[$state] ?? $state),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        Payment::STATUS_SUCCESS => 'success',
                        Payment::STATUS_PENDING => 'warning',
                        Payment::STATUS_FAILED => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state) => Payment::STATUSES[$state] ?? $state),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Waktu Bayar')
                    ->dateTime('d M Y H:i'),
            ])
            ->actions([
                Actions\Action::make('downloadReceipt')
                    ->label('Bukti Bayar PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (Payment $record) => $record->isSuccess())
                    ->action(function (Payment $record) {
                        $documentService = new DocumentService;

                        return $documentService->downloadPaymentReceipt($record);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyPayments::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
