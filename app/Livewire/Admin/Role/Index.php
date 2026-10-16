<?php

namespace App\Livewire\Admin\Role;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Layout('layouts.admin.app')]
class Index extends Component
{
    use WithPagination;

    public string $pageTitle = 'Role & Hak Akses';

    public string $search = '';

    public int $perPage = 10;

    // Delete confirmation
    public ?int $roleToDeleteId = null;

    public string $roleToDeleteName = '';

    protected $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->hasRole('superadmin') || Auth::user()?->can('manage-system'),
            403,
            'Anda tidak memiliki wewenang untuk mengelola Role & Hak Akses.'
        );
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $role = Role::withCount('users')->findOrFail($id);

        // Lindungi 3 role bawaan sistem
        if (in_array(strtolower($role->name), ['superadmin', 'admin', 'staff'])) {
            session()->flash('error', "Role sistem bawaan ({$role->name}) tidak boleh dihapus.");

            return;
        }

        // Jangan hapus role jika masih ada user yang menggunakannya
        if ($role->users_count > 0) {
            session()->flash('error', "Role \"{$role->name}\" tidak dapat dihapus karena masih digunakan oleh {$role->users_count} pengguna.");

            return;
        }

        $this->roleToDeleteId = $role->id;
        $this->roleToDeleteName = $role->name;

        $this->dispatch('open-delete-role-modal');
    }

    public function delete(): void
    {
        if (! $this->roleToDeleteId) {
            return;
        }

        $role = Role::withCount('users')->findOrFail($this->roleToDeleteId);

        if (in_array(strtolower($role->name), ['superadmin', 'admin', 'staff']) || $role->users_count > 0) {
            session()->flash('error', 'Role tidak dapat dihapus.');
            $this->dispatch('close-delete-role-modal');

            return;
        }

        $name = $role->name;
        $role->delete();

        // Reset cache permission Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        session()->flash('success', "Role \"{$name}\" berhasil dihapus.");

        $this->dispatch('close-delete-role-modal');
        $this->reset(['roleToDeleteId', 'roleToDeleteName']);
    }

    public function render()
    {
        $roles = Role::query()
            ->withCount('users')
            ->with('permissions')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->latest('id')
            ->paginate($this->perPage);

        return view('livewire.admin.role.index', [
            'roles' => $roles,
        ]);
    }
}
