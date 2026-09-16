<?php

namespace Tests\Unit;

use App\Models\PaymentType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaymentTypeClientListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Verifikasi 11 Daftar Pembayaran Resmi dari Klien MTs Miftahul 'Ulum.
     */
    public function test_all_11_client_payment_types_exist_with_exact_nominals(): void
    {
        $expectedNominals = [
            PaymentType::CODE_SPP => 75000,
            PaymentType::CODE_PENDAFTARAN_BARU => 1190000,
            PaymentType::CODE_DAFTAR_ULANG_GANJIL => 440000,
            PaymentType::CODE_ASTS_PTS_GANJIL => 75000,
            PaymentType::CODE_ASAS_GANJIL => 150000,
            PaymentType::CODE_LDKS => 450000,
            PaymentType::CODE_DAFTAR_ULANG_GENAP => 440000,
            PaymentType::CODE_ASTS_PTS_GENAP => 75000,
            PaymentType::CODE_ASATA_PAT => 150000,
            PaymentType::CODE_STUDY_TOUR => 450000,
            PaymentType::CODE_AKHIR_TAHUN => 2000000,
        ];

        foreach ($expectedNominals as $code => $expectedAmount) {
            $paymentType = PaymentType::where('code', $code)->first();
            $this->assertNotNull($paymentType, "Payment type dengan kode '{$code}' harus ada di database.");
            $this->assertEquals($expectedAmount, $paymentType->default_amount, "Nominal untuk '{$code}' harus Rp {$expectedAmount}");
        }

        $this->assertEquals(11, PaymentType::count(), 'Total jenis pembayaran harus berjumlah tepat 11.');
    }
}
