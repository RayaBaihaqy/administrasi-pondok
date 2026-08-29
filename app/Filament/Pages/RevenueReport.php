<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RevenueByMethodWidget;
use App\Filament\Widgets\RevenueByPaymentTypeWidget;
use App\Filament\Widgets\RevenueStatsWidget;
use App\Services\DocumentService;
use Filament\Actions;
use Filament\Forms;
use Filament\Pages\Page;

class RevenueReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Pemasukan';

    protected static ?string $title = 'Laporan Pemasukan';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.revenue-report';

    public ?string $filter_type = 'period';

    public ?string $period = 'this_month';

    public ?string $from_date = null;

    public ?string $until_date = null;

    public function resetFilters(): void
    {
        $this->filter_type = 'period';
        $this->period = 'this_month';
        $this->from_date = null;
        $this->until_date = null;

        $this->dispatch('updateRevenueFilter',
            filter_type: $this->filter_type,
            period: $this->period,
            from_date: $this->from_date,
            until_date: $this->until_date
        );
    }

    protected function getHeaderActions(): array
    {
        $isCustomActive = $this->filter_type === 'custom_date' && ($this->from_date || $this->until_date);
        $isNonDefaultPeriod = $this->filter_type === 'period' && $this->period !== 'this_month';
        $activeFilterCount = ($isCustomActive || $isNonDefaultPeriod) ? 1 : 0;

        return [
            Actions\Action::make('generateReport')
                ->label('Generate Rekapitulasi Pemasukan')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->modalSubmitActionLabel('Generate')
                ->form([
                    Forms\Components\Select::make('format')
                        ->label('Pilih Format Dokumen')
                        ->options([
                            'pdf' => 'PDF Document (.pdf)',
                            'excel' => 'Excel Spreadsheet (.csv)',
                        ])
                        ->default('pdf')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $documentService = new DocumentService;
                    if ($data['format'] === 'pdf') {
                        return $documentService->downloadRevenueReportPdf(
                            $this->filter_type,
                            $this->period,
                            $this->from_date,
                            $this->until_date
                        );
                    } else {
                        return $documentService->downloadRevenueReportExcel(
                            $this->filter_type,
                            $this->period,
                            $this->from_date,
                            $this->until_date
                        );
                    }
                }),

            Actions\Action::make('filterDate')
                ->iconButton()
                ->icon('heroicon-o-funnel')
                ->tooltip('Filter Periode / Tanggal Pemasukan')
                ->color($activeFilterCount > 0 ? 'warning' : 'gray')
                ->badge($activeFilterCount > 0 ? (string) $activeFilterCount : '0')
                ->form([
                    Forms\Components\Select::make('filter_type')
                        ->label('Mode Filter Pemasukan')
                        ->options([
                            'period' => 'Berdasarkan Opsi Periode (Bulan/Tahun)',
                            'custom_date' => 'Berdasarkan Rentang Tanggal Spesifik',
                        ])
                        ->default($this->filter_type ?? 'period')
                        ->reactive()
                        ->required(),

                    Forms\Components\Select::make('period')
                        ->label('Filter Opsi Periode')
                        ->options([
                            'this_month' => 'Bulan Ini',
                            'last_month' => 'Bulan Kemarin',
                            '3_months' => '3 Bulan Terakhir',
                            '6_months' => '6 Bulan Terakhir',
                            'this_year' => 'Tahun Ini',
                        ])
                        ->default($this->period ?? 'this_month')
                        ->visible(fn (callable $get) => $get('filter_type') === 'period')
                        ->required(),

                    Forms\Components\DatePicker::make('from_date')
                        ->label('Dari Tanggal Pembayaran')
                        ->default($this->from_date)
                        ->visible(fn (callable $get) => $get('filter_type') === 'custom_date'),

                    Forms\Components\DatePicker::make('until_date')
                        ->label('Sampai Tanggal Pembayaran')
                        ->default($this->until_date)
                        ->visible(fn (callable $get) => $get('filter_type') === 'custom_date'),
                ])
                ->action(function (array $data) {
                    $this->filter_type = $data['filter_type'] ?? 'period';
                    $this->period = $data['period'] ?? 'this_month';
                    $this->from_date = $data['from_date'] ?? null;
                    $this->until_date = $data['until_date'] ?? null;

                    $this->dispatch('updateRevenueFilter',
                        filter_type: $this->filter_type,
                        period: $this->period,
                        from_date: $this->from_date,
                        until_date: $this->until_date
                    );
                }),

            Actions\Action::make('resetFilter')
                ->iconButton()
                ->icon('heroicon-o-arrow-path')
                ->tooltip('Reset Filter ke Bulan Ini')
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
            RevenueStatsWidget::class,
        ];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'filter_type' => $this->filter_type,
            'period' => $this->period,
            'from_date' => $this->from_date,
            'until_date' => $this->until_date,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            RevenueByPaymentTypeWidget::class,
            RevenueByMethodWidget::class,
        ];
    }

    protected function getFooterWidgetsData(): array
    {
        return [
            'filter_type' => $this->filter_type,
            'period' => $this->period,
            'from_date' => $this->from_date,
            'until_date' => $this->until_date,
        ];
    }
}
