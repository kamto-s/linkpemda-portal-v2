<?php

use App\Livewire\Admin\SystemSetting\Index as SystemSettingIndex;
use App\Models\SystemSetting;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
});

test('non-superadmin cannot access system settings page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(SystemSettingIndex::class)
        ->assertForbidden();
});

test('superadmin can access system settings page and view settings', function () {
    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');

    SystemSetting::set('test_portal_title', 'Portal Uji Coba', 'Deskripsi pengujian');

    Livewire::actingAs($superadmin)
        ->test(SystemSettingIndex::class)
        ->assertOk()
        ->assertSee('test_portal_title')
        ->assertSee('Portal Uji Coba')
        ->assertSee('Deskripsi pengujian');
});

test('superadmin can create, edit, and delete system settings', function () {
    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');

    // Create
    Livewire::actingAs($superadmin)
        ->test(SystemSettingIndex::class)
        ->call('openCreateModal')
        ->set('key', 'api_timeout_seconds')
        ->set('value', '30')
        ->set('description', 'Batas waktu koneksi API dalam detik')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('close-setting-modal');

    $this->assertDatabaseHas('system_settings', [
        'key' => 'api_timeout_seconds',
        'value' => '30',
    ]);

    $setting = SystemSetting::where('key', 'api_timeout_seconds')->first();

    // Edit
    Livewire::actingAs($superadmin)
        ->test(SystemSettingIndex::class)
        ->call('edit', $setting->id)
        ->assertSet('key', 'api_timeout_seconds')
        ->assertSet('value', '30')
        ->set('value', '60')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('system_settings', [
        'key' => 'api_timeout_seconds',
        'value' => '60',
    ]);

    // Delete
    Livewire::actingAs($superadmin)
        ->test(SystemSettingIndex::class)
        ->call('confirmDelete', $setting->id)
        ->assertSet('settingToDeleteId', $setting->id)
        ->call('delete')
        ->assertDispatched('close-delete-setting-modal');

    $this->assertDatabaseMissing('system_settings', [
        'key' => 'api_timeout_seconds',
    ]);
});

test('system setting helper get and set work correctly', function () {
    SystemSetting::set('global_currency', 'IDR', 'Mata uang default');

    expect(SystemSetting::get('global_currency'))->toBe('IDR');
    expect(SystemSetting::get('non_existent_key', 'default_val'))->toBe('default_val');
    expect(system_setting('global_currency'))->toBe('IDR');
    expect(system_setting('non_existent_key', 'fallback'))->toBe('fallback');
    expect(SystemSetting::has('global_currency'))->toBeTrue();
    expect(SystemSetting::has('unknown_key'))->toBeFalse();

    SystemSetting::forget('global_currency');
    expect(SystemSetting::has('global_currency'))->toBeFalse();
});

test('application timezone is set to WIB (Asia/Jakarta)', function () {
    expect(config('app.timezone'))->toBe('Asia/Jakarta');
    expect(date_default_timezone_get())->toBe('Asia/Jakarta');
});
