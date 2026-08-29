<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\ParentProfile;
use App\Models\Student;
use Filament\Actions;
use Filament\Forms;
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
                            ->required(),

                        Forms\Components\Select::make('rombel')
                            ->label('Rombel (Rombongan Belajar)')
                            ->options([
                                '1' => 'Kelas 1 (e.g. 7.1)',
                                '2' => 'Kelas 2 (e.g. 7.2)',
                                '3' => 'Kelas 3 (e.g. 7.3)',
                                '4' => 'Kelas 4 (e.g. 7.4)',
                                '5' => 'Kelas 5',
                                'A' => 'Kelas A',
                                'B' => 'Kelas B',
                                'C' => 'Kelas C',
                                'D' => 'Kelas D',
                                'E' => 'Kelas E',
                                'F' => 'Kelas F',
                            ])
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
                                // Buat user account untuk orang tua
                                $user = \App\Models\User::create([
                                    'name' => $data['full_name'],
                                    'email' => $data['contact_email'] ?? 'parent_'.time().'@pondok.test',
                                    'password' => bcrypt('password'),
                                    'role' => 'parent',
                                ]);

                                $parent = ParentProfile::create([
                                    'user_id' => $user->id,
                                    'full_name' => $data['full_name'],
                                    'phone' => $data['phone'] ?? null,
                                    'contact_email' => $data['contact_email'] ?? null,
                                ]);

                                return $parent->id;
                            }),
                    ]),

                Section::make('Kontak Siswa')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. HP')
                            ->tel()
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
                    ->options(
                        collect(Student::ROMBELS)->mapWithKeys(fn ($r) => [$r => $r])
                    ),

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
