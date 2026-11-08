<div>
    <!-- Page Header & Breadcrumb -->
    <div class="py-3 py-lg-4">
        <div class="row">
            <div class="col-lg-6">
                <h4 class="page-title mb-0">{{ $pageTitle }}</h4>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
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
                            <span class="text-muted fs-12 d-block">Total Invoice</span>
                            <h5 class="mb-0 fw-bold">{{ number_format($totalInvoices) }}</h5>
                        </div>
                        <i class="mdi mdi-file-document-multiple text-primary fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card mb-0">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fs-12 d-block">Disetujui (ACC)</span>
                            <h5 class="mb-0 fw-bold text-success">{{ number_format($approvedCount) }}</h5>
                        </div>
                        <i class="mdi mdi-check-decagram text-success fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card mb-0">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fs-12 d-block">Menunggu ACC</span>
                            <h5 class="mb-0 fw-bold text-warning">{{ number_format($pendingCount) }}</h5>
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
                            <span class="text-muted fs-12 d-block">Total Tagihan</span>
                            <h5 class="mb-0 fw-bold text-dark fs-15">Rp {{ number_format($totalAmount, 0, ',', '.') }}
                            </h5>
                        </div>
                        <i class="mdi mdi-cash-multiple text-info fs-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card & Tabel Invoice -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1">Daftar {{ $pageTitle }}</h4>
                            <p class="mb-0 text-muted fs-13">
                                Kelola tagihan invoice bimtek dan pelatihan kedinasan Linkpemda.
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.invoice.linkpemda.create') }}" wire:navigate
                                class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Tambah
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <!-- Filter Toolbar -->
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control" placeholder="Cari invoice, instansi, tema..."
                                    wire:model.live.debounce.300ms="search">
                                @if ($search !== '')
                                    <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')"
                                        title="Hapus pencarian">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-2">
                            <select class="form-select" wire:model.live="selectedStatus">
                                <option value="">Semua Status ACC</option>
                                <option value="pending">Menunggu ACC</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-lg-2">
                            <select class="form-select" wire:model.live="selectedPaymentStatus">
                                <option value="">Semua Pembayaran</option>
                                <option value="unpaid">Belum Lunas</option>
                                <option value="partial">DP (Sebagian)</option>
                                <option value="paid">Lunas</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="selectedMenu">
                                <option value="">Semua Menu</option>
                                <option value="Meeting">Meeting</option>
                                <option value="Offline">Offline</option>
                                <option value="Online">Online</option>
                            </select>
                        </div>

                        @if ($search !== '' || $selectedStatus !== '' || $selectedPaymentStatus !== '' || $selectedMenu !== '')
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

                    <!-- Tabel Invoice Linkpemda -->
                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 125px;">No. Invoice</th>
                                    <th style="min-width: 160px; max-width: 180px;">Instansi / Klien</th>
                                    <th style="min-width: 170px; max-width: 200px;">Kegiatan Bimtek</th>
                                    <th class="text-center" style="width: 75px;">Peserta</th>
                                    <th style="min-width: 105px;">Total Biaya</th>
                                    <th class="text-center" style="width: 100px;">Status ACC</th>
                                    <th class="text-center" style="width: 105px;">Pembayaran</th>
                                    <th class="text-center" style="width: 90px;">E-Invoice</th>
                                    <th class="text-end" style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $item)
                                    @php
                                        $statusEnum = $item->status instanceof \App\Enums\InvoiceStatus ? $item->status : \App\Enums\InvoiceStatus::tryFrom($item->status);
                                        $payEnum = $item->payment_status instanceof \App\Enums\InvoicePaymentStatus ? $item->payment_status : \App\Enums\InvoicePaymentStatus::tryFrom($item->payment_status);

                                        $statusSubtle = match ($statusEnum) {
                                            \App\Enums\InvoiceStatus::Pending => 'bg-warning-subtle text-warning border border-warning-subtle',
                                            \App\Enums\InvoiceStatus::Approved => 'bg-success-subtle text-success border border-success-subtle',
                                            \App\Enums\InvoiceStatus::Rejected => 'bg-danger-subtle text-danger border border-danger-subtle',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };

                                        $paySubtle = match ($payEnum) {
                                            \App\Enums\InvoicePaymentStatus::Unpaid => 'bg-danger-subtle text-danger border border-danger-subtle',
                                            \App\Enums\InvoicePaymentStatus::Partial => 'bg-warning-subtle text-warning border border-warning-subtle',
                                            \App\Enums\InvoicePaymentStatus::Paid => 'bg-success-subtle text-success border border-success-subtle',
                                            default => 'bg-secondary-subtle text-secondary',
                                        };
                                    @endphp
                                    <tr wire:key="{{ $item->id }}">
                                        <!-- 1. No. Invoice -->
                                        <td>
                                            <a href="{{ route('admin.invoice.linkpemda.show', $item->id) }}" wire:navigate
                                                class="fw-semibold text-primary font-monospace text-decoration-none">
                                                {{ $item->invoice_number }}
                                            </a>
                                            <small class="text-muted d-block">{{ $item->short_invoice_date }}</small>
                                        </td>

                                        <!-- 2. Instansi / Klien -->
                                        <td>
                                            <a href="{{ route('admin.invoice.linkpemda.show', $item->id) }}" wire:navigate
                                                class="fw-semibold text-dark text-decoration-none text-truncate d-block"
                                                style="max-width: 175px;" title="{{ $item->company_name }}">
                                                {{ $item->company_name }}
                                            </a>
                                            <small class="text-muted d-block text-truncate" style="max-width: 175px;"
                                                title="{{ $item->email ?: ($item->phone ?: '-') }}">
                                                {{ $item->email ?: ($item->phone ?: '-') }}
                                            </small>
                                        </td>

                                        <!-- 3. Kegiatan Bimtek & Jadwal -->
                                        <td>
                                            <span class="fw-medium text-dark text-truncate d-block"
                                                style="max-width: 185px;" title="{{ $item->activity_theme }}">
                                                {{ $item->activity_theme ?: '-' }}
                                            </span>
                                            <small class="text-muted d-block text-nowrap">
                                                {{ $item->short_event_date }}
                                            </small>
                                        </td>

                                        <!-- 4. Jumlah Peserta -->
                                        <td class="text-center text-nowrap">
                                            <span class="badge bg-light text-dark border px-2 py-1 fs-11 rounded-pill">
                                                {{ $item->participant_count }} Orang
                                            </span>
                                        </td>

                                        <!-- 5. Total Biaya & Sisa -->
                                        <td class="text-nowrap">
                                            <span class="fw-bold text-dark">
                                                Rp {{ number_format((float) $item->total_amount, 0, ',', '.') }}
                                            </span>
                                            @if ((float) $item->remaining_amount > 0 && (float) $item->deposit_amount > 0)
                                                <small class="text-danger d-block fs-11">
                                                    Sisa: Rp {{ number_format((float) $item->remaining_amount, 0, ',', '.') }}
                                                </small>
                                            @endif
                                        </td>

                                        <!-- 6. Status ACC -->
                                        <td class="text-center text-nowrap">
                                            @if ($statusEnum)
                                                <span class="badge {{ $statusSubtle }} px-2 py-1 fs-11 rounded-pill">
                                                    <i class="mdi {{ $statusEnum->icon() }} me-1"></i>{{ $statusEnum->label() }}
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary px-2 py-1 fs-11 rounded-pill">{{ ucfirst($item->status) }}</span>
                                            @endif
                                        </td>

                                        <!-- 7. Status Pembayaran -->
                                        <td class="text-center text-nowrap">
                                            @if ($payEnum)
                                                <span class="badge {{ $paySubtle }} px-2 py-1 fs-11 rounded-pill">
                                                    <i class="mdi {{ $payEnum->icon() }} me-1"></i>{{ $payEnum->label() }}
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary px-2 py-1 fs-11 rounded-pill">{{ ucfirst($item->payment_status ?? 'unpaid') }}</span>
                                            @endif
                                        </td>

                                        <!-- 8. E-Invoice Email Status -->
                                        <td class="text-center text-nowrap">
                                            @if ($item->email_sent_at)
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11 rounded-pill"
                                                    title="Terkirim pada {{ $item->email_sent_at->translatedFormat('d M Y, H:i') }} WIB">
                                                    <i class="mdi mdi-email-check me-1"></i>Terkirim
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fs-11 rounded-pill">
                                                    <i class="mdi mdi-clock-outline me-1"></i>Belum
                                                </span>
                                            @endif
                                        </td>

                                        <!-- 9. Aksi -->
                                        <td class="text-end" style="width: 100px;">
                                            <div class="d-flex align-items-center justify-content-end gap-1">
                                                {{-- Tombol Cepat: Lihat Detail --}}
                                                <a href="{{ route('admin.invoice.linkpemda.show', $item->id) }}"
                                                    wire:navigate class="btn btn-sm btn-light border"
                                                    title="Lihat Detail & Dokumen Invoice">
                                                    <i class="mdi mdi-eye-outline text-primary"></i>
                                                </a>

                                                {{-- Dropdown Aksi Lainnya --}}
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-sm btn-light border dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                        title="Pilihan Aksi">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end shadow-sm"
                                                        style="z-index: 9999;">
                                                        <!-- Kirim Email -->
                                                        <a class="dropdown-item text-primary" href="javascript:void(0);"
                                                            wire:click="sendInvoiceEmail('{{ $item->id }}')"
                                                            wire:loading.attr="disabled">
                                                            <i class="mdi mdi-email-send me-2"></i> Kirim via Email
                                                        </a>
                                                        <div class="dropdown-divider my-1"></div>

                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.invoice.linkpemda.preview', $item->id) }}"
                                                            target="_blank">
                                                            <i class="mdi mdi-file-pdf-box me-2 text-danger"></i> Pratinjau
                                                            PDF
                                                        </a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.invoice.linkpemda.download', $item->id) }}">
                                                            <i class="mdi mdi-download me-2 text-success"></i> Unduh PDF
                                                        </a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.invoice.linkpemda.edit', $item->id) }}"
                                                            wire:navigate>
                                                            <i class="mdi mdi-pencil me-2 text-warning"></i> Edit Data
                                                        </a>

                                                        @if ($item->status === \App\Enums\InvoiceStatus::Pending)
                                                            <div class="dropdown-divider my-1"></div>
                                                            <a class="dropdown-item text-success" href="javascript:void(0);"
                                                                wire:click="approveInvoice('{{ $item->id }}')">
                                                                <i class="mdi mdi-check-decagram text-success me-2"></i> ACC /
                                                                Setujui
                                                            </a>
                                                            <a class="dropdown-item text-warning" href="javascript:void(0);"
                                                                wire:click="confirmReject('{{ $item->id }}')">
                                                                <i class="mdi mdi-close-circle text-warning me-2"></i> Tolak
                                                                Invoice
                                                            </a>
                                                        @endif

                                                        @if ($item->payment_status !== \App\Enums\InvoicePaymentStatus::Paid)
                                                            <div class="dropdown-divider my-1"></div>
                                                            <a class="dropdown-item text-success" href="javascript:void(0);"
                                                                wire:click="markAsPaid('{{ $item->id }}')">
                                                                <i class="mdi mdi-cash-check text-success me-2"></i> Tandai
                                                                Lunas
                                                            </a>
                                                        @endif

                                                        <div class="dropdown-divider my-1"></div>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                            wire:click="confirmDelete('{{ $item->id }}')">
                                                            <i class="mdi mdi-trash-can me-2"></i> Hapus
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <!-- <i class="mdi mdi-file-document-outline font-size-24 d-block mb-1"></i> -->
                                            Tidak ada data invoice yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($invoices->hasPages())
                    <div class="card-footer mb-0">
                        <div class="pagination-rounded" x-data
                            x-effect="$el.querySelectorAll('.pagination').forEach(el => el.classList.add('pagination-rounded'))">
                            {{ $invoices->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true" wire:ignore.self
        x-data="{ modal: null }" x-init="modal = new bootstrap.Modal($el)" @open-delete-modal.window="modal.show()"
        @close-delete-modal.window="modal.hide()">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-3">
                <div class="modal-body">
                    <div class="text-danger mb-2">
                        <i class="mdi mdi-alert-circle-outline font-size-48" style="font-size: 54px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Hapus Invoice?</h5>
                    <p class="text-muted fs-13 mb-3">
                        Apakah Anda yakin ingin menghapus invoice <strong>{{ $invoiceToDeleteNumber }}</strong>?
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" wire:click="delete" wire:loading.attr="disabled">
                            <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm me-1"
                                role="status"></span>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Tolak Invoice -->
    <div class="modal fade" id="rejectConfirmModal" tabindex="-1" aria-hidden="true" wire:ignore.self
        x-data="{ modal: null }" x-init="modal = new bootstrap.Modal($el)" @open-reject-modal.window="modal.show()"
        @close-reject-modal.window="modal.hide()">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-3">
                <div class="modal-body">
                    <div class="text-warning mb-2">
                        <i class="mdi mdi-alert-circle-outline font-size-48" style="font-size: 54px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Tolak Invoice?</h5>
                    <p class="text-muted fs-13 mb-3">
                        Tolak pengajuan invoice <strong>{{ $invoiceToRejectNumber }}</strong>.
                    </p>
                    <div class="text-start mb-3">
                        <label class="form-label fs-12 text-muted fw-medium">Catatan / Alasan Penolakan</label>
                        <textarea class="form-control form-control-sm" rows="3" wire:model="rejectionNote"
                            placeholder="Opsional / alasan revisi..."></textarea>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-warning" wire:click="rejectInvoice"
                            wire:loading.attr="disabled">
                            <span wire:loading wire:target="rejectInvoice" class="spinner-border spinner-border-sm me-1"
                                role="status"></span>
                            Tolak Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>