<div>
    <!-- Page Header & Breadcrumb -->
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.participant.linkpemda.index') }}" wire:navigate
                        class="btn btn-sm btn-light border" title="Kembali ke Daftar">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="page-title mb-0">Detail Peserta Linkpemda: {{ $participant->name }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.participant.linkpemda.index') }}"
                                wire:navigate>Data Peserta</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="mdi mdi-information-outline me-1"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header Profil Peserta & Action Bar -->
    <div class="card mb-3">
        <div class="card-body p-3 p-lg-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <span
                        class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center fw-bold fs-20"
                        style="width: 56px; height: 56px; flex-shrink: 0;">
                        {{ mb_substr($participant->name, 0, 2) }}
                    </span>
                    <div>
                        <h4 class="mb-1 text-dark fw-bold">{{ $participant->name }}</h4>
                        <div class="d-flex align-items-center gap-2 flex-wrap text-muted fs-13">
                            <span><i
                                    class="mdi mdi-briefcase-outline me-1"></i>{{ $participant->position ?: 'Peserta Bimtek' }}</span>
                            <span>•</span>
                            <span><i class="mdi mdi-domain me-1"></i>{{ $participant->institution_name ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Tombol Sertifikat --}}
                    <div class="btn-group" role="group">
                        <a href="{{ $this->getPdfUrl('sertifikat', 'view') }}" target="_blank"
                            class="btn btn-outline-danger" title="Lihat / Preview Sertifikat PDF">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Sertifikat
                        </a>
                        <button type="button" class="btn btn-outline-danger dropdown-toggle dropdown-toggle-split px-2"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Pilihan Dokumen Sertifikat">
                            <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu shadow-sm">
                            <li class="dropdown-header text-uppercase fs-11 text-muted fw-bold py-1">Sertifikat Warna
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ $this->getPdfUrl('sertifikat', 'view') }}"
                                    target="_blank">
                                    <i class="mdi mdi-eye-outline me-2 text-info"></i> Lihat Sertifikat
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ $this->getPdfUrl('sertifikat', 'download') }}"
                                    target="_blank">
                                    <i class="mdi mdi-download me-2 text-danger"></i> Unduh Sertifikat
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider my-1">
                            </li>
                            <li class="dropdown-header text-uppercase fs-11 text-muted fw-bold py-1">Sertifikat Blank
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ $this->getPdfUrl('sertifikat', 'print') }}"
                                    target="_blank">
                                    <i class="mdi mdi-eye-outline me-2 text-info"></i> Lihat Sertifikat
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ $this->getPdfUrl('sertifikat', 'download-blank') }}"
                                    target="_blank">
                                    <i class="mdi mdi-download me-2 text-primary"></i> Unduh Sertifikat
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Tombol Kwitansi --}}
                    <div class="btn-group" role="group">
                        <a href="{{ $this->getPdfUrl('kwitansi', 'view') }}" target="_blank"
                            class="btn btn-outline-success" title="Lihat / Preview Kwitansi PDF">
                            <i class="mdi mdi-receipt me-1"></i> Kwitansi
                        </a>
                        <button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split px-2"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Pilihan Dokumen Kwitansi">
                            <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu shadow-sm">
                            <li>
                                <a class="dropdown-item" href="{{ $this->getPdfUrl('kwitansi', 'view') }}"
                                    target="_blank">
                                    <i class="mdi mdi-eye-outline me-2 text-info"></i> Lihat Kwitansi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ $this->getPdfUrl('kwitansi', 'download') }}"
                                    target="_blank">
                                    <i class="mdi mdi-download me-2 text-success"></i> Unduh Kwitansi
                                </a>
                            </li>
                        </ul>
                    </div>

                    @canany(['edit-participants', 'edit-certificates'])
                        {{-- Tombol Kirim Undangan & Tiket QR --}}
                        <button type="button" class="btn btn-outline-success" wire:click="sendInvitationEmail"
                            wire:loading.attr="disabled"
                            @disabled(empty($participant->email))
                            title="{{ empty($participant->email) ? 'Peserta tidak memiliki alamat email' : 'Kirim email undangan & kartu barcode QR Code absensi' }}">
                            <span wire:loading.remove wire:target="sendInvitationEmail">
                                <i class="mdi mdi-qrcode-scan me-1"></i>
                                {{ $participant->is_invitation_sent ? 'Kirim Ulang Undangan' : 'Kirim Undangan' }}
                            </span>
                            <span wire:loading wire:target="sendInvitationEmail">
                                <span class="spinner-border spinner-border-sm me-1"></span> Mengirim Undangan...
                            </span>
                        </button>

                        {{-- Tombol Kirim Sertifikat --}}
                        <button type="button" class="btn btn-outline-primary" wire:click="sendCertificateEmail"
                            wire:loading.attr="disabled"
                            @disabled(empty($participant->email))
                            title="{{ empty($participant->email) ? 'Peserta tidak memiliki alamat email' : 'Kirim email Sertifikat resmi kelulusan' }}">
                            <span wire:loading.remove wire:target="sendCertificateEmail">
                                <i
                                    class="mdi {{ $participant->is_email_sent ? 'mdi-email-sync' : 'mdi-certificate' }} me-1"></i>
                                {{ $participant->is_email_sent ? 'Kirim Ulang Sertifikat' : 'Kirim Sertifikat' }}
                            </span>
                            <span wire:loading wire:target="sendCertificateEmail">
                                <span class="spinner-border spinner-border-sm me-1"></span> Mengirim Sertifikat...
                            </span>
                        </button>

                        <a href="{{ route('admin.participant.linkpemda.edit', $participant->id) }}" wire:navigate
                            class="btn btn-warning">
                            <i class="mdi mdi-pencil me-1"></i> Edit Data
                        </a>
                    @endcanany

                    @canany(['delete-participants', 'delete-certificates'])
                        <button type="button" class="btn btn-danger" wire:click="delete"
                            wire:confirm="Apakah Anda yakin ingin menghapus data peserta {{ $participant->name }}?">
                            <i class="mdi mdi-trash-can me-1"></i> Hapus
                        </button>
                    @endcanany
                </div>
            </div>
        </div>
    </div>

    <!-- Rincian Detail Data -->
    <div class="row g-3">
        <!-- Kolom Kiri: Informasi Utama -->
        <div class="col-lg-8">
            <!-- 1. Informasi Peserta & Instansi -->
            <div class="card mb-3">
                <div class="card-header bg-light-subtle border-bottom py-2">
                    <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                        Informasi Peserta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Nama Lengkap</span>
                            <span class="fw-semibold text-dark fs-14">{{ $participant->name }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Jabatan</span>
                            <span class="fw-medium text-dark">{{ $participant->position ?: '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Instansi / Perushaan</span>
                            <span class="fw-medium text-dark">{{ $participant->institution_name ?: '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Tanggal Registrasi</span>
                            <span class="text-dark">{{ $participant->registration_date ?: '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Alamat Email</span>
                            @if ($participant->email)
                                <a href="mailto:{{ $participant->email }}"
                                    class="text-primary text-decoration-none fw-medium">
                                    <i class="mdi mdi-email-outline me-1"></i>{{ $participant->email }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">No. WhatsApp / HP</span>
                            @if ($participant->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $participant->phone) }}"
                                    target="_blank" class="text-success text-decoration-none fw-medium">
                                    <i class="bx bxl-whatsapp me-1"></i>{{ $participant->phone }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Data Kegiatan Bimtek -->
            <div class="card mb-3">
                <div class="card-header bg-light-subtle border-bottom py-2">
                    <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                        Kegiatan Pelatihan &amp; Sertifikat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <span class="text-muted fs-12 d-block">Tema / Judul Kegiatan</span>
                            <p class="mb-0 fw-medium text-dark">{{ $participant->theme ?: 'Belum diisi' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Nomor Seri Sertifikat</span>
                            <span class="badge bg-light text-dark border font-monospace fs-13 px-2 py-1">
                                {{ $participant->certificate_number ?: '-' }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Tanggal Pelaksanaan</span>
                            <span class="text-dark fw-medium">
                                <i
                                    class="mdi mdi-calendar me-1 text-primary"></i>{{ $participant->certificate_date ?: '-' }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Tempat / Gedung Acara</span>
                            <span class="text-dark">{{ $participant->event_venue ?: '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Lokasi Acara</span>
                            <span class="text-dark">{{ $participant->event_location ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Kwitansi Pembayaran & Biaya Kontribusi -->
            <div class="card mb-3">
                <div class="card-header bg-light-subtle border-bottom py-2">
                    <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                        Kwitansi Pembayaran &amp; Biaya Kontribusi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <span class="text-muted fs-12 d-block">Nomor Kwitansi</span>
                            <span
                                class="font-monospace fw-medium text-dark">{{ $participant->receipt_number ?: $participant->certificate_number }}</span>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted fs-12 d-block">Tanggal Kwitansi</span>
                            <span class="text-dark">{{ $participant->receipt_date ?: '-' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted fs-12 d-block">Biaya Kontribusi</span>
                            <span class="fw-bold text-dark fs-16">{{ $participant->formatted_amount }}</span>
                        </div>
                        @if ($participant->notes)
                            <div class="col-12 border-top pt-2 mt-2">
                                <span class="text-muted fs-12 d-block">Catatan Internal</span>
                                <p class="mb-0 fs-13 text-secondary">{{ $participant->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: QR Code & Status Tracking -->
        <div class="col-lg-4">
            <!-- Card QR Code Sertifikat -->
            <div class="card mb-3">
                <div class="card-header bg-light-subtle border-bottom py-2">
                    <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                        QR Code Verifikasi
                    </h5>
                </div>
                <div class="card-body text-center p-4">
                    @if ($participant->qr_code_url)
                        <div class="p-2 border rounded d-inline-block bg-white shadow-sm">
                            <img src="{{ $participant->qr_code_url }}" alt="QR Code {{ $participant->certificate_number }}"
                                class="img-fluid" style="width: 160px; height: 160px; object-fit: contain;">
                        </div>
                    @else
                        <div class="py-4 text-muted">
                            <i class="bx bx-qr font-size-48 d-block mb-2 text-secondary opacity-50"></i>
                            <span class="fs-13 d-block">File QR Code belum terunggah</span>
                            <small class="font-monospace text-muted">{{ $participant->qr_code_path ?: '-' }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Status Pengiriman Email & Log Sistem -->
            <div class="card mb-3">
                <div class="card-header bg-light-subtle border-bottom py-2">
                    <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                        Status &amp; Riwayat
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Status Pengiriman Undangan & Tiket QR -->
                    <div class="mb-3">
                        <span class="text-muted fs-12 d-block mb-1">Status Undangan &amp; Tiket QR</span>
                        @if (empty($participant->email))
                            <span class="badge bg-light text-secondary border fs-12 px-2 py-1">
                                <i class="mdi mdi-email-off-outline me-1"></i> Tidak Ada Email
                            </span>
                            <div class="text-muted fs-11 mt-1">Alamat email belum diisi</div>
                        @elseif ($participant->is_invitation_sent)
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-12 px-2 py-1">
                                <i class="mdi mdi-check-circle me-1"></i> Terkirim
                            </span>
                            @if ($participant->invitation_sent_at)
                                <div class="text-muted fs-11 mt-1">
                                    <i class="mdi mdi-calendar-clock me-1 text-success"></i>
                                    {{ $participant->invitation_sent_at->translatedFormat('d M Y, H:i') }} WIB
                                </div>
                            @endif
                        @elseif ($participant->invitation_status === 'failed')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-12 px-2 py-1">
                                <i class="mdi mdi-alert-circle me-1"></i> Gagal Terkirim
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border fs-12 px-2 py-1">
                                <i class="mdi mdi-clock-outline me-1"></i> Belum Dikirim
                            </span>
                        @endif
                    </div>

                    <!-- Status Pengiriman Sertifikat -->
                    <div class="mb-3">
                        <span class="text-muted fs-12 d-block mb-1">Status Sertifikat Resmi</span>
                        @if (empty($participant->email))
                            <span class="badge bg-light text-secondary border fs-12 px-2 py-1">
                                <i class="mdi mdi-email-off-outline me-1"></i> Tidak Ada Email
                            </span>
                            <div class="text-muted fs-11 mt-1">Alamat email belum diisi</div>
                        @elseif ($participant->is_email_sent)
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-12 px-2 py-1">
                                <i class="mdi mdi-check-circle me-1"></i> Terkirim
                            </span>
                            @if ($participant->email_sent_at)
                                <div class="text-muted fs-11 mt-1">
                                    <i class="mdi mdi-calendar-clock me-1 text-success"></i>
                                    {{ $participant->email_sent_at->translatedFormat('d M Y, H:i') }} WIB
                                </div>
                            @endif
                        @elseif ($participant->email_status === 'failed')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-12 px-2 py-1">
                                <i class="mdi mdi-alert-circle me-1"></i> Gagal Terkirim
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-12 px-2 py-1">
                                <i class="mdi mdi-clock-outline me-1"></i> Belum Dikirim
                            </span>
                        @endif
                    </div>

                    <div class="border-top pt-3">
                        <span class="text-muted fs-12 d-block">Waktu Pembuatan Data</span>
                        <span class="text-dark fs-13">
                            {{ $participant->created_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIB
                        </span>
                    </div>

                    @if ($participant->updated_at)
                        <div class="mt-2">
                            <span class="text-muted fs-12 d-block">Terakhir Diperbarui</span>
                            <span class="text-dark fs-13">
                                {{ $participant->updated_at->translatedFormat('d F Y, H:i') }} WIB
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>