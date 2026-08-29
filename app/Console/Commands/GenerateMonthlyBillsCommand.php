<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyBillsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spp:generate {--month= : Periode bulan (format YYYY-MM)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate tagihan SPP bulanan secara otomatis dan idempotent untuk seluruh siswa aktif';

    /**
     * Execute the console command.
     */
    public function handle(BillingService $billingService): int
    {
        $monthInput = $this->option('month');
        $billingPeriod = $monthInput ? Carbon::parse($monthInput)->startOfMonth() : Carbon::now()->startOfMonth();

        $this->info('Menjalankan generate SPP untuk periode: '.$billingPeriod->format('F Y'));

        $result = $billingService->generateMonthlyBills(billingPeriod: $billingPeriod);

        $this->info("Berhasil! Tagihan baru dibuat: {$result['created']} | Dilewati (sudah ada): {$result['skipped']} | Error: {$result['errors']}");

        return Command::SUCCESS;
    }
}
