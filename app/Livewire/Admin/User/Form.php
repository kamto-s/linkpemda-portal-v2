<?php

namespace App\Livewire\Admin\User;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'role' => ['required', 'string', "in:{$roleValues}"],
            ]);

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

            if (! empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }

            $this->user->update($updateData);
            $this->user->syncRoles([$this->role]);

            session()->flash('success', "Pengguna \"{$this->user->name}\" berhasil diperbarui.");
        } else {
            $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role' => ['required', 'string', "in:{$roleValues}"],
            ]);

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'is_active' => $this->isActive,
            ]);

            $user->assignRole($this->role);

            session()->flash('success', "Pengguna \"{$user->name}\" berhasil ditambahkan.");
        }

        return redirect()->route('admin.user.index');
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
        ]);
    }
}
