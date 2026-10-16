<?php

namespace App\Livewire\Admin\Role;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

#[Layout('layouts.admin.app')]
class Form extends Component
{
    public ?Role $role = null;

    public string $name = '';

    /** @var array<string> */
    public array $selectedPermissions = [];

    public bool $isEditing = false;

    public function mount(Role|string|int|null $role = null): void
    {
        abort_unless(
            Auth::user()?->hasRole('superadmin') || Auth::user()?->can('manage-system'),
            403,
            'Anda tidak memiliki hak akses untuk mengelola Role.'
        );

        if ($role) {
            $this->role = $role instanceof Role ? $role : Role::findOrFail($role);
            $this->name = $this->role->name;
            $this->selectedPermissions = $this->role->permissions()->pluck('name')->toArray();
            $this->isEditing = true;
        } else {
            $this->isEditing = false;
            $this->selectedPermissions = [];
        }
    }

    public function selectAll(): void
    {
        if ($this->isEditing && strtolower($this->name) === 'superadmin') {
            return;
        }

        $this->selectedPermissions = Permission::pluck('name')->toArray();
    }

    public function deselectAll(): void
    {
        if ($this->isEditing && strtolower($this->name) === 'superadmin') {
            return;
        }

        $this->selectedPermissions = [];
    }

    /**
     * @param  array<string>  $featureKeys
     */
    public function toggleFeatureRow(array $featureKeys): void
    {
        if ($this->isEditing && strtolower($this->name) === 'superadmin') {
            return;
        }

        $validKeys = array_values(array_filter($featureKeys));
        if (empty($validKeys)) {
            return;
        }

        $allSelected = true;
        foreach ($validKeys as $key) {
            if (! in_array($key, $this->selectedPermissions, true)) {
                $allSelected = false;
                break;
            }
        }

        if ($allSelected) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $validKeys));
        } else {
            $this->selectedPermissions = array_values(array_unique(array_merge($this->selectedPermissions, $validKeys)));
        }
    }

    public function save(): mixed
    {
        if ($this->isEditing) {
            $this->validate([
                'name' => ['required', 'string', 'max:50', 'unique:roles,name,'.$this->role->id],
            ]);

            // Lindungi nama role sistem bawaan
            if (! in_array(strtolower($this->role->name), ['superadmin', 'admin', 'staff'])) {
                $this->role->name = strtolower(trim($this->name));
                $this->role->save();
            }

            // Superadmin selalu memiliki SEMUA permissions
            if (strtolower($this->role->name) === 'superadmin') {
                $this->role->syncPermissions(Permission::all());
            } else {
                $this->role->syncPermissions($this->selectedPermissions);
            }

            session()->flash('success', "Hak akses role \"{$this->role->name}\" berhasil disimpan.");
        } else {
            $this->validate([
                'name' => ['required', 'string', 'max:50', 'unique:roles,name'],
            ]);

            $role = Role::create([
                'name' => strtolower(trim($this->name)),
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($this->selectedPermissions);

            session()->flash('success', "Role baru \"{$role->name}\" berhasil ditambahkan.");
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.role.index');
    }

    /**
     * Matriks 4 Kolom CRUD Murni per Fitur
     *
     * @return array<string, array{
     *     name: string,
     *     view: ?string,
     *     create: ?string,
     *     edit: ?string,
     *     delete: ?string
     * }>
     */
    public function getCrudMatrix(): array
    {
        return [
            'posts' => [
                'name' => 'Artikel & Materi',
                'view' => 'view-posts',
                'create' => 'create-posts',
                'edit' => 'edit-posts',
                'delete' => 'delete-posts',
            ],
            'categories' => [
                'name' => 'Kategori Konten',
                'view' => 'view-categories',
                'create' => 'create-categories',
                'edit' => 'edit-categories',
                'delete' => 'delete-categories',
            ],
            'users' => [
                'name' => 'Pengguna & Staff',
                'view' => 'view-users',
                'create' => 'create-users',
                'edit' => 'edit-users',
                'delete' => 'delete-users',
            ],
            'settings' => [
                'name' => 'Identitas Portal',
                'view' => 'view-settings',
                'create' => null,
                'edit' => 'edit-settings',
                'delete' => null,
            ],
            'system' => [
                'name' => 'Sistem & Role',
                'view' => null,
                'create' => null,
                'edit' => 'manage-system',
                'delete' => null,
            ],
        ];
    }

    public function render()
    {
        $totalAllPermissions = Permission::count();
        $totalSelected = count($this->selectedPermissions);

        return view('livewire.admin.role.form', [
            'matrixFeatures' => $this->getCrudMatrix(),
            'totalAllPermissions' => $totalAllPermissions,
            'totalSelected' => $totalSelected,
        ]);
    }
}
