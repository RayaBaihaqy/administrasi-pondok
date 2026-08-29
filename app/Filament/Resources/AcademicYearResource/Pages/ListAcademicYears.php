<?php

namespace App\Filament\Resources\AcademicYearResource\Pages;

use App\Filament\Resources\AcademicYearResource;
use App\Models\Student;
use App\Services\AcademicYearTransitionService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListAcademicYears extends ListRecords
{
    protected static string $resource = AcademicYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Header Action: Mulai Tahun Ajaran Baru & Kenaikan Kelas Massal
            Actions\Action::make('startNewYear')
                ->label('Tahun Ajaran Baru & Kenaikan Kelas')
                ->color('warning')
                ->visible(fn () => Auth::user()?->isSuperAdmin())
                ->modalHeading('Mulai Tahun Ajaran Baru & Kenaikan Kelas Massal')
                ->modalDescription('Tindakan ini akan mengaktifkan tahun ajaran baru, memproses kenaikan kelas siswa, dan otomatis menerbitkan tagihan Daftar Ulang.')
                ->form([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Tahun Ajaran Baru')
                        ->placeholder((now()->year).'/'.(now()->year + 1))
                        ->required(),

                    Forms\Components\DatePicker::make('start_date')
                        ->label('Tanggal Mulai (Tahun Ajaran Baru)')
                        ->default(fn () => \Carbon\Carbon::create(now()->year, 7, 15))
                        ->helperText('Sesuai kalender akademik MTs, tahun ajaran baru dimulai sekitar 15 Juli.')
                        ->required(),

                    Forms\Components\DatePicker::make('end_date')
                        ->label('Tanggal Selesai')
                        ->default(fn () => \Carbon\Carbon::create(now()->year + 1, 7, 14))
                        ->required(),

                    Forms\Components\Repeater::make('retained_students')
                        ->label('Siswa Tidak Naik Kelas (Tinggal Kelas)')
                        ->helperText('Pilih siswa yang tinggal kelas (jika ada). Sistem akan otomatis mempertahankan tingkat kelas siswa tersebut dan menerbitkan tagihan daftar ulang sesuai tingkatannya saat ini.')
                        ->schema([
                            Forms\Components\Select::make('student_id')
                                ->label('Cari Nama Siswa')
                                ->options(function () {
                                    $students = Student::active()->orderBy('full_name')->get();

                                    return [
                                        'Kelas 7' => $students->where('class_level', 7)->mapWithKeys(fn (Student $s) => [
                                            $s->id => "{$s->full_name} (Kelas 7.{$s->rombel} | NISN: {$s->nis})",
                                        ])->all(),
                                        'Kelas 8' => $students->where('class_level', 8)->mapWithKeys(fn (Student $s) => [
                                            $s->id => "{$s->full_name} (Kelas 8.{$s->rombel} | NISN: {$s->nis})",
                                        ])->all(),
                                        'Kelas 9' => $students->where('class_level', 9)->mapWithKeys(fn (Student $s) => [
                                            $s->id => "{$s->full_name} (Kelas 9.{$s->rombel} | NISN: {$s->nis})",
                                        ])->all(),
                                    ];
                                })
                                ->searchable()
                                ->required()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                        ])
                        ->addActionLabel('Tambah Siswa Tinggal Kelas (+)')
                        ->collapsible()
                        ->default([])
                        ->columns(1),
                ])
                ->action(function (array $data) {
                    try {
                        $retainedStudentIds = collect($data['retained_students'] ?? [])
                            ->pluck('student_id')
                            ->filter()
                            ->all();

                        $transitionService = new AcademicYearTransitionService;
                        $result = $transitionService->startNewAcademicYear(
                            name: $data['name'],
                            startDate: $data['start_date'],
                            endDate: $data['end_date'],
                            retainedStudentIds: $retainedStudentIds
                        );

                        Notification::make()
                            ->title('Tahun Ajaran Baru Berhasil Dimulai')
                            ->body("Tahun Ajaran {$result['new_year']->name} Aktif | Siswa Naik Kelas: {$result['promoted_count']} | Siswa Tinggal Kelas: {$result['retained_count']} | Siswa Lulus: {$result['graduated_count']} | Tagihan Daftar Ulang: {$result['bills_created']}")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Gagal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make()
                ->label('Tambah Tahun Ajaran'),
        ];
    }
}
