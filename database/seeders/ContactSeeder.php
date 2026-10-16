<?php

namespace Database\Seeders;

use App\Enums\ContactCategory;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus kontak lama agar hanya tersisa 2 kontak pengujian
        Contact::query()->forceDelete();

        $contacts = [
            // 1. Pemerintah
            [
                'name' => 'Bpk. Hendra Gunawan, S.STP, M.Si',
                'email' => 'kamto.076s+pemerintah@gmail.com',
                'organization_name' => 'Pemerintah Daerah (Pemda)',
                'position' => 'Kepala Dinas Kominfo',
                'phone' => '082199240262',
                'category' => ContactCategory::Pemerintah,
                'is_active' => true,
            ],
            // 2. Swasta
            [
                'name' => 'Bpk. Dimas Pratama',
                'email' => 'kamto.076s+swasta@gmail.com',
                'organization_name' => 'PT Solusi Teknologi Nusantara',
                'position' => 'Direktur Utama',
                'phone' => '082199240262',
                'category' => ContactCategory::Swasta,
                'is_active' => true,
            ],
        ];

        foreach ($contacts as $contactData) {
            Contact::create($contactData);
        }
    }
}
