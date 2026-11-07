<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceDocumentService;
use Illuminate\Http\Response;

class InvoiceDocumentController extends Controller
{
    public function __construct(
        protected InvoiceDocumentService $documentService
    ) {}

    /**
     * Preview PDF invoice secara langsung di browser (tab baru).
     */
    public function streamInline(Invoice $invoice): Response
    {
        $pdf = $this->documentService->buildPdf($invoice);
        $filename = "Invoice_{$invoice->invoice_number}.pdf";
        $content = $pdf->Output('S');

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Unduh PDF invoice langsung ke komputer pengguna.
     */
    public function streamDownload(Invoice $invoice): Response
    {
        $pdf = $this->documentService->buildPdf($invoice);
        $filename = "Invoice_{$invoice->invoice_number}.pdf";
        $content = $pdf->Output('S');

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}

