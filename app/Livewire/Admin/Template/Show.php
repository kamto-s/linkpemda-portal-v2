<?php

namespace App\Livewire\Admin\Template;

use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin.app')]
class Show extends Component
{
    public EmailTemplate $template;
    public string $previewDevice = 'desktop'; // 'desktop' or 'mobile'

    public function mount(EmailTemplate|string $template): void
    {
        $this->template = $template instanceof EmailTemplate ? $template : EmailTemplate::findOrFail($template);
    }

    public function setDevice(string $device): void
    {
        $this->previewDevice = in_array($device, ['desktop', 'mobile']) ? $device : 'desktop';
    }

    public function toggleActive(): void
    {
        $this->template->update(['is_active' => !$this->template->is_active]);
        $this->template->refresh();
        session()->flash('success', 'Status keaktifan template berhasil diubah.');
    }

    public function delete(): mixed
    {
        if ($this->template->is_system) {
            session()->flash('error', 'Template bawaan sistem tidak dapat dihapus.');
            return null;
        }

        $this->template->delete();
        session()->flash('success', 'Template email berhasil dihapus.');
        return redirect()->route('admin.template.index');
    }

    public function render()
    {
        $sampleContact = Contact::where('is_active', true)->first();
        if (!$sampleContact) {
            $sampleContact = new Contact([
                'name' => 'Bpk. Hendra Gunawan, S.STP, M.Si',
                'organization_name' => 'Dinas Kominfo Provinsi Jawa Barat',
                'position' => 'Kepala Dinas',
                'email' => 'hendra@jabarprov.go.id',
            ]);
        }

        $replacements = [
            '{nama}' => $sampleContact->name,
            '{instansi}' => $sampleContact->organization_name,
            '{jabatan}' => $sampleContact->position ?: 'Pimpinan / Pejabat Terkait',
            '{email}' => $sampleContact->email,
        ];

        $renderedContent = str_ireplace(array_keys($replacements), array_values($replacements), $this->template->body_content);

        $previewHtml = view('emails.campaign', [
            'campaign' => new EmailCampaign([
                'title' => $this->template->name,
                'subject' => $this->template->subject,
                'body_content' => $this->template->body_content,
            ]),
            'contact' => $sampleContact,
            'renderedContent' => $renderedContent,
            'subject' => $this->template->subject,
        ])->render();

        return view('livewire.admin.template.show', [
            'previewHtml' => $previewHtml,
            'sampleContact' => $sampleContact,
        ])->title('Detail Template: ' . $this->template->name);
    }
}

