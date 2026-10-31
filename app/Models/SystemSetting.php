<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SystemSetting extends Model
{
    use HasUuids;

    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
        'description',
        'setting_key',
        'setting_value',
    ];

    /**
     * Mengambil nilai pengaturan berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting && Schema::hasColumn('system_settings', 'setting_key')) {
            $setting = static::where('setting_key', $key)->first();
        }

        if (! $setting) {
            return $default;
        }

        return $setting->value ?? $setting->setting_value ?? $default;
    }

    /**
     * Menyimpan atau memperbarui nilai pengaturan.
     */
    public static function set(string $key, mixed $value, ?string $description = null): static
    {
        $val = is_array($value) ? json_encode($value) : $value;
        $data = ['value' => $val];

        if ($description !== null) {
            $data['description'] = $description;
        }

        if (Schema::hasColumn('system_settings', 'key')) {
            return static::updateOrCreate(
                ['key' => $key],
                $data
            );
        }

        return static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $val, 'description' => $description]
        );
    }

    /**
     * Memeriksa apakah setting dengan key tertentu ada.
     */
    public static function has(string $key): bool
    {
        if (Schema::hasColumn('system_settings', 'key')) {
            return static::where('key', $key)->exists();
        }

        return static::where('setting_key', $key)->exists();
    }

    /**
     * Menghapus pengaturan berdasarkan key.
     */
    public static function forget(string $key): bool
    {
        if (Schema::hasColumn('system_settings', 'key')) {
            return (bool) static::where('key', $key)->delete();
        }

        return (bool) static::where('setting_key', $key)->delete();
    }

    /**
     * Dukungan fleksibel untuk akses PascalCase & aliases:
     * $setting->key, $setting->setting_key, $setting->SettingKey,
     * $setting->value, $setting->setting_value, $setting->SettingValue.
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($value !== null) {
            return $value;
        }

        if (in_array($key, ['setting_key', 'SettingKey'])) {
            return $this->attributes['key'] ?? $this->attributes['setting_key'] ?? null;
        }

        if (in_array($key, ['setting_value', 'SettingValue'])) {
            return $this->attributes['value'] ?? $this->attributes['setting_value'] ?? null;
        }

        if ($key === 'Description') {
            return $this->attributes['description'] ?? null;
        }

        $snake = Str::snake($key);
        if ($snake !== $key) {
            return parent::getAttribute($snake);
        }

        return null;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, ['setting_key', 'SettingKey'])) {
            $key = Schema::hasColumn('system_settings', 'key') ? 'key' : 'setting_key';
        }

        if (in_array($key, ['setting_value', 'SettingValue'])) {
            $key = Schema::hasColumn('system_settings', 'value') ? 'value' : 'setting_value';
        }

        if ($key === 'Description') {
            $key = 'description';
        }

        return parent::setAttribute($key, $value);
    }
}
