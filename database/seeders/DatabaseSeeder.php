<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat 3 Role
        $superadminRole = Role::create(['name' => UserRole::Superadmin->value]);
        $adminRole      = Role::create(['name' => UserRole::Admin->value]);
        $staffRole      = Role::create(['name' => UserRole::Staff->value]);

        // 3. Buat Permissions Dasar
        $permissions = [
            // Hak Akses Sistem & Pengaturan
            'manage-system',
            'manage-settings',

            // Hak Akses Pengguna & Karyawan
            'manage-users',
            'view-users',

            // Hak Akses Konten / CMS
            'publish-posts',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'manage-categories',
        ];

        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm]);
        }

        // 4. Pembagian Izin ke Role:
        // Superadmin (Dev) -> Dapat SEMUA izin teknis & sistem
        $superadminRole->givePermissionTo(Permission::all());

        // Admin (Pemilik) -> Dapat kelola user, pengaturan web, publish & hapus konten
        $adminRole->givePermissionTo([
            'manage-settings',
            'manage-users',
            'view-users',
            'publish-posts',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'manage-categories',
        ]);

        // Staff (Karyawan) -> Hanya buat/edit draft konten dan kategori, tidak bisa hapus atau kelola user
        $staffRole->givePermissionTo([
            'create-posts',
            'edit-posts',
            'manage-categories',
        ]);

        // 5. Buat 3 Akun Testing (Password semua: "password")

        // Akun 1: Developer
        $dev = User::create([
            'name'     => 'Superadmin',
            'email'    => 'kamto.076s+superadmin@gmail.com',
            'password' => Hash::make('Admin@123'),
        ]);
        $dev->assignRole($superadminRole);

        // Akun 2: Pemilik Perusahaan / Admin
        $owner = User::create([
            'name'     => 'Admin',
            'email'    => 'kamto.076s+admin@gmail.com',
            'password' => Hash::make('Admin@123'),
        ]);
        $owner->assignRole($adminRole);

        // Akun 3: Karyawan / Staff
        $staff = User::create([
            'name'     => 'Staff',
            'email'    => 'kamto.076s+staff@gmail.com',
            'password' => Hash::make('Admin@123'),
        ]);
        $staff->assignRole($staffRole);
    }
}
