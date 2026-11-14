@extends('emails.layout', ['subject' => 'Undangan & Tiket Barcode Absensi Bimtek Linkpemda'])

@section('content')
    <h2 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 12px;">
        Halo, {{ $participant->name }}
    </h2>

    <p style="margin-bottom: 16px; color: #334155; line-height: 1.65;">
        Selamat! Pendaftaran Anda dalam kegiatan bimbingan teknis / pelatihan yang diselenggarakan oleh <strong>Linkpemda</strong> telah terdaftar dan terkonfirmasi secara resmi. Berikut kartu tanda peserta digital dan barcode kehadiran Anda:
    </p>

    <!-- KARTU TANDA PESERTA DIGITAL & KODE QR SCAN -->
    <div style="text-align: center; margin: 24px 0; padding: 24px 18px; background-color: #f0fdfa !important; background-image: linear-gradient(#f0fdfa, #f0fdfa) !important; border: 2px dashed #008F83; border-radius: 12px;">
        <span style="display: inline-block; background-color: #008F83; color: #ffffff; font-size: 11px; font-weight: 700; padding: 4px 14px; border-radius: 20px; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 10px;">
            KARTU TANDA PESERTA DIGITAL
        </span>
        <h3 style="font-size: 16px; font-weight: 700; color: #006b62; margin: 0 0 6px 0;">
            Kode QR Kehadiran (Check-in)
        </h3>
        <p style="font-size: 12.5px; color: #475569; margin: 0 0 16px 0; line-height: 1.5;">
            Mohon simpan dan tunjukkan kode QR di bawah ini kepada panitia saat registrasi / check-in kehadiran di meja pendaftaran:
        </p>

        @php
            $qrPath = public_path('uploads/qr_code/' . ($participant->qr_code_path ?: preg_replace('/[^a-zA-Z0-9_-]/', '-', $participant->certificate_number) . '.png'));
        @endphp

        @if (file_exists($qrPath))
            <div style="background-color: #ffffff; padding: 14px; border-radius: 10px; display: inline-block; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #ccfbf1;">
                <img src="{{ $message->embed($qrPath) }}" alt="Barcode QR Code Absensi" width="160" height="160" style="display: block; margin: 0 auto; width: 160px; height: 160px;">
            </div>
        @endif

        <div style="margin-top: 14px;">
            <div style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px;">Nomor Registrasi Peserta</div>
            <span style="display: inline-block; background-color: #ffffff; color: #0f172a; font-family: monospace; font-size: 14px; font-weight: 700; padding: 4px 14px; border-radius: 6px; border: 1px solid #cbd5e1;">
                {{ $participant->certificate_number }}
            </span>
        </div>
    </div>

    <!-- Kotak Rincian Jadwal & Acara -->
    <div style="background-color: #f8fafc !important; background-image: linear-gradient(#f8fafc, #f8fafc) !important; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px; margin: 20px 0;">
        <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 12px; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px;">
            RINCIAN KEGIATAN BIMTEK
        </div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 13px; line-height: 1.6;">
            @if ($participant->theme)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; width: 140px; vertical-align: top;">Tema Kegiatan:</td>
                    <td style="color: #0f172a; font-weight: 600; padding: 4px 0;">{{ $participant->theme }}</td>
                </tr>
            @endif

            @if ($participant->certificate_date)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Tanggal Pelaksanaan:</td>
                    <td style="color: #0f172a; font-weight: 600; padding: 4px 0;">{{ $participant->certificate_date }}</td>
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

            @if ($participant->institution_name)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Instansi / OPD:</td>
                    <td style="color: #0f172a; padding: 4px 0;">{{ $participant->institution_name }}</td>
                </tr>
            @endif

            @if ($participant->position)
                <tr>
                    <td style="color: #64748b; padding: 4px 0; vertical-align: top;">Jabatan:</td>
                    <td style="color: #0f172a; padding: 4px 0;">{{ $participant->position }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div style="text-align: center; margin: 24px 0;">
        <a href="{{ route('certificate.verify', ['certificate_number' => $participant->certificate_number]) }}"
            target="_blank"
            style="display: inline-block; background-color: #008F83; color: #ffffff; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 12px 24px; border-radius: 6px;">
            Cek Status Registrasi &amp; Acara &rarr;
        </a>
    </div>

    <div class="info-box"
        style="background-color: #f0fdf4 !important; background-image: linear-gradient(#f0fdf4, #f0fdf4) !important; border: 1px solid #dcfce7 !important; border-left: 4px solid #008F83 !important; border-radius: 6px; padding: 14px 18px; margin: 24px 0; font-size: 13px; color: #166534 !important; line-height: 1.6;">
        <strong>Informasi Sertifikat Resmi:</strong> E-Sertifikat kelulusan resmi akan diaktifkan dan dikirimkan setelah Anda menyelesaikan rangkaian kegiatan bimbingan teknis.
    </div>

    <p style="margin-bottom: 0; color: #64748b; font-size: 13px; line-height: 1.6;">
        Apabila ada pertanyaan mengenai jadwal atau lokasi acara, silakan hubungi tim sekretariat panitia Linkpemda.
    </p>
@endsection

