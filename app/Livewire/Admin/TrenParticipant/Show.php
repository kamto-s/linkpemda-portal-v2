<?php

namespace App\Livewire\Admin\TrenParticipant;

use App\Models\TrenParticipant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin.app')]
#[Title('Detail Data Peserta Tren Media')]
class Show extends Component
{
    public TrenParticipant $participant;

    public function mount(TrenParticipant|string $participant): void
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk melihat detail data peserta.'
        );

        $this->participant = $participant instanceof TrenParticipant ? $participant : TrenParticipant::findOrFail($participant);
    }

    /**
     * Mengembalikan URL route PDF sesuai type & action.
     * Digunakan dari blade via $this->getPdfUrl('sertifikat','view') dll.
     */
    public function getPdfUrl(string $type, string $action = 'view'): string
    {
        $routeMap = [
            'sertifikat' => [
                'view'           => 'admin.participant.trendiklat.certificate.view',
                'download'       => 'admin.participant.trendiklat.certificate.download',
                'print'          => 'admin.participant.trendiklat.certificate.print',
                'download-blank' => 'admin.participant.trendiklat.certificate.download-blank',
            ],
            'kwitansi' => [
                'view'     => 'admin.participant.trendiklat.kwitansi.view',
                'download' => 'admin.participant.trendiklat.kwitansi.download',
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

        return app(\App\Services\TrenParticipantDocumentService::class)->streamDownload($this->participant, $type);
    }

    public function downloadKwitansi()
    {
        abort_unless(
            auth()->user()?->can('view-participants') || auth()->user()?->can('view-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk mengunduh kwitansi.'
        );

        return app(\App\Services\TrenParticipantDocumentService::class)->streamDownload($this->participant, 'kwitansi');
    }

    public function delete(): void
    {
        abort_unless(
            auth()->user()?->can('delete-participants') || auth()->user()?->can('delete-certificates'),
            403,
            'Anda tidak memiliki hak akses untuk menghapus data peserta.'
        );

        $name = $this->participant->name;
        $this->participant->delete();

        session()->flash('success', "Data peserta \"{$name}\" berhasil dihapus.");

        $this->redirect(route('admin.participant.trendiklat.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.tren-participant.show');
    }
}

