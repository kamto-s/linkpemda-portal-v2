<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Superadmin (Developer)
        $dev = User::updateOrCreate(
            ['email' => 'kamto.076s+superadmin@gmail.com'],
            [
                'name' => 'Superadmin',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
            ]
        );
        $dev->syncRoles([UserRole::Superadmin->value]);

        // 2. Akun Admin (Pemilik Perusahaan / Manajemen)
        $owner = User::updateOrCreate(
            ['email' => 'kamto.076s+admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
            ]
        );
        $owner->syncRoles([UserRole::Admin->value]);

        // 3. Akun Staff (Karyawan / Operator)
        $staff = User::updateOrCreate(
            ['email' => 'kamto.076s+staff@gmail.com'],
            [
                'name' => 'Staff',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
            ]
        );
        $staff->syncRoles([UserRole::Staff->value]);
    }
}

