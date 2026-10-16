<?php

namespace App\Livewire\Admin\User;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
class Index extends Component
{
    use WithPagination;

    public string $pageTitle = 'Pengguna & Staff';

    public string $search = '';

    public string $selectedRole = '';

    public int $perPage = 10;

    // Form Properties (Create & Edit)
    public ?string $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = '';

    public bool $isEditing = false;

    // Delete confirmation properties
    public ?string $userToDeleteId = null;

    public string $userToDeleteName = '';

    protected $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        abort_unless(Auth::user()->can('manage-users'), 403, 'Anda tidak memiliki hak akses untuk mengelola pengguna.');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedRole(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        abort_unless(Auth::user()->can('manage-users'), 403);

        $this->resetValidation();
        $this->reset(['userId', 'name', 'email', 'password', 'password_confirmation']);
        $this->role = UserRole::Staff->value;
        $this->isEditing = false;

        $this->dispatch('open-user-modal');
    }

    public function edit(string $id): void
    {
        abort_unless(Auth::user()->can('manage-users'), 403);

        $this->resetValidation();
        $user = User::with('roles')->findOrFail($id);

        // Hierarki: Hanya Superadmin yang boleh mengedit akun Superadmin
        if ($user->hasRole(UserRole::Superadmin->value) && ! Auth::user()->hasRole(UserRole::Superadmin->value)) {
            session()->flash('error', 'Hanya Superadmin yang memiliki wewenang untuk mengedit akun Superadmin.');

            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->roles->first()?->name ?? UserRole::Staff->value;
        $this->password = '';
        $this->password_confirmation = '';
        $this->isEditing = true;

        $this->dispatch('open-user-modal');
    }

    public function save(): void
    {
        abort_unless(Auth::user()->can('manage-users'), 403);

        $assignableRoles = $this->getAssignableRoles();
        $roleValues = implode(',', array_map(fn ($r) => $r->value, $assignableRoles));

        if ($this->isEditing) {
            $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->userId],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'role' => ['required', 'string', "in:{$roleValues}"],
            ]);

            $user = User::findOrFail($this->userId);

            // Hierarki: Admin biasa tidak boleh mengedit akun Superadmin
            if ($user->hasRole(UserRole::Superadmin->value) && ! Auth::user()->hasRole(UserRole::Superadmin->value)) {
                session()->flash('error', 'Hanya Superadmin yang berwenang mengubah akun Superadmin.');

                return;
            }

            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if (! empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }

            $user->update($updateData);
            $user->syncRoles([$this->role]);

            session()->flash('success', "Pengguna \"{$user->name}\" berhasil diperbarui.");
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
            ]);

            $user->assignRole($this->role);

            session()->flash('success', "Pengguna \"{$user->name}\" berhasil ditambahkan.");
        }

        $this->dispatch('close-user-modal');
        $this->reset(['userId', 'name', 'email', 'password', 'password_confirmation', 'role', 'isEditing']);
    }

    public function confirmDelete(string $id): void
    {
        abort_unless(Auth::user()->can('manage-users'), 403);

        if ($id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');

            return;
        }

        $user = User::with('roles')->findOrFail($id);

        // Hierarki: Admin biasa tidak boleh menghapus akun Superadmin
        if ($user->hasRole(UserRole::Superadmin->value) && ! Auth::user()->hasRole(UserRole::Superadmin->value)) {
            session()->flash('error', 'Hanya Superadmin yang berwenang menghapus akun Superadmin.');

            return;
        }

        // Proteksi: Tidak boleh menghapus Superadmin terakhir di sistem
        if ($user->hasRole(UserRole::Superadmin->value) && User::role(UserRole::Superadmin->value)->count() <= 1) {
            session()->flash('error', 'Tidak dapat menghapus Superadmin terakhir dalam sistem.');

            return;
        }

        $this->userToDeleteId = $user->id;
        $this->userToDeleteName = $user->name;

        $this->dispatch('open-delete-modal');
    }

    public function delete(): void
    {
        abort_unless(Auth::user()->can('manage-users'), 403);

        if (! $this->userToDeleteId) {
            return;
        }

        if ($this->userToDeleteId === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            $this->dispatch('close-delete-modal');

            return;
        }

        $user = User::findOrFail($this->userToDeleteId);

        // Hierarki: Admin biasa tidak boleh menghapus akun Superadmin
        if ($user->hasRole(UserRole::Superadmin->value) && ! Auth::user()->hasRole(UserRole::Superadmin->value)) {
            session()->flash('error', 'Hanya Superadmin yang berwenang menghapus akun Superadmin.');
            $this->dispatch('close-delete-modal');

            return;
        }

        $name = $user->name;
        $user->delete();

        session()->flash('success', "Pengguna \"{$name}\" berhasil dihapus.");

        $this->dispatch('close-delete-modal');
        $this->reset(['userToDeleteId', 'userToDeleteName']);
    }

    /**
     * Role yang boleh di-assign: Jika login sebagai Superadmin boleh semua role.
     * Jika login sebagai Admin biasa, role Superadmin disembunyikan.
     *
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
        $users = User::query()
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->selectedRole, function ($query) {
                $query->role($this->selectedRole);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.user.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
            'assignableRoles' => $this->getAssignableRoles(),
            'isSuperadmin' => Auth::user()?->hasRole(UserRole::Superadmin->value),
        ]);
    }
}
