<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth.auth')]
#[Title('Atur Password Akun')]
class SetPassword extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $isInitialSetup = true;

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->query('email', '');

        $user = ! empty($this->email) ? User::where('email', $this->email)->first() : null;
        if ($user) {
            $this->isInitialSetup = is_null($user->email_verified_at) || request()->routeIs('password.set');
        } else {
            $this->isInitialSetup = request()->routeIs('password.set') || request()->is('set-password/*');
        }
    }

    public function savePassword(): mixed
    {
        $passwordLabel = $this->isInitialSetup ? 'Password' : 'Password baru';

        $this->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => "{$passwordLabel} wajib diisi.",
            'password.min' => "{$passwordLabel} minimal terdiri dari 8 karakter.",
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $status = Password::broker()->reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                    'is_active' => true,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $user = User::where('email', $this->email)->first();

            if ($user && $user->is_active) {
                Auth::login($user);
                session()->regenerate();

                $welcomeMsg = $this->isInitialSetup
                    ? "Selamat datang, {$user->name}! Akun Anda telah berhasil diaktifkan."
                    : 'Password Anda berhasil diperbarui.';

                session()->flash('success', $welcomeMsg);

                return redirect()->route('admin.dashboard');
            }

            $statusMsg = $this->isInitialSetup
                ? 'Akun Anda telah aktif. Silakan masuk menggunakan password Anda.'
                : 'Password berhasil diperbarui. Silakan login menggunakan password baru Anda.';

            session()->flash('status', $statusMsg);

            return redirect()->route('login');
        }

        $this->addError('token', 'Tautan ini tidak valid atau sudah kedaluwarsa. Silakan hubungi Administrator untuk meminta tautan baru.');

        return null;
    }

    public function render()
    {
        return view('livewire.auth.set-password');
    }
}
