<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // -----------------------------------------------------------------
            // Pengaturan Identitas & Umum
            // -----------------------------------------------------------------
            [
                'key' => 'app_name',
                'value' => 'Linkpemda',
                'description' => 'Nama resmi portal aplikasi yang ditampilkan pada judul browser, header, dan navigasi portal.',
            ],
            [
                'key' => 'app_tagline',
                'value' => 'Lembaga Informasi Keuangan dan Pembangunan Daerah',
                'description' => 'Slogan atau deskripsi singkat identitas kelembagaan portal.',
            ],
            [
                'key' => 'app_timezone',
                'value' => 'Asia/Jakarta',
                'description' => 'Zona waktu standar operasional sistem (WIB: Asia/Jakarta, WITA: Asia/Makassar, WIT: Asia/Jayapura).',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'description' => 'Status pemeliharaan/perbaikan portal (true = hanya superadmin yang dapat login, false = normal).',
            ],

            // -----------------------------------------------------------------
            // Pengaturan Email & Blast Campaign
            // -----------------------------------------------------------------
            [
                'key' => 'contact_support_email',
                'value' => 'info@linkpemda.com',
                'description' => 'Alamat email pusat bantuan resmi / helpdesk yang ditampilkan ke publik.',
            ],
            [
                'key' => 'email_header_logo',
                'value' => 'https://linkpemda.com/storage/logo-header-dark.png',
                'description' => 'URL logo header institusi yang disematkan pada setiap template email blast dan notifikasi keluar.',
            ],
            [
                'key' => 'mail_sender_address',
                'value' => 'info@linkpemda.com',
                'description' => 'Alamat email pengirim default (From Address) untuk pengiriman broadcast campaign & notifikasi sistem.',
            ],
            [
                'key' => 'mail_sender_name',
                'value' => 'Linkpemda',
                'description' => 'Nama pengirim default (From Name) yang tampil di kotak masuk penerima email broadcast campaign.',
            ],
            [
                'key' => 'max_daily_email_limit',
                'value' => '100',
                'description' => 'Batas kuota harian maksimum pengiriman email broadcast campaign (0 = tanpa batas).',
            ],

            // -----------------------------------------------------------------
            // Pengaturan Sistem & Unggahan
            // -----------------------------------------------------------------
            [
                'key' => 'max_upload_size_mb',
                'value' => '10',
                'description' => 'Batas ukuran maksimum berkas lampiran atau dokumen yang diizinkan untuk diunggah (dalam satuan MB).',
            ],
            [
                'key' => 'session_lifetime_minutes',
                'value' => '120',
                'description' => 'Batas durasi inaktivitas sesi login pengguna sebelum otomatis logout demi keamanan (dalam menit).',
            ],

            // -----------------------------------------------------------------
            // Template PDF - Blanko Cetak Fisik
            // -----------------------------------------------------------------
            [
                'key' => 'temp_landscape_blank',
                'value' => 'https://linkpemda.com/uploads/blank-landscape-6m2x4p9h.pdf',
                'description' => 'Path URL berkas template PDF sertifikat blanko format Landscape (tanpa background/desain warna, khusus cetak di kertas blanko fisik).',
            ],
            [
                'key' => 'temp_portrait_blank',
                'value' => 'https://linkpemda.com/uploads/blank-potrait-3w7q5v8k.pdf',
                'description' => 'Path URL berkas template PDF sertifikat blanko format Portrait (tanpa background/desain warna, khusus cetak di kertas blanko fisik).',
            ],

            // -----------------------------------------------------------------
            // Template PDF - Linkpemda
            // -----------------------------------------------------------------
            [
                'key' => 'temp_linkpemda_invoice',
                'value' => 'https://linkpemda.com/uploads/blank-potrait-3w7q5v8k.pdf',
                'description' => 'Path URL berkas template PDF invoice tagihan pembayaran peserta kegiatan Linkpemda.',
            ],
            [
                'key' => 'temp_linkpemda_kwitansi',
                'value' => 'https://linkpemda.com/uploads/linkpemda-kwitansi-4h8x9p2m.pdf',
                'description' => 'Path URL berkas template PDF kwitansi bukti pembayaran lunas peserta kegiatan Linkpemda.',
            ],
            [
                'key' => 'temp_linkpemda_sertifikat',
                'value' => 'https://linkpemda.com/uploads/linkpemda-sertifikat-7q3w5k9v.pdf',
                'description' => 'Path URL berkas template PDF sertifikat resmi Linkpemda (lengkap dengan ornamen dan background warna).',
            ],

            // -----------------------------------------------------------------
            // Template PDF - Tren Media Diklat (Trans Edukasi Nasional)
            // -----------------------------------------------------------------
            [
                'key' => 'temp_tren_invoice',
                'value' => 'https://linkpemda.com/uploads/blank-potrait-3w7q5v8k.pdf',
                'description' => 'Path URL berkas template PDF invoice tagihan pembayaran peserta Tren Media Diklat (Trans Edukasi Nasional).',
            ],
            [
                'key' => 'temp_tren_kwitansi',
                'value' => 'https://linkpemda.com/uploads/blank-potrait-3w7q5v8k.pdf',
                'description' => 'Path URL berkas template PDF kwitansi bukti pembayaran lunas peserta Tren Media Diklat (Trans Edukasi Nasional).',
            ],
            [
                'key' => 'temp_tren_sertifikat',
                'value' => 'https://linkpemda.com/uploads/tren-sertifikat-5k7v3q2w.pdf',
                'description' => 'Path URL berkas template PDF sertifikat resmi Tren Media Diklat / Trans Edukasi Nasional (lengkap dengan ornamen dan background warna).',
            ],
        ];

        SystemSetting::upsert(
            $settings,
            ['key'],
            ['value', 'description']
        );
    }
}
