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

    /**
     * Kirim email undangan & tiket barcode absensi ke peserta.
     */
    public function sendInvitationEmail(): void
    {
        abort_unless(
            auth()->user()?->can('edit-participants') || auth()->user()?->can('edit-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengirim undangan.'
        );

        if (empty($this->participant->email)) {
            session()->flash('error', "Peserta {$this->participant->name} tidak memiliki alamat email terdaftar.");

            return;
        }

        if (empty($this->participant->qr_code_path) || !file_exists(public_path('uploads/qr_code/' . $this->participant->qr_code_path))) {
            Participant::generateQrCodeFile($this->participant);
        }

        try {
            Mail::to($this->participant->email)->send(new \App\Mail\InvitationTicketMail($this->participant));

            $update = [];
            if (\Illuminate\Support\Facades\Schema::hasColumn('participants', 'invitation_sent_at')) {
                $update['invitation_sent_at'] = now();
                $update['invitation_status'] = 'sent';
            }
            if (!empty($update)) {
                $this->participant->update($update);
            }

            $this->participant->refresh();
            session()->flash('success', "Undangan & tiket barcode absensi berhasil dikirim ke {$this->participant->email}!");
        } catch (\Throwable $e) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('participants', 'invitation_status')) {
                $this->participant->update(['invitation_status' => 'failed']);
            }
            $this->participant->refresh();
            session()->flash('error', "Gagal mengirimkan undangan ke {$this->participant->email}: " . $e->getMessage());
        }
    }

    public function sendCertificateEmail(): void
    {
        abort_unless(
            auth()->user()?->can('edit-participants') || auth()->user()?->can('edit-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengirim Sertifikat.'
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
            session()->flash('success', "Sertifikat berhasil dikirimkan ke email {$this->participant->email}!");
        } catch (\Throwable $e) {
            $this->participant->update(['email_status' => 'failed']);
            $this->participant->refresh();
            session()->flash('error', "Gagal mengirimkan email ke {$this->participant->email}: " . $e->getMessage());
        }
    }

    /**
     * Mengembalikan URL route PDF sesuai type & action.
     * Digunakan dari blade via @this.pdfUrl('sertifikat','view') dll.
     */
    public function getPdfUrl(string $type, string $action = 'view'): string
    {
        $routeMap = [
            'sertifikat' => [
                'view' => 'admin.participant.linkpemda.certificate.view',
                'download' => 'admin.participant.linkpemda.certificate.download',
                'print' => 'admin.participant.linkpemda.certificate.print',
                'download-blank' => 'admin.participant.linkpemda.certificate.download-blank',
            ],
            'kwitansi' => [
                'view' => 'admin.participant.linkpemda.kwitansi.view',
                'download' => 'admin.participant.linkpemda.kwitansi.download',
            ],
        ];

        $routeName = $routeMap[$type][$action] ?? null;

        return $routeName ? route($routeName, $this->participant) : '#';
    }

    public function downloadCertificate(string $type = 'default')
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengunduh sertifikat.'
        );

        return app(\App\Services\ParticipantDocumentService::class)->streamDownload($this->participant, $type);
    }

    public function downloadKwitansi()
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengunduh kwitansi.'
        );

        return app(\App\Services\ParticipantDocumentService::class)->streamDownload($this->participant, 'kwitansi');
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

