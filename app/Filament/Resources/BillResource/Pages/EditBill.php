<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Filament\Traits\HasFriendlyNotifications;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBill extends EditRecord
{
    use HasFriendlyNotifications;

    protected static string $resource = BillResource::class;

    protected static ?string $title = 'Edit Tagihan';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

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
            $this->handleDatabaseException($e, 'tagihan');
            $this->halt();
        }
    }

    protected function afterSave(): void
    {
        $this->record->recalculateStatusAndAmounts();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
