<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat 3 Role Dasar
        $superadminRole = Role::firstOrCreate(['name' => UserRole::Superadmin->value, 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => UserRole::Admin->value, 'guard_name' => 'web']);
        $staffRole = Role::firstOrCreate(['name' => UserRole::Staff->value, 'guard_name' => 'web']);

        // 3. Buat Permissions Dasar (Format CRUD Murni)
        $permissions = [
            // CMS - Artikel & Materi
            'view-posts',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'publish-posts',

            // CMS - Kategori
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',
            'manage-categories',

            // Pengguna & Staff
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'manage-users',

            // Pengaturan Portal
            'view-settings',
            'edit-settings',
            'manage-settings',

            // Sistem & Role
            'manage-system',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 4. Pembagian Izin ke Role:
        // Superadmin -> Dapat SEMUA izin teknis & sistem
        $superadminRole->syncPermissions(Permission::all());

        // Admin -> Dapat semua kecuali manage-system
        $adminRole->syncPermissions(
            Permission::where('name', '!=', 'manage-system')->get()
        );

        // Staff -> Hanya buat/edit konten & kategori
        $staffRole->syncPermissions([
            'view-posts',
            'create-posts',
            'edit-posts',
            'view-categories',
            'create-categories',
            'edit-categories',
        ]);
    }
}
