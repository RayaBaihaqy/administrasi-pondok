<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Traits\HasFriendlyNotifications;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use HasFriendlyNotifications;

    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Tambah Admin Baru';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = \App\Models\User::ROLE_ADMIN;

        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (\Throwable $e) {
            $this->handleDatabaseException($e, 'akun admin');
            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
