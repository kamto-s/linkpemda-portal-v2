<?php

use App\Livewire\Auth\ForgotPassword;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('user can view forgot password page', function () {
    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee('Lupa Password');
});

test('user can request reset password link', function () {
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'user.lupa@example.com',
        'is_active' => true,
    ]);

    Livewire::test(ForgotPassword::class)
        ->set('email', 'user.lupa@example.com')
        ->call('sendResetLink')
        ->assertHasNoErrors()
        ->assertSet('emailSent', true);

    Mail::assertSent(ResetPasswordMail::class, function ($mail) use ($user) {
        return $mail->hasTo('user.lupa@example.com') && $mail->user->id === $user->id;
    });
});

test('user with inactive account cannot request reset password', function () {
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'nonaktif@example.com',
        'is_active' => false,
    ]);

    Livewire::test(ForgotPassword::class)
        ->set('email', 'nonaktif@example.com')
        ->call('sendResetLink')
        ->assertHasErrors(['email']);

    Mail::assertNothingSent();
});

test('non-existent email cannot request reset password', function () {
    Mail::fake();

    Livewire::test(ForgotPassword::class)
        ->set('email', 'tidakada@example.com')
        ->call('sendResetLink')
        ->assertHasErrors(['email']);

    Mail::assertNothingSent();
});
