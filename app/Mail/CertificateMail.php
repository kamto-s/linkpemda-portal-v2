<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Participant $participant
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Sertifikat Pelatihan Bimtek Linkpemda - ' . $this->participant->name;
        if (!empty($this->participant->certificate_number)) {
            $subject .= ' (No: ' . $this->participant->certificate_number . ')';
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate',
            with: [
                'participant' => $this->participant,
            ],
        );
    }

    /**
     * Lampirkan file PDF Sertifikat ke dalam email.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        try {
            $service = app(\App\Services\ParticipantDocumentService::class);
            $binary = $service->getPdfBinary($this->participant, 'default');
            $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->participant->name);
            $filename = "Sertifikat_{$safeName}_{$this->participant->certificate_number}.pdf";

            return [
                \Illuminate\Mail\Mailables\Attachment::fromData(fn() => $binary, $filename)
                    ->withMime('application/pdf'),
            ];
        } catch (\Throwable) {
            return [];
        }
    }
}

