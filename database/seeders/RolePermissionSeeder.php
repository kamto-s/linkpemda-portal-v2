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

        // 3. Buat Permissions Lengkap Sesuai Seluruh Modul & Menu
        $permissions = [
            // Dashboard
            'view-dashboard',

            // CMS - Artikel & Materi
            'view-posts',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'publish-posts',

            // CMS - Kategori Konten
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',

            // CMS - Jadwal Kegiatan
            'view-schedules',
            'create-schedules',
            'edit-schedules',
            'delete-schedules',

            // CMS - Galeri & Media
            'view-galleries',
            'create-galleries',
            'edit-galleries',
            'delete-galleries',

            // CMS - Halaman Statis (Pages)
            'view-pages',
            'create-pages',
            'edit-pages',
            'delete-pages',

            // Email Marketing - Campaign
            'view-campaigns',
            'create-campaigns',
            'edit-campaigns',
            'delete-campaigns',
            'send-campaigns',

            // Email Marketing - Template Pesan
            'view-templates',
            'create-templates',
            'edit-templates',
            'delete-templates',

            // Email Marketing - Kontak Penerima
            'view-contacts',
            'create-contacts',
            'edit-contacts',
            'delete-contacts',
            'import-contacts',
            'export-contacts',

            // Administrasi - Sertifikat Digital
            'view-certificates',
            'create-certificates',
            'edit-certificates',
            'delete-certificates',

            // Administrasi - Data Peserta / OPD
            'view-participants',
            'create-participants',
            'edit-participants',
            'delete-participants',

            // Administrasi - Pendaftaran Masuk
            'view-registrations',
            'create-registrations',
            'edit-registrations',
            'delete-registrations',

            // Administrasi - Dokumen & Regulasi
            'view-documents',
            'create-documents',
            'edit-documents',
            'delete-documents',

            // Administrasi - Pesan Masuk (Kontak Kami)
            'view-messages',
            'reply-messages',
            'delete-messages',

            // Pengaturan - Pengguna & Staff
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'manage-users',

            // Pengaturan - Role & Hak Akses
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',

            // Pengaturan - Identitas Portal
            'view-settings',
            'edit-settings',

            // Pengaturan - Konfigurasi Sertifikat
            'view-certificate-settings',
            'edit-certificate-settings',

            // Sistem & Developer
            'manage-system',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $superadminRole->syncPermissions(Permission::all());

        $adminRole->syncPermissions([
            'view-dashboard',
        ]);

        $staffRole->syncPermissions([
            'view-dashboard',
        ]);
    }
}
