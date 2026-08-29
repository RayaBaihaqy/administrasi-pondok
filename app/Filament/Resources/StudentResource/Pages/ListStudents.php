<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Services\StudentImportService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. Tombol Download Template Excel / CSV
            Actions\Action::make('downloadTemplate')
                ->label('Download Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    $importService = new StudentImportService;

                    return $importService->downloadTemplate();
                }),

            // 2. Tombol Import Excel / CSV
            Actions\Action::make('importExcel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Import Data Siswa Massal dari Excel / CSV')
                ->modalDescription('Silakan unggah file spreadsheet template (.csv / .xlsx / .xls) yang telah diisi data siswa. Sistem akan otomatis membuat data siswa, orang tua, akun login portal wali murid, dan penempatan kelas.')
                ->form([
                    Forms\Components\FileUpload::make('attachment')
                        ->label('Pilih File Spreadsheet Template (.csv / .txt)')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/vnd.ms-excel',
                            'application/csv',
                            'text/comma-separated-values',
                        ])
                        ->disk('local')
                        ->directory('temp-imports')
                        ->required()
                        ->helperText('Pastikan menggunakan format kolom sesuai file yang diunduh dari tombol "Download Template".'),
                ])
                ->action(function (array $data) {
                    try {
                        $filePath = Storage::disk('local')->path($data['attachment']);

                        $importService = new StudentImportService;
                        $result = $importService->importFromSpreadsheet($filePath);

                        // Hapus file temporary setelah selesai
                        Storage::disk('local')->delete($data['attachment']);

                        $bodyMsg = "Berhasil mengimpor {$result['success_count']} siswa baru.";
                        if (! empty($result['errors'])) {
                            $bodyMsg .= ' Catatan: '.count($result['errors']).' baris dilewati karena format tidak lengkap.';
                        }

                        Notification::make()
                            ->title('Import Data Siswa Selesai')
                            ->body($bodyMsg)
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Gagal Mengimpor Data')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // 3. Tombol Tambah Siswa Manual
            Actions\CreateAction::make()
                ->label('Tambah Siswa'),
        ];
    }
}
