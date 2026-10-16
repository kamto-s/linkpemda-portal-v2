<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat 3 Role
        $superadminRole = Role::create(['name' => UserRole::Superadmin->value]);
        $adminRole = Role::create(['name' => UserRole::Admin->value]);
        $staffRole = Role::create(['name' => UserRole::Staff->value]);

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
            'view-posts', 'create-posts', 'edit-posts',
            'view-categories', 'create-categories', 'edit-categories',
        ]);

        // 5. Buat 3 Akun Testing (Password semua: "password")

        // Akun 1: Developer
        $dev = User::create([
            'name' => 'Superadmin',
            'email' => 'kamto.076s+superadmin@gmail.com',
            'password' => Hash::make('Admin@123'),
        ]);
        $dev->assignRole($superadminRole);

        // Akun 2: Pemilik Perusahaan / Admin
        $owner = User::create([
            'name' => 'Admin',
            'email' => 'kamto.076s+admin@gmail.com',
            'password' => Hash::make('Admin@123'),
        ]);
        $owner->assignRole($adminRole);

        // Akun 3: Karyawan / Staff
        $staff = User::create([
            'name' => 'Staff',
            'email' => 'kamto.076s+staff@gmail.com',
            'password' => Hash::make('Admin@123'),
        ]);
        $staff->assignRole($staffRole);

        // 6. Seed Kontak Target (Pemerintah & Swasta)
        $this->call(ContactSeeder::class);
    }
}
