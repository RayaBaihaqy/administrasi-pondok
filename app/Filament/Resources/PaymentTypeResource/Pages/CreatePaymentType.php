<?php

namespace App\Filament\Resources\PaymentTypeResource\Pages;

use App\Filament\Resources\PaymentTypeResource;
use App\Filament\Traits\HasFriendlyNotifications;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentType extends CreateRecord
{
    use HasFriendlyNotifications;

    protected static string $resource = PaymentTypeResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (\Throwable $e) {
            $this->handleDatabaseException($e, 'jenis pembayaran');
            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
