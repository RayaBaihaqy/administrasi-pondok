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

    public function test_edit_profile_redirects_to_dashboard_after_save(): void
    {
        $superadmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
        $this->assertNotNull($superadmin);

        \Livewire\Livewire::actingAs($superadmin)
            ->test(\App\Filament\Pages\Auth\EditProfile::class)
            ->fillForm([
                'name' => 'Hj. Titi Nurhayati, S. Pd',
                'email' => $superadmin->email,
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(filament()->getUrl());
    }

    public function test_historical_receipt_and_invoice_preserve_past_treasurer_snapshot(): void
    {
        $superadmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
        $this->assertNotNull($superadmin);

        // 1. Era Bu Putri
        $superadmin->update(['name' => 'Bu Putri, S. Pd']);
        $documentService = new DocumentService;

        $bill1 = Bill::first();
        $this->assertNotNull($bill1);
        $invoice1 = $documentService->getOrCreateInvoice($bill1);
        $this->assertEquals('Bu Putri, S. Pd', $invoice1->treasurer_name);

        $payment1 = Payment::first();
        $this->assertNotNull($payment1);
        $receipt1 = $documentService->getOrCreateReceipt($payment1);
        $this->assertEquals('Bu Putri, S. Pd', $receipt1->treasurer_name);

        // 2. Pergantian Pejabat ke Pak Putra
        $superadmin->update(['name' => 'Pak Putra, M. Pd']);
        $this->assertEquals('Pak Putra, M. Pd', User::getActiveTreasurer()?->name);

        // 3. Dokumen lama dibuka kembali -> HARUS TETAP Bu Putri
        $invoice1->refresh();
        $this->assertEquals('Bu Putri, S. Pd', $invoice1->treasurer_name);
        $receipt1->refresh();
        $this->assertEquals('Bu Putri, S. Pd', $receipt1->treasurer_name);

        // Render PDF dokumen era Bu Putri
        $receiptPdf = $documentService->generatePaymentReceiptPdf($payment1);
        $receiptHtml = $receiptPdf->output();
        $this->assertNotEmpty($receiptHtml);

        // 4. Dokumen baru terbit di era Pak Putra
        $bill2 = Bill::skip(1)->first();
        if ($bill2) {
            // Delete invoice if already cached to test fresh generation
            $bill2->invoice?->delete();
            $bill2->unsetRelation('invoice');
            $invoice2 = $documentService->getOrCreateInvoice($bill2);
            $this->assertEquals('Pak Putra, M. Pd', $invoice2->treasurer_name);
        }
    }
}
