<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class LoginAuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup test users
        User::whereIn('email', ['test_admin@pondok.test', 'test_wali@parent.test'])->delete();

        User::create([
            'name' => 'Test Admin Staff',
            'email' => 'test_admin@pondok.test',
            'phone' => '081211112222',
            'password' => Hash::make('secret123'),
            'role' => User::ROLE_ADMIN,
        ]);

        $waliUser = User::create([
            'name' => 'Test Wali Santri',
            'email' => 'test_wali@parent.test',
            'phone' => '081344445555',
            'password' => Hash::make('secret123'),
            'role' => User::ROLE_PARENT,
        ]);

        ParentProfile::create([
            'user_id' => $waliUser->id,
            'full_name' => 'Test Wali Santri Profile',
            'phone' => '081344445555',
            'contact_email' => 'test_wali@parent.test',
        ]);
    }

    public function test_can_login_with_email(): void
    {
        Livewire::test(Login::class)
            ->set('data.login', 'test_admin@pondok.test')
            ->set('data.password', 'secret123')
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertAuthenticated();
    }

    public function test_can_login_with_phone_number_various_formats(): void
    {
        // 1. Format 08...
        Livewire::test(Login::class)
            ->set('data.login', '081211112222')
            ->set('data.password', 'secret123')
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertAuthenticated();

        auth()->logout();

        // 2. Format 628...
        Livewire::test(Login::class)
            ->set('data.login', '6281211112222')
            ->set('data.password', 'secret123')
            ->call('authenticate')
            ->assertHasNoErrors();

        $this->assertAuthenticated();
    }

    public function test_shows_specific_error_when_email_not_found(): void
    {
        Livewire::test(Login::class)
            ->set('data.login', 'email_tidak_ada_12345@pondok.test')
            ->set('data.password', 'secret123')
            ->call('authenticate')
            ->assertHasErrors(['data.login' => 'Email tidak ditemukan.']);

        $this->assertGuest();
    }

    public function test_shows_specific_error_when_phone_number_not_found(): void
    {
        Livewire::test(Login::class)
            ->set('data.login', '089999888777')
            ->set('data.password', 'secret123')
            ->call('authenticate')
            ->assertHasErrors(['data.login' => 'Nomor telepon tidak ditemukan.']);

        $this->assertGuest();
    }

    public function test_shows_specific_error_when_password_is_incorrect(): void
    {
        // Login via email tapi password salah
        Livewire::test(Login::class)
            ->set('data.login', 'test_admin@pondok.test')
            ->set('data.password', 'password_salah_total')
            ->call('authenticate')
            ->assertHasErrors(['data.password' => 'Password salah.']);

        $this->assertGuest();

        // Login via telepon tapi password salah
        Livewire::test(Login::class)
            ->set('data.login', '081211112222')
            ->set('data.password', 'password_salah_total')
            ->call('authenticate')
            ->assertHasErrors(['data.password' => 'Password salah.']);

        $this->assertGuest();
    }

    public function test_parent_cannot_access_admin_panel(): void
    {
        // Login dengan akun wali santri ke panel admin
        Livewire::test(Login::class)
            ->set('data.login', 'test_wali@parent.test')
            ->set('data.password', 'secret123')
            ->call('authenticate')
            ->assertHasErrors(['data.login' => 'Akun Anda tidak memiliki hak akses ke panel ini.']);

        $this->assertGuest();
    }
}
