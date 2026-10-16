<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $renderedContent;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public EmailCampaign $campaign,
        public Contact $contact
    ) {
        $this->renderedContent = $this->parseVariables($this->campaign->body_content, $this->contact);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
            with: [
                'campaign' => $this->campaign,
                'contact' => $this->contact,
                'renderedContent' => $this->renderedContent,
                'subject' => $this->campaign->subject,
            ],
        );
    }

    /**
     * Parse template variables {nama}, {instansi}, {perusahaan}, {jabatan}, {email}
     */
    protected function parseVariables(string $content, Contact $contact): string
    {
        $replacements = [
            '{nama}' => e($contact->name),
            '{instansi}' => e($contact->organization_name),
            '{perusahaan}' => e($contact->organization_name),
            '{jabatan}' => e($contact->position ?? '-'),
            '{email}' => e($contact->email),
            '{kategori}' => e($contact->category->label()),
        ];

        return str_ireplace(array_keys($replacements), array_values($replacements), $content);
    }
}

