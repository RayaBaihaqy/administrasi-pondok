<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Bill;
use App\Models\ParentProfile;
use App\Models\Student;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?string $navigationLabel = 'Siswa';

    protected static ?string $modelLabel = 'Siswa';

    protected static ?string $pluralModelLabel = 'Siswa';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Siswa')
                    ->schema([
                        Forms\Components\TextInput::make('nis')
                            ->label('NISN (Nomor Induk Siswa Nasional)')
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'NISN [:input] sudah terdaftar pada siswa lain. Mohon periksa kembali.',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('nism')
                            ->label('NISM (Nomor Induk Siswa Madrasah)')
                            ->nullable(),

                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required(),

                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(Student::GENDERS)
                            ->required(),

                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir'),

                        Forms\Components\Select::make('class_level')
                            ->label('Tingkat Kelas')
                            ->options([
                                7 => 'Kelas 7 (Tujuh)',
                                8 => 'Kelas 8 (Delapan)',
                                9 => 'Kelas 9 (Sembilan)',
                            ])
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('rombel', null))
                            ->required(),

                        Forms\Components\Select::make('rombel')
                            ->label('Rombel (Rombongan Belajar)')
                            ->options(fn (callable $get) => Student::getRombelsForClass($get('class_level')))
                            ->disabled(fn (callable $get) => blank($get('class_level')))
                            ->placeholder(fn (callable $get) => blank($get('class_level')) ? 'Pilih tingkat kelas terlebih dahulu...' : 'Pilih salah satu rombel')
                            ->helperText(fn (callable $get) => blank($get('class_level')) ? 'Silakan pilih tingkat kelas terlebih dahulu.' : null)
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status Siswa')
                            ->options([
                                Student::STATUS_ACTIVE => 'Aktif',
                                Student::STATUS_GRADUATED => 'Lulus',
                                Student::STATUS_WITHDRAWN => 'Keluar',
                                Student::STATUS_INACTIVE => 'Nonaktif',
                            ])
                            ->default(Student::STATUS_ACTIVE)
                            ->visible(fn (string $operation): bool => $operation === 'edit')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Orang Tua / Wali')
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('Orang Tua / Wali Siswa')
                            ->relationship('parentProfile', 'full_name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('full_name')
                                    ->label('Nama Lengkap Wali')
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('No. Telepon / WhatsApp')
                                    ->required(),
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('Email Wali'),
                                Forms\Components\Textarea::make('address')
                                    ->label('Alamat')
                                    ->rows(2),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $phone = trim($data['phone'] ?? '');
                                $email = trim($data['contact_email'] ?? '');

                                // Cek duplikat nomor HP di User dan ParentProfile
                                if ($phone !== '') {
                                    $existsInUser = \App\Models\User::where('phone', $phone)->exists();
                                    $existsInParent = ParentProfile::where('phone', $phone)->exists();

                                    if ($existsInUser || $existsInParent) {
                                        Notification::make()
                                            ->title('Nomor Telepon Duplikat')
                                            ->body("Nomor telepon [{$phone}] sudah terdaftar pada orang tua/wali lain. Silakan pilih dari dropdown atau gunakan nomor yang berbeda.")
                                            ->danger()
                                            ->persistent()
                                            ->send();

                                        throw \Illuminate\Validation\ValidationException::withMessages([
                                            'phone' => "Nomor telepon [{$phone}] sudah terdaftar pada orang tua/wali lain.",
                                        ]);
                                    }
                                }

                                // Cek duplikat email jika diisi
                                if ($email !== '') {
                                    $existsEmail = \App\Models\User::where('email', $email)->exists();
                                    if ($existsEmail) {
                                        Notification::make()
                                            ->title('Email Duplikat')
                                            ->body("Email [{$email}] sudah terdaftar di sistem. Mohon gunakan email yang lain.")
                                            ->danger()
                                            ->persistent()
                                            ->send();

                                        throw \Illuminate\Validation\ValidationException::withMessages([
                                            'contact_email' => "Email [{$email}] sudah terdaftar di sistem.",
                                        ]);
                                    }
                                }

                                try {
                                    $userEmail = ! empty($email) ? $email : 'parent_'.time().'_'.rand(100, 999).'@pondok.test';

                                    $user = \App\Models\User::create([
                                        'name' => $data['full_name'],
                                        'email' => $userEmail,
                                        'phone' => ! empty($phone) ? $phone : null,
                                        'password' => bcrypt('password'),
                                        'role' => 'parent',
                                    ]);

                                    $parent = ParentProfile::create([
                                        'user_id' => $user->id,
                                        'full_name' => $data['full_name'],
                                        'phone' => ! empty($phone) ? $phone : null,
                                        'contact_email' => ! empty($email) ? $email : null,
                                        'address' => $data['address'] ?? null,
                                    ]);

                                    return $parent->id;
                                } catch (\Throwable $e) {
                                    Notification::make()
                                        ->title('Gagal Menambahkan Wali')
                                        ->body('Terjadi kendala saat menambahkan data wali. Mohon periksa kembali isian Anda.')
                                        ->danger()
                                        ->persistent()
                                        ->send();

                                    throw $e;
                                }
                            }),
                    ]),

                Section::make('Kontak Siswa')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(table: 'students', column: 'email', ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'Email [:input] sudah terdaftar pada siswa lain.',
                            ])
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. HP')
                            ->tel()
                            ->unique(table: 'students', column: 'phone', ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'Nomor HP [:input] sudah terdaftar pada siswa lain.',
                            ])
                            ->maxLength(30),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['parentProfile']))
            ->columns([
                Tables\Columns\TextColumn::make('nis')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nism')
                    ->label('NISM')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('class_rombel')
                    ->label('Kelas')
                    ->getStateUsing(fn (Student $record) => $record->class_rombel)
                    ->sortable(['class_level', 'rombel']),

                Tables\Columns\TextColumn::make('gender')
                    ->label('JK')
                    ->formatStateUsing(fn (string $state) => Student::GENDERS[$state] ?? $state),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        Student::STATUS_ACTIVE => 'success',
                        Student::STATUS_GRADUATED => 'info',
                        Student::STATUS_WITHDRAWN => 'danger',
                        Student::STATUS_INACTIVE => 'warning',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        Student::STATUS_ACTIVE => 'Aktif',
                        Student::STATUS_GRADUATED => 'Lulus',
                        Student::STATUS_WITHDRAWN => 'Keluar',
                        Student::STATUS_INACTIVE => 'Nonaktif',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('parentProfile.full_name')
                    ->label('Orang Tua')
                    ->searchable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_level')
                    ->label('Kelas')
                    ->options(
                        collect(Student::CLASS_LEVELS)->mapWithKeys(fn ($level) => [$level => "Kelas $level"])
                    ),

                Tables\Filters\SelectFilter::make('rombel')
                    ->label('Rombel')
                    ->options([
                        '1' => 'Rombel 1 (.1)',
                        '2' => 'Rombel 2 (.2)',
                        '3' => 'Rombel 3 (.3)',
                        '4' => 'Rombel 4 (.4)',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Student::STATUS_ACTIVE => 'Aktif',
                        Student::STATUS_GRADUATED => 'Lulus',
                        Student::STATUS_WITHDRAWN => 'Keluar',
                        Student::STATUS_INACTIVE => 'Nonaktif',
                    ]),

                Tables\Filters\SelectFilter::make('gender')
                    ->label('Jenis Kelamin')
                    ->options(Student::GENDERS),
            ])
            ->actions([
                Actions\Action::make('mutate')
                    ->label('Mutasi / Pindah')
                    ->icon('heroicon-o-arrow-right-start-on-rectangle')
                    ->color('warning')
                    ->visible(fn (Student $record): bool => $record->status === Student::STATUS_ACTIVE)
                    ->modalHeading(fn (Student $record): string => "Mutasi Siswa Keluar: {$record->full_name}")
                    ->modalDescription(function (Student $record): string {
                        $unpaidCount = $record->bills()->whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])->count();
                        $unpaidTotal = $record->bills()->whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])->sum('outstanding_amount');

                        if ($unpaidCount > 0) {
                            return "Siswa ini memiliki {$unpaidCount} tagihan belum lunas (Total Rp ".number_format($unpaidTotal, 0, ',', '.').'). Memproses mutasi akan otomatis membatalkan tagihan yang belum dibayar.';
                        }

                        return 'Siswa tidak memiliki tagihan tertunggak. Status siswa akan diubah menjadi Keluar/Pindah.';
                    })
                    ->form([
                        Forms\Components\DatePicker::make('withdrawal_date')
                            ->label('Tanggal Efektif Mutasi')
                            ->default(now())
                            ->required(),
                        Forms\Components\Textarea::make('withdrawal_reason')
                            ->label('Alasan Mutasi / Sekolah Tujuan')
                            ->placeholder('Contoh: Pindah domisili orang tua / mutasi ke sekolah lain')
                            ->rows(2),
                    ])
                    ->modalSubmitActionLabel('Proses Mutasi Siswa')
                    ->action(function (Student $record, array $data): void {
                        $cancelledCount = $record->mutateOut(
                            reason: $data['withdrawal_reason'] ?? '',
                            date: $data['withdrawal_date'] ?? null
                        );

                        Notification::make()
                            ->title('Mutasi Siswa Berhasil')
                            ->body("Status {$record->full_name} diubah menjadi Keluar/Pindah. Sebanyak {$cancelledCount} tagihan belum lunas telah dibatalkan.")
                            ->success()
                            ->send();
                    }),

                Actions\Action::make('revert_mutation')
                    ->label('Aktifkan Kembali')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn (Student $record): bool => $record->status === Student::STATUS_WITHDRAWN)
                    ->requiresConfirmation()
                    ->modalHeading(fn (Student $record): string => "Aktifkan Kembali Siswa: {$record->full_name}")
                    ->modalDescription('Apakah Anda yakin ingin mengembalikan status siswa ini menjadi Aktif?')
                    ->modalSubmitActionLabel('Ya, Aktifkan Siswa')
                    ->action(function (Student $record): void {
                        $record->revertMutation();

                        Notification::make()
                            ->title('Siswa Diaktifkan Kembali')
                            ->body("Status {$record->full_name} telah dikembalikan menjadi Aktif.")
                            ->success()
                            ->send();
                    }),

                Actions\EditAction::make(),
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('full_name');
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
