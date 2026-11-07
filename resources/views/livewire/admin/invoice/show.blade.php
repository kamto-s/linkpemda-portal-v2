<div>
    <!-- Page Header & Breadcrumb -->
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.invoice.linkpemda.index') }}" wire:navigate
                        class="btn btn-sm btn-light border" title="Kembali ke Daftar">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="page-title mb-0">Detail Invoice Linkpemda: {{ $invoice->invoice_number }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.invoice.linkpemda.index') }}"
                                wire:navigate>Invoice Linkpemda</a></li>
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
                        {{ mb_strtoupper(mb_substr($invoice->company_name, 0, 2)) }}
                    </span>
                    <div>
                        <h4 class="mb-1 text-dark fw-bold">{{ $invoice->company_name }}</h4>
                        <div class="d-flex align-items-center gap-2 flex-wrap text-muted fs-13">
                            <span><i
                                    class="mdi mdi-file-document-outline me-1"></i>{{ $invoice->invoice_number }}</span>
                            <span>•</span>
                            <span><i class="mdi mdi-calendar me-1"></i>{{ $invoice->formatted_invoice_date }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Tombol Invoice PDF (Split dropdown) --}}
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.invoice.linkpemda.preview', $invoice->id) }}" target="_blank"
                            class="btn btn-outline-danger" title="Lihat / Preview Invoice PDF">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Invoice PDF
                        </a>
                        <button type="button" class="btn btn-outline-danger dropdown-toggle dropdown-toggle-split px-2"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Pilihan Dokumen Invoice">
                            <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.invoice.linkpemda.preview', $invoice->id) }}"
                                    target="_blank">
                                    <i class="mdi mdi-eye-outline me-2 text-info"></i> Lihat Invoice
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.invoice.linkpemda.download', $invoice->id) }}">
                                    <i class="mdi mdi-download me-2 text-danger"></i> Unduh Invoice
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Tombol Kirim Email --}}
                    <button type="button" class="btn btn-outline-primary" wire:click="sendInvoiceEmail"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="sendInvoiceEmail">
                            <i class="mdi {{ $invoice->email_sent_at ? 'mdi-email-sync' : 'mdi-send' }} me-1"></i>
                            {{ $invoice->email_sent_at ? 'Kirim Ulang Email' : 'Kirim E-Invoice' }}
                        </span>
                        <span wire:loading wire:target="sendInvoiceEmail">
                            <span class="spinner-border spinner-border-sm me-1"></span> Mengirim...
                        </span>
                    </button>

                    {{-- Tombol ACC / Tolak jika status Pending --}}
                    @if ($invoice->status === \App\Enums\InvoiceStatus::Pending)
                        <button type="button" class="btn btn-outline-success" wire:click="approveInvoice"
                            wire:confirm="Setujui dan ACC invoice {{ $invoice->invoice_number }}?">
                            <i class="mdi mdi-check-circle me-1"></i> ACC Invoice
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#rejectConfirmModal">
                            <i class="mdi mdi-close-circle me-1"></i> Tolak
                        </button>
                    @endif

                    {{-- Tombol Set Lunas jika belum lunas --}}
                    @if ($invoice->payment_status !== \App\Enums\InvoicePaymentStatus::Paid)
                        <button type="button" class="btn btn-outline-success" wire:click="markAsPaid"
                            wire:confirm="Tandai pembayaran invoice {{ $invoice->invoice_number }} sebagai Lunas?">
                            <i class="mdi mdi-cash-check me-1"></i> Set Lunas
                        </button>
                    @endif

                    <a href="{{ route('admin.invoice.linkpemda.edit', $invoice->id) }}" wire:navigate
                        class="btn btn-warning">
                        <i class="mdi mdi-pencil me-1"></i> Edit Data
                    </a>

                    <button type="button" class="btn btn-danger" wire:click="delete"
                        wire:confirm="Apakah Anda yakin ingin menghapus data invoice {{ $invoice->invoice_number }}?">
                        <i class="mdi mdi-trash-can me-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rincian Detail Data -->
    <div class="row g-3">
        <!-- Kolom Kiri: Informasi Utama -->
        <div class="col-lg-8">
            <!-- 1. Informasi Instansi -->
            <div class="card mb-3">
                <div class="card-header bg-light-subtle border-bottom py-2">
                    <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                        Informasi Instansi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Nama Instansi / Perusahaan</span>
                            <span class="fw-semibold text-dark fs-14">{{ $invoice->company_name }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Menu Pelatihan</span>
                            <span class="fw-medium text-dark">{{ ucfirst($invoice->menu_type) }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Alamat Email</span>
                            @if ($invoice->email)
                                <a href="mailto:{{ $invoice->email }}"
                                    class="text-primary text-decoration-none fw-medium">
                                    <i class="mdi mdi-email-outline me-1"></i>{{ $invoice->email }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">No. WhatsApp / HP</span>
                            @if ($invoice->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->phone) }}"
                                    target="_blank" class="text-success text-decoration-none fw-medium">
                                    <i class="bx bxl-whatsapp me-1"></i>{{ $invoice->phone }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                        <div class="col-12">
                            <span class="text-muted fs-12 d-block">Alamat Lengkap</span>
                            <span class="text-dark">{{ $invoice->address ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Data Kegiatan Bimtek -->
            <div class="card mb-3">
                <div class="card-header bg-light-subtle border-bottom py-2">
                    <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                        Kegiatan Pelatihan &amp; Bimtek
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <span class="text-muted fs-12 d-block">Tema / Judul Kegiatan</span>
                            <p class="mb-0 fw-medium text-dark">{{ $invoice->activity_theme ?: 'Belum diisi' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Nomor Invoice</span>
                            <span class="badge bg-light text-dark border font-monospace fs-13 px-2 py-1">
                                {{ $invoice->invoice_number ?: '-' }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Tanggal Pelaksanaan</span>
                            <span class="text-dark fw-medium">
                                <i
                                    class="mdi mdi-calendar me-1 text-primary"></i>{{ $invoice->formatted_event_date ?: '-' }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Waktu Acara</span>
                            <span class="text-dark">{{ $invoice->event_time ?: '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Lokasi Acara</span>
                            <span class="text-dark">{{ $invoice->event_location ?: '-' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-12 d-block">Metode Pembayaran</span>
                            <span class="text-dark fw-medium">{{ $invoice->payment_method ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Kwitansi Pembayaran & Biaya Kontribusi (Di bawah Card Kegiatan Pelatihan) -->
            <div class="card mb-3">
                <div class="card-header bg-light-subtle border-bottom py-2">
                    <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                        Kwitansi Pembayaran &amp; Biaya Kontribusi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <span class="text-muted fs-12 d-block">Harga per Peserta</span>
                            <span class="fw-semibold text-dark fs-14">
                                Rp {{ number_format((float) $invoice->price_per_participant, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted fs-12 d-block">Jumlah Peserta</span>
                            <span class="fw-medium text-dark">{{ $invoice->participant_count }} Orang</span>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted fs-12 d-block">Total Biaya Kontribusi</span>
                            <span class="fw-bold text-dark fs-16">
                                Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted fs-12 d-block">Terbilang Total</span>
                            <span class="fst-italic text-secondary fs-13">"{{ $invoice->terbilang_total }}"</span>
                        </div>
                        <div class="col-sm-4 border-top pt-2">
                            <span class="text-muted fs-12 d-block">Deposit / DP Terbayar</span>
                            <span class="fw-semibold text-dark">
                                Rp {{ number_format((float) $invoice->deposit_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="col-sm-4 border-top pt-2">
                            <span class="text-danger fs-12 d-block fw-semibold">Sisa Tagihan / Pembayaran</span>
                            <span class="fw-bold text-danger fs-16">
                                Rp {{ number_format((float) $invoice->remaining_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="col-sm-4 border-top pt-2">
                            <span class="text-muted fs-12 d-block mb-1">Status Pembayaran</span>
                            @php
                                $payEnum = $invoice->payment_status instanceof \App\Enums\InvoicePaymentStatus ? $invoice->payment_status : \App\Enums\InvoicePaymentStatus::tryFrom($invoice->payment_status);
                            @endphp
                            @if ($payEnum)
                                @php
                                    $paySubtle = match($payEnum) {
                                        \App\Enums\InvoicePaymentStatus::Unpaid => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        \App\Enums\InvoicePaymentStatus::Partial => 'bg-warning-subtle text-warning border border-warning-subtle',
                                        \App\Enums\InvoicePaymentStatus::Paid => 'bg-success-subtle text-success border border-success-subtle',
                                    };
                                @endphp
                                <span class="badge {{ $paySubtle }} fs-12 px-2 py-1">
                                    <i class="mdi {{ $payEnum->icon() }} me-1"></i> {{ $payEnum->label() }}
                                </span>
                            @endif
                        </div>
                        @if ($invoice->remaining_amount > 0)
                            <div class="col-12 pt-1">
                                <span class="text-muted fs-12 d-block">Terbilang Sisa</span>
                                <span class="fst-italic text-danger fs-13">"{{ $invoice->terbilang_remaining }}"</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 4. Daftar Peserta Bimtek -->
            <div class="card mb-3">
                <div class="card-header bg-light-subtle border-bottom py-2 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                        Daftar Peserta Bimtek
                    </h5>
                    <span class="badge bg-light text-dark border px-2 py-1">
                        {{ $invoice->participant_count }} Orang Peserta
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Nama Peserta &amp; Gelar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ((array) $invoice->participants as $idx => $participantName)
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                                        <td class="fw-medium text-dark">{{ $participantName }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-3 text-muted">Tidak ada data nama peserta.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 5. Catatan Internal -->
            @if ($invoice->notes)
                <div class="card mb-3">
                    <div class="card-header bg-light-subtle border-bottom py-2">
                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                            Catatan Internal
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0 fs-13 text-secondary">{{ $invoice->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Status & Riwayat -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-light-subtle border-bottom py-2">
                    <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                        Status &amp; Riwayat
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Status Persetujuan (ACC) -->
                    <div class="mb-3">
                        <span class="text-muted fs-12 d-block mb-1">Status Persetujuan (ACC)</span>
                        @php
                            $statusEnum = $invoice->status instanceof \App\Enums\InvoiceStatus ? $invoice->status : \App\Enums\InvoiceStatus::tryFrom($invoice->status);
                        @endphp
                        @if ($statusEnum)
                            @php
                                $statusSubtle = match($statusEnum) {
                                    \App\Enums\InvoiceStatus::Pending => 'bg-warning-subtle text-warning border border-warning-subtle',
                                    \App\Enums\InvoiceStatus::Approved => 'bg-success-subtle text-success border border-success-subtle',
                                    \App\Enums\InvoiceStatus::Rejected => 'bg-danger-subtle text-danger border border-danger-subtle',
                                };
                            @endphp
                            <span class="badge {{ $statusSubtle }} fs-12 px-2 py-1">
                                <i class="mdi {{ $statusEnum->icon() }} me-1"></i> {{ $statusEnum->label() }}
                            </span>
                        @endif
                    </div>

                    @if ($invoice->approved_by && $invoice->approver)
                        <div class="mb-3">
                            <span class="text-muted fs-12 d-block">Disetujui Oleh</span>
                            <span class="text-dark fs-13 fw-medium">
                                <i class="mdi mdi-account-check me-1 text-success"></i>
                                {{ $invoice->approver->name }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted fs-12 d-block">Waktu ACC</span>
                            <span class="text-dark fs-13">
                                <i class="mdi mdi-calendar-clock me-1 text-primary"></i>
                                {{ $invoice->approved_at?->translatedFormat('d F Y, H:i') }} WIB
                            </span>
                        </div>
                    @endif

                    @if ($invoice->rejection_note)
                        <div class="mb-3 p-2 bg-danger-subtle rounded border border-danger-subtle">
                            <span class="text-danger fw-semibold fs-12 d-block">Catatan Penolakan:</span>
                            <span class="text-danger fs-12">{{ $invoice->rejection_note }}</span>
                        </div>
                    @endif

                    <!-- Status Pembayaran -->
                    <div class="mb-3 border-top pt-3">
                        <span class="text-muted fs-12 d-block mb-1">Status Pembayaran Tagihan</span>
                        @if ($payEnum)
                            <span class="badge {{ $paySubtle }} fs-12 px-2 py-1">
                                <i class="mdi {{ $payEnum->icon() }} me-1"></i> {{ $payEnum->label() }}
                            </span>
                        @endif
                    </div>

                    <!-- Status Pengiriman E-Invoice -->
                    <div class="mb-3 border-top pt-3">
                        <span class="text-muted fs-12 d-block mb-1">Status Pengiriman E-Invoice</span>
                        @if ($invoice->email_sent_at)
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-12 px-2 py-1">
                                <i class="mdi mdi-check-circle me-1"></i> Terkirim
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-12 px-2 py-1">
                                <i class="mdi mdi-clock-outline me-1"></i> Belum Dikirim
                            </span>
                        @endif
                    </div>

                    @if ($invoice->email)
                        <div class="mb-3">
                            <span class="text-muted fs-12 d-block">Email Tujuan</span>
                            <a href="mailto:{{ $invoice->email }}" class="text-primary text-decoration-none fw-medium fs-13">
                                <i class="mdi mdi-email-outline me-1"></i>{{ $invoice->email }}
                            </a>
                        </div>
                    @endif

                    @if ($invoice->email_sent_at)
                        <div class="mb-3">
                            <span class="text-muted fs-12 d-block">Waktu Pengiriman Email</span>
                            <span class="text-dark fs-13 fw-medium">
                                <i class="mdi mdi-calendar-clock me-1 text-success"></i>
                                {{ $invoice->email_sent_at->translatedFormat('d F Y, H:i') }} WIB
                            </span>
                        </div>
                    @endif

                    <div class="border-top pt-3">
                        <span class="text-muted fs-12 d-block">Waktu Pembuatan Data</span>
                        <span class="text-dark fs-13">
                            {{ $invoice->created_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIB
                        </span>
                    </div>

                    @if ($invoice->updated_at)
                        <div class="mt-2">
                            <span class="text-muted fs-12 d-block">Terakhir Diperbarui</span>
                            <span class="text-dark fs-13">
                                {{ $invoice->updated_at->translatedFormat('d F Y, H:i') }} WIB
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Tolak Invoice -->
    <div class="modal fade" id="rejectConfirmModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-3">
                <div class="modal-body">
                    <div class="text-warning mb-2">
                        <i class="mdi mdi-alert-circle-outline font-size-48" style="font-size: 54px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Tolak Invoice?</h5>
                    <p class="text-muted fs-13 mb-3">
                        Tolak pengajuan invoice <strong>{{ $invoice->invoice_number }}</strong>.
                    </p>
                    <div class="text-start mb-3">
                        <label class="form-label fs-12 text-muted fw-medium">Catatan / Alasan Penolakan</label>
                        <textarea class="form-control form-control-sm" rows="3" wire:model="rejectionNote"
                            placeholder="Opsional / alasan revisi..."></textarea>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-warning" wire:click="rejectInvoice" data-bs-dismiss="modal">
                            Tolak Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
