<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Models\Contracts\FilamentUser;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class Login extends BaseLogin
{
    /**
     * Ubah komponen input email menjadi input fleksibel (Email atau Nomor Telepon).
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Email atau Nomor Telepon')
            ->placeholder('nama@email.com atau 081234567890')
            ->required()
            ->autocomplete('username')
            ->autofocus();
    }

    /**
     * Ekstrak kredensial dari form data.
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        return [
            'login' => $data['login'] ?? '',
            'password' => $data['password'] ?? '',
        ];
    }

    /**
     * Normalisasi variasi nomor telepon untuk pencarian fleksibel di database.
     */
    public static function normalizePhoneVariants(string $input): array
    {
        $clean = preg_replace('/[^0-9]/', '', $input);
        if (empty($clean)) {
            return [$input];
        }

        $variants = [$input, $clean];

        if (str_starts_with($clean, '08')) {
            $withoutZero = substr($clean, 1);
            $variants[] = '62'.$withoutZero;
            $variants[] = '+62'.$withoutZero;
            $variants[] = '0'.$withoutZero;
        } elseif (str_starts_with($clean, '628')) {
            $without62 = substr($clean, 2);
            $variants[] = '0'.$without62;
            $variants[] = '62'.$without62;
            $variants[] = '+62'.$without62;
        }

        return array_values(array_unique($variants));
    }

    /**
     * Autentikasi kustom dengan pesan error yang spesifik.
     */
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();
        $loginInput = trim($data['login'] ?? '');
        $password = (string) ($data['password'] ?? '');
        $remember = (bool) ($data['remember'] ?? false);

        $isEmail = str_contains($loginInput, '@');
        $user = null;

        if ($isEmail) {
            // 1. Pencarian berdasarkan Alamat Email
            $user = User::where('email', strtolower($loginInput))->first();

            if (! $user) {
                $this->fireFailedEvent(Filament::auth(), null, ['email' => $loginInput]);

                throw ValidationException::withMessages([
                    'data.login' => 'Email tidak ditemukan.',
                ]);
            }
        } else {
            // 2. Pencarian berdasarkan Nomor Telepon (termasuk normalisasi 08/62/+62 dan relasi ParentProfile)
            $phoneVariants = self::normalizePhoneVariants($loginInput);

            $user = User::where(function ($query) use ($phoneVariants, $loginInput) {
                $query->whereIn('phone', $phoneVariants)
                    ->orWhere('email', $loginInput)
                    ->orWhereHas('parentProfile', function ($q) use ($phoneVariants) {
                        $q->whereIn('phone', $phoneVariants);
                    });
            })->first();

            if (! $user) {
                $this->fireFailedEvent(Filament::auth(), null, ['login' => $loginInput]);

                throw ValidationException::withMessages([
                    'data.login' => 'Nomor telepon tidak ditemukan.',
                ]);
            }
        }

        // 3. Verifikasi Password / Kata Sandi
        if (! Hash::check($password, $user->password)) {
            $this->fireFailedEvent(Filament::auth(), $user, ['login' => $loginInput]);

            throw ValidationException::withMessages([
                'data.password' => 'Password salah.',
            ]);
        }

        // 4. Verifikasi Hak Akses Panel Filament
        $currentPanel = Filament::getCurrentOrDefaultPanel();
        if ($user instanceof FilamentUser && $currentPanel && ! $user->canAccessPanel($currentPanel)) {
            $this->fireFailedEvent(Filament::auth(), $user, ['login' => $loginInput]);

            throw ValidationException::withMessages([
                'data.login' => 'Akun Anda tidak memiliki hak akses ke panel ini.',
            ]);
        }

        // 5. Login Sukses
        Filament::auth()->login($user, $remember);
        session()->regenerate();

        return app(LoginResponse::class);
    }
}
