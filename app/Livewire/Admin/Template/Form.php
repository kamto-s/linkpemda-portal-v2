<?php

namespace App\Livewire\Admin\Template;

use App\Enums\EmailTemplateCategory;
use App\Models\EmailTemplate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin.app')]
class Form extends Component
{
    public ?EmailTemplate $template = null;
    public bool $isEditing = false;

    public string $name = '';
    public string $category = 'marketing';
    public string $subject = '';
    public string $body_content = '';
    public bool $is_active = true;

    public function mount(EmailTemplate|string|null $template = null): void
    {
        if ($template) {
            $this->template = $template instanceof EmailTemplate ? $template : EmailTemplate::findOrFail($template);
            $this->isEditing = true;

            $this->name = $this->template->name;
            $this->category = $this->template->category->value;
            $this->subject = $this->template->subject;
            $this->body_content = $this->template->body_content;
            $this->is_active = (bool) $this->template->is_active;
        } else {
            $this->isEditing = false;
            $this->category = 'marketing';
            $this->is_active = true;
        }
    }

    public function insertTag(string $tag): void
    {
        $this->body_content .= $tag;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|in:marketing,info,agenda,reminder,formal',
            'subject' => 'required|string|max:255',
            'body_content' => 'required|string',
            'is_active' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama template wajib diisi.',
            'category.required' => 'Kategori template wajib dipilih.',
            'subject.required' => 'Subjek email default wajib diisi.',
            'body_content.required' => 'Isi konten email tidak boleh kosong.',
        ];
    }

    public function save(?string $content = null): mixed
    {
        if ($content !== null && $content !== '') {
            $this->body_content = $content;
        }

        $validated = $this->validate();

        if ($this->isEditing && $this->template) {
            $this->template->update($validated);
            session()->flash('success', 'Template email berhasil diperbarui.');
        } else {
            $validated['is_system'] = false;
            EmailTemplate::create($validated);
            session()->flash('success', 'Template email baru berhasil ditambahkan.');
        }

        return redirect()->route('admin.template.index');
    }

    public function render()
    {
        $pageTitle = $this->isEditing ? 'Ubah Template: ' . $this->template->name : 'Tambah Template Email Baru';

        return view('livewire.admin.template.form', [
            'categories' => EmailTemplateCategory::cases(),
            'pageTitle' => $pageTitle,
        ])->title($pageTitle);
    }
}
