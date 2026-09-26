<?php

namespace App\Filament\Resources\ParentResource\Pages;

use App\Filament\Resources\ParentResource;
use App\Filament\Traits\HasFriendlyNotifications;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditParent extends EditRecord
{
    use HasFriendlyNotifications;

    protected static string $resource = ParentResource::class;

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
            $phone = ! empty($data['phone']) ? trim($data['phone']) : null;
            $userEmail = ! empty($this->data['user_email']) ? trim($this->data['user_email']) : null;
            $user = $record->user;

            if ($phone && $user) {
                $duplicatePhone = User::where('phone', $phone)->where('id', '!=', $user->id)->exists();
                if ($duplicatePhone) {
                    Notification::make()
                        ->title('Nomor Telepon Duplikat')
                        ->body("Nomor telepon [{$phone}] sudah digunakan oleh akun pengguna lain. Mohon gunakan nomor yang berbeda.")
                        ->danger()
                        ->persistent()
                        ->send();
                    $this->halt();
                }
            }

            if ($user) {
                if (! empty($userEmail)) {
                    $user->email = $userEmail;
                }
                $user->name = $data['full_name'];
                $user->phone = $phone;

                if (! empty($this->data['user_password'])) {
                    $user->password = bcrypt($this->data['user_password']);
                }

                $user->save();
            }

            return parent::handleRecordUpdate($record, $data);
        } catch (\Throwable $e) {
            $this->handleDatabaseException($e, 'orang tua');
            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
