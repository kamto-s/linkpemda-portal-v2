<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth.auth')]
#[Title('Login')]
class Login extends Component
{
    #[Validate('required|email', message: [
        'required' => 'Email wajib diisi.',
        'email' => 'Format email tidak valid.'
    ])]
    public string $email = '';

    #[Validate('required', message: [
        'required' => 'Password wajib diisi.'
    ])]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Email atau Password yang Anda masukkan salah.');
            return;
        }

        session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
