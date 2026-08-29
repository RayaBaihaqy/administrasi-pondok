<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Generate Kuitansi PDF untuk transaksi pembayaran tertentu.
     */
    public function generatePaymentReceiptPdf(Payment $payment)
    {
        $payment->loadMissing(['student', 'bill.paymentType', 'bill.academicYear', 'recorder', 'receipt']);

        $receipt = $payment->receipt;
        $receiptNumber = $receipt?->receipt_number ?? ($payment->payment_number ?? ('PAY-'.str_pad($payment->id, 6, '0', STR_PAD_LEFT)));
        $printDate = now()->translatedFormat('d F Y');

        $pdf = Pdf::loadView('pdf.receipt', [
            'payment' => $payment,
            'receipt' => $receipt,
            'receiptNumber' => $receiptNumber,
            'printDate' => $printDate,
        ]);

        return $pdf;
    }

    /**
     * Stream PDF Kuitansi langsung ke browser (Inline View).
     */
    public function streamPaymentReceipt(Payment $payment)
    {
        $pdf = $this->generatePaymentReceiptPdf($payment);
        $receiptNumber = $payment->payment_number.'.pdf';

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            $receiptNumber,
            ['Content-Type' => 'application/pdf'],
            'inline'
        );
    }

    /**
     * Download PDF Kuitansi sebagai attachment.
     */
    public function downloadPaymentReceipt(Payment $payment)
    {
        $pdf = $this->generatePaymentReceiptPdf($payment);
        $receiptNumber = $payment->payment_number.'.pdf';

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            $receiptNumber
        );
    }

    /**
     * Alias method untuk generate Kuitansi PDF.
     */
    public function generateReceiptPdf(Payment $payment)
    {
        return $this->generatePaymentReceiptPdf($payment);
    }

    /**
     * Dapatkan atau buat record Receipt Eloquent dan simpan PDF ke disk public.
     */
    public function getOrCreateReceipt(Payment $payment): Receipt
    {
        $payment->loadMissing(['receipt']);

        if ($payment->receipt) {
            return $payment->receipt;
        }

        $receiptNumber = 'REC-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        $filePath = 'receipts/'.$receiptNumber.'.pdf';

        $pdf = $this->generatePaymentReceiptPdf($payment);
        Storage::disk('public')->put($filePath, $pdf->output());

        return Receipt::create([
            'payment_id' => $payment->id,
            'receipt_number' => $receiptNumber,
            'file_path' => $filePath,
            'generated_at' => now(),
        ]);
    }

    /**
     * Generate Invoice PDF untuk tagihan tertentu.
     */
    public function generateInvoicePdf(Bill $bill)
    {
        $bill->loadMissing(['student', 'paymentType', 'academicYear', 'parentProfile']);
        $invoice = $this->getOrCreateInvoice($bill);
        $printDate = now()->translatedFormat('d F Y');

        return Pdf::loadView('pdf.invoice', [
            'bill' => $bill,
            'invoice' => $invoice,
            'printDate' => $printDate,
        ]);
    }

    /**
     * Dapatkan atau buat record Invoice Eloquent dan simpan PDF ke disk public.
     */
    public function getOrCreateInvoice(Bill $bill): Invoice
    {
        $bill->loadMissing(['student', 'paymentType', 'academicYear', 'parentProfile', 'invoice']);

        if ($bill->invoice) {
            return $bill->invoice;
        }

        $invoiceNumber = 'INV-'.now()->format('Ym').'-'.Str::upper(Str::random(6));
        $filePath = 'invoices/'.$invoiceNumber.'.pdf';
        $printDate = now()->translatedFormat('d F Y');

        $invoice = new Invoice([
            'bill_id' => $bill->id,
            'invoice_number' => $invoiceNumber,
            'file_path' => $filePath,
            'generated_at' => now(),
        ]);

        $pdf = Pdf::loadView('pdf.invoice', [
            'bill' => $bill,
            'invoice' => $invoice,
            'printDate' => $printDate,
        ]);

        Storage::disk('public')->put($filePath, $pdf->output());
        $invoice->save();

        return $invoice;
    }

    /**
     * Download Invoice Tagihan PDF sebagai attachment.
     */
    public function downloadBillInvoice(Bill $bill)
    {
        $pdf = $this->generateInvoicePdf($bill);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            $bill->bill_number.'.pdf'
        );
    }

    /**
     * Download Rekapitulasi Pemasukan dalam format PDF.
     */
    public function downloadRevenueReportPdf(?string $filterType = 'period', ?string $period = 'this_month', ?string $fromDate = null, ?string $untilDate = null)
    {
        $periodLabel = $this->resolveRevenuePeriodLabel($filterType, $period, $fromDate, $untilDate);

        $paymentsQuery = Payment::query()
            ->where('status', Payment::STATUS_SUCCESS)
            ->whereNull('deleted_at');
        $this->applyRevenueFilter($paymentsQuery, $filterType, $period, $fromDate, $untilDate, 'paid_at');

        $payments = (clone $paymentsQuery)->with(['student', 'bill.paymentType'])->get();

        $totalAmount = (clone $paymentsQuery)->sum('amount');
        $midtransAmount = (clone $paymentsQuery)->where('source', Payment::SOURCE_MIDTRANS)->sum('amount');
        $manualAmount = (clone $paymentsQuery)->where('source', Payment::SOURCE_MANUAL)->sum('amount');

        $byType = \Illuminate\Support\Facades\DB::table('payments')
            ->join('bills', 'payments.bill_id', '=', 'bills.id')
            ->join('payment_types', 'bills.payment_type_id', '=', 'payment_types.id')
            ->where('payments.status', Payment::STATUS_SUCCESS)
            ->whereNull('payments.deleted_at');
        $this->applyRevenueFilter($byType, $filterType, $period, $fromDate, $untilDate, 'payments.paid_at');
        $byType = $byType->select(
            'payment_types.name as type_name',
            \Illuminate\Support\Facades\DB::raw('SUM(payments.amount) as total_amount'),
            \Illuminate\Support\Facades\DB::raw('COUNT(payments.id) as total_count')
        )
            ->groupBy('payment_types.id', 'payment_types.name')
            ->get();

        $byMethod = \Illuminate\Support\Facades\DB::table('payments')
            ->where('payments.status', Payment::STATUS_SUCCESS)
            ->whereNull('payments.deleted_at');
        $this->applyRevenueFilter($byMethod, $filterType, $period, $fromDate, $untilDate, 'payments.paid_at');
        $byMethod = $byMethod->select(
            \Illuminate\Support\Facades\DB::raw('COALESCE(method, source) as method_key'),
            \Illuminate\Support\Facades\DB::raw('SUM(amount) as total_amount'),
            \Illuminate\Support\Facades\DB::raw('COUNT(id) as total_count')
        )
            ->groupBy(\Illuminate\Support\Facades\DB::raw('COALESCE(method, source)'))
            ->get();

        $pdf = Pdf::loadView('pdf.revenue-rekap', compact(
            'periodLabel',
            'totalAmount',
            'midtransAmount',
            'manualAmount',
            'byType',
            'byMethod',
            'payments'
        ));

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'Rekapitulasi_Pemasukan_'.now()->format('Ymd_His').'.pdf'
        );
    }

    /**
     * Download Rekapitulasi Pemasukan dalam format Clean CSV (Kompatibel 100% dengan Power Query & Excel).
     */
    public function downloadRevenueReportExcel(?string $filterType = 'period', ?string $period = 'this_month', ?string $fromDate = null, ?string $untilDate = null)
    {
        $paymentsQuery = Payment::query()
            ->where('status', Payment::STATUS_SUCCESS)
            ->whereNull('deleted_at');
        $this->applyRevenueFilter($paymentsQuery, $filterType, $period, $fromDate, $untilDate, 'paid_at');

        $payments = $paymentsQuery->with(['student', 'bill.paymentType'])->get();

        $filename = 'Rekapitulasi_Pemasukan_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM
            fwrite($file, "\xEF\xBB\xBF");

            // Header langsung pada baris pertama agar Power Query / Get Data mendeteksi kolom secara otomatis
            fputcsv($file, [
                'No. Transaksi',
                'Waktu Pembayaran',
                'NIS',
                'Nama Siswa',
                'Jenis Tagihan',
                'Metode Pembayaran',
                'Nominal (Rp)',
            ]);

            $total = 0;
            foreach ($payments as $p) {
                $total += $p->amount;
                fputcsv($file, [
                    $p->payment_number,
                    \Carbon\Carbon::parse($p->paid_at)->format('d/m/Y H:i'),
                    $p->student?->nis ?? '-',
                    $p->student?->full_name ?? '-',
                    $p->bill?->paymentType?->name ?? '-',
                    strtoupper($p->method ?? $p->source),
                    $p->amount,
                ]);
            }

            fputcsv($file, [
                'TOTAL PEMASUKAN',
                '',
                '',
                '',
                '',
                '',
                $total,
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function resolveRevenuePeriodLabel(?string $filterType, ?string $period, ?string $fromDate, ?string $untilDate): string
    {
        if ($filterType === 'custom_date' || ($fromDate || $untilDate)) {
            if ($fromDate && $untilDate) {
                return \Carbon\Carbon::parse($fromDate)->format('d/m/Y').' - '.\Carbon\Carbon::parse($untilDate)->format('d/m/Y');
            } elseif ($untilDate) {
                return 's/d '.\Carbon\Carbon::parse($untilDate)->format('d/m/Y');
            } elseif ($fromDate) {
                return 'Mulai '.\Carbon\Carbon::parse($fromDate)->format('d/m/Y');
            }
        }

        $periodLabels = [
            'this_month' => 'Bulan Ini ('.now()->translatedFormat('F Y').')',
            'last_month' => 'Bulan Kemarin ('.now()->subMonth()->startOfMonth()->format('d/m/Y').' - '.now()->subMonth()->endOfMonth()->format('d/m/Y').')',
            '3_months' => '3 Bulan Terakhir',
            '6_months' => '6 Bulan Terakhir',
            'this_year' => 'Tahun Ini ('.now()->year.')',
        ];

        return $periodLabels[$period] ?? $periodLabels['this_month'];
    }

    private function applyRevenueFilter($query, ?string $filterType, ?string $period, ?string $fromDate, ?string $untilDate, string $column = 'paid_at')
    {
        if ($filterType === 'custom_date' || ($fromDate || $untilDate)) {
            if ($fromDate) {
                $query->whereDate($column, '>=', $fromDate);
            }
            if ($untilDate) {
                $query->whereDate($column, '<=', $untilDate);
            }

            return $query;
        }

        return match ($period) {
            'this_month' => $query->whereMonth($column, now()->month)->whereYear($column, now()->year),
            'last_month' => $query->whereMonth($column, now()->subMonth()->month)->whereYear($column, now()->subMonth()->year),
            '3_months' => $query->where($column, '>=', now()->subMonths(3)->startOfDay()),
            '6_months' => $query->where($column, '>=', now()->subMonths(6)->startOfDay()),
            'this_year' => $query->whereYear($column, now()->year),
            default => $query->whereMonth($column, now()->month)->whereYear($column, now()->year),
        };
    }


    /**
     * Download Rekapitulasi Tunggakan dalam format PDF.
     */
    public function downloadOutstandingReportPdf(string $period = 'this_month')
    {
        $periodLabels = [
            'this_month' => 'Bulan Ini ('.now()->translatedFormat('F Y').')',
            'last_month' => 'Bulan Kemarin ('.now()->subMonth()->translatedFormat('F Y').')',
            '3_months' => '3 Bulan Terakhir',
            '6_months' => '6 Bulan Terakhir',
            'this_year' => 'Tahun Ini ('.now()->year.')',
        ];

        $periodLabel = $periodLabels[$period] ?? $periodLabels['this_month'];

        $billsQuery = Bill::query()
            ->whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])
            ->whereNull('deleted_at');
        $this->applyRevenueFilter($billsQuery, 'period', $period, null, null, 'created_at');

        $unpaidBills = (clone $billsQuery)->with(['student', 'paymentType'])->get();
        $totalOutstanding = (clone $billsQuery)->sum('outstanding_amount');

        $overdueBillsQuery = Bill::query()
            ->where(function ($query) {
                $query->where('status', Bill::STATUS_OVERDUE)
                    ->orWhere(function ($q) {
                        $q->where('status', Bill::STATUS_UNPAID)
                            ->where('due_date', '<', now()->startOfDay());
                    });
            })
            ->whereNull('deleted_at');
        $this->applyRevenueFilter($overdueBillsQuery, 'period', $period, null, null, 'created_at');

        $overdueBills = (clone $overdueBillsQuery)->with(['student', 'paymentType'])->get();
        $overdueOutstanding = (clone $overdueBillsQuery)->sum('outstanding_amount');

        $pdf = Pdf::loadView('pdf.outstanding-rekap', compact(
            'periodLabel',
            'totalOutstanding',
            'overdueOutstanding',
            'unpaidBills',
            'overdueBills'
        ));

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'Rekapitulasi_Tunggakan_'.Str::slug($period).'_'.now()->format('Ymd').'.pdf'
        );
    }

    /**
     * Download Rekapitulasi Tunggakan dalam format Clean CSV (Kompatibel 100% dengan Power Query & Excel).
     */
    public function downloadOutstandingReportExcel(string $period = 'this_month')
    {
        $billsQuery = Bill::query()
            ->whereIn('status', [Bill::STATUS_UNPAID, Bill::STATUS_OVERDUE])
            ->whereNull('deleted_at');
        $this->applyRevenueFilter($billsQuery, 'period', $period, null, null, 'created_at');

        $unpaidBills = $billsQuery->with(['student', 'paymentType'])->get();

        $filename = 'Rekapitulasi_Tunggakan_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($unpaidBills) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM
            fwrite($file, "\xEF\xBB\xBF");

            // Header langsung pada baris pertama
            fputcsv($file, [
                'No. Tagihan',
                'NIS',
                'Nama Siswa',
                'Kelas',
                'Jenis Tagihan',
                'Jatuh Tempo',
                'Status',
                'Sisa Tunggakan (Rp)',
            ]);

            $total = 0;
            foreach ($unpaidBills as $b) {
                $total += $b->outstanding_amount;
                fputcsv($file, [
                    $b->bill_number,
                    $b->student?->nis ?? '-',
                    $b->student?->full_name ?? '-',
                    $b->student?->class_rombel ?? '-',
                    $b->paymentType?->name ?? '-',
                    \Carbon\Carbon::parse($b->due_date)->format('d/m/Y'),
                    Bill::STATUSES[$b->status] ?? $b->status,
                    $b->outstanding_amount,
                ]);
            }

            fputcsv($file, [
                'TOTAL SISA TUNGGAKAN',
                '',
                '',
                '',
                '',
                '',
                '',
                $total,
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
