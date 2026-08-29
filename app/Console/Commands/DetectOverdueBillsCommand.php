<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Services\WhatsAppAutomationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DetectOverdueBillsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:detect-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mendeteksi tagihan yang melewati jatuh tempo dan memperbarui statusnya menjadi overdue';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppAutomationService $waService): int
    {
        $today = Carbon::now()->startOfDay();

        $unpaidBills = Bill::where('status', Bill::STATUS_UNPAID)
            ->where('outstanding_amount', '>', 0)
            ->whereDate('due_date', '<', $today)
            ->with(['student.parentProfile', 'paymentType'])
            ->get();

        $count = 0;
        foreach ($unpaidBills as $bill) {
            $bill->status = Bill::STATUS_OVERDUE;
            $bill->save();

            // Catat notifikasi pengingat WhatsApp
            try {
                $waService->logDueReminder($bill);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to log WA overdue reminder for bill #{$bill->id}: ".$e->getMessage());
            }

            $count++;
        }

        $this->info("Pemeriksaan selesai. {$count} tagihan diperbarui statusnya menjadi Terlambat (Overdue) dan notifikasi WhatsApp telah disiapkan.");

        return Command::SUCCESS;
    }
}
