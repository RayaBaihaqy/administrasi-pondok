<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\PaymentType;
use App\Models\Student;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_billing_service_instantiation(): void
    {
        $service = new BillingService;
        $this->assertInstanceOf(BillingService::class, $service);
    }

    public function test_installment_amount_calculation_math(): void
    {
        $totalAmount = 1500000;
        $tenorCount = 3;

        $baseAmount = intdiv($totalAmount, $tenorCount);
        $remainder = $totalAmount % $tenorCount;

        $this->assertEquals(500000, $baseAmount);
        $this->assertEquals(0, $remainder);

        // Uneven split test
        $totalAmount2 = 1000000;
        $tenorCount2 = 3;

        $baseAmount2 = intdiv($totalAmount2, $tenorCount2);
        $remainder2 = $totalAmount2 % $tenorCount2;

        $firstInstallment = $baseAmount2 + $remainder2;
        $this->assertEquals(333334, $firstInstallment);
        $this->assertEquals(333333, $baseAmount2);
        $this->assertEquals(1000000, $firstInstallment + ($baseAmount2 * 2));
    }

    public function test_generate_bulk_bills_for_specific_class_level(): void
    {
        $service = new BillingService;
        $academicYear = AcademicYear::current() ?? AcademicYear::first();
        $paymentType = PaymentType::firstOrCreate(
            ['code' => 'TEST_BULK_ST'],
            ['name' => 'Studi Tour Test', 'billing_type' => 'one_time', 'default_amount' => 750000]
        );

        $period = '2099-01-01';

        // Target hanya Kelas 8
        $res = $service->generateBulkBills(
            paymentType: $paymentType,
            academicYear: $academicYear,
            billingPeriod: $period,
            classLevel: 8
        );

        $class8Count = Student::active()->where('class_level', 8)->count();
        $this->assertEquals($class8Count, $res['created']);

        // Idempotency: Jalankan kedua kali harus 0 created, semua skipped
        $res2 = $service->generateBulkBills(
            paymentType: $paymentType,
            academicYear: $academicYear,
            billingPeriod: $period,
            classLevel: 8
        );

        $this->assertEquals(0, $res2['created']);
        $this->assertEquals($class8Count, $res2['skipped']);
    }
}
