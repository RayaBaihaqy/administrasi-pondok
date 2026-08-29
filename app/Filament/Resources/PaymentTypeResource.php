<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentTypeResource\Pages;
use App\Models\PaymentType;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PaymentTypeResource extends Resource
{
    protected static ?string $model = PaymentType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'Konfigurasi';

    protected static ?string $navigationLabel = 'Jenis Pembayaran';

    protected static ?string $modelLabel = 'Jenis Pembayaran';

    protected static ?string $pluralModelLabel = 'Jenis Pembayaran';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konfigurasi Jenis Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Jenis')
                            ->placeholder('SPP / AT / DAFTAR_ULANG')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Jenis Pembayaran')
                            ->placeholder('SPP Bulanan')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('billing_type')
                            ->label('Tipe Penagihan')
                            ->options(PaymentType::BILLING_TYPES)
                            ->required()
                            ->default(PaymentType::BILLING_TYPE_MONTHLY),

                        Forms\Components\TextInput::make('default_amount')
                            ->label('Nominal Default (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0)
                            ->helperText('Nominal dasar jika tidak ada aturan khusus per kelas'),

                        Forms\Components\TextInput::make('default_due_day')
                            ->label('Tanggal Jatuh Tempo Default')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(31)
                            ->default(1)
                            ->helperText('Tanggal dalam bulan (1-31) untuk tagihan bulanan'),

                        Forms\Components\Toggle::make('allows_installment')
                            ->label('Izinkan Cicilan')
                            ->helperText('Apakah tagihan jenis ini boleh dibayar secara mencicil?')
                            ->default(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),

                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('billing_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state) => PaymentType::BILLING_TYPES[$state] ?? $state),

                Tables\Columns\TextColumn::make('default_amount')
                    ->label('Nominal Default')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('default_due_day')
                    ->label('Jatuh Tempo')
                    ->formatStateUsing(fn ($state) => "Tanggal $state"),

                Tables\Columns\IconColumn::make('allows_installment')
                    ->label('Cicilan')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('billing_type')
                    ->label('Tipe Penagihan')
                    ->options(PaymentType::BILLING_TYPES),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->visible(fn (): bool => Auth::user()?->isSuperAdmin() ?? false),
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => Auth::user()?->isSuperAdmin() ?? false),
                ]),
            ])
            ->defaultSort('code');
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
            'index' => Pages\ListPaymentTypes::route('/'),
            'create' => Pages\CreatePaymentType::route('/create'),
            'edit' => Pages\EditPaymentType::route('/{record}/edit'),
        ];
    }

    /**
     * Hanya Super Admin yang boleh membuat, mengedit, atau menghapus master jenis pembayaran.
     */
    public static function canCreate(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }
}
