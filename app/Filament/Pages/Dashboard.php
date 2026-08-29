<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PaymentChart;
use App\Filament\Widgets\PaymentChartNonSpp;
use App\Filament\Widgets\RecentPayments;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UnpaidBillsWidget;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected string $view = 'filament.pages.dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    public function getHeading(): string
    {
        return 'Dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            PaymentChart::class,
            PaymentChartNonSpp::class,
            RecentPayments::class,
            UnpaidBillsWidget::class,
        ];
    }

    protected function getColumns(): int|array
    {
        return [
            'md' => 2,
            'xl' => 2,
        ];
    }
}
