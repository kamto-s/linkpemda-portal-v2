<?php

namespace App\Livewire\Admin\SystemSetting;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Pengaturan Sistem')]
class Index extends Component
{
    use WithPagination;

    public string $pageTitle = 'Pengaturan Sistem';

    public string $search = '';

    public int $perPage = 10;

    // Form Modal State
    public ?string $settingId = null;

    public string $key = '';

    public ?string $value = '';

    public ?string $description = '';

    public bool $isEditing = false;

    // Delete Modal State
    public ?string $settingToDeleteId = null;

    public string $settingToDeleteKey = '';

    protected string $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        abort_unless(
            Auth::user()?->hasRole('superadmin'),
            403,
            'Hanya Superadmin yang memiliki hak akses untuk mengelola Pengaturan Sistem.'
        );
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function edit(string $id): void
    {
        abort_unless(Auth::user()?->hasRole('superadmin'), 403);

        $setting = SystemSetting::findOrFail($id);

        $this->settingId = $setting->id;
        $this->key = (string) ($setting->key ?? $setting->setting_key ?? '');
        $this->value = (string) ($setting->value ?? $setting->setting_value ?? '');
        $this->description = $setting->description ?? '';
        $this->isEditing = true;
        $this->resetValidation();

        $this->dispatch('open-setting-modal');
    }

    public function save(): void
    {
        abort_unless(Auth::user()?->hasRole('superadmin'), 403);

        if (! $this->settingId) {
            return;
        }

        $valColumn = Schema::hasColumn('system_settings', 'value') ? 'value' : 'setting_value';

        $this->validate([
            'value' => ['nullable', 'string'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'description.max' => 'Deskripsi tidak boleh melebihi 1000 karakter.',
        ]);

        $setting = SystemSetting::findOrFail($this->settingId);
        $setting->update([
            $valColumn => $this->value,
            'description' => $this->description,
        ]);

        session()->flash('success', "Nilai pengaturan \"{$this->key}\" berhasil diperbarui.");

        $this->dispatch('close-setting-modal');
        $this->reset(['settingId', 'key', 'value', 'description', 'isEditing']);
    }

    public function confirmDelete(string $id): void
    {
        abort_unless(Auth::user()?->hasRole('superadmin'), 403);

        $setting = SystemSetting::findOrFail($id);

        $this->settingToDeleteId = $setting->id;
        $this->settingToDeleteKey = (string) ($setting->key ?? $setting->setting_key ?? '');

        $this->dispatch('open-delete-setting-modal');
    }

    public function delete(): void
    {
        abort_unless(Auth::user()?->hasRole('superadmin'), 403);

        if (! $this->settingToDeleteId) {
            return;
        }

        $setting = SystemSetting::findOrFail($this->settingToDeleteId);
        $keyName = $setting->key ?? $setting->setting_key ?? '';
        $setting->delete();

        session()->flash('success', "Pengaturan \"{$keyName}\" berhasil dihapus.");
        $this->dispatch('close-delete-setting-modal');
        $this->reset(['settingToDeleteId', 'settingToDeleteKey']);
    }

    public function render()
    {
        abort_unless(Auth::user()?->hasRole('superadmin'), 403);

        $keyColumn = Schema::hasColumn('system_settings', 'key') ? 'key' : 'setting_key';
        $valColumn = Schema::hasColumn('system_settings', 'value') ? 'value' : 'setting_value';

        $settings = SystemSetting::query()
            ->when(filled($this->search), function ($query) {
                $term = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('key', 'like', $term)
                        ->orWhere('value', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->orderBy('key', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.system-setting.index', [
            'settings' => $settings,
        ]);
    }
}
