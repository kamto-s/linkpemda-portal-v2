<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat Digital -
        {{ function_exists('system_setting') ? system_setting('app_name', 'Linkpemda') : config('app.name', 'Linkpemda') }}
    </title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">

    <style>
        :root {
            --brand-green: #008F83;
            --brand-green-hover: #007369;
            --brand-green-light: #2ad5c4;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 24px 16px;
        }

        .verify-wrapper {
            max-width: 620px;
            margin: 20px auto;
            width: 100%;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .verify-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .card-status-header {
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }

        .status-valid {
            background: linear-gradient(135deg, #008F83 0%, #006b62 100%);
        }

        .status-in-progress {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        }

        .status-invalid {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        }

        .status-icon-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .cert-badge-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 12px 16px;
            text-align: center;
            margin-bottom: 24px;
        }

        .cert-number {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 0.5px;
            font-size: 15px;
        }

        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            width: 38%;
            color: #64748b;
            font-weight: 500;
        }

        .detail-value {
            width: 62%;
            color: #0f172a;
            font-weight: 600;
        }

        .btn-brand {
            background: #008F83;
            color: #ffffff;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 18px;
            transition: all 0.2s ease;
        }

        .btn-brand:hover {
            background: #007369;
            color: #ffffff;
        }

        .form-control:focus {
            border-color: #008F83;
            box-shadow: 0 0 0 3px rgba(0, 143, 131, 0.15);
        }

        .footer-note {
            text-align: center;
            font-size: 12.5px;
            color: #94a3b8;
            margin-top: 24px;
        }

        @media (max-width: 576px) {
            .detail-row {
                flex-direction: column;
                gap: 4px;
            }

            .detail-label,
            .detail-value {
                width: 100%;
            }

            .card-status-header {
                padding: 24px 16px;
            }
        }
    </style>
</head>

<body>

    <div class="verify-wrapper">
        <!-- Logo Linkpemda -->
        <div class="brand-header">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/admin') }}/images/logo-dark.png" alt="Logo Linkpemda" height="38"
                    onerror="this.src='{{ asset('assets/admin') }}/images/logo-sm.png'">
            </a>
        </div>

        <div class="verify-card">
            <!-- Header Status -->
            @if ($participant)
                @php
                    $validity = $validity ?? (app(\App\Services\AttendanceService::class)->checkCertificateValidity($participant));
                    $isValid = $validity['is_valid'] ?? true;
                    $isInProgress = $validity['is_in_progress'] ?? false;
                @endphp

                @if ($isValid)
                    <div class="card-status-header status-valid">
                        <div class="status-icon-circle">
                            <i class="mdi mdi-check-decagram text-white fs-28"></i>
                        </div>
                        <h3 class="fw-bold mb-1 fs-20">{{ $validity['status_title'] ?? 'Sertifikat Resmi Terverifikasi' }}</h3>
                        <p class="mb-0 text-white-50 fs-13">{{ $validity['status_desc'] ?? 'Data terdaftar resmi dalam pangkalan data sistem Linkpemda' }}</p>
                    </div>
                @else
                    <div class="card-status-header status-in-progress">
                        <div class="status-icon-circle">
                            <i class="mdi mdi-account-clock text-white fs-28"></i>
                        </div>
                        <h3 class="fw-bold mb-1 fs-20">{{ $validity['status_title'] ?? 'Peserta Terdaftar (Kegiatan Berlangsung)' }}</h3>
                        <p class="mb-0 text-white-75 fs-13">{{ $validity['status_desc'] ?? 'Sertifikat digital akan aktif tervalidasi setelah menyelesaikan absensi penutupan.' }}</p>
                    </div>
                @endif
            @elseif (!empty($certificate_number))
                <div class="card-status-header status-invalid">
                    <div class="status-icon-circle">
                        <i class="mdi mdi-alert-circle text-white fs-28"></i>
                    </div>
                    <h3 class="fw-bold mb-1 fs-20">Sertifikat Tidak Ditemukan</h3>
                    <p class="mb-0 text-white-50 fs-13">Nomor sertifikat tidak terdaftar atau belum dipublikasikan</p>
                </div>
            @else
                <div class="card-status-header" style="background: linear-gradient(135deg, #008F83 0%, #006b62 100%);">
                    <div class="status-icon-circle">
                        <i class="mdi mdi-certificate text-white fs-28"></i>
                    </div>
                    <h3 class="fw-bold mb-1 fs-20">Verifikasi Sertifikat Digital</h3>
                    <p class="mb-0 text-white-50 fs-13">Periksa keabsahan dan keaslian dokumen sertifikat bimtek Anda</p>
                </div>
            @endif

            <!-- Body Konten -->
            <div class="p-4 p-sm-5">
                @if ($participant)
                    <!-- Badge Nomor Sertifikat -->
                    <div class="cert-badge-box">
                        <div class="text-muted fs-12 mb-1">Nomor Registrasi Sertifikat</div>
                        <div class="cert-number">{{ $participant->certificate_number }}</div>
                    </div>

                    <!-- Tabel Data Peserta -->
                    <div class="mb-4">
                        <div class="detail-row">
                            <div class="detail-label"><i class="mdi mdi-account me-1 text-muted"></i> Nama Peserta</div>
                            <div class="detail-value text-dark fs-15">{{ $participant->name }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label"><i class="mdi mdi-badge-account me-1 text-muted"></i> Jabatan</div>
                            <div class="detail-value">{{ $participant->position ?: '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label"><i class="mdi mdi-city me-1 text-muted"></i> Instansi / Perusahaan</div>
                            <div class="detail-value">{{ $participant->institution_name ?: '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label"><i class="mdi mdi-book-open-page-variant me-1 text-muted"></i> Tema
                                Bimtek</div>
                            <div class="detail-value text-primary" style="color: #008F83 !important;">
                                {{ $participant->theme ?: '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label"><i class="mdi mdi-calendar me-1 text-muted"></i> Tanggal Kegiatan
                            </div>
                            <div class="detail-value">{{ $participant->certificate_date ?: '-' }}</div>
                        </div>
                        @if ($participant->event_venue)
                            <div class="detail-row">
                                <div class="detail-label"><i class="mdi mdi-map-marker me-1 text-muted"></i> Tempat Acara</div>
                                <div class="detail-value">{{ $participant->event_venue }}</div>
                            </div>
                        @endif
                        @if ($participant->event_location)
                            <div class="detail-row">
                                <div class="detail-label"><i class="mdi mdi-crosshairs-gps me-1 text-muted"></i> Lokasi</div>
                                <div class="detail-value">{{ $participant->event_location }}</div>
                            </div>
                        @endif
                    </div>

                    @if ($isValid ?? true)
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-0 py-2 fs-13"
                            style="background-color: rgba(0, 143, 131, 0.08); border-color: rgba(0, 143, 131, 0.25); color: #007369;">
                            <i class="mdi mdi-shield-check fs-18 flex-shrink-0"></i>
                            <div>Dokumen ini sah dan terdaftar resmi oleh penyelenggara <strong>Linkpemda</strong>.</div>
                        </div>
                    @else
                        <div class="alert alert-info d-flex align-items-center gap-2 mb-0 py-2 fs-13"
                            style="background-color: rgba(2, 132, 199, 0.08); border-color: rgba(2, 132, 199, 0.25); color: #0369a1;">
                            <i class="mdi mdi-clock-check-outline fs-18 flex-shrink-0"></i>
                            <div>Peserta telah terdaftar secara sah. Dokumen sertifikat digital akan aktif penuh setelah peserta menyelesaikan absensi di hari terakhir kegiatan.</div>
                        </div>
                    @endif
                @elseif (!empty($certificate_number))
                    <div class="cert-badge-box">
                        <div class="text-muted fs-12 mb-1">Nomor Sertifikat Dicari</div>
                        <div class="cert-number text-danger">{{ $certificate_number }}</div>
                    </div>

                    <div class="text-center py-2 mb-4">
                        <p class="text-muted fs-14">
                            Nomor sertifikat tidak ditemukan di pangkalan data kami. Pastikan nomor sertifikat yang Anda
                            masukkan sudah benar atau silakan hubungi panitia penyelenggara.
                        </p>
                    </div>

                    <!-- Form Cari Ulang -->
                    <form action="{{ route('sertifikat.status') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="no_certf" class="form-control"
                                placeholder="Masukkan Nomor Sertifikat Lain..." required>
                            <button type="submit" class="btn btn-brand">Cek Ulang</button>
                        </div>
                    </form>
                @else
                    <!-- Form Pencarian Pertama Kali -->
                    <form action="{{ route('sertifikat.status') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold fs-13">Masukkan Nomor Sertifikat</label>
                            <input type="text" name="no_certf" class="form-control form-control-lg fs-15"
                                placeholder="Contoh: LP/2026/001..." required autofocus>
                        </div>
                        <button type="submit" class="btn btn-brand w-100 py-2 fs-14">
                            <i class="mdi mdi-magnify me-1"></i> Periksa Keaslian Sertifikat
                        </button>
                    </form>
                @endif
            </div>

            <!-- Footer Card -->
            <div class="p-3 bg-light border-top text-center text-muted fs-12">
                &copy; {{ date('Y') }}
                {{ function_exists('system_setting') ? system_setting('app_name', 'Linkpemda') : config('app.name', 'Linkpemda') }}.
                Sistem Verifikasi Sertifikat Digital.
            </div>
        </div>

        <div class="footer-note">
            Untuk bantuan administrasi sertifikat, silakan hubungi kontak panitia Linkpemda.
        </div>
    </div>

</body>

</html>