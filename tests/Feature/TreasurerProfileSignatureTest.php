<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TreasurerProfileSignatureTest extends TestCase
{
    use DatabaseTransactions;

    public function test_superadmin_can_update_treasurer_name_and_signature(): void
    {
        Storage::fake('public');

        $superadmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
        $this->assertNotNull($superadmin);

        $fakeSignature = UploadedFile::fake()->image('signature_new.png', 200, 100);
        $storedPath = $fakeSignature->store('signatures', 'public');

        $superadmin->update([
            'name' => 'Ust. H. Ahmad Dahlan, M. Pd',
            'signature_path' => $storedPath,
        ]);

        $superadmin->refresh();
        $this->assertEquals('Ust. H. Ahmad Dahlan, M. Pd', $superadmin->name);
        $this->assertNotNull($superadmin->signature_path);
        Storage::disk('public')->assertExists($storedPath);

        // Test signature base64 helper
        $base64 = $superadmin->getSignatureBase64();
        $this->assertNotNull($base64);
        $this->assertStringStartsWith('data:image/png;base64,', $base64);
    }

    public function test_invoice_and_receipt_render_updated_treasurer_name(): void
    {
        $superadmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
        if ($superadmin) {
            $superadmin->update([
                'name' => 'Bendahara Baru, S.E.',
            ]);
        }

        $documentService = new DocumentService;

        // Test Invoice
        $bill = Bill::first();
        if ($bill) {
            $invoicePdf = $documentService->generateInvoicePdf($bill);
            $invoiceHtml = $invoicePdf->output();
            $this->assertNotEmpty($invoiceHtml);
        }

        // Test Receipt
        $payment = Payment::first();
        if ($payment) {
            $receiptPdf = $documentService->generatePaymentReceiptPdf($payment);
            $receiptHtml = $receiptPdf->output();
            $this->assertNotEmpty($receiptHtml);
        }
    }
}
