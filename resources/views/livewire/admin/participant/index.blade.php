<div>
    <div class="py-3 py-lg-4">
        <div class="row">
            <div class="col-lg-6">
                <h4 class="page-title mb-0">{{ $pageTitle }}</h4>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Data Peserta</li>
                        <li class="breadcrumb-item active">{{ $pageTitle }}</li>
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

    <!-- Statistik Ringkasan -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="card mb-0">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fs-12 d-block">Total Peserta</span>
                            <h5 class="mb-0 fw-bold">{{ number_format($totalParticipants) }}</h5>
                        </div>
                        <i class="mdi mdi-account-group text-primary fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card mb-0">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fs-12 d-block">Sertifikat Terkirim</span>
                            <h5 class="mb-0 fw-bold text-success">{{ number_format($emailSentCount) }}</h5>
                        </div>
                        <i class="mdi mdi-email-check text-success fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card mb-0">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fs-12 d-block">Belum Dikirim</span>
                            <h5 class="mb-0 fw-bold text-warning">{{ number_format($emailPendingCount) }}</h5>
                        </div>
                        <i class="mdi mdi-clock-outline text-warning fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card mb-0">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fs-12 d-block">Total Biaya</span>
                            <h5 class="mb-0 fw-bold text-dark fs-15">Rp {{ number_format($totalAmount, 0, ',', '.') }}</h5>
                        </div>
                        <i class="mdi mdi-cash-multiple text-info fs-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <div>
                            <h4 class="">Daftar {{ $pageTitle }}</h4>
                            <p class="mb-0">Kelola data peserta pelatihan bimtek, penerbitan sertifikat, kwitansi, dan pengiriman email.</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            @canany(['create-participants', 'create-certificates'])
                                <a href="{{ route('admin.participant.linkpemda.create') }}" wire:navigate class="btn btn-primary">
                                    <i class="mdi mdi-plus"></i> Tambah
                                </a>
                            @endcanany
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Toolbar -->
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control" placeholder="Cari nama, email, instansi, no. sertifikat..."
                                    wire:model.live.debounce.300ms="search">
                                @if ($search !== '')
                                    <button class="btn btn-outline-secondary" type="button"
                                        wire:click="$set('search', '')" title="Hapus pencarian">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="selectedEmailStatus">
                                <option value="">Semua Status Email</option>
                                <option value="sent">Terkirim</option>
                                <option value="pending">Belum Dikirim</option>
                                <option value="failed">Gagal Terkirim</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="selectedMonth">
                                <option value="">Semua Bulan</option>
                                @foreach ($months as $num => $monthName)
                                    <option value="{{ $num }}">{{ $monthName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="selectedYear">
                                <option value="">Semua Tahun</option>
                                @foreach ($years as $yr)
                                    <option value="{{ $yr }}">{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if ($search !== '' || $selectedEmailStatus !== '' || $selectedMonth !== '' || $selectedYear !== '')
                            <div class="col-auto">
                                <button type="button" class="btn btn-light text-danger" wire:click="resetFilters">
                                    <i class="mdi mdi-filter-remove me-1"></i> Reset
                                </button>
                            </div>
                        @endif

                        <div class="col d-flex justify-content-end gap-2">
                            <select class="form-select w-auto" wire:model.live="perPage">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bar Aksi Masal (Bulk Actions) -->
                    @if (count($selectedParticipants) > 0)
                        <div class="alert alert-primary d-flex align-items-center justify-content-between mb-3 py-2 px-3 border-0 bg-primary-subtle text-primary-emphasis flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary fs-12 rounded-pill px-2 py-1">{{ count($selectedParticipants) }}</span>
                                <span class="fw-semibold">Peserta terpilih</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <!-- Tombol Kirim Email Masal -->
                                <button type="button" class="btn btn-sm btn-primary" wire:click="sendBulkCertificateEmail"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="sendBulkCertificateEmail">
                                        <i class="mdi mdi-email-send me-1"></i> Kirim E-Sertifikat ({{ count($selectedParticipants) }})
                                    </span>
                                    <span wire:loading wire:target="sendBulkCertificateEmail">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Mengirim...
                                    </span>
                                </button>

                                <!-- Tombol Download Sertifikat Masal (.ZIP) -->
                                <button type="button" class="btn btn-sm btn-outline-danger" style="--bs-btn-bg: #fff;"
                                    wire:click="downloadBulkPdf('sertifikat')" wire:loading.attr="disabled"
                                    title="Unduh kumpulan PDF sertifikat warna yang dipilih dalam file .ZIP">
                                    <span wire:loading.remove wire:target="downloadBulkPdf('sertifikat')">
                                        <i class="mdi mdi-download me-1"></i> Sertifikat Warna (.ZIP)
                                    </span>
                                    <span wire:loading wire:target="downloadBulkPdf('sertifikat')">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Mengompres...
                                    </span>
                                </button>

                                <!-- Tombol Download Sertifikat Blank Masal (.ZIP) -->
                                <button type="button" class="btn btn-sm btn-outline-secondary" style="--bs-btn-bg: #fff;"
                                    wire:click="downloadBulkPdf('blank')" wire:loading.attr="disabled"
                                    title="Unduh kumpulan PDF sertifikat blank (tanpa background) dalam file .ZIP">
                                    <span wire:loading.remove wire:target="downloadBulkPdf('blank')">
                                        <i class="mdi mdi-download me-1"></i> Sertifikat Blank (.ZIP)
                                    </span>
                                    <span wire:loading wire:target="downloadBulkPdf('blank')">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Mengompres...
                                    </span>
                                </button>

                                <!-- Tombol Download Kwitansi Masal (.ZIP) -->
                                <button type="button" class="btn btn-sm btn-outline-success" style="--bs-btn-bg: #fff;"
                                    wire:click="downloadBulkPdf('kwitansi')" wire:loading.attr="disabled"
                                    title="Unduh kumpulan PDF kwitansi yang dipilih dalam file .ZIP">
                                    <span wire:loading.remove wire:target="downloadBulkPdf('kwitansi')">
                                        <i class="mdi mdi-download me-1"></i> Kwitansi (.ZIP)
                                    </span>
                                    <span wire:loading wire:target="downloadBulkPdf('kwitansi')">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Mengompres...
                                    </span>
                                </button>

                                <!-- Batal Seleksi -->
                                <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-2 text-decoration-none" wire:click="$set('selectedParticipants', [])">
                                    <i class="mdi mdi-close"></i> Batal
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Tabel Peserta -->
                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 36px;">
                                        <input type="checkbox" class="form-check-input" wire:model.live="selectAll" title="Pilih Semua di Halaman Ini">
                                    </th>
                                    <th>Peserta</th>
                                    <th>Email &amp; No. HP</th>
                                    <th>Instansi / OPD</th>
                                    <th>No. Sertifikat</th>
                                    <th>Kwitansi</th>
                                    <th>Status Email</th>
                                    <th>Tgl Pembuatan</th>
                                    <th class="text-end" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($participants as $participant)
                                    <tr wire:key="{{ $participant->id }}" class="{{ in_array((string) $participant->id, $selectedParticipants) ? 'table-active' : '' }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input" value="{{ (string) $participant->id }}" wire:model.live="selectedParticipants">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span
                                                    class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center fw-bold text-uppercase"
                                                    style="width: 32px; height: 32px; font-size: 11px; flex-shrink: 0;">
                                                    {{ mb_substr($participant->name, 0, 2) }}
                                                </span>
                                                <div>
                                                    <a href="{{ route('admin.participant.linkpemda.show', $participant->id) }}" wire:navigate
                                                        class="fw-semibold text-dark text-decoration-none">
                                                        {{ $participant->name }}
                                                    </a>
                                                    <small class="text-muted d-block">{{ $participant->position ?: '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                @if ($participant->email)
                                                    <a href="mailto:{{ $participant->email }}"
                                                        class="text-primary text-decoration-none">
                                                        {{ $participant->email }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $participant->phone ?: '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark text-truncate d-block" style="max-width: 180px;" title="{{ $participant->institution_name }}">
                                                {{ $participant->institution_name ?: '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-monospace fs-11" title="{{ $participant->theme ?: 'Sertifikat Bimtek' }}">
                                                {{ $participant->certificate_number ?: '-' }}
                                            </span>
                                            @if ($participant->qr_code_path)
                                                <button type="button" class="btn btn-xs btn-link text-muted p-0 ms-1 align-baseline"
                                                    wire:click="showQrModal('{{ $participant->id }}')" title="Lihat gambar QR Code">
                                                    <i class="bx bx-qr fs-14"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-dark fw-medium">{{ $participant->formatted_amount }}</span>
                                        </td>
                                        <td>
                                            @if ($participant->is_email_sent)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"
                                                    title="{{ $participant->email_sent_at?->translatedFormat('d M Y, H:i') }}">
                                                    Terkirim
                                                </span>
                                            @elseif ($participant->email_status === 'failed')
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                    Gagal
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                                    Belum Dikirim
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-dark fs-12">{{ $participant->created_at?->translatedFormat('d M Y') ?? '-' }}</div>
                                            <small class="text-muted">{{ $participant->created_at ? $participant->created_at->format('H:i') . ' WIB' : '-' }}</small>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex align-items-center justify-content-end gap-1">
                                                {{-- Tombol Cepat: Lihat Detail --}}
                                                <a href="{{ route('admin.participant.linkpemda.show', $participant->id) }}" wire:navigate
                                                    class="btn btn-sm btn-light border" title="Lihat Detail & Dokumen Lengkap">
                                                    <i class="mdi mdi-eye-outline text-primary"></i>
                                                </a>

                                                {{-- Dropdown Aksi Lainnya --}}
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-sm btn-light border dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 9999;">
                                                        @canany(['edit-participants', 'edit-certificates'])
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                wire:click="sendCertificateEmail('{{ $participant->id }}')">
                                                                <i class="mdi {{ $participant->is_email_sent ? 'mdi-email-sync text-info' : 'mdi-send text-primary' }} me-2"></i>
                                                                {{ $participant->is_email_sent ? 'Kirim Ulang Email' : 'Kirim E-Sertifikat' }}
                                                            </a>
                                                            <div class="dropdown-divider my-1"></div>
                                                            <a class="dropdown-item" target="_blank"
                                                                href="{{ route('admin.participant.linkpemda.certificate.view', $participant->id) }}">
                                                                <i class="mdi mdi-file-pdf-box me-2 text-danger"></i> Sertifikat Warna
                                                            </a>
                                                            <a class="dropdown-item" target="_blank"
                                                                href="{{ route('admin.participant.linkpemda.certificate.print', $participant->id) }}">
                                                                <i class="mdi mdi-printer me-2 text-secondary"></i> Sertifikat Blank
                                                            </a>
                                                            <a class="dropdown-item" target="_blank"
                                                                href="{{ route('admin.participant.linkpemda.kwitansi.view', $participant->id) }}">
                                                                <i class="mdi mdi-receipt me-2 text-success"></i> Kwitansi
                                                            </a>
                                                            <div class="dropdown-divider my-1"></div>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.participant.linkpemda.edit', $participant->id) }}"
                                                                wire:navigate>
                                                                <i class="mdi mdi-pencil me-2 text-warning"></i> Edit Data
                                                            </a>
                                                        @endcanany
                                                        @canany(['delete-participants', 'delete-certificates'])
                                                            <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                                wire:click="confirmDelete('{{ $participant->id }}')">
                                                                <i class="mdi mdi-trash-can me-2"></i> Hapus
                                                            </a>
                                                        @endcanany
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-account-off font-size-24 d-block mb-1"></i>
                                            Tidak ada data peserta yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($participants->hasPages())
                    <div class="card-footer mb-0">
                        {{ $participants->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div wire:ignore.self class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title text-white mb-0" id="deleteConfirmModalLabel">
                        <i class="mdi mdi-alert-circle me-1"></i> Konfirmasi Hapus Peserta
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-0 text-dark">
                        Apakah Anda yakin ingin menghapus data peserta <strong>{{ $participantToDeleteName }}</strong>?
                        Data yang dihapus akan dipindahkan ke tempat pembuangan (soft delete).
                    </p>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">
                        <i class="mdi mdi-trash-can me-1"></i> Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pratinjau QR Code -->
    <div wire:ignore.self class="modal fade" id="qrPreviewModal" tabindex="-1" aria-labelledby="qrPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title mb-0 fs-13 text-dark fw-bold" id="qrPreviewModalLabel">
                        {{ $previewQrTitle }}
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4 bg-light">
                    @if ($previewQrUrl)
                        <img src="{{ $previewQrUrl }}" alt="QR Code" class="img-fluid border rounded p-2 bg-white shadow-sm" style="max-height: 220px;">
                    @else
                        <div class="text-muted fs-13">
                            <i class="bx bx-image-alt fs-36 d-block mb-1 text-muted"></i>
                            File gambar QR belum ditemukan di server.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    const deleteModalEl = document.getElementById('deleteConfirmModal');
    let deleteModal = null;
    if (deleteModalEl) {
        deleteModal = new bootstrap.Modal(deleteModalEl);
    }

    $wire.on('open-delete-modal', () => {
        if (deleteModal) deleteModal.show();
    });

    $wire.on('close-delete-modal', () => {
        if (deleteModal) deleteModal.hide();
    });

    const qrModalEl = document.getElementById('qrPreviewModal');
    let qrModal = null;
    if (qrModalEl) {
        qrModal = new bootstrap.Modal(qrModalEl);
    }

    $wire.on('open-qr-modal', () => {
        if (qrModal) qrModal.show();
    });

    $wire.on('close-qr-modal', () => {
        if (qrModal) qrModal.hide();
    });
</script>
@endscript
