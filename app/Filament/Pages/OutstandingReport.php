<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\OutstandingBillsWidget;
use App\Filament\Widgets\OutstandingStatsWidget;
use Filament\Actions;
use Filament\Forms;
use Filament\Pages\Page;

class OutstandingReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Tunggakan';

    protected static ?string $title = 'Laporan Tunggakan Tagihan';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.outstanding-report';

    public ?string $from_date = null;

    public ?string $until_date = null;

    public function resetFilters(): void
    {
        $this->from_date = null;
        $this->until_date = null;

        $this->dispatch('updateOutstandingFilter',
            from_date: $this->from_date,
            until_date: $this->until_date
        );
    }

    protected function getHeaderActions(): array
    {
        $activeFilterCount = ($this->from_date ? 1 : 0) + ($this->until_date ? 1 : 0);

        return [
            Actions\Action::make('filterDate')
                ->iconButton()
                ->icon('heroicon-o-funnel')
                ->tooltip('Filter Tanggal Jatuh Tempo')
                ->color($activeFilterCount > 0 ? 'warning' : 'gray')
                ->badge($activeFilterCount > 0 ? (string) $activeFilterCount : '0')
                ->form([
                    Forms\Components\DatePicker::make('from_date')
                        ->label('Dari Tanggal Jatuh Tempo')
                        ->default($this->from_date),

                    Forms\Components\DatePicker::make('until_date')
                        ->label('Sampai Tanggal Jatuh Tempo')
                        ->default($this->until_date),
                ])
                ->action(function (array $data) {
                    $this->from_date = $data['from_date'] ?? null;
                    $this->until_date = $data['until_date'] ?? null;

                    $this->dispatch('updateOutstandingFilter',
                        from_date: $this->from_date,
                        until_date: $this->until_date
                    );
                }),

            Actions\Action::make('resetFilter')
                ->iconButton()
                ->icon('heroicon-o-arrow-path')
                ->tooltip('Reset Filter Tanggal')
                ->color('danger')
                ->visible(fn (): bool => $activeFilterCount > 0)
                ->action(function () {
                    $this->resetFilters();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OutstandingStatsWidget::class,
        ];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'from_date' => $this->from_date,
            'until_date' => $this->until_date,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            OutstandingBillsWidget::class,
        ];
    }

    protected function getFooterWidgetsData(): array
    {
        return [
            'from_date' => $this->from_date,
            'until_date' => $this->until_date,
        ];
    }
}
