<?php

namespace App\Livewire\Admin\Participant;

use App\Models\Participant;
use Carbon\Carbon;
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

    public ?string $registration_date = null;

    // Data E-Sertifikat & Kegiatan Bimtek
    public string $certificate_number = '';

    public string $qr_code_path = '';

    public string $theme = '';

    public ?string $event_start_date = null;

    public ?string $event_end_date = null;

    public ?string $certificate_date = null;

    public string $event_venue = '';

    public string $event_location = '';

    // Data Kwitansi Pembayaran
    public string $receipt_number = '';

    public ?string $receipt_date = null;

    public $amount = '';

    // Catatan Tambahan
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
            $this->receipt_number = $this->participant->receipt_number ?: $this->certificate_number;
            $this->qr_code_path = $this->participant->qr_code_path ?? '';
            $this->theme = $this->participant->theme ?? '';
            $this->certificate_date = $this->participant->certificate_date ?? '';
            $this->event_venue = $this->participant->event_venue ?? '';
            $this->event_location = $this->participant->event_location ?? '';
            $this->notes = $this->participant->notes ?? '';
            $this->amount = $this->participant->amount > 0 ? number_format((float) $this->participant->amount, 0, ',', '.') : '';

            // Format tanggal ke format standar Y-m-d untuk kontrol <input type="date">
            $this->registration_date = $this->formatDateForInput($this->participant->registration_date);
            $this->receipt_date = $this->formatDateForInput($this->participant->receipt_date);
            $this->parseCertificateDateRange($this->participant->certificate_date);
        } else {
            abort_unless(
                auth()->user()?->can('create-participants') || auth()->user()?->can('create-certificates'),
                403,
                'Anda tidak memiliki hak akses untuk menambah peserta baru.'
            );

            $this->isEditing = false;
            $this->certificate_number = Participant::generateCertificateNumber();
            $this->receipt_number = $this->certificate_number;
            $this->qr_code_path = "{$this->certificate_number}.png";
            $this->amount = ''; // Kosong secara default tanpa nilai otomatis

            $today = now()->format('Y-m-d');
            $this->registration_date = $today;
            $this->receipt_date = $today;
            $this->event_start_date = $today;
            $this->event_end_date = $today;
        }
    }

    protected function formatDateForInput(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        $months = [
            'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
            'April' => 'April', 'Mei' => 'May', 'Juni' => 'June',
            'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September',
            'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December',
        ];

        try {
            return Carbon::parse(strtr($date, $months))->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    protected function parseCertificateDateRange(?string $range): void
    {
        if (empty($range)) {
            return;
        }

        $parts = preg_split('/\s*(-|s\/d|s\.d\.)\s*/i', $range);
        if (count($parts) === 2) {
            $endParsed = $this->formatDateForInput($parts[1]);
            if ($endParsed) {
                $this->event_end_date = $endParsed;
                if (is_numeric(trim($parts[0]))) {
                    try {
                        $this->event_start_date = Carbon::parse($endParsed)->day((int) trim($parts[0]))->format('Y-m-d');
                    } catch (\Throwable) {
                        $this->event_start_date = $endParsed;
                    }
                } else {
                    $this->event_start_date = $this->formatDateForInput($parts[0]) ?? $endParsed;
                }
            }
        } else {
            $parsed = $this->formatDateForInput($range);
            $this->event_start_date = $parsed;
            $this->event_end_date = $parsed;
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'theme' => 'nullable|string',
            'event_start_date' => 'nullable|date',
            'event_end_date' => 'nullable|date',
            'event_venue' => 'nullable|string|max:255',
            'event_location' => 'nullable|string|max:255',
            'registration_date' => 'nullable|date',
            'receipt_date' => 'nullable|date',
            'amount' => 'nullable',
            'notes' => 'nullable|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama peserta wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ];
    }

    public function updatedAmount($value): void
    {
        if (! empty($value)) {
            $digits = preg_replace('/[^0-9]/', '', (string) $value);
            $this->amount = $digits !== '' ? number_format((float) $digits, 0, ',', '.') : '';
        } else {
            $this->amount = '';
        }
    }

    public function save()
    {
        $this->validate();

        // Susun teks tanggal sertifikat resmi dari rentang tanggal kegiatan
        $certDate = $this->certificate_date;
        if ($this->event_start_date) {
            try {
                $start = Carbon::parse($this->event_start_date)->locale('id');
                if ($this->event_end_date && $this->event_end_date !== $this->event_start_date) {
                    $end = Carbon::parse($this->event_end_date)->locale('id');
                    if ($start->isSameMonth($end) && $start->isSameYear($end)) {
                        $certDate = $start->format('d').' - '.$end->translatedFormat('d F Y');
                    } elseif ($start->isSameYear($end)) {
                        $certDate = $start->translatedFormat('d F').' - '.$end->translatedFormat('d F Y');
                    } else {
                        $certDate = $start->translatedFormat('d F Y').' - '.$end->translatedFormat('d F Y');
                    }
                } else {
                    $certDate = $start->translatedFormat('d F Y');
                }
            } catch (\Throwable) {
                $certDate = $this->event_start_date;
            }
        }

        // Format tanggal registrasi & kwitansi ke teks bahasa Indonesia
        $regDate = $this->registration_date;
        if ($regDate) {
            try {
                $regDate = Carbon::parse($regDate)->locale('id')->translatedFormat('d F Y');
            } catch (\Throwable) {
            }
        }

        $recDate = $this->receipt_date;
        if ($recDate) {
            try {
                $recDate = Carbon::parse($recDate)->locale('id')->translatedFormat('d F Y');
            } catch (\Throwable) {
            }
        }

        $certNumber = $this->certificate_number ?: Participant::generateCertificateNumber();
        $numericAmount = ! empty($this->amount) ? (float) preg_replace('/[^0-9]/', '', (string) $this->amount) : 0;

        $data = [
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'position' => $this->position ?: null,
            'institution_name' => $this->institution_name ?: null,
            'certificate_number' => $certNumber,
            'qr_code_path' => "{$certNumber}.png",
            'theme' => $this->theme ?: null,
            'certificate_date' => $certDate ?: null,
            'event_venue' => $this->event_venue ?: null,
            'event_location' => $this->event_location ?: null,
            'registration_date' => $regDate ?: null,
            'receipt_number' => $certNumber,
            'receipt_date' => $recDate ?: null,
            'amount' => $numericAmount,
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
