<?php

namespace App\Filament\Parent\Resources\MyBillsResource\Pages;

use App\Filament\Parent\Resources\MyBillsResource;
use Filament\Resources\Pages\ListRecords;

class ListMyBills extends ListRecords
{
    protected static string $resource = MyBillsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
