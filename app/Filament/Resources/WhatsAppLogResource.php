<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WhatsAppLogResource\Pages;
use App\Models\WhatsAppLog;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class WhatsAppLogResource extends Resource
{
    protected static ?string $model = WhatsAppLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $navigationLabel = 'Log WhatsApp';

    protected static ?string $modelLabel = 'Log WhatsApp';

    protected static ?string $pluralModelLabel = 'Riwayat Pesan WhatsApp';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('recipient_name')
                    ->label('Nama Wali / Penerima')
                    ->disabled(),

                Forms\Components\TextInput::make('phone_number')
                    ->label('Nomor WhatsApp')
                    ->disabled(),

                Forms\Components\TextInput::make('message_type')
                    ->label('Jenis Pesan')
                    ->disabled(),

                Forms\Components\TextInput::make('amount')
                    ->label('Nominal (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),

                Forms\Components\Textarea::make('message_content')
                    ->label('Isi Pesan WhatsApp')
                    ->rows(8)
                    ->columnSpanFull()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['student', 'parent'])->latest('id'))
            ->columns([
                Tables\Columns\TextColumn::make('recipient_name')
                    ->label('Wali Murid')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable()
                    ->description(fn ($record) => $record->student?->nis ? 'NISN: '.$record->student->nis : null),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('No. WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor WhatsApp disalin ke clipboard'),

                Tables\Columns\TextColumn::make('message_type')
                    ->label('Jenis Notifikasi')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        WhatsAppLog::TYPE_PAYMENT_SUCCESS => 'Bukti Pembayaran',
                        WhatsAppLog::TYPE_DUE_REMINDER => 'Pengingat Jatuh Tempo',
                        WhatsAppLog::TYPE_NEW_BILL => 'Tagihan Baru',
                        WhatsAppLog::TYPE_OVERDUE => 'Tagihan Menunggak',
                        default => ucfirst((string) $state),
                    })
                    ->color(fn ($state) => match ($state) {
                        WhatsAppLog::TYPE_PAYMENT_SUCCESS => 'success',
                        WhatsAppLog::TYPE_DUE_REMINDER => 'warning',
                        WhatsAppLog::TYPE_NEW_BILL => 'info',
                        WhatsAppLog::TYPE_OVERDUE => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id_ID')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Waktu Kirim')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        WhatsAppLog::STATUS_SENT => 'Terkirim',
                        WhatsAppLog::STATUS_PENDING => 'Menunggu',
                        WhatsAppLog::STATUS_FAILED => 'Gagal',
                        default => ucfirst((string) $state),
                    })
                    ->color(fn ($state) => match ($state) {
                        WhatsAppLog::STATUS_SENT => 'success',
                        WhatsAppLog::STATUS_PENDING => 'warning',
                        WhatsAppLog::STATUS_FAILED => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('message_type')
                    ->label('Jenis Notifikasi')
                    ->options([
                        WhatsAppLog::TYPE_PAYMENT_SUCCESS => 'Bukti Pembayaran',
                        WhatsAppLog::TYPE_DUE_REMINDER => 'Pengingat Jatuh Tempo',
                        WhatsAppLog::TYPE_NEW_BILL => 'Tagihan Baru',
                        WhatsAppLog::TYPE_OVERDUE => 'Tagihan Menunggak',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        WhatsAppLog::STATUS_SENT => 'Terkirim',
                        WhatsAppLog::STATUS_PENDING => 'Menunggu',
                        WhatsAppLog::STATUS_FAILED => 'Gagal',
                    ]),
            ])
            ->actions([
                // Action: Lihat Pesan
                Actions\Action::make('viewMessage')
                    ->label('Lihat Pesan')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn ($record) => 'Pesan WhatsApp - '.$record->recipient_name)
                    ->modalDescription(fn ($record) => 'Tujuan: '.$record->phone_number.' | Waktu: '.($record->sent_at?->translatedFormat('d F Y H:i') ?? '-'))
                    ->infolist([
                        \Filament\Infolists\Components\TextEntry::make('message_content')
                            ->label('Teks Pesan')
                            ->formatStateUsing(fn ($state) => nl2br(e($state)))
                            ->html(),
                    ])
                    ->modalSubmitAction(false),

                // Action: Buka WhatsApp Langsung (Direct wa.me)
                Actions\Action::make('openWhatsApp')
                    ->label('Buka WA')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('success')
                    ->url(fn (WhatsAppLog $record): string => $record->whatsapp_url)
                    ->openUrlInNewTab(),

                Actions\DeleteAction::make()
                    ->visible(fn (): bool => Auth::user()?->isSuperAdmin() ?? false),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isSuperAdmin() ?? false),
                ]),
            ]);
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
            'index' => Pages\ListWhatsAppLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Logs are automatically recorded by the system
    }
}
