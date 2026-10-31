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

    public string $renderedSubject;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public EmailCampaign $campaign,
        public Contact $contact
    ) {
        $this->renderedContent = $this->parseVariables($this->campaign->body_content, $this->contact, true);
        $this->renderedSubject = $this->parseVariables($this->campaign->subject, $this->contact, false);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->renderedSubject,
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
                'subject' => $this->renderedSubject,
            ],
        );
    }

    /**
     * Parse template variables {nama}, {instansi}, {perusahaan}, {jabatan}, {email}, {kategori}
     */
    protected function parseVariables(string $content, Contact $contact, bool $escape = true): string
    {
        $organization = $contact->organization_name ?? '-';
        $position = $contact->position ?? '-';
        $category = $contact->category ? (method_exists($contact->category, 'label') ? $contact->category->label() : (string) $contact->category) : 'Umum';

        $replacements = [
            '{nama}' => $escape ? e($contact->name) : $contact->name,
            '{instansi}' => $escape ? e($organization) : $organization,
            '{perusahaan}' => $escape ? e($organization) : $organization,
            '{jabatan}' => $escape ? e($position) : $position,
            '{email}' => $escape ? e($contact->email) : $contact->email,
            '{kategori}' => $escape ? e($category) : $category,
        ];

        return str_ireplace(array_keys($replacements), array_values($replacements), $content);
    }
}
