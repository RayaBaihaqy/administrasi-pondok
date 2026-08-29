<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Builder query Laporan Pembayaran Transaksi.
     */
    public function getPaymentReportQuery(array $filters = []): Builder
    {
        $query = Payment::query()->with([
            'bill',
            'student',
            'parentProfile',
            'recorder',
            'bill.paymentType',
            'bill.academicYear',
        ]);

        if (! empty($filters['academic_year_id'])) {
            $query->whereHas('bill', fn ($q) => $q->where('academic_year_id', $filters['academic_year_id']));
        }

        if (! empty($filters['payment_type_id'])) {
            $query->whereHas('bill', fn ($q) => $q->where('payment_type_id', $filters['payment_type_id']));
        }

        if (! empty($filters['class_level'])) {
            $query->whereHas('student', fn ($q) => $q->where('class_level', $filters['class_level']));
        }

        if (! empty($filters['rombel'])) {
            $query->whereHas('student', fn ($q) => $q->where('rombel', $filters['rombel']));
        }

        if (! empty($filters['source'])) {
            $query->where('payments.source', $filters['source']);
        }

        if (! empty($filters['status'])) {
            $query->where('payments.status', $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('payments.paid_at', '>=', Carbon::parse($filters['date_from']));
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('payments.paid_at', '<=', Carbon::parse($filters['date_to']));
        }

        return $query;
    }

    /**
     * Kalkulasi Laporan Pemasukan (Revenue Report) - HANYA menghitung pembayaran BERHASIL.
     */
    public function getRevenueReport(array $filters = []): array
    {
        $query = $this->getPaymentReportQuery($filters)
            ->where('payments.status', Payment::STATUS_SUCCESS);

        $totalRevenue = (int) $query->sum('payments.amount');
        $transactionCount = (int) $query->count();

        // Revenue per sumber (Midtrans vs Manual)
        $midtransRevenue = (int) (clone $query)->where('payments.source', Payment::SOURCE_MIDTRANS)->sum('payments.amount');
        $manualRevenue = (int) (clone $query)->where('payments.source', Payment::SOURCE_MANUAL)->sum('payments.amount');

        // Revenue per jenis pembayaran (standalone query to avoid double-join)
        $byPaymentTypeQuery = DB::table('payments')
            ->join('bills', 'payments.bill_id', '=', 'bills.id')
            ->join('payment_types', 'bills.payment_type_id', '=', 'payment_types.id')
            ->where('payments.status', Payment::STATUS_SUCCESS)
            ->whereNull('payments.deleted_at');

        if (! empty($filters['academic_year_id'])) {
            $byPaymentTypeQuery->where('bills.academic_year_id', $filters['academic_year_id']);
        }
        if (! empty($filters['payment_type_id'])) {
            $byPaymentTypeQuery->where('bills.payment_type_id', $filters['payment_type_id']);
        }
        if (! empty($filters['date_from'])) {
            $byPaymentTypeQuery->whereDate('payments.paid_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $byPaymentTypeQuery->whereDate('payments.paid_at', '<=', $filters['date_to']);
        }

        $byPaymentType = $byPaymentTypeQuery
            ->select('payment_types.name as type_name', DB::raw('SUM(payments.amount) as total'), DB::raw('COUNT(payments.id) as count'))
            ->groupBy('payment_types.id', 'payment_types.name')
            ->get()
            ->toArray();

        // Revenue per metode
        $byMethod = (clone $query)
            ->select('payments.method', DB::raw('SUM(payments.amount) as total'), DB::raw('COUNT(payments.id) as count'))
            ->groupBy('payments.method')
            ->get()
            ->toArray();

        return [
            'total_revenue' => $totalRevenue,
            'transaction_count' => $transactionCount,
            'midtrans_revenue' => $midtransRevenue,
            'manual_revenue' => $manualRevenue,
            'by_payment_type' => $byPaymentType,
            'by_method' => $byMethod,
        ];
    }

    /**
     * Builder query Laporan Tunggakan Tagihan (Outstanding Bills).
     */
    public function getOutstandingReportQuery(array $filters = []): Builder
    {
        $query = Bill::query()
            ->with(['student', 'parentProfile', 'paymentType', 'academicYear'])
            ->where('bills.outstanding_amount', '>', 0)
            ->where('bills.status', '!=', Bill::STATUS_CANCELLED);

        if (! empty($filters['academic_year_id'])) {
            $query->where('bills.academic_year_id', $filters['academic_year_id']);
        }

        if (! empty($filters['payment_type_id'])) {
            $query->where('bills.payment_type_id', $filters['payment_type_id']);
        }

        if (! empty($filters['class_level'])) {
            $query->whereHas('student', fn ($q) => $q->where('class_level', $filters['class_level']));
        }

        if (! empty($filters['rombel'])) {
            $query->whereHas('student', fn ($q) => $q->where('rombel', $filters['rombel']));
        }

        if (! empty($filters['status'])) {
            $query->where('bills.status', $filters['status']);
        }

        return $query;
    }

    /**
     * Summary Laporan Tunggakan.
     */
    public function getOutstandingSummary(array $filters = []): array
    {
        $query = $this->getOutstandingReportQuery($filters);

        $totalOutstanding = (int) $query->sum('bills.outstanding_amount');
        $billCount = (int) $query->count();
        $overdueCount = (int) (clone $query)->where('bills.status', Bill::STATUS_OVERDUE)->count();
        $overdueAmount = (int) (clone $query)->where('bills.status', Bill::STATUS_OVERDUE)->sum('bills.outstanding_amount');

        return [
            'total_outstanding' => $totalOutstanding,
            'bill_count' => $billCount,
            'overdue_count' => $overdueCount,
            'overdue_amount' => $overdueAmount,
        ];
    }
}
