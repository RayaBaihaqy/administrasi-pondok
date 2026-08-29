<?php

namespace App\Filament\Resources\WhatsAppLogResource\Pages;

use App\Filament\Resources\WhatsAppLogResource;
use Filament\Resources\Pages\ListRecords;

class ListWhatsAppLogs extends ListRecords
{
    protected static string $resource = WhatsAppLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
