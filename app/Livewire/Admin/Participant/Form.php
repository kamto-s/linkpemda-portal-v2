<?php

namespace App\Livewire\Admin\Participant;

use App\Models\Participant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin.app')]
#[Title('Form Data Peserta Linkpemda')]
class Form extends Component
{
    public ?Participant $participant = null;

    public bool $isEditing = false;

    // Data Pribadi Peserta
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $position = '';

    public string $institution_name = '';

    // Data E-Sertifikat
    public string $certificate_number = '';

    public string $qr_code_path = '';

    public string $theme = '';

    public string $certificate_date = '';

    public string $event_venue = '';

    public string $event_location = '';

    public string $registration_date = '';

    // Data Kwitansi Pembayaran
    public string $receipt_number = '';

    public string $receipt_date = '';

    public $amount = 0;

    // Catatan
    public string $notes = '';

    public function mount(Participant|string|null $participant = null): void
    {
        if ($participant) {
            abort_unless(
                auth()->user()?->can('edit-participants') || auth()->user()?->can('edit-certificates'),
                403,
                'Anda tidak memiliki hak akses untuk mengubah data peserta.'
            );

            $this->participant = $participant instanceof Participant ? $participant : Participant::findOrFail($participant);
            $this->isEditing = true;

            $this->name = $this->participant->name;
            $this->email = $this->participant->email ?? '';
            $this->phone = $this->participant->phone ?? '';
            $this->position = $this->participant->position ?? '';
            $this->institution_name = $this->participant->institution_name ?? '';

            $this->certificate_number = $this->participant->certificate_number ?? '';
            $this->qr_code_path = $this->participant->qr_code_path ?? '';
            $this->theme = $this->participant->theme ?? '';
            $this->certificate_date = $this->participant->certificate_date ?? '';
            $this->event_venue = $this->participant->event_venue ?? '';
            $this->event_location = $this->participant->event_location ?? '';
            $this->registration_date = $this->participant->registration_date ?? '';

            $this->receipt_number = $this->participant->receipt_number ?? '';
            $this->receipt_date = $this->participant->receipt_date ?? '';
            $this->amount = $this->participant->amount;
            $this->notes = $this->participant->notes ?? '';
        } else {
            abort_unless(
                auth()->user()?->can('create-participants') || auth()->user()?->can('create-certificates'),
                403,
                'Anda tidak memiliki hak akses untuk menambah peserta baru.'
            );

            $this->isEditing = false;
            // Rekomendasi otomatis nomor sertifikat berikutnya
            $this->certificate_number = Participant::generateNextCertificateNumber();
            $this->qr_code_path = $this->certificate_number ? "{$this->certificate_number}.png" : '';
            $this->amount = 5000000;
        }
    }

    public function generateNewCertificateNumber(): void
    {
        $this->certificate_number = Participant::generateNextCertificateNumber();
        $this->qr_code_path = "{$this->certificate_number}.png";
    }

    public function updatedCertificateNumber(string $value): void
    {
        if (empty($this->qr_code_path) || str_ends_with($this->qr_code_path, '.png')) {
            $sanitized = preg_replace('/[^a-zA-Z0-9_-]/', '-', $value);
            $this->qr_code_path = "{$sanitized}.png";
        }
    }

    protected function rules(): array
    {
        $uniqueCertRule = 'nullable|string|max:255|unique:participants,certificate_number';
        if ($this->isEditing && $this->participant) {
            $uniqueCertRule .= ",{$this->participant->id}";
        }

        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'certificate_number' => $uniqueCertRule,
            'qr_code_path' => 'nullable|string|max:255',
            'theme' => 'nullable|string',
            'certificate_date' => 'nullable|string|max:255',
            'event_venue' => 'nullable|string|max:255',
            'event_location' => 'nullable|string|max:255',
            'registration_date' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'receipt_date' => 'nullable|string|max:255',
            'amount' => 'nullable',
            'notes' => 'nullable|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama peserta wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'certificate_number.unique' => 'Nomor sertifikat ini sudah digunakan oleh peserta lain.',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'position' => $this->position ?: null,
            'institution_name' => $this->institution_name ?: null,
            'certificate_number' => $this->certificate_number ?: null,
            'qr_code_path' => $this->qr_code_path ?: ($this->certificate_number ? "{$this->certificate_number}.png" : null),
            'theme' => $this->theme ?: null,
            'certificate_date' => $this->certificate_date ?: null,
            'event_venue' => $this->event_venue ?: null,
            'event_location' => $this->event_location ?: null,
            'registration_date' => $this->registration_date ?: null,
            'receipt_number' => $this->receipt_number ?: null,
            'receipt_date' => $this->receipt_date ?: null,
            'amount' => $this->amount,
            'notes' => $this->notes ?: null,
        ];

        if ($this->isEditing && $this->participant) {
            $this->participant->update($data);
            session()->flash('success', "Data peserta \"{$this->participant->name}\" berhasil diperbarui.");
        } else {
            $participant = Participant::create($data);
            session()->flash('success', "Peserta baru \"{$participant->name}\" berhasil ditambahkan.");
        }

        return $this->redirect(route('admin.participant.linkpemda.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.participant.form');
    }
}

