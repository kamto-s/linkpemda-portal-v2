<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class TempAbsensiLinkpemdaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::updateOrCreate(
            ['key' => 'temp_absensi_linkpemda'],
            [
                'value' => 'https://linkpemda.com/uploads/blank-potrait-3w7q5v8k.pdf',
                'description' => 'Path URL berkas template PDF daftar hadir / absensi kegiatan Linkpemda (Kop surat A4 Portrait).',
            ]
        );
    }
}

