<?php

namespace App\Livewire\Admin\User;

use App\Enums\UserRole;
use App\Mail\UserInvitationMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin.app')]
class Form extends Component
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $role = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $isActive = true;

    public bool $isEditing = false;

    public function mount(User|string|null $user = null): void
    {
        abort_unless(Auth::user()?->can('manage-users'), 403, 'Anda tidak memiliki hak akses untuk mengelola pengguna.');

        if ($user) {
            $this->user = $user instanceof User ? $user : User::findOrFail($user);

            // Hierarki: Admin biasa tidak boleh membuka form edit Superadmin
            if ($this->user->hasRole(UserRole::Superadmin->value) && ! Auth::user()->hasRole(UserRole::Superadmin->value)) {
                abort(403, 'Hanya Superadmin yang memiliki wewenang untuk mengedit akun Superadmin.');
            }

            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->role = $this->user->roles->first()?->name ?? UserRole::Staff->value;
            $this->isActive = (bool) $this->user->is_active;
            $this->isEditing = true;
        } else {
            $this->role = UserRole::Staff->value;
            $this->isActive = true;
            $this->isEditing = false;
        }
    }

    public function save(): mixed
    {
        abort_unless(Auth::user()?->can('manage-users'), 403);

        $assignableRoles = $this->getAssignableRoles();
        $roleValues = implode(',', array_map(fn ($r) => $r->value, $assignableRoles));

        if ($this->isEditing) {
            $isSuperadmin = (bool) Auth::user()?->hasRole(UserRole::Superadmin->value);

            // Proteksi: Khusus Superadmin yang boleh mengubah password secara langsung
            if (! empty($this->password) && ! $isSuperadmin) {
                abort(403, 'Hanya Superadmin yang berwenang mengubah password pengguna secara langsung.');
            }

            $validationRules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
                'role' => ['required', 'string', "in:{$roleValues}"],
            ];

            if ($isSuperadmin && ! empty($this->password)) {
                $validationRules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
            }

            $this->validate($validationRules);

            // Hierarki check
            if ($this->user->hasRole(UserRole::Superadmin->value) && ! Auth::user()->hasRole(UserRole::Superadmin->value)) {
                abort(403, 'Hanya Superadmin yang berwenang mengubah akun Superadmin.');
            }

            // Proteksi: Tidak boleh menonaktifkan akun sendiri
            if ($this->user->id === Auth::id() && ! $this->isActive) {
                session()->flash('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');

                return null;
            }

            // Proteksi: Tidak boleh menonaktifkan Superadmin aktif terakhir
            if ($this->user->hasRole(UserRole::Superadmin->value) && ! $this->isActive && User::role(UserRole::Superadmin->value)->where('is_active', true)->count() <= 1) {
                session()->flash('error', 'Tidak dapat menonaktifkan Superadmin aktif terakhir dalam sistem.');

                return null;
            }

            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
                'is_active' => $this->isActive,
            ];

            if ($isSuperadmin && ! empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }

            $this->user->update($updateData);
            $this->user->syncRoles([$this->role]);

            session()->flash('success', "Pengguna \"{$this->user->name}\" berhasil diperbarui.");
        } else {
            // Mode Tambah Baru: Password selalu dibuat sendiri oleh pengguna via email
            $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'role' => ['required', 'string', "in:{$roleValues}"],
            ]);

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make(Str::random(32)),
                'is_active' => $this->isActive,
            ]);

            $user->assignRole($this->role);

            try {
                $token = Password::broker()->createToken($user);
                Mail::to($user->email)->send(new UserInvitationMail($user, $token));

                session()->flash('success', "Pengguna \"{$user->name}\" berhasil ditambahkan dan email undangan telah dikirim ke {$user->email} untuk mengatur password.");
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim email undangan ke {$user->email}: ".$e->getMessage());
                session()->flash('success', "Pengguna \"{$user->name}\" berhasil ditambahkan. Namun pengiriman email undangan gagal: {$e->getMessage()}");
            }
        }

        return redirect()->route('admin.user.index');
    }

    public function resendInvitation(): void
    {
        abort_unless(Auth::user()?->can('manage-users'), 403);

        if (! $this->isEditing || ! $this->user) {
            return;
        }

        try {
            $token = Password::broker()->createToken($this->user);
            Mail::to($this->user->email)->send(new UserInvitationMail($this->user, $token));

            session()->flash('success', "Email tautan atur password berhasil dikirim ulang ke {$this->user->email}.");
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim ulang email tautan atur password ke {$this->user->email}: ".$e->getMessage());
            session()->flash('error', 'Gagal mengirim email tautan atur password: '.$e->getMessage());
        }
    }

    /**
     * @return array<UserRole>
     */
    public function getAssignableRoles(): array
    {
        if (Auth::user()?->hasRole(UserRole::Superadmin->value)) {
            return UserRole::cases();
        }

        return array_values(array_filter(
            UserRole::cases(),
            fn (UserRole $role) => $role !== UserRole::Superadmin
        ));
    }

    public function render()
    {
        return view('livewire.admin.user.form', [
            'assignableRoles' => $this->getAssignableRoles(),
            'isSuperadmin' => Auth::user()?->hasRole(UserRole::Superadmin->value),
        ]);
    }
}
