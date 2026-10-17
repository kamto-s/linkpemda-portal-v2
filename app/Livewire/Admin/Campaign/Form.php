<?php

namespace App\Livewire\Admin\Campaign;

use App\Enums\CampaignStatus;
use App\Enums\ContactCategory;
use App\Enums\EmailTemplateCategory;
use App\Enums\RecipientStatus;
use App\Jobs\SendCampaignEmailJob;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use App\Models\EmailTemplate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin.app')]
#[Title('Email Blast')]
class Form extends Component
{
    public ?EmailCampaign $campaign = null;
    public bool $isEditing = false;

    // Wizard Step
    public int $currentStep = 1; // 1 = Konten Email, 2 = Target Penerima, 3 = Review & Kirim

    // Fields
    public string $title = '';
    public string $subject = '';
    public string $body_content = '';

    // Target Selection
    public array $selectedContacts = [];
    public string $contactCategory = 'all'; // all, pemerintah, swasta
    public string $searchContact = '';

    // Preview
    public bool $showPreview = false;
    public string $previewHtml = '';

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_content' => 'required|string|min:5',
            'selectedContacts' => 'required|array|min:1',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Judul campaign wajib diisi.',
            'subject.required' => 'Subjek email wajib diisi.',
            'body_content.required' => 'Isi email tidak boleh kosong.',
            'selectedContacts.required' => 'Pilih minimal 1 kontak penerima.',
            'selectedContacts.min' => 'Pilih minimal 1 kontak penerima.',
        ];
    }

    public function mount(?EmailCampaign $campaign = null): void
    {
        if ($campaign && $campaign->exists) {
            $this->campaign = $campaign;
            $this->isEditing = true;
            $this->title = $campaign->title;
            $this->subject = $campaign->subject;
            $this->body_content = $campaign->body_content;
            $this->selectedContacts = $campaign->recipients()->pluck('contact_id')->toArray();
        } else {
            $templateId = request()->query('template');
            $customTemplate = $templateId ? EmailTemplate::find($templateId) : null;

            if ($customTemplate) {
                $this->title = $customTemplate->name;
                $this->subject = $customTemplate->subject;
                $this->body_content = $customTemplate->body_content;
            } else {
                // Default template
                $this->body_content = '
                <p>Halo Bapak/Ibu <strong>{nama}</strong> (<em>{jabatan} &ndash; {instansi}</em>),</p>

                <p>Semoga pesan ini menemui Anda dalam keadaan sehat dan penuh semangat!</p>

                <p>Ingin tingkatkan performa tim dan tata kelola di <strong>{instansi}</strong> agar semakin efektif dan siap menghadapi regulasi terbaru? <strong>Linkpemda Portal</strong> hadir kembali membawa program pelatihan &amp; Bimtek interaktif yang dirancang khusus untuk Anda!</p>

                <p><strong>Kenapa Bimtek di Linkpemda Jadi Pilihan Favorit?</strong></p>
                <ul>
                    <li>🚀 <strong>Materi Praktis &amp; Up-to-Date:</strong> Langsung berfokus pada solusi tantangan kerja sehari-hari.</li>
                    <li>💡 <strong>Mentor &amp; Praktisi Ahli:</strong> Sesi diskusi interaktif, bukan sekadar teori!</li>
                    <li>📜 <strong>Fasilitas Lengkap:</strong> Modul eksklusif, sertifikat resmi, dan jaringan kolaborasi antar-daerah.</li>
                </ul>

                <p>Jangan lewatkan kesempatan untuk memperluas wawasan dan memperkuat kompetensi SDM di {instansi}.</p>

                <!-- ===== BUTTON CALL TO ACTION (PRESISI DI TENGAH) ===== -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 24px auto; text-align: center;">
                    <tr>
                        <td align="center" bgcolor="#22c55e" style="border-radius: 6px;">
                            <a href="https://linkpemda.com/layanan" target="_blank" style="display: inline-block; padding: 12px 26px; font-family: \'Plus Jakarta Sans\', Arial, sans-serif; font-size: 14px; color: #ffffff; font-weight: 700; text-decoration: none; border-radius: 6px; background-color: #22c55e;">
                                Daftar &amp; Amankan Kuota Sekarang &rarr;
                            </a>
                        </td>
                    </tr>
                </table>

                <p>Ada pertanyaan seputar materi atau jadwal? Kami siap membantu diskusi lebih lanjut melalui WhatsApp maupun Balas Email ini.</p>

                <p>Sampai jumpa di kelas pelatihan!<br><br>Salam hangat,<br><strong>Tim Linkpemda Portal</strong></p>
                ';
                // Default: mulai kosong agar admin leluasa memilih kontak per campaign
                $this->selectedContacts = [];
            }
        }
    }

    public function goToStep2(?string $content = null): void
    {
        if ($content !== null) {
            $this->body_content = $content;
        }

        $this->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_content' => 'required|string|min:5',
        ], [
            'title.required' => 'Judul campaign wajib diisi.',
            'subject.required' => 'Subjek email wajib diisi.',
            'body_content.required' => 'Isi email tidak boleh kosong.',
            'body_content.min' => 'Isi email terlalu pendek.',
        ]);

        $this->currentStep = 2;
    }

    public function goToStep3(): void
    {
        $this->validate([
            'selectedContacts' => 'required|array|min:1',
        ], [
            'selectedContacts.required' => 'Pilih minimal 1 kontak penerima.',
            'selectedContacts.min' => 'Pilih minimal 1 kontak penerima.',
        ]);

        $this->generatePreview();
        $this->currentStep = 3;
    }

    public function goToStep(int $step, ?string $content = null): void
    {
        if ($content !== null) {
            $this->body_content = $content;
        }

        if ($step === 1) {
            $this->currentStep = 1;
        } elseif ($step === 2) {
            $this->goToStep2();
        } elseif ($step === 3) {
            $this->goToStep2();
            $this->goToStep3();
        }
    }

    public function toggleSelectVisible(): void
    {
        $visibleIds = Contact::query()
            ->where('is_active', true)
            ->when($this->contactCategory !== 'all', fn($q) => $q->where('category', $this->contactCategory))
            ->when($this->searchContact, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->searchContact}%")
                        ->orWhere('organization_name', 'like', "%{$this->searchContact}%")
                        ->orWhere('email', 'like', "%{$this->searchContact}%");
                });
            })
            ->pluck('id')
            ->toArray();

        $allSelected = !empty($visibleIds) && count(array_intersect($visibleIds, $this->selectedContacts)) === count($visibleIds);

        if ($allSelected) {
            $this->selectedContacts = array_values(array_diff($this->selectedContacts, $visibleIds));
        } else {
            $this->selectedContacts = array_values(array_unique(array_merge($this->selectedContacts, $visibleIds)));
        }
    }

    public function selectAll(string $type = 'all'): void
    {
        $query = Contact::where('is_active', true);
        if ($type !== 'all') {
            $query->where('category', $type);
        }
        if ($this->searchContact) {
            $query->where(function ($sub) {
                $sub->where('name', 'like', "%{$this->searchContact}%")
                    ->orWhere('organization_name', 'like', "%{$this->searchContact}%")
                    ->orWhere('email', 'like', "%{$this->searchContact}%");
            });
        }
        $ids = $query->pluck('id')->toArray();
        $this->selectedContacts = array_values(array_unique(array_merge($this->selectedContacts, $ids)));
    }

    public function deselectAll(string $type = 'all'): void
    {
        if ($type === 'all' && empty($this->searchContact)) {
            $this->selectedContacts = [];
        } else {
            $query = Contact::where('is_active', true);
            if ($type !== 'all') {
                $query->where('category', $type);
            }
            if ($this->searchContact) {
                $query->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->searchContact}%")
                        ->orWhere('organization_name', 'like', "%{$this->searchContact}%")
                        ->orWhere('email', 'like', "%{$this->searchContact}%");
                });
            }
            $idsToRemove = $query->pluck('id')->toArray();
            $this->selectedContacts = array_values(array_diff($this->selectedContacts, $idsToRemove));
        }
    }

    public function generatePreview(): void
    {
        $sampleContact = null;
        if (!empty($this->selectedContacts)) {
            $sampleContact = Contact::find($this->selectedContacts[0]);
        }

        if (!$sampleContact) {
            $sampleContact = new Contact([
                'name' => 'Bpk. Hendra Gunawan, S.STP, M.Si',
                'organization_name' => 'Instansi Pemerintah',
                'position' => 'Kepala Dinas Kominfo',
                'email' => 'kamto.076s+pemerintah@gmail.com',
                'category' => ContactCategory::Pemerintah,
            ]);
        }

        $replacements = [
            '{nama}' => e($sampleContact->name),
            '{instansi}' => e($sampleContact->organization_name),
            '{perusahaan}' => e($sampleContact->organization_name),
            '{jabatan}' => e($sampleContact->position ?? '-'),
            '{email}' => e($sampleContact->email),
            '{kategori}' => e($sampleContact->category?->label() ?? 'Pemerintah'),
        ];

        $renderedContent = str_ireplace(array_keys($replacements), array_values($replacements), $this->body_content);

        $this->previewHtml = view('emails.campaign', [
            'campaign' => new EmailCampaign([
                'title' => $this->title ?: 'Preview',
                'subject' => $this->subject ?: 'Preview Subjek',
                'body_content' => $this->body_content,
            ]),
            'contact' => $sampleContact,
            'renderedContent' => $renderedContent,
            'subject' => $this->subject ?: 'Preview Subjek',
        ])->render();
    }

    public function openPreview(?string $content = null): void
    {
        if ($content !== null) {
            $this->body_content = $content;
        }
        $this->generatePreview();
        $this->showPreview = true;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
    }

    public function saveDraft(?string $content = null)
    {
        if ($content !== null) {
            $this->body_content = $content;
        }

        $this->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_content' => 'required|string',
        ]);

        $campaign = $this->campaign ?? new EmailCampaign();
        $campaign->fill([
            'title' => $this->title,
            'subject' => $this->subject,
            'body_content' => $this->body_content,
            'status' => CampaignStatus::Draft,
            'total_recipients' => count($this->selectedContacts),
        ]);
        $campaign->save();

        $this->syncRecipients($campaign);

        session()->flash('success', 'Draft berhasil disimpan.');
        return $this->redirect(route('admin.campaign.index'), navigate: true);
    }

    public function sendBlast()
    {
        $this->validate();

        $campaign = $this->campaign ?? new EmailCampaign();
        $campaign->fill([
            'title' => $this->title,
            'subject' => $this->subject,
            'body_content' => $this->body_content,
            'status' => CampaignStatus::Queued,
            'total_recipients' => count($this->selectedContacts),
            'sent_count' => 0,
            'failed_count' => 0,
        ]);
        $campaign->save();

        $this->syncRecipients($campaign);

        foreach ($campaign->recipients as $recipient) {
            SendCampaignEmailJob::dispatch($recipient);
        }

        session()->flash('success', "Email blast siap dikirimkan ke {$campaign->total_recipients} kontak.");
        return $this->redirect(route('admin.campaign.show', $campaign->id), navigate: true);
    }

    protected function syncRecipients(EmailCampaign $campaign): void
    {
        $existing = $campaign->recipients()->pluck('contact_id')->toArray();
        $toAdd = array_diff($this->selectedContacts, $existing);
        $toRemove = array_diff($existing, $this->selectedContacts);

        if (!empty($toRemove)) {
            $campaign->recipients()->whereIn('contact_id', $toRemove)->delete();
        }

        foreach ($toAdd as $contactId) {
            EmailCampaignRecipient::create([
                'campaign_id' => $campaign->id,
                'contact_id' => $contactId,
                'status' => RecipientStatus::Pending,
            ]);
        }

        $campaign->update(['total_recipients' => count($this->selectedContacts)]);
    }

    public function render()
    {
        $contacts = Contact::query()
            ->where('is_active', true)
            ->when($this->contactCategory !== 'all', fn($q) => $q->where('category', $this->contactCategory))
            ->when($this->searchContact, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->searchContact}%")
                        ->orWhere('organization_name', 'like', "%{$this->searchContact}%")
                        ->orWhere('email', 'like', "%{$this->searchContact}%");
                });
            })
            ->orderBy('name')
            ->get();

        $allContactsCount = Contact::where('is_active', true)->count();
        $pemdaCount = Contact::where('is_active', true)->where('category', ContactCategory::Pemerintah)->count();
        $swastaCount = Contact::where('is_active', true)->where('category', ContactCategory::Swasta)->count();

        $selectedPemerintahCount = Contact::whereIn('id', $this->selectedContacts)->where('category', ContactCategory::Pemerintah)->count();
        $selectedSwastaCount = Contact::whereIn('id', $this->selectedContacts)->where('category', ContactCategory::Swasta)->count();

        $visibleIds = $contacts->pluck('id')->toArray();
        $isAllVisibleSelected = !empty($visibleIds) && count(array_intersect($visibleIds, $this->selectedContacts)) === count($visibleIds);

        $sampleContact = null;
        if (!empty($this->selectedContacts)) {
            $sampleContact = Contact::find($this->selectedContacts[0]);
        }
        if (!$sampleContact) {
            $sampleContact = new Contact([
                'name' => 'Bpk. Hendra Gunawan, S.STP, M.Si',
                'organization_name' => 'Instansi Pemerintah',
                'position' => 'Kepala Dinas Kominfo',
                'email' => 'kamto.076s+pemerintah@gmail.com',
                'category' => ContactCategory::Pemerintah,
            ]);
        }

        $emailTemplates = EmailTemplate::active()->get();
        $templateCategories = EmailTemplateCategory::cases();

        return view('livewire.admin.campaign.form', [
            'contacts' => $contacts,
            'allContactsCount' => $allContactsCount,
            'pemdaCount' => $pemdaCount,
            'swastaCount' => $swastaCount,
            'selectedCount' => count($this->selectedContacts),
            'selectedPemerintahCount' => $selectedPemerintahCount,
            'selectedSwastaCount' => $selectedSwastaCount,
            'isAllVisibleSelected' => $isAllVisibleSelected,
            'sampleContact' => $sampleContact,
            'emailTemplates' => $emailTemplates,
            'templateCategories' => $templateCategories,
        ]);
    }
}
