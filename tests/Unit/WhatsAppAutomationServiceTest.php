<?php

namespace Tests\Unit;

use App\Models\Bill;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use App\Models\User;
use App\Models\WhatsAppLog;
use App\Services\WhatsAppAutomationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WhatsAppAutomationServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_whatsapp_automation_service_instantiation(): void
    {
        $service = new WhatsAppAutomationService;
        $this->assertInstanceOf(WhatsAppAutomationService::class, $service);
    }

    public function test_create_whatsapp_url_normalizes_indonesian_phone_numbers(): void
    {
        $url1 = WhatsAppAutomationService::createWhatsAppUrl('081234567890', 'Halo Test');
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url1);
        $this->assertStringContainsString('Halo%20Test', $url1);

        $url2 = WhatsAppAutomationService::createWhatsAppUrl('+62 812-3456-7890', 'Pesan');
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url2);

        $url3 = WhatsAppAutomationService::createWhatsAppUrl('81234567890', 'Pesan Tanpa 0');
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url3);

        $log = new WhatsAppLog(['phone_number' => '081234567890', 'message_content' => 'Test Accessor']);
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $log->whatsapp_url);
    }

    public function test_format_new_bill_message_uses_parent_full_name(): void
    {
        $user = User::create([
            'name' => 'Wali Test',
            'email' => 'walitest_'.uniqid().'@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PARENT,
        ]);

        $parent = ParentProfile::create([
            'user_id' => $user->id,
            'full_name' => 'Bapak Ahmad Ridwan',
            'phone' => '081299998888',
        ]);

        $student = Student::create([
            'nis' => '9991112223',
            'full_name' => 'Ananda Fatih',
            'gender' => 'male',
            'class_level' => 7,
            'rombel' => '1',
            'status' => Student::STATUS_ACTIVE,
            'parent_id' => $parent->id,
        ]);

        $paymentType = PaymentType::firstOrCreate(
            ['code' => 'TEST_SPP'],
            ['name' => 'SPP Bulanan Test', 'billing_type' => 'monthly', 'default_amount' => 350000]
        );

        $bill = Bill::create([
            'student_id' => $student->id,
            'parent_id' => $parent->id,
            'payment_type_id' => $paymentType->id,
            'academic_year_id' => \App\Models\AcademicYear::first()?->id ?? 1,
            'billing_period' => now()->startOfMonth(),
            'billing_date' => now(),
            'due_date' => now()->addDays(10),
            'amount' => 350000,
            'paid_amount' => 0,
            'outstanding_amount' => 350000,
            'status' => Bill::STATUS_UNPAID,
        ]);

        $service = new WhatsAppAutomationService;
        $data = $service->formatNewBillMessage($bill);

        $this->assertEquals('Bapak Ahmad Ridwan', $data['recipient_name']);
        $this->assertEquals('081299998888', $data['phone_number']);
        $this->assertStringContainsString('Bapak Ahmad Ridwan', $data['message']);
        $this->assertStringContainsString('Ananda Fatih', $data['message']);
        $this->assertStringContainsString('/docs/invoice/', $data['message']);
        $this->assertStringContainsString('Bank BRI', $data['message']);
    }
}
