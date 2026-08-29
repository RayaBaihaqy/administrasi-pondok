<?php

namespace App\Filament\Resources\StudentPaymentOverrideResource\Pages;

use App\Filament\Resources\StudentPaymentOverrideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentPaymentOverride extends EditRecord
{
    protected static string $resource = StudentPaymentOverrideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
