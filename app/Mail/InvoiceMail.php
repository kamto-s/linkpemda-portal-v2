<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Services\InvoiceDocumentService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Invoice $invoice
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Tagihan Invoice Bimtek Linkpemda - ' . $this->invoice->company_name;
        if (! empty($this->invoice->invoice_number)) {
            $subject .= ' (No: ' . $this->invoice->invoice_number . ')';
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
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
            ],
        );
    }

    /**
     * Lampirkan berkas resmi PDF Invoice ke dalam email.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        try {
            $service = app(InvoiceDocumentService::class);
            $pdf = $service->buildPdf($this->invoice);
            $binary = $pdf->Output('S');

            $safeNumber = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->invoice->invoice_number);
            $filename = "Invoice_{$safeNumber}.pdf";

            return [
                Attachment::fromData(fn () => $binary, $filename)
                    ->withMime('application/pdf'),
            ];
        } catch (\Throwable) {
            return [];
        }
    }
}

