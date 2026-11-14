@extends('emails.layout', ['subject' => 'Sertifikat Pelatihan Bimtek Linkpemda'])

@section('content')
    <h2 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 12px;">
        Halo, {{ $participant->name }}
    </h2>

    <p style="margin-bottom: 16px; color: #334155; line-height: 1.65;">
        Selamat! Anda telah resmi menyelesaikan seluruh rangkaian kegiatan bimbingan teknis / pelatihan yang diselenggarakan
        oleh <strong>Linkpemda</strong>. Bersama email ini, kami lampirkan dokumen <strong>Sertifikat Resmi</strong> Anda.
    </p>

    <!-- Kotak Rincian Sertifikat & Kelulusan -->
    <div
        style="background-color: #f8fafc !important; background-image: linear-gradient(#f8fafc, #f8fafc) !important; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px; margin: 20px 0;">
        <div
            style="font-size: 13px; font-weight: 700; color: #008F83; margin-bottom: 12px; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px;">
            RINCIAN Sertifikat RESMI
        </div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
            style="font-size: 13px; line-height: 1.6;">
            @if ($participant->certificate_number)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; width: 140px; vertical-align: top;">No. Sertifikat:</td>
                    <td style="color: #0f172a; font-weight: 700; padding: 4px 0;">
                        <span
                            style="display: inline-block; background-color: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-family: monospace; font-size: 13px;">
                            {{ $participant->certificate_number }}
                        </span>
                    </td>
                </tr>
            @endif

            @if ($participant->name)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Nama Peserta:</td>
                    <td style="color: #0f172a; font-weight: 600; padding: 4px 0;">{{ $participant->name }}</td>
                </tr>
            @endif

            @if ($participant->position)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Jabatan:</td>
                    <td style="color: #0f172a; font-weight: 600; padding: 4px 0;">{{ $participant->position }}</td>
                </tr>
            @endif

            @if ($participant->institution_name)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Instansi / Perusahaan:</td>
                    <td style="color: #0f172a; font-weight: 600; padding: 4px 0;">{{ $participant->institution_name }}</td>
                </tr>
            @endif

            @if ($participant->theme)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Tema Kegiatan:</td>
                    <td style="color: #0f172a; font-weight: 600; padding: 4px 0;">{{ $participant->theme }}</td>
                </tr>
            @endif

            @if ($participant->certificate_date)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Tanggal Kegiatan:</td>
                    <td style="color: #0f172a; padding: 4px 0;">{{ $participant->certificate_date }}</td>
                </tr>
            @endif

            @if ($participant->event_venue || $participant->event_location)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Tempat &amp; Lokasi:</td>
                    <td style="color: #0f172a; padding: 4px 0;">
                        {{ implode(', ', array_filter([$participant->event_venue, $participant->event_location])) }}
                    </td>
                </tr>
            @endif
        </table>
    </div>

    <!-- Kotak Info Lampiran & Keabsahan -->
    <div class="info-box"
        style="background-color: #f0fdf4 !important; background-image: linear-gradient(#f0fdf4, #f0fdf4) !important; border: 1px solid #dcfce7 !important; border-left: 4px solid #22c55e !important; border-radius: 6px; padding: 14px 18px; margin: 24px 0; font-size: 13px; color: #166534 !important; line-height: 1.6;">
        <strong>Dokumen Terlampir:</strong> File fisik Sertifikat resmi Anda telah kami lampirkan dalam format PDF pada
        email ini dan dapat langsung Anda unduh atau cetak.
    </div>

    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ route('certificate.verify', ['certificate_number' => $participant->certificate_number]) }}"
            target="_blank"
            style="display: inline-block; background-color: #008F83; color: #ffffff; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 12px 24px; border-radius: 6px;">
            Verifikasi Keabsahan Sertifikat Digital &rarr;
        </a>
    </div>

    <p style="margin-bottom: 0; color: #64748b; font-size: 13px; line-height: 1.6;">
        Apabila terdapat kesalahan penulisan nama, gelar, atau instansi, silakan hubungi tim sekretariat Linkpemda untuk
        perbaikan.
    </p>
@endsection