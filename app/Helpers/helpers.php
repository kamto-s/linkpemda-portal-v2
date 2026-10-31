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
