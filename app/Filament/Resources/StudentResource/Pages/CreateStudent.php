<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Filament\Traits\HasFriendlyNotifications;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    use HasFriendlyNotifications;

    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = \App\Models\Student::STATUS_ACTIVE;

        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (\Throwable $e) {
            $this->handleDatabaseException($e, 'siswa');
            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
