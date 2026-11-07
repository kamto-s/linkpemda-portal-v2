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
            [
                'key' => 'app_name',
                'value' => 'Linkpemda',
                'description' => 'Nama resmi portal aplikasi yang ditampilkan pada judul dan header portal.',
            ],
            [
                'key' => 'app_tagline',
                'value' => 'Lembaga Informasi Keuangan dan Pembangunan Daerah',
                'description' => 'Slogan atau deskripsi singkat identitas portal.',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'description' => 'Status mode perbaikan/pemeliharaan portal (true = aktif, false = nonaktif).',
            ],
            [
                'key' => 'mail_sender_name',
                'value' => 'Linkpemda',
                'description' => 'Nama identitas pengirim default untuk pengiriman email campaign & notifikasi sistem.',
            ],
            [
                'key' => 'mail_sender_address',
                'value' => 'info@linkpemda.com',
                'description' => 'Alamat email default untuk pengiriman campaign & notifikasi sistem.',
            ],
            [
                'key' => 'contact_support_email',
                'value' => 'info@linkpemda.com',
                'description' => 'Email helpdesk / bantuan resmi yang dapat dihubungi oleh pengunjung portal.',
            ],
            [
                'key' => 'email_header_logo',
                'value' => 'https://linkpemda.com/storage/logo-header-dark.png',
                'description' => 'Path atau tautan URL logo yang ditampilkan pada header email template dan blast campaign.',
            ],
            [
                'key' => 'max_daily_email_limit',
                'value' => '200',
                'description' => 'Batas maksimum kuota pengiriman email broadcast atau campaign dalam 1 hari (0 = tidak terbatas).',
            ],
            [
                'key' => 'max_upload_size_mb',
                'value' => '10',
                'description' => 'Batas maksimum ukuran berkas lampiran yang diizinkan untuk diunggah (dalam Megabyte).',
            ],
            [
                'key' => 'session_lifetime_minutes',
                'value' => '120',
                'description' => 'Durasi waktu sesi pengguna sebelum logout otomatis karena tidak aktif (dalam menit).',
            ],
            [
                'key' => 'app_timezone',
                'value' => 'Asia/Jakarta',
                'description' => 'Zona waktu standar aplikasi (WIB: Asia/Jakarta, WITA: Asia/Makassar, WIT: Asia/Jayapura).',
            ],
            // -----------------------------------------------------------------
            // Template PDF - Linkpemda
            // -----------------------------------------------------------------
            [
                'key' => 'pdf_linkpemda_sertifikat',
                'value' => 'assets/pdf-templates/linkpemda/sertifikat.pdf',
                'description' => 'Path file template PDF sertifikat default Linkpemda (dengan background/ornamen warna).',
            ],
            [
                'key' => 'pdf_linkpemda_kwitansi',
                'value' => 'assets/pdf-templates/linkpemda/kwitansi.pdf',
                'description' => 'Path file template PDF kwitansi pembayaran peserta Linkpemda.',
            ],
            [
                'key' => 'pdf_linkpemda_invoice',
                'value' => 'assets/pdf-templates/linkpemda/invoice.pdf',
                'description' => 'Path file template PDF invoice pembayaran peserta Linkpemda.',
            ],
            // -----------------------------------------------------------------
            // Template PDF - Tren Media Diklat (Trans Edukasi Nasional)
            // -----------------------------------------------------------------
            [
                'key' => 'pdf_tren_sertifikat',
                'value' => 'assets/pdf-templates/trenmedia/sertifikat.pdf',
                'description' => 'Path file template PDF sertifikat default Tren Media Diklat (Trans Edukasi Nasional).',
            ],
            [
                'key' => 'pdf_tren_kwitansi',
                'value' => 'assets/pdf-templates/trenmedia/kwitansi.pdf',
                'description' => 'Path file template PDF kwitansi pembayaran peserta Tren Media Diklat.',
            ],
            [
                'key' => 'pdf_tren_invoice',
                'value' => 'assets/pdf-templates/trenmedia/invoice.pdf',
                'description' => 'Path file template PDF invoice pembayaran peserta Tren Media Diklat.',
            ],
            // blank sertifikat
            [
                'key' => 'pdf_sertifikat_blank',
                'value' => 'assets/pdf-templates/linkpemda/sertifikat_blank.pdf',
                'description' => 'Path file template PDF sertifikat blank Linkpemda (tanpa background, untuk cetak di blanko fisik).',
            ],
        ];

        foreach ($settings as $setting) {
            $key = $setting['key'] ?? null;
            $value = $setting['value'] ?? null;
            $description = $setting['description'] ?? null;

            if ($key) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'description' => $description,
                    ]
                );
            }
        }
    }
}
