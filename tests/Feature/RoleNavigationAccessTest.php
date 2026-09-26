<?php

namespace Tests\Feature;

use App\Filament\Resources\AcademicYearResource;
use App\Filament\Resources\AuditLogResource;
use App\Filament\Resources\BillResource;
use App\Filament\Resources\PaymentResource;
use App\Filament\Resources\PaymentTypeResource;
use App\Filament\Resources\StudentResource;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class RoleNavigationAccessTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first()
            ?? User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->admin = User::where('role', User::ROLE_ADMIN)->first()
            ?? User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_super_admin_can_access_all_master_and_config_resources(): void
    {
        $this->actingAs($this->superAdmin);

        $this->assertTrue(AcademicYearResource::canViewAny());
        $this->assertTrue(PaymentTypeResource::canViewAny());
        $this->assertTrue(AuditLogResource::canViewAny());

        Livewire::test(AcademicYearResource\Pages\ListAcademicYears::class)
            ->assertSuccessful();

        Livewire::test(PaymentTypeResource\Pages\ListPaymentTypes::class)
            ->assertSuccessful();

        // Audit Log is hidden from sidebar navigation, but accessible via direct URL for Super Admin
        $this->assertFalse(AuditLogResource::shouldRegisterNavigation());
        Livewire::test(AuditLogResource\Pages\ListAuditLogs::class)
            ->assertSuccessful();

        $response = $this->get('/admin/audit-log');
        $response->assertRedirect('/admin/audit-logs');
    }

    public function test_admin_cannot_access_academic_year_and_payment_type_resources(): void
    {
        $this->actingAs($this->admin);

        // Sidebar / Permission Check: must return FALSE for regular Admin
        $this->assertFalse(AcademicYearResource::canViewAny());
        $this->assertFalse(PaymentTypeResource::canViewAny());
        $this->assertFalse(AuditLogResource::canViewAny());

        // Direct page access must be forbidden (403)
        Livewire::test(AcademicYearResource\Pages\ListAcademicYears::class)
            ->assertForbidden();

        Livewire::test(PaymentTypeResource\Pages\ListPaymentTypes::class)
            ->assertForbidden();

        Livewire::test(AuditLogResource\Pages\ListAuditLogs::class)
            ->assertForbidden();
    }

    public function test_admin_can_still_access_operational_resources(): void
    {
        $this->actingAs($this->admin);

        $this->assertTrue(StudentResource::canViewAny());
        $this->assertTrue(BillResource::canViewAny());
        $this->assertTrue(PaymentResource::canViewAny());

        Livewire::test(StudentResource\Pages\ListStudents::class)
            ->assertSuccessful();

        Livewire::test(BillResource\Pages\ListBills::class)
            ->assertSuccessful();

        Livewire::test(PaymentResource\Pages\ListPayments::class)
            ->assertSuccessful();
    }
}
