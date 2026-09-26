<?php

namespace App\Filament\Resources\ParentResource\Pages;

use App\Filament\Resources\ParentResource;
use App\Filament\Traits\HasFriendlyNotifications;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateParent extends CreateRecord
{
    use HasFriendlyNotifications;

    protected static string $resource = ParentResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            $phone = ! empty($data['phone']) ? trim($data['phone']) : null;
            $userEmail = trim($this->data['user_email']);

            if ($phone && User::where('phone', $phone)->exists()) {
                Notification::make()
                    ->title('Nomor Telepon Duplikat')
                    ->body("Nomor telepon [{$phone}] sudah terdaftar pada akun login lain. Mohon gunakan nomor yang berbeda.")
                    ->danger()
                    ->persistent()
                    ->send();
                $this->halt();
            }

            // Buat user account untuk orang tua
            $user = User::create([
                'name' => $data['full_name'],
                'email' => $userEmail,
                'phone' => $phone,
                'password' => bcrypt($this->data['user_password'] ?? 'password'),
                'role' => 'parent',
            ]);

            $data['user_id'] = $user->id;

            return parent::handleRecordCreation($data);
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
