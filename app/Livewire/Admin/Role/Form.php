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
            Auth::user()?->hasRole('superadmin') || Auth::user()?->can('manage-system') || Auth::user()?->can('view-roles') || Auth::user()?->can('create-roles') || Auth::user()?->can('edit-roles'),
            403,
            'Anda tidak memiliki hak akses untuk mengelola Role.'
        );

        if ($role) {
            $this->role = $role instanceof Role ? $role : Role::findOrFail($role);

            // Hierarki: Non-superadmin tidak boleh membuka form edit role Superadmin
            if (strtolower($this->role->name) === 'superadmin' && ! Auth::user()?->hasRole('superadmin')) {
                abort(403, 'Hanya Superadmin yang memiliki wewenang untuk mengelola role Superadmin.');
            }

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
            if (strtolower($this->role->name) === 'superadmin' && ! Auth::user()?->hasRole('superadmin')) {
                abort(403, 'Hanya Superadmin yang memiliki wewenang untuk mengubah role Superadmin.');
            }

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
                $perms = $this->syncActionPermissions($this->selectedPermissions);
                $this->role->syncPermissions($perms);
            }

            session()->flash('success', "Hak akses role \"{$this->role->name}\" berhasil disimpan.");
        } else {
            if (strtolower(trim($this->name)) === 'superadmin' && ! Auth::user()?->hasRole('superadmin')) {
                abort(403, 'Hanya Superadmin yang memiliki wewenang untuk membuat role Superadmin.');
            }

            $this->validate([
                'name' => ['required', 'string', 'max:50', 'unique:roles,name'],
            ]);

            $role = Role::create([
                'name' => strtolower(trim($this->name)),
                'guard_name' => 'web',
            ]);

            $perms = $this->syncActionPermissions($this->selectedPermissions);
            $role->syncPermissions($perms);

            session()->flash('success', "Role baru \"{$role->name}\" berhasil ditambahkan.");
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.role.index');
    }

    /**
     * Sinkronisasi izin aksi turunan (kirim blast, import/export kontak, publish postingan)
     * saat izin utama (create/edit) diaktifkan pada role.
     *
     * @param  array<string>  $permissions
     * @return array<string>
     */
    protected function syncActionPermissions(array $permissions): array
    {
        $perms = $permissions;

        // Kirim blast campaign otomatis aktif jika tambah atau ubah campaign dipilih
        if (in_array('create-campaigns', $perms, true) || in_array('edit-campaigns', $perms, true)) {
            $perms[] = 'send-campaigns';
        }

        // Import dan export kontak otomatis aktif jika tambah kontak dipilih
        if (in_array('create-contacts', $perms, true)) {
            $perms[] = 'import-contacts';
            $perms[] = 'export-contacts';
        }

        // Publish artikel/materi otomatis aktif jika tambah atau ubah post dipilih
        if (in_array('create-posts', $perms, true) || in_array('edit-posts', $perms, true)) {
            $perms[] = 'publish-posts';
        }

        return array_values(array_unique($perms));
    }

    /**
     * Matriks 4 Kolom CRUD Murni per Fitur
     *
     * @return array<string, array{
     *     group: string,
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
            // Dashboard
            'dashboard' => [
                'group' => 'Dashboard',
                'name' => 'Dashboard',
                'view' => 'view-dashboard',
                'create' => null,
                'edit' => null,
                'delete' => null,
            ],

            // CMS
            'posts' => [
                'group' => 'CMS',
                'name' => 'Artikel & Materi',
                'view' => 'view-posts',
                'create' => 'create-posts',
                'edit' => 'edit-posts',
                'delete' => 'delete-posts',
            ],
            'categories' => [
                'group' => 'CMS',
                'name' => 'Kategori',
                'view' => 'view-categories',
                'create' => 'create-categories',
                'edit' => 'edit-categories',
                'delete' => 'delete-categories',
            ],
            'schedules' => [
                'group' => 'CMS',
                'name' => 'Jadwal',
                'view' => 'view-schedules',
                'create' => 'create-schedules',
                'edit' => 'edit-schedules',
                'delete' => 'delete-schedules',
            ],
            'galleries' => [
                'group' => 'CMS',
                'name' => 'Galeri',
                'view' => 'view-galleries',
                'create' => 'create-galleries',
                'edit' => 'edit-galleries',
                'delete' => 'delete-galleries',
            ],
            'pages' => [
                'group' => 'CMS',
                'name' => 'Halaman',
                'view' => 'view-pages',
                'create' => 'create-pages',
                'edit' => 'edit-pages',
                'delete' => 'delete-pages',
            ],

            // Email Marketing
            'campaigns' => [
                'group' => 'Email Marketing',
                'name' => 'Kirim Email & Campaign',
                'view' => 'view-campaigns',
                'create' => 'create-campaigns',
                'edit' => 'edit-campaigns',
                'delete' => 'delete-campaigns',
            ],
            'templates' => [
                'group' => 'Email Marketing',
                'name' => 'Template Pesan Email',
                'view' => 'view-templates',
                'create' => 'create-templates',
                'edit' => 'edit-templates',
                'delete' => 'delete-templates',
            ],
            'contacts' => [
                'group' => 'Email Marketing',
                'name' => 'Kontak Penerima',
                'view' => 'view-contacts',
                'create' => 'create-contacts',
                'edit' => 'edit-contacts',
                'delete' => 'delete-contacts',
            ],

            // Administrasi
            'certificates' => [
                'group' => 'Administrasi',
                'name' => 'Sertifikat Digital',
                'view' => 'view-certificates',
                'create' => 'create-certificates',
                'edit' => 'edit-certificates',
                'delete' => 'delete-certificates',
            ],
            'participants' => [
                'group' => 'Administrasi',
                'name' => 'Data Peserta / OPD',
                'view' => 'view-participants',
                'create' => 'create-participants',
                'edit' => 'edit-participants',
                'delete' => 'delete-participants',
            ],
            'registrations' => [
                'group' => 'Administrasi',
                'name' => 'Pendaftaran Masuk',
                'view' => 'view-registrations',
                'create' => 'create-registrations',
                'edit' => 'edit-registrations',
                'delete' => 'delete-registrations',
            ],
            'documents' => [
                'group' => 'Administrasi',
                'name' => 'Dokumen & Regulasi',
                'view' => 'view-documents',
                'create' => 'create-documents',
                'edit' => 'edit-documents',
                'delete' => 'delete-documents',
            ],
            'messages' => [
                'group' => 'Administrasi',
                'name' => 'Pesan Masuk (Kontak)',
                'view' => 'view-messages',
                'create' => null,
                'edit' => 'reply-messages',
                'delete' => 'delete-messages',
            ],

            // Pengaturan
            'users' => [
                'group' => 'Pengaturan',
                'name' => 'Pengguna & Staff',
                'view' => 'view-users',
                'create' => 'create-users',
                'edit' => 'edit-users',
                'delete' => 'delete-users',
            ],
            'roles' => [
                'group' => 'Pengaturan',
                'name' => 'Role & Hak Akses',
                'view' => 'view-roles',
                'create' => 'create-roles',
                'edit' => 'edit-roles',
                'delete' => 'delete-roles',
            ],
            'settings' => [
                'group' => 'Pengaturan',
                'name' => 'Identitas Portal',
                'view' => 'view-settings',
                'create' => null,
                'edit' => 'edit-settings',
                'delete' => null,
            ],
            'certificate_settings' => [
                'group' => 'Pengaturan',
                'name' => 'Konfigurasi Sertifikat',
                'view' => 'view-certificate-settings',
                'create' => null,
                'edit' => 'edit-certificate-settings',
                'delete' => null,
            ],
            'system' => [
                'group' => 'Pengaturan',
                'name' => 'Sistem & Developer',
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
