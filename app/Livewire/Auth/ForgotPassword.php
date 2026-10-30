<?php

namespace App\Livewire\Auth;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth.auth')]
#[Title('Lupa Password')]
class ForgotPassword extends Component
{
    #[Validate('required|email', message: [
        'required' => 'Email wajib diisi.',
        'email' => 'Format email tidak valid.',
    ])]
    public string $email = '';

    public bool $emailSent = false;

    public function sendResetLink(): void
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->addError('email', 'Alamat email ini tidak terdaftar dalam sistem.');

            return;
        }

        if (! $user->is_active) {
            $this->addError('email', 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator.');

            return;
        }

        try {
            $token = Password::broker()->createToken($user);
            Mail::to($user->email)->send(new ResetPasswordMail($user, $token));

            $this->emailSent = true;
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim email reset password ke {$user->email}: ".$e->getMessage());
            $this->addError('email', 'Gagal mengirim email reset password. Silakan coba beberapa saat lagi.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
