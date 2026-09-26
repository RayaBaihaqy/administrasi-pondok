<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $navigationLabel = 'Kelola Admin';

    protected static ?string $modelLabel = 'Admin';

    protected static ?string $pluralModelLabel = 'Kelola Admin';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun Admin')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap Admin')
                            ->placeholder('Contoh: Ustadz Abdullah, S.Pd.')
                            ->required()
                            ->validationMessages([
                                'required' => 'Nama lengkap admin wajib diisi.',
                            ])
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Login')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'Email login wajib diisi.',
                                'email' => 'Format email login tidak valid.',
                                'unique' => 'Email login [:input] sudah terdaftar pada akun lain. Mohon gunakan email yang berbeda.',
                            ]),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. WhatsApp / HP')
                            ->tel()
                            ->maxLength(30)
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'Nomor WhatsApp/HP [:input] sudah digunakan oleh akun lain.',
                            ]),

                        Forms\Components\Hidden::make('role')
                            ->default(User::ROLE_ADMIN),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->minLength(6)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                            ->validationMessages([
                                'required' => 'Password wajib diisi untuk admin baru.',
                                'min' => 'Password minimal harus :min karakter.',
                            ])
                            ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Kosongkan jika tidak ingin mengubah password akun.' : 'Minimal 6 karakter.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('role', User::ROLE_ADMIN))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Admin')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email Login')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('No. WhatsApp')
                    ->searchable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->label('Hak Akses')
                    ->color('warning')
                    ->formatStateUsing(fn (string $state): string => 'Admin Staff'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record) => "Reset Password: {$record->name}")
                    ->modalDescription('Masukkan password baru untuk akun admin ini.')
                    ->form([
                        Forms\Components\TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(6)
                            ->validationMessages([
                                'required' => 'Password baru wajib diisi.',
                                'min' => 'Password baru minimal harus :min karakter.',
                            ]),
                    ])
                    ->action(function (User $record, array $data): void {
                        try {
                            $record->update([
                                'password' => bcrypt($data['new_password']),
                            ]);

                            Notification::make()
                                ->title('Password Berhasil Diubah')
                                ->body("Password untuk akun admin {$record->name} berhasil diperbarui.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Gagal Mengubah Password')
                                ->body('Terjadi kendala saat mereset password. Silakan coba lagi.')
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),

                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->visible(fn (User $record): bool => $record->id !== Auth::id())
                    ->modalHeading(fn (User $record) => "Hapus Akun Admin: {$record->name}")
                    ->modalDescription('Apakah Anda yakin ingin menghapus akun admin ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus Admin'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * HANYA Super Admin yang boleh melihat dan mengelola akun admin.
     */
    public static function canViewAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        if ($record instanceof User && $record->isSuperAdmin()) {
            return false;
        }

        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        if ($record->id === Auth::id() || ($record instanceof User && $record->isSuperAdmin())) {
            return false;
        }

        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }
}
