<?php

namespace App\Livewire\Admin\Participant;

use App\Mail\CertificateMail;
use App\Models\Participant;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin.app')]
#[Title('Detail Data Peserta')]
class Show extends Component
{
    public Participant $participant;

    public function mount(Participant|string $participant): void
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk melihat detail data peserta.'
        );

        $this->participant = $participant instanceof Participant ? $participant : Participant::findOrFail($participant);
    }

    public function sendCertificateEmail(): void
    {
        abort_unless(
            auth()->user()?->can('edit-participants') || auth()->user()?->can('edit-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengirim e-sertifikat.'
        );

        if (empty($this->participant->email)) {
            session()->flash('error', "Peserta {$this->participant->name} tidak memiliki alamat email terdaftar.");

            return;
        }

        try {
            Mail::to($this->participant->email)->send(new CertificateMail($this->participant));

            $this->participant->update([
                'email_sent_at' => now(),
                'email_status' => 'sent',
            ]);

            $this->participant->refresh();
            session()->flash('success', "E-Sertifikat berhasil dikirimkan ke email {$this->participant->email}!");
        } catch (\Throwable $e) {
            $this->participant->update(['email_status' => 'failed']);
            $this->participant->refresh();
            session()->flash('error', "Gagal mengirimkan email ke {$this->participant->email}: ".$e->getMessage());
        }
    }

    public function delete(): mixed
    {
        abort_unless(
            auth()->user()?->can('delete-participants') || auth()->user()?->can('delete-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk menghapus data peserta.'
        );

        $name = $this->participant->name;
        $this->participant->delete();
        session()->flash('success', "Data peserta \"{$name}\" berhasil dihapus.");

        return $this->redirect(route('admin.participant.linkpemda.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.participant.show');
    }
}

