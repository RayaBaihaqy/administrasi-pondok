<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Traits\HasFriendlyNotifications;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    use HasFriendlyNotifications;

    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Edit Akun Admin';

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if ($this->record->isSuperAdmin()) {
            abort(403, 'Akun Super Admin tidak dapat diubah dari menu Kelola Admin.');
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['role'] = \App\Models\User::ROLE_ADMIN;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->visible(fn (): bool => $this->record->id !== Auth::id() && ! $this->record->isSuperAdmin())
                ->modalHeading(fn () => "Hapus Akun Admin: {$this->record->name}")
                ->modalDescription('Apakah Anda yakin ingin menghapus akun admin ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus Admin'),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
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
