@extends('emails.layout', ['subject' => 'Tagihan Invoice Bimtek Linkpemda - ' . $invoice->company_name])

@section('content')
    <h2 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 12px;">
        Yth. Pimpinan / Bagian Keuangan {{ $invoice->company_name }}
    </h2>

    <p style="margin-bottom: 16px; color: #334155; line-height: 1.65;">
        Bersama ini kami sampaikan tagihan resmi (Invoice) untuk pelaksanaan kegiatan bimbingan teknis / pelatihan kedinasan
        yang diselenggarakan oleh <strong>Lembaga Informasi Keuangan dan Pembangunan Daerah (Linkpemda)</strong>:
    </p>

    <!-- Kotak Rincian Invoice & Kegiatan -->
    <div
        style="background-color: #f8fafc !important; background-image: linear-gradient(#f8fafc, #f8fafc) !important; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px; margin: 20px 0;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
            style="font-size: 13px; line-height: 1.6;">
            <tr>
                <td style="color: #64748b; padding: 4px 0; width: 140px; vertical-align: top;">No. Invoice:</td>
                <td style="color: #0f172a; font-weight: 700; padding: 4px 0;">
                    <span
                        style="display: inline-block; background-color: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-family: monospace; font-size: 13px;">
                        {{ $invoice->invoice_number }}
                    </span>
                </td>
            </tr>

            <tr>
                <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Tanggal Invoice:</td>
                <td style="color: #0f172a; font-weight: 600; padding: 4px 0;">{{ $invoice->formatted_invoice_date }}</td>
            </tr>

            <tr>
                <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Instansi / Klien:</td>
                <td style="color: #0f172a; font-weight: 600; padding: 4px 0;">{{ $invoice->company_name }}</td>
            </tr>

            <tr>
                <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Tema Kegiatan:</td>
                <td style="color: #0f172a; font-weight: 600; padding: 4px 0;">{{ $invoice->activity_theme }}</td>
            </tr>

            <tr>
                <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Jadwal &amp; Waktu:</td>
                <td style="color: #0f172a; padding: 4px 0;">
                    {{ $invoice->formatted_event_date }} ({{ $invoice->event_time }})
                </td>
            </tr>

            <tr>
                <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Lokasi Acara:</td>
                <td style="color: #0f172a; padding: 4px 0;">{{ $invoice->event_location }}</td>
            </tr>

            <tr>
                <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Jumlah Peserta:</td>
                <td style="color: #0f172a; font-weight: 600; padding: 4px 0;">{{ $invoice->participant_count }} Orang</td>
            </tr>

            <tr>
                <td style="color: #64748b; padding: 6px 0 4px 0; border-top: 1px dashed #cbd5e1; vertical-align: top;">Total
                    Biaya:</td>
                <td
                    style="color: #0284c7; font-weight: 700; font-size: 15px; padding: 6px 0 4px 0; border-top: 1px dashed #cbd5e1;">
                    Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }},-
                </td>
            </tr>

            @if ((float) $invoice->deposit_amount > 0)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Deposit / DP:</td>
                    <td style="color: #0f172a; padding: 4px 0;">
                        Rp {{ number_format((float) $invoice->deposit_amount, 0, ',', '.') }},-
                    </td>
                </tr>
            @endif

            <tr>
                <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Sisa Pembayaran:</td>
                <td style="color: #dc2626; font-weight: 700; padding: 4px 0;">
                    Rp {{ number_format((float) $invoice->remaining_amount, 0, ',', '.') }},-
                </td>
            </tr>

            <tr>
                <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Metode Pembayaran:</td>
                <td style="color: #0f172a; padding: 4px 0;">{{ $invoice->payment_method }}</td>
            </tr>
        </table>
    </div>

    <!-- Alert Lampiran PDF -->
    <div
        style="background-color: #f0fdf4 !important; background-image: linear-gradient(#f0fdf4, #f0fdf4) !important; border: 1px solid #dcfce7 !important; border-left: 4px solid #22c55e !important; border-radius: 6px; padding: 14px 18px; margin: 24px 0; font-size: 13px; color: #166534 !important; line-height: 1.6;">
        <strong>Berkas Resmi Terlampir:</strong> Dokumen cetak Invoice resmi dalam format <strong>PDF</strong> telah kami
        lampirkan pada email ini untuk dapat diunduh, dicetak, atau diproses bagian perbendaharaan/keuangan.
    </div>

    <p style="margin-bottom: 0; color: #64748b; font-size: 13px; line-height: 1.6;">
        Apabila Bapak/Ibu membutuhkan informasi lebih lanjut mengenai pembayaran, konfirmasi transfer, atau berkas
        administrasi lainnya, silakan hubungi tim sekretariat kami melalui nomor <strong>+6282241312016</strong> atau email
        <strong>info@linkpemda.com</strong>.
    </p>
@endsection