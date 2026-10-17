<?php

namespace App\Livewire\Admin\Template;

use App\Enums\EmailTemplateCategory;
use App\Models\EmailTemplate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
class Index extends Component
{
    use WithPagination;

    public string $pageTitle = 'Template Email';

    public string $search = '';

    public string $selectedCategory = '';

    public string $selectedStatus = '';

    public int $perPage = 10;

    // Delete confirmation properties (matching User Index modal)
    public ?string $templateToDeleteId = null;

    public string $templateToDeleteName = '';

    protected $paginationTheme = 'bootstrap';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedCategory', 'selectedStatus']);
        $this->resetPage();
    }

    public function toggleStatus(string $id): void
    {
        $tpl = EmailTemplate::findOrFail($id);
        $tpl->update(['is_active' => !$tpl->is_active]);
        $statusText = $tpl->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Status template \"{$tpl->name}\" berhasil {$statusText}.");
    }

    public function toggleActive(string $id): void
    {
        $this->toggleStatus($id);
    }

    public function confirmDelete(string $id): void
    {
        $tpl = EmailTemplate::findOrFail($id);

        if ($tpl->is_system) {
            session()->flash('error', 'Template bawaan sistem tidak dapat dihapus.');
            return;
        }

        $this->templateToDeleteId = $tpl->id;
        $this->templateToDeleteName = $tpl->name;

        $this->dispatch('open-delete-modal');
    }

    public function delete(?string $id = null): void
    {
        $targetId = $id ?: $this->templateToDeleteId;

        if (!$targetId) {
            return;
        }

        $tpl = EmailTemplate::findOrFail($targetId);

        if ($tpl->is_system) {
            session()->flash('error', 'Template bawaan sistem tidak dapat dihapus.');
            $this->dispatch('close-delete-modal');
            return;
        }

        $name = $tpl->name;
        $tpl->delete();

        session()->flash('success', "Template \"{$name}\" berhasil dihapus.");

        $this->dispatch('close-delete-modal');
        $this->reset(['templateToDeleteId', 'templateToDeleteName']);
    }

    public function render()
    {
        $templates = EmailTemplate::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('subject', 'like', "%{$this->search}%");
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('category', $this->selectedCategory);
            })
            ->when($this->selectedStatus === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->selectedStatus === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->latest('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.template.index', [
            'templates' => $templates,
            'categories' => EmailTemplateCategory::cases(),
        ]);
    }
}
