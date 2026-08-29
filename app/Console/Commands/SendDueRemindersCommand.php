<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\WhatsAppLog;
use App\Services\WhatsAppAutomationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDueRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Catat dan siapkan notifikasi pengingat WhatsApp H-2 jatuh tempo tagihan';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppAutomationService $waService): int
    {
        $today = Carbon::now()->startOfDay();
        $targetDueDate = Carbon::now()->addDays(2)->endOfDay();

        // Cari tagihan belum lunas yang jatuh temponya dalam rentang hari ini s/d H-2
        $bills = Bill::whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])
            ->where('outstanding_amount', '>', 0)
            ->whereDate('due_date', '>=', $today)
            ->whereDate('due_date', '<=', $targetDueDate)
            ->with(['student.parent', 'paymentType'])
            ->get();

        $this->info("Menemukan {$bills->count()} tagihan dengan tanggal jatuh tempo s/d {$targetDueDate->format('d M Y')}.");

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($bills as $bill) {
            // Idempotency Check: Jangan duplikat log pengingat pada tanggal yang sama
            $alreadySent = WhatsAppLog::where('student_id', $bill->student_id)
                ->where('message_type', WhatsAppLog::TYPE_DUE_REMINDER)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if ($alreadySent) {
                $skippedCount++;

                continue;
            }

            $waService->logDueReminder($bill);
            $sentCount++;
        }

        $this->info("Proses selesai: {$sentCount} notifikasi WhatsApp dicatat | {$skippedCount} dilewati (sudah pernah dicatat hari ini).");

        return Command::SUCCESS;
    }
}
