<?php

namespace App\Filament\Parent\Resources\MyPaymentsResource\Pages;

use App\Filament\Parent\Resources\MyPaymentsResource;
use Filament\Resources\Pages\ListRecords;

class ListMyPayments extends ListRecords
{
    protected static string $resource = MyPaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
