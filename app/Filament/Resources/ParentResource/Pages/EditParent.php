<?php

namespace App\Filament\Resources\ParentResource\Pages;

use App\Filament\Resources\ParentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParent extends EditRecord
{
    protected static string $resource = ParentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update user account jika email/password berubah
        $user = $this->record->user;

        if (! empty($this->data['user_email']) && $user) {
            $user->email = $this->data['user_email'];
            $user->name = $data['full_name'];

            if (! empty($this->data['user_password'])) {
                $user->password = bcrypt($this->data['user_password']);
            }

            $user->save();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
