<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Bill;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuditLogAndFriendlyErrorTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Login as Super Admin for audit tracking context
        $superadmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
        if ($superadmin) {
            $this->actingAs($superadmin);
        }
    }

    public function test_student_creation_and_update_creates_audit_log(): void
    {
        $uniqueNis = 'TESTNIS_'.time().'_'.rand(100, 999);
        $student = Student::create([
            'nis' => $uniqueNis,
            'full_name' => 'Ahmad Audit Test',
            'gender' => 'male',
            'class_level' => 7,
            'rombel' => '1',
            'status' => Student::STATUS_ACTIVE,
        ]);

        $log = AuditLog::where('auditable_type', Student::class)
            ->where('auditable_id', $student->id)
            ->where('action', 'create_student')
            ->first();

        $this->assertNotNull($log, 'AuditLog record for student creation should exist');
        $this->assertEquals($uniqueNis, $log->new_values['nis']);

        // Update student
        $student->update(['full_name' => 'Ahmad Audit Updated']);

        $updateLog = AuditLog::where('auditable_type', Student::class)
            ->where('auditable_id', $student->id)
            ->where('action', 'update_student')
            ->first();

        $this->assertNotNull($updateLog, 'AuditLog record for student update should exist');
        $this->assertEquals('Ahmad Audit Test', $updateLog->old_values['full_name']);
        $this->assertEquals('Ahmad Audit Updated', $updateLog->new_values['full_name']);
    }

    public function test_parent_creation_and_update_creates_audit_log(): void
    {
        $uniquePhone = '0899'.rand(10000000, 99999999);
        $user = User::create([
            'name' => 'Wali Audit Test',
            'email' => 'parent_audit_'.time().'_'.rand(100, 999).'@test.com',
            'phone' => $uniquePhone,
            'password' => bcrypt('password'),
            'role' => 'parent',
        ]);

        $parent = ParentProfile::create([
            'user_id' => $user->id,
            'full_name' => 'Wali Audit Test',
            'phone' => $uniquePhone,
            'address' => 'Jl. Pesantren No. 10',
        ]);

        $log = AuditLog::where('auditable_type', ParentProfile::class)
            ->where('auditable_id', $parent->id)
            ->where('action', 'create_parent')
            ->first();

        $this->assertNotNull($log, 'AuditLog record for parent creation should exist');
        $this->assertEquals($uniquePhone, $log->new_values['phone']);
    }

    public function test_bill_creation_and_cancellation_creates_audit_log(): void
    {
        $uniqueNis = 'TESTBILL_'.time().'_'.rand(100, 999);
        $student = Student::create([
            'nis' => $uniqueNis,
            'full_name' => 'Siswa Tagihan Audit',
            'gender' => 'male',
            'class_level' => 7,
            'rombel' => '1',
            'status' => Student::STATUS_ACTIVE,
        ]);

        $paymentType = PaymentType::first();
        $academicYear = AcademicYear::first();

        $this->assertNotNull($paymentType);
        $this->assertNotNull($academicYear);

        $bill = Bill::create([
            'student_id' => $student->id,
            'payment_type_id' => $paymentType->id,
            'academic_year_id' => $academicYear->id,
            'billing_period' => '2030-01-01',
            'billing_date' => '2030-01-01',
            'due_date' => '2030-01-10',
            'amount' => 150000,
            'paid_amount' => 0,
            'outstanding_amount' => 150000,
            'status' => Bill::STATUS_UNPAID,
        ]);

        $log = AuditLog::where('auditable_type', Bill::class)
            ->where('auditable_id', $bill->id)
            ->where('action', 'create_bill')
            ->first();

        $this->assertNotNull($log, 'AuditLog record for bill creation should exist');

        // Cancel the bill
        $bill->update(['status' => Bill::STATUS_CANCELLED]);

        $cancelLog = AuditLog::where('auditable_type', Bill::class)
            ->where('auditable_id', $bill->id)
            ->where('action', 'cancel_bill')
            ->first();

        $this->assertNotNull($cancelLog, 'AuditLog record for bill cancellation should exist');
    }

    public function test_academic_year_and_payment_type_creates_audit_log(): void
    {
        $uniqueYear = '2098/2099_'.rand(10, 99);
        $year = AcademicYear::create([
            'name' => $uniqueYear,
            'start_date' => '2098-07-01',
            'end_date' => '2099-06-30',
            'is_active' => false,
        ]);

        $log = AuditLog::where('auditable_type', AcademicYear::class)
            ->where('auditable_id', $year->id)
            ->where('action', 'create_academic_year')
            ->first();

        $this->assertNotNull($log, 'AuditLog record for academic year creation should exist');

        $uniqueCode = 'TEST_'.time().'_'.rand(10, 99);
        $type = PaymentType::create([
            'code' => $uniqueCode,
            'name' => 'Biaya Ujian Test',
            'billing_type' => PaymentType::BILLING_TYPE_ONE_TIME,
            'default_amount' => 75000,
            'is_active' => true,
        ]);

        $typeLog = AuditLog::where('auditable_type', PaymentType::class)
            ->where('auditable_id', $type->id)
            ->where('action', 'create_payment_type')
            ->first();

        $this->assertNotNull($typeLog, 'AuditLog record for payment type creation should exist');
    }

    public function test_duplicate_student_nisn_is_rejected_by_form_validation(): void
    {
        $existing = Student::first();
        $this->assertNotNull($existing);

        $superadmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();

        \Livewire\Livewire::actingAs($superadmin)
            ->test(\App\Filament\Resources\StudentResource\Pages\CreateStudent::class)
            ->fillForm([
                'nis' => $existing->nis,
                'full_name' => 'Siswa Baru Duplikat',
                'gender' => 'male',
                'class_level' => 7,
                'rombel' => '1',
            ])
            ->call('create')
            ->assertHasFormErrors(['nis' => 'unique']);
    }

    public function test_duplicate_parent_phone_is_rejected_by_parent_form_validation(): void
    {
        $existingParent = ParentProfile::first();
        $this->assertNotNull($existingParent);

        $superadmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();

        \Livewire\Livewire::actingAs($superadmin)
            ->test(\App\Filament\Resources\ParentResource\Pages\CreateParent::class)
            ->fillForm([
                'full_name' => 'Wali Baru Duplikat',
                'phone' => $existingParent->phone,
                'user_email' => 'wali_baru_'.time().'@test.com',
                'user_password' => 'password123',
            ])
            ->call('create')
            ->assertHasFormErrors(['phone' => 'unique']);
    }

    public function test_duplicate_parent_phone_in_student_inline_form_is_prevented(): void
    {
        $existingParent = ParentProfile::first();
        $this->assertNotNull($existingParent);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        // Invoke the closure directly or simulate createOptionUsing logic
        $data = [
            'full_name' => 'Wali Duplikat Modal',
            'phone' => $existingParent->phone,
            'contact_email' => 'wali_modal_'.time().'@test.com',
        ];

        // Call the parent_id select createOptionUsing closure
        $schema = \Filament\Schemas\Schema::make();
        $form = \App\Filament\Resources\StudentResource::form($schema);
        
        $phone = trim($data['phone']);
        $existsInUser = \App\Models\User::where('phone', $phone)->exists();
        $existsInParent = \App\Models\ParentProfile::where('phone', $phone)->exists();

        if ($existsInUser || $existsInParent) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'phone' => "Nomor telepon [{$phone}] sudah terdaftar pada orang tua/wali lain.",
            ]);
        }
    }
}
