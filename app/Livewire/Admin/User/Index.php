<?php

namespace App\Livewire\Admin\User;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

    public string $selectedStatus = '';

    public int $perPage = 10;

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

    public function updatedSelectedStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedRole', 'selectedStatus']);
        $this->resetPage();
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

    public function toggleStatus(string $id): void
    {
        abort_unless(Auth::user()->can('manage-users'), 403);

        if ($id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat mengubah status akun Anda sendiri.');

            return;
        }

        $user = User::with('roles')->findOrFail($id);

        // Hierarki: Admin biasa tidak boleh mengubah status Superadmin
        if ($user->hasRole(UserRole::Superadmin->value) && ! Auth::user()->hasRole(UserRole::Superadmin->value)) {
            session()->flash('error', 'Hanya Superadmin yang berwenang mengubah status Superadmin.');

            return;
        }

        // Proteksi: Tidak boleh menonaktifkan Superadmin aktif terakhir
        if ($user->is_active && $user->hasRole(UserRole::Superadmin->value) && User::role(UserRole::Superadmin->value)->where('is_active', true)->count() <= 1) {
            session()->flash('error', 'Tidak dapat menonaktifkan Superadmin aktif terakhir dalam sistem.');

            return;
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Status akun \"{$user->name}\" berhasil {$statusText}.");
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
            ->when($this->selectedStatus === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->selectedStatus === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.user.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
            'isSuperadmin' => Auth::user()?->hasRole(UserRole::Superadmin->value),
        ]);
    }
}
