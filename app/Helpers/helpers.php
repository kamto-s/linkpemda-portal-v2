<?php

use App\Models\SystemSetting;

if (! function_exists('system_setting')) {
    /**
     * Helper global untuk mengambil nilai pengaturan sistem secara cepat.
     */
    function system_setting(string $key, mixed $default = null): mixed
    {
        return SystemSetting::get($key, $default);
    }
}

if (! function_exists('terbilang')) {
    /**
     * Mengubah angka nominal menjadi kalimat terbilang Bahasa Indonesia.
     * Contoh: 5000000 -> lima juta
     */
    function terbilang(float|int|string $angka): string
    {
        $angka = abs((float) $angka);
        $baca = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        $terbilang = '';

        if ($angka < 12) {
            $terbilang = ' ' . $baca[(int) $angka];
        } elseif ($angka < 20) {
            $terbilang = terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            $terbilang = terbilang(intdiv((int) $angka, 10)) . ' puluh ' . terbilang((int) $angka % 10);
        } elseif ($angka < 200) {
            $terbilang = ' seratus ' . terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = terbilang(intdiv((int) $angka, 100)) . ' ratus ' . terbilang((int) $angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = ' seribu ' . terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = terbilang(intdiv((int) $angka, 1000)) . ' ribu ' . terbilang((int) $angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = terbilang(intdiv((int) $angka, 1000000)) . ' juta ' . terbilang((int) $angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $terbilang = terbilang(intdiv((int) $angka, 1000000000)) . ' milyar ' . terbilang(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $terbilang = terbilang(intdiv((int) $angka, 1000000000000)) . ' triliun ' . terbilang(fmod($angka, 1000000000000));
        }

        return trim((string) preg_replace('/\s+/', ' ', $terbilang));
    }
}

