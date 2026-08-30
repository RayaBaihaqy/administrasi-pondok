<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Services\DocumentService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    protected DocumentService $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Download / View PDF Invoice Tagihan Resmi.
     */
    public function downloadInvoice(string $billNumber): StreamedResponse
    {
        $bill = Bill::where('bill_number', $billNumber)
            ->with(['student.parentProfile', 'paymentType', 'academicYear', 'billItems'])
            ->firstOrFail();

        return $this->documentService->downloadBillInvoice($bill);
    }

    /**
     * Download / View PDF Kuitansi Pembayaran Sah Berstempel.
     */
    public function downloadReceipt(string $paymentNumber): StreamedResponse
    {
        $payment = Payment::where('payment_number', $paymentNumber)
            ->with(['student.parentProfile', 'bill.paymentType', 'bill.academicYear', 'recorder'])
            ->firstOrFail();

        return $this->documentService->downloadPaymentReceipt($payment);
    }
}
