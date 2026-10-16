<?php

namespace App\Livewire\Admin\Contact;

use App\Enums\ContactCategory;
use App\Models\Contact;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Kontak Target')]
class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $pageTitle = 'Kontak Target';

    public string $search = '';
    public string $filterCategory = 'all';
    public int $perPage = 10;

    // Form modal state
    public bool $showModal = false;
    public ?string $contactId = null;
    public string $name = '';
    public string $email = '';
    public string $organization_name = '';
    public string $position = '';
    public string $phone = '';
    public string $category = 'pemerintah';
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'organization_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'category' => 'required|in:pemerintah,swasta',
            'is_active' => 'boolean',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCategory(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset(['contactId', 'name', 'email', 'organization_name', 'position', 'phone']);
        $this->category = 'pemerintah';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(string $id): void
    {
        $this->resetValidation();
        $contact = Contact::findOrFail($id);
        $this->contactId = $contact->id;
        $this->name = $contact->name;
        $this->email = $contact->email;
        $this->organization_name = $contact->organization_name;
        $this->position = $contact->position ?? '';
        $this->phone = $contact->phone ?? '';
        $this->category = $contact->category->value;
        $this->is_active = $contact->is_active;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->contactId) {
            $contact = Contact::findOrFail($this->contactId);
            $contact->update($validated);
            session()->flash('success', 'Kontak berhasil diperbarui.');
        } else {
            Contact::create($validated);
            session()->flash('success', 'Kontak baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function toggleActive(string $id): void
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['is_active' => !$contact->is_active]);
        session()->flash('success', 'Status kontak berhasil diperbarui.');
    }

    public function delete(string $id): void
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        session()->flash('success', 'Kontak berhasil dihapus.');
    }

    public function render()
    {
        $contacts = Contact::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('organization_name', 'like', "%{$this->search}%")
                        ->orWhere('position', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterCategory !== 'all', function ($query) {
                $query->where('category', $this->filterCategory);
            })
            ->latest()
            ->paginate($this->perPage);

        $countPemerintah = Contact::where('category', ContactCategory::Pemerintah->value)->count();
        $countSwasta = Contact::where('category', ContactCategory::Swasta->value)->count();
        $countTotal = Contact::count();

        return view('livewire.admin.contact.index', [
            'contacts' => $contacts,
            'countPemerintah' => $countPemerintah,
            'countSwasta' => $countSwasta,
            'countTotal' => $countTotal,
        ]);
    }
}

