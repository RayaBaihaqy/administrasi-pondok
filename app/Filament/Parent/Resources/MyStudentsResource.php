<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\MyStudentsResource\Pages;
use App\Models\Student;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyStudentsResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Anak Saya';

    protected static ?string $modelLabel = 'Siswa';

    protected static ?string $pluralModelLabel = 'Data Siswa (Anak)';

    protected static ?int $navigationSort = 1;

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
                Forms\Components\TextInput::make('nis')->label('NISN')->disabled(),
                Forms\Components\TextInput::make('nism')->label('NISM')->disabled(),
                Forms\Components\TextInput::make('full_name')->label('Nama Lengkap')->disabled(),
                Forms\Components\TextInput::make('class_level')->label('Kelas')->disabled(),
                Forms\Components\TextInput::make('rombel')->label('Rombel')->disabled(),
                Forms\Components\TextInput::make('status')->label('Status')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nis')
                    ->label('NISN')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nism')
                    ->label('NISM')
                    ->searchable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable(),

                Tables\Columns\TextColumn::make('class_rombel')
                    ->label('Kelas')
                    ->getStateUsing(fn (Student $record) => $record->class_rombel),

                Tables\Columns\TextColumn::make('gender')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn (string $state) => Student::GENDERS[$state] ?? $state),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        Student::STATUS_ACTIVE => 'success',
                        Student::STATUS_GRADUATED => 'info',
                        Student::STATUS_WITHDRAWN => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        Student::STATUS_ACTIVE => 'Aktif',
                        Student::STATUS_GRADUATED => 'Lulus',
                        Student::STATUS_WITHDRAWN => 'Keluar',
                        default => $state,
                    }),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyStudents::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
