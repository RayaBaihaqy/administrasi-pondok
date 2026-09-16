<?php

namespace Tests\Feature;

use App\Filament\Resources\AcademicYearResource\Pages\ListAcademicYears;
use App\Filament\Resources\BillResource\Pages\ListBills;
use App\Filament\Resources\ParentResource\Pages\ListParents;
use App\Filament\Resources\PaymentResource\Pages\ListPayments;
use App\Filament\Resources\PaymentTypeResource\Pages\ListPaymentTypes;
use App\Filament\Resources\StudentResource\Pages\ListStudents;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentResourceRenderTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::where('role', User::ROLE_SUPER_ADMIN)->first()
            ?? User::where('role', User::ROLE_ADMIN)->first();

        $this->actingAs($admin);
    }

    public function test_can_render_student_list_page(): void
    {
        Livewire::test(ListStudents::class)
            ->assertSuccessful();
    }

    public function test_can_render_create_student_page_and_status_field_is_hidden(): void
    {
        Livewire::test(\App\Filament\Resources\StudentResource\Pages\CreateStudent::class)
            ->assertSuccessful()
            ->assertFormFieldHidden('status');
    }

    public function test_can_render_bill_list_page(): void
    {
        Livewire::test(ListBills::class)
            ->assertSuccessful();
    }

    public function test_can_render_payment_list_page(): void
    {
        Livewire::test(ListPayments::class)
            ->assertSuccessful();
    }

    public function test_can_render_payment_type_list_page(): void
    {
        Livewire::test(ListPaymentTypes::class)
            ->assertSuccessful();
    }

    public function test_can_render_academic_year_list_page(): void
    {
        Livewire::test(ListAcademicYears::class)
            ->assertSuccessful();
    }

    public function test_can_render_parent_list_page(): void
    {
        Livewire::test(ListParents::class)
            ->assertSuccessful();
    }
}
