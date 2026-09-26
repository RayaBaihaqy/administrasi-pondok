<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class UserAdminManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->firstOrFail();
        $this->admin = User::where('role', User::ROLE_ADMIN)->firstOrFail();
    }

    public function test_super_admin_can_access_user_resource_and_see_navigation(): void
    {
        $this->actingAs($this->superAdmin);

        $this->assertTrue(UserResource::canViewAny());
        $this->assertTrue(UserResource::shouldRegisterNavigation());

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->assertSuccessful()
            ->assertSee('Kelola Admin');
    }

    public function test_list_users_table_only_shows_admin_and_excludes_super_admin(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->assertCanSeeTableRecords([$this->admin])
            ->assertCanNotSeeTableRecords([$this->superAdmin]);
    }

    public function test_super_admin_cannot_be_edited_or_deleted_via_user_resource(): void
    {
        $this->actingAs($this->superAdmin);

        $this->assertFalse(UserResource::canEdit($this->superAdmin));
        $this->assertFalse(UserResource::canDelete($this->superAdmin));

        // Direct access to edit super admin page must abort 403
        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $this->superAdmin->getKey()])
            ->assertForbidden();
    }

    public function test_admin_staff_cannot_access_user_resource(): void
    {
        $this->actingAs($this->admin);

        $this->assertFalse(UserResource::canViewAny());
        $this->assertFalse(UserResource::shouldRegisterNavigation());

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->assertForbidden();
    }

    public function test_super_admin_can_create_new_admin_and_creates_audit_log(): void
    {
        $this->actingAs($this->superAdmin);

        $uniqueEmail = 'admin_baru_'.time().'@pondok.test';
        $uniquePhone = '0877'.rand(10000000, 99999999);

        Livewire::test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'Ustadz Baru Admin',
                'email' => $uniqueEmail,
                'phone' => $uniquePhone,
                'password' => 'password123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $createdUser = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($createdUser);
        $this->assertEquals('Ustadz Baru Admin', $createdUser->name);
        $this->assertEquals(User::ROLE_ADMIN, $createdUser->role);

        // Check AuditLog
        $log = AuditLog::where('auditable_type', User::class)
            ->where('auditable_id', $createdUser->id)
            ->where('action', 'create_admin')
            ->first();

        $this->assertNotNull($log, 'AuditLog for create_admin should exist');
    }

    public function test_duplicate_email_or_phone_is_rejected_in_admin_form(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'Admin Duplikat',
                'email' => $this->superAdmin->email,
                'phone' => $this->superAdmin->phone ?? '08123456789',
                'password' => 'password123',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique']);
    }

    public function test_super_admin_can_reset_admin_password(): void
    {
        $this->actingAs($this->superAdmin);

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->callTableAction('resetPassword', $this->admin, [
                'new_password' => 'newSecret123',
            ])
            ->assertHasNoTableActionErrors();

        $this->admin->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newSecret123', $this->admin->password));
    }

    public function test_system_strictly_rejects_creating_second_super_admin_at_model_level(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Hanya boleh ada 1 akun Super Admin');

        User::create([
            'name' => 'Super Admin Kedua Ilegal',
            'email' => 'superadmin2_'.time().'@pondok.test',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
    }

    public function test_system_strictly_rejects_promoting_user_to_super_admin(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Hanya boleh ada 1 akun Super Admin');

        $this->admin->update([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
    }

    public function test_system_strictly_rejects_deleting_primary_super_admin(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Akun Super Admin utama tidak boleh dihapus');

        $this->superAdmin->delete();
    }
}
