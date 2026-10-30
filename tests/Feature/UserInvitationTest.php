<?php

use App\Enums\UserRole;
use App\Livewire\Admin\User\Form;
use App\Livewire\Admin\User\Index;
use App\Livewire\Auth\SetPassword;
use App\Mail\UserInvitationMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Pastikan roles dan permission tersedia
    Role::firstOrCreate(['name' => UserRole::Superadmin->value]);
    Role::firstOrCreate(['name' => UserRole::Admin->value]);
    Role::firstOrCreate(['name' => UserRole::Staff->value]);

    $permission = Permission::firstOrCreate(['name' => 'manage-users']);
    Role::findByName(UserRole::Superadmin->value)->givePermissionTo($permission);
    Role::findByName(UserRole::Admin->value)->givePermissionTo($permission);
});

test('admin can create a new user and email invitation is automatically sent', function () {
    Mail::fake();

    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole(UserRole::Admin->value);

    Livewire::actingAs($admin)
        ->test(Form::class)
        ->set('name', 'Budi Baru')
        ->set('email', 'budi.baru@example.com')
        ->set('role', UserRole::Staff->value)
        ->call('save')
        ->assertRedirect(route('admin.user.index'));

    $user = User::where('email', 'budi.baru@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Budi Baru')
        ->and($user->hasRole(UserRole::Staff->value))->toBeTrue();

    Mail::assertSent(UserInvitationMail::class, function ($mail) use ($user) {
        return $mail->hasTo('budi.baru@example.com') && $mail->user->id === $user->id;
    });
});

test('superadmin can edit user password manually', function () {
    $superadmin = User::factory()->create(['is_active' => true]);
    $superadmin->assignRole(UserRole::Superadmin->value);

    $staff = User::factory()->create(['is_active' => true, 'password' => Hash::make('old-password-123')]);
    $staff->assignRole(UserRole::Staff->value);

    Livewire::actingAs($superadmin)
        ->test(Form::class, ['user' => $staff])
        ->set('password', 'NewSuperPassword123!')
        ->set('password_confirmation', 'NewSuperPassword123!')
        ->call('save')
        ->assertRedirect(route('admin.user.index'));

    $staff->refresh();
    expect(Hash::check('NewSuperPassword123!', $staff->password))->toBeTrue();
});

test('non-superadmin admin cannot edit user password directly', function () {
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole(UserRole::Admin->value);

    $staff = User::factory()->create(['is_active' => true, 'password' => Hash::make('old-password-123')]);
    $staff->assignRole(UserRole::Staff->value);

    Livewire::actingAs($admin)
        ->test(Form::class, ['user' => $staff])
        ->set('password', 'HackedPassword123!')
        ->set('password_confirmation', 'HackedPassword123!')
        ->call('save')
        ->assertStatus(403);
});

test('user can set password using valid token link', function () {
    $user = User::factory()->create([
        'email' => 'peserta@example.com',
        'is_active' => true,
        'password' => Hash::make('old-random-pass'),
    ]);

    $token = Password::broker()->createToken($user);

    Livewire::test(SetPassword::class, ['token' => $token])
        ->set('email', 'peserta@example.com')
        ->set('password', 'PasswordBaru123!')
        ->set('password_confirmation', 'PasswordBaru123!')
        ->call('savePassword')
        ->assertRedirect(route('admin.dashboard'));

    $user->refresh();
    expect(Hash::check('PasswordBaru123!', $user->password))->toBeTrue()
        ->and(auth()->id())->toBe($user->id);
});

test('user cannot set password with invalid token', function () {
    $user = User::factory()->create([
        'email' => 'peserta2@example.com',
        'is_active' => true,
    ]);

    Livewire::test(SetPassword::class, ['token' => 'invalid-token-123'])
        ->set('email', 'peserta2@example.com')
        ->set('password', 'PasswordBaru123!')
        ->set('password_confirmation', 'PasswordBaru123!')
        ->call('savePassword')
        ->assertHasErrors(['token']);
});

test('admin can filter users by verification status', function () {
    $admin = User::factory()->create(['is_active' => true]);
    $admin->assignRole(UserRole::Superadmin->value);

    $verifiedUser = User::factory()->create([
        'name' => 'User Terverifikasi',
        'email' => 'verified@example.com',
        'email_verified_at' => now(),
    ]);

    $unverifiedUser = User::factory()->create([
        'name' => 'User Belum Aktivasi',
        'email' => 'unverified@example.com',
        'email_verified_at' => null,
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('selectedVerification', 'verified')
        ->assertSee('User Terverifikasi')
        ->assertDontSee('User Belum Aktivasi')
        ->set('selectedVerification', 'unverified')
        ->assertSee('User Belum Aktivasi')
        ->assertDontSee('User Terverifikasi');
});

test('superadmin accounts are hidden from non-superadmin users', function () {
    $superadmin = User::factory()->create([
        'name' => 'Lord Superadmin',
        'email' => 'super@example.com',
        'is_active' => true,
    ]);
    $superadmin->assignRole(UserRole::Superadmin->value);

    $regularAdmin = User::factory()->create([
        'name' => 'Regular Admin',
        'email' => 'admin@example.com',
        'is_active' => true,
    ]);
    $regularAdmin->assignRole(UserRole::Admin->value);

    // Superadmin melihat kedua akun
    Livewire::actingAs($superadmin)
        ->test(Index::class)
        ->assertSee('Lord Superadmin')
        ->assertSee('Regular Admin');

    // Admin biasa TIDAK melihat akun Superadmin
    Livewire::actingAs($regularAdmin)
        ->test(Index::class)
        ->assertDontSee('Lord Superadmin')
        ->assertSee('Regular Admin');
});
