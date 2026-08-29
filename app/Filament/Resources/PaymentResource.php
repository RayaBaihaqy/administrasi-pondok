<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Pembayaran';

    protected static ?string $navigationLabel = 'Riwayat Transaksi';

    protected static ?string $modelLabel = 'Pembayaran';

    protected static ?string $pluralModelLabel = 'Riwayat Pembayaran';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rincian Transaksi Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('payment_number')
                            ->label('No. Pembayaran / Transaksi')
                            ->disabled(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Nominal Dibayar (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),

                        Forms\Components\Select::make('source')
                            ->label('Sumber Pembayaran')
                            ->options(Payment::SOURCES)
                            ->disabled(),

                        Forms\Components\TextInput::make('method')
                            ->label('Metode Pembayaran')
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(Payment::STATUSES)
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Waktu Berhasil Bayar')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['student', 'bill.paymentType']))
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
                    ->label('No. Transaksi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bill.bill_number')
                    ->label('No. Tagihan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bill.paymentType.name')
                    ->label('Jenis Tagihan'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal (Rp)')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

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
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->label('Sumber')
                    ->options(Payment::SOURCES),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(Payment::STATUSES),
            ])
            ->actions([
                Actions\Action::make('downloadReceipt')
                    ->label('Kuitansi PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->visible(fn (Payment $record) => $record->isSuccess())
                    ->action(function (Payment $record) {
                        $documentService = new \App\Services\DocumentService;

                        return $documentService->downloadPaymentReceipt($record);
                    }),

                Actions\Action::make('sendWhatsApp')
                    ->label('Kirim WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn (Payment $record) => $record->isSuccess())
                    ->url(function (Payment $record) {
                        $waService = new \App\Services\WhatsAppAutomationService;
                        $data = $waService->formatPaymentSuccessMessage($record);

                        return \App\Services\WhatsAppAutomationService::createWhatsAppUrl($data['phone_number'], $data['message']);
                    })
                    ->openUrlInNewTab(),

                Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Pembayaran dicatat via BillResource atau Midtrans webhook
    }
}
