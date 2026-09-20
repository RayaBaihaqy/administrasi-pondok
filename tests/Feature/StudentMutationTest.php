<?php

namespace Tests\Feature;

use App\Filament\Resources\StudentResource\Pages\ListStudents;
use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class StudentMutationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', User::ROLE_SUPER_ADMIN)->first()
            ?? User::where('role', User::ROLE_ADMIN)->first();

        $this->actingAs($this->admin);
    }

    public function test_student_can_mutate_out_and_cancels_unpaid_bills(): void
    {
        $parentProfile = \App\Models\ParentProfile::first();
        $student = Student::create([
            'parent_id' => $parentProfile?->id ?? 1,
            'nis' => '9999999991',
            'nism' => '9999999991',
            'full_name' => 'Siswa Test Mutasi',
            'gender' => 'male',
            'birth_date' => '2012-01-01',
            'class_level' => 7,
            'rombel' => '1',
            'entry_year' => 2026,
            'status' => Student::STATUS_ACTIVE,
        ]);

        $academicYear = AcademicYear::current() ?? AcademicYear::first();
        $paymentType = PaymentType::first();

        // 1. Create an unpaid bill
        $unpaidBill = Bill::create([
            'student_id' => $student->id,
            'parent_id' => $student->parent_id,
            'payment_type_id' => $paymentType->id,
            'academic_year_id' => $academicYear->id,
            'bill_number' => 'INV-TEST-MUTATE-001',
            'billing_period' => '2026-09-01',
            'billing_date' => '2026-09-01',
            'due_date' => '2026-09-10',
            'amount' => 75000,
            'paid_amount' => 0,
            'outstanding_amount' => 75000,
            'status' => Bill::STATUS_UNPAID,
        ]);

        // 2. Create a paid bill with payment record
        $paidBill = Bill::create([
            'student_id' => $student->id,
            'parent_id' => $student->parent_id,
            'payment_type_id' => $paymentType->id,
            'academic_year_id' => $academicYear->id,
            'bill_number' => 'INV-TEST-PAID-001',
            'billing_period' => '2026-08-01',
            'billing_date' => '2026-08-01',
            'due_date' => '2026-08-10',
            'amount' => 75000,
            'paid_amount' => 75000,
            'outstanding_amount' => 0,
            'status' => Bill::STATUS_PAID,
        ]);

        $payment = Payment::create([
            'bill_id' => $paidBill->id,
            'student_id' => $student->id,
            'parent_id' => $student->parent_id,
            'recorded_by' => $this->admin->id,
            'payment_number' => 'PAY-TEST-001',
            'amount' => 75000,
            'status' => Payment::STATUS_SUCCESS,
            'source' => Payment::SOURCE_MANUAL,
            'method' => 'cash',
            'paid_at' => now(),
        ]);

        // Execute mutation
        $cancelledCount = $student->mutateOut(
            reason: 'Pindah domisili orang tua ke Bandung',
            date: '2026-09-20'
        );

        $student->refresh();
        $unpaidBill->refresh();
        $paidBill->refresh();
        $payment->refresh();

        // Assertions
        $this->assertEquals(Student::STATUS_WITHDRAWN, $student->status);
        $this->assertEquals(1, $cancelledCount);

        // Unpaid bill is cancelled
        $this->assertEquals(Bill::STATUS_CANCELLED, $unpaidBill->status);
        $this->assertEquals(0, $unpaidBill->outstanding_amount);
        $this->assertStringContainsString('Dibatalkan - Siswa Pindah', $unpaidBill->notes);
        $this->assertStringContainsString('Pindah domisili orang tua ke Bandung', $unpaidBill->notes);

        // Paid bill and payment remain intact
        $this->assertEquals(Bill::STATUS_PAID, $paidBill->status);
        $this->assertEquals(75000, $paidBill->paid_amount);
        $this->assertEquals(Payment::STATUS_SUCCESS, $payment->status);
        $this->assertEquals(75000, $payment->amount);
    }

    public function test_student_mutation_can_be_reverted(): void
    {
        $student = Student::where('status', Student::STATUS_ACTIVE)->first();
        $this->assertNotNull($student);

        $student->mutateOut('Salah input mutasi');
        $student->refresh();
        $this->assertEquals(Student::STATUS_WITHDRAWN, $student->status);

        $student->revertMutation();
        $student->refresh();
        $this->assertEquals(Student::STATUS_ACTIVE, $student->status);
    }

    public function test_list_students_renders_tabs_and_mutation_action(): void
    {
        Livewire::test(ListStudents::class)
            ->assertSuccessful()
            ->assertSee('Siswa Aktif')
            ->assertSee('Siswa Mutasi / Pindah');
    }
}
