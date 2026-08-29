<?php

namespace App\Filament\Resources\StudentPaymentOverrideResource\Pages;

use App\Filament\Resources\StudentPaymentOverrideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListStudentPaymentOverrides extends ListRecords
{
    protected static string $resource = StudentPaymentOverrideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Override Pricing')
                ->visible(fn (): bool => Auth::user()?->isSuperAdmin() ?? false),
        ];
    }
}
