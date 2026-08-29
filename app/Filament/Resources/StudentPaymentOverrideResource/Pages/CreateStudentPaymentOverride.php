<?php

namespace App\Filament\Resources\StudentPaymentOverrideResource\Pages;

use App\Filament\Resources\StudentPaymentOverrideResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentPaymentOverride extends CreateRecord
{
    protected static string $resource = StudentPaymentOverrideResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
