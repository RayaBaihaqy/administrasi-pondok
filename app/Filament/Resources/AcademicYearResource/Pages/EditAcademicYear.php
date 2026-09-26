<?php

namespace App\Filament\Resources\AcademicYearResource\Pages;

use App\Filament\Resources\AcademicYearResource;
use App\Filament\Traits\HasFriendlyNotifications;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademicYear extends EditRecord
{
    use HasFriendlyNotifications;

    protected static string $resource = AcademicYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus'),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (\Throwable $e) {
            $this->handleDatabaseException($e, 'tahun ajaran');
            $this->halt();
        }
    }

    protected function afterSave(): void
    {
        // Jika tahun ajaran diset aktif saat edit, nonaktifkan yang lain
        if ($this->record->is_active) {
            $this->record->setAsActive();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
