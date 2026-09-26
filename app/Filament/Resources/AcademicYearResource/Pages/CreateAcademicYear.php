<?php

namespace App\Filament\Resources\AcademicYearResource\Pages;

use App\Filament\Resources\AcademicYearResource;
use App\Filament\Traits\HasFriendlyNotifications;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicYear extends CreateRecord
{
    use HasFriendlyNotifications;

    protected static string $resource = AcademicYearResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (\Throwable $e) {
            $this->handleDatabaseException($e, 'tahun ajaran');
            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        // Jika tahun ajaran baru diset aktif, nonaktifkan yang lain
        if ($this->record->is_active) {
            $this->record->setAsActive();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
