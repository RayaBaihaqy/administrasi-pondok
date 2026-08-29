<?php

namespace App\Filament\Resources\ParentResource\Pages;

use App\Filament\Resources\ParentResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateParent extends CreateRecord
{
    protected static string $resource = ParentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Buat user account untuk orang tua
        $user = User::create([
            'name' => $data['full_name'],
            'email' => $this->data['user_email'],
            'password' => bcrypt($this->data['user_password']),
            'role' => 'parent',
        ]);

        $data['user_id'] = $user->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
