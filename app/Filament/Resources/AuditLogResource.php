<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $navigationLabel = 'Audit Log';

    protected static ?string $modelLabel = 'Audit Log';

    protected static ?string $pluralModelLabel = 'Audit Log';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rincian Log Aktivitas')
                    ->schema([
                        Forms\Components\TextInput::make('created_at')
                            ->label('Waktu Kejadian')
                            ->disabled(),

                        Forms\Components\TextInput::make('user_name')
                            ->label('Pelaku (Actor)')
                            ->disabled(),

                        Forms\Components\TextInput::make('user_role')
                            ->label('Role')
                            ->disabled(),

                        Forms\Components\TextInput::make('action')
                            ->label('Tindakan / Action')
                            ->disabled(),

                        Forms\Components\TextInput::make('auditable_type')
                            ->label('Tipe Entitas')
                            ->disabled(),

                        Forms\Components\TextInput::make('auditable_id')
                            ->label('ID Entitas')
                            ->disabled(),

                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),

                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->rows(2)
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('old_values')
                            ->label('Data Sebelum Perubahan (Old Values)')
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('new_values')
                            ->label('Data Sesudah Perubahan (New Values)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user_name')
                    ->label('Pelaku')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user_role')
                    ->badge()
                    ->label('Role')
                    ->color(fn (?string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'parent' => 'info',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('action')
                    ->label('Aktivitas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Entitas')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('ID')
                    ->default('-'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_role')
                    ->label('Role Pelaku')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin',
                        'parent' => 'Parent',
                        'system' => 'System',
                    ]),

                Tables\Filters\SelectFilter::make('action')
                    ->label('Aktivitas')
                    ->options([
                        'create_student' => 'Tambah Siswa',
                        'update_student' => 'Update Siswa',
                        'create_parent' => 'Tambah Orang Tua',
                        'record_manual_payment' => 'Bayar Manual',
                        'process_webhook' => 'Webhook Gateway',
                        'generate_spp' => 'Generate SPP',
                    ]),
            ])
            ->actions([
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
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }

    /**
     * HANYA Super Admin yang boleh melihat UI Audit Log (Sesuai RULES.md §69).
     */
    public static function canViewAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return false; // Immutable, log dibuat otomatis oleh sistem
    }
}
