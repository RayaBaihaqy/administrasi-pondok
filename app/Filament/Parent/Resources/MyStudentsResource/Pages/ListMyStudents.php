<?php

namespace App\Filament\Parent\Resources\MyStudentsResource\Pages;

use App\Filament\Parent\Resources\MyStudentsResource;
use Filament\Resources\Pages\ListRecords;

class ListMyStudents extends ListRecords
{
    protected static string $resource = MyStudentsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
