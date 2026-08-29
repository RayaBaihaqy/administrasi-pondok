<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentPaymentOverrideResource\Pages;
use App\Models\AcademicYear;
use App\Models\StudentPaymentOverride;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class StudentPaymentOverrideResource extends Resource
{
    protected static ?string $model = StudentPaymentOverride::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|\UnitEnum|null $navigationGroup = 'Konfigurasi';

    protected static ?string $navigationLabel = 'Beasiswa';

    protected static ?string $modelLabel = 'Beasiswa';

    protected static ?string $pluralModelLabel = 'Data Beasiswa Siswa';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Beasiswa Siswa')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Siswa')
                            ->relationship('student', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->relationship('academicYear', 'name')
                            ->default(fn () => AcademicYear::current()?->id)
                            ->required(),

                        Forms\Components\DatePicker::make('disbursed_at')
                            ->label('Tanggal Diberikan (Penyerahan)')
                            ->default(now())
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Nominal Beasiswa (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->helperText('Nominal uang tunai beasiswa yang diserahkan kepada siswa.'),

                        Forms\Components\Textarea::make('reason')
                            ->label('Keterangan / Jenis Beasiswa')
                            ->placeholder('Contoh: Beasiswa Prestasi Tahfidz Al-Qur\'an, Bantuan Yatim/Dhuafa, Juara Lomba Sains')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['student', 'academicYear']))
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.nis')
                    ->label('NISN')
                    ->searchable(),

                Tables\Columns\TextColumn::make('disbursed_at')
                    ->label('Tanggal Diberikan')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal Beasiswa')
                    ->money('IDR', locale: 'id_ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Keterangan / Jenis Beasiswa')
                    ->limit(40)
                    ->default('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name'),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->visible(fn (): bool => Auth::user()?->isSuperAdmin() ?? false),
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
            'index' => Pages\ListStudentPaymentOverrides::route('/'),
            'create' => Pages\CreateStudentPaymentOverride::route('/create'),
            'edit' => Pages\EditStudentPaymentOverride::route('/{record}/edit'),
        ];
    }

    /**
     * Hanya Super Admin yang boleh membuat, mengedit, atau menghapus data beasiswa siswa.
     */
    public static function canCreate(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }
}
