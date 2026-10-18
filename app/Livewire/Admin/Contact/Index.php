<?php

namespace App\Livewire\Admin\Contact;

use App\Enums\ContactCategory;
use App\Models\Contact;
use App\Services\ContactImportService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Kontak')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $pageTitle = 'Kontak';

    public string $search = '';

    public string $selectedCategory = '';

    public string $selectedStatus = '';

    public int $perPage = 10;

    // Delete confirmation properties
    public ?string $contactToDeleteId = null;

    public string $contactToDeleteName = '';

    // Import Excel properties
    public $importFile = null;

    public string $duplicateHandling = 'update';

    public ?array $importSummary = null;

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

    public function openImportModal(): void
    {
        $this->resetValidation();
        $this->reset(['importFile', 'importSummary']);
        $this->dispatch('open-import-modal');
    }

    public function closeImportModal(): void
    {
        $this->reset(['importFile', 'importSummary']);
        $this->dispatch('close-import-modal');
    }

    public function downloadTemplate(ContactImportService $service)
    {
        return response()->streamDownload(function () use ($service) {
            echo $service->generateTemplate();
        }, 'template-import-kontak.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importExcel(ContactImportService $service): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ], [
            'importFile.required' => 'Silakan pilih berkas Excel atau CSV terlebih dahulu.',
            'importFile.mimes' => 'Format berkas harus .xlsx, .xls, atau .csv.',
            'importFile.max' => 'Ukuran berkas maksimal 10 MB.',
        ]);

        $filePath = $this->importFile->getRealPath();
        $summary = $service->import($filePath, $this->duplicateHandling);

        $this->importSummary = $summary;

        if ($summary['imported'] > 0 || $summary['updated'] > 0) {
            $msg = "Proses import selesai: {$summary['imported']} kontak baru ditambahkan";
            if ($summary['updated'] > 0) {
                $msg .= ", {$summary['updated']} kontak diperbarui";
            }
            if ($summary['skipped'] > 0) {
                $msg .= ", {$summary['skipped']} kontak dilewati";
            }
            $msg .= '.';
            session()->flash('success', $msg);
        }

        $this->reset('importFile');
        $this->resetPage();
    }

    public function toggleStatus(string $id): void
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['is_active' => ! $contact->is_active]);
        $statusText = $contact->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "Status kontak \"{$contact->name}\" berhasil {$statusText}.");
    }

    public function toggleActive(string $id): void
    {
        $this->toggleStatus($id);
    }

    public function confirmDelete(string $id): void
    {
        $contact = Contact::findOrFail($id);
        $this->contactToDeleteId = $contact->id;
        $this->contactToDeleteName = $contact->name;

        $this->dispatch('open-delete-modal');
    }

    public function delete(?string $id = null): void
    {
        $targetId = $id ?: $this->contactToDeleteId;

        if (! $targetId) {
            return;
        }

        $contact = Contact::findOrFail($targetId);
        $name = $contact->name;
        $contact->delete();

        session()->flash('success', "Kontak \"{$name}\" berhasil dihapus.");

        $this->dispatch('close-delete-modal');
        $this->reset(['contactToDeleteId', 'contactToDeleteName']);
    }

    public function render()
    {
        $contacts = Contact::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('organization_name', 'like', "%{$this->search}%")
                        ->orWhere('position', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
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

        return view('livewire.admin.contact.index', [
            'contacts' => $contacts,
            'categories' => ContactCategory::cases(),
        ]);
    }
}
