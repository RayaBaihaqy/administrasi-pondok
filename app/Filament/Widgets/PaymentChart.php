<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class PaymentChart extends ChartWidget
{
    protected ?string $heading = 'Monitoring Pembayaran SPP';

    protected ?string $description = 'Total pembayaran SPP per bulan tahun ini';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $months = collect(range(1, 12));

        $payments = Payment::query()
            ->where('status', Payment::STATUS_SUCCESS)
            ->whereYear('paid_at', now()->year)
            ->whereHas('bill.paymentType', function ($query) {
                $query->where('code', 'SPP');
            })
            ->selectRaw('MONTH(paid_at) as month')
            ->selectRaw('SUM(amount) as total')
            ->groupByRaw('MONTH(paid_at)')
            ->pluck('total', 'month');

        return [
            'datasets' => [
                [
                    'label' => 'Pembayaran SPP (Rp)',
                    'data' => $months
                        ->map(fn ($month) => $payments->get($month, 0))
                        ->values()
                        ->toArray(),
                    'backgroundColor' => '#2e7d32',
                    'borderColor' => '#1b5e20',
                ],
            ],

            'labels' => [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
