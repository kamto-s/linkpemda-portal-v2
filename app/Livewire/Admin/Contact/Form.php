<?php

namespace App\Livewire\Admin\Contact;

use App\Enums\ContactCategory;
use App\Models\Contact;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin.app')]
class Form extends Component
{
    public ?Contact $contact = null;

    public bool $isEditing = false;

    public string $name = '';

    public string $email = '';

    public string $organization_name = '';

    public string $position = '';

    public string $phone = '';

    public string $category = 'pemerintah';

    public bool $is_active = true;

    public function mount(Contact|string|null $contact = null): void
    {
        if ($contact) {
            $this->contact = $contact instanceof Contact ? $contact : Contact::findOrFail($contact);
            $this->isEditing = true;

            $this->name = $this->contact->name;
            $this->email = $this->contact->email;
            $this->organization_name = $this->contact->organization_name;
            $this->position = $this->contact->position ?? '';
            $this->phone = $this->contact->phone ?? '';
            $this->category = $this->contact->category->value;
            $this->is_active = (bool) $this->contact->is_active;
        } else {
            $this->isEditing = false;
            $this->category = 'pemerintah';
            $this->is_active = true;
        }
    }

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

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama kontak wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'organization_name.required' => 'Instansi / perusahaan wajib diisi.',
            'category.required' => 'Kategori kontak wajib dipilih.',
        ];
    }

    public function save(): mixed
    {
        $validated = $this->validate();

        if ($this->isEditing && $this->contact) {
            $this->contact->update($validated);
            session()->flash('success', "Kontak \"{$this->contact->name}\" berhasil diperbarui.");
        } else {
            Contact::create($validated);
            session()->flash('success', "Kontak \"{$validated['name']}\" berhasil ditambahkan.");
        }

        return redirect()->route('admin.contact.index');
    }

    public function render()
    {
        $pageTitle = $this->isEditing ? 'Ubah Kontak: ' . $this->contact->name : 'Tambah Kontak Baru';

        return view('livewire.admin.contact.form', [
            'categories' => ContactCategory::cases(),
            'pageTitle' => $pageTitle,
        ])->title($pageTitle);
    }
}

