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

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <div>
                            <h4 class="header-title">Daftar {{ $pageTitle }}</h4>
                            <p class="sub-header">
                                Kelola riwayat pengiriman email blast, pantau status pengiriman, dan buat kampanye baru.
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.campaign.create') }}" wire:navigate class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i> Tambah
                            </a>
                        </div>
                    </div>

                    <!-- Search & Filter Controls -->
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control" placeholder="Cari judul campaign atau subjek..."
                                    wire:model.live.debounce.300ms="search">
                                @if ($search !== '')
                                    <button class="btn btn-outline-secondary" type="button"
                                        wire:click="$set('search', '')" title="Hapus pencarian">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="selectedStatus">
                                <option value="">Semua Status</option>
                                @foreach ($statuses as $st)
                                    <option value="{{ $st->value }}">{{ $st->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if ($search !== '' || $selectedStatus !== '')
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

                    <!-- Tabel Campaign Email -->
                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Judul Campaign</th>
                                    <th>Subjek Email</th>
                                    <th>Target Penerima</th>
                                    <th>Status &amp; Progres</th>
                                    <th>Tanggal Dibuat</th>
                                    <th class="text-end" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($campaigns as $campaign)
                                    <tr wire:key="{{ $campaign->id }}">
                                        <td>
                                            <a href="{{ route('admin.campaign.show', $campaign->id) }}" wire:navigate
                                                class="fw-semibold text-dark text-decoration-none">
                                                {{ $campaign->title }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="text-muted fs-13 text-truncate d-inline-block" style="max-width: 300px;" title="{{ $campaign->subject }}">
                                                <i class="mdi mdi-email-outline me-1"></i>{{ $campaign->subject }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-info px-2 py-1 font-size-12">
                                                <i class="mdi mdi-account-group me-1"></i> {{ number_format($campaign->total_recipients) }} Kontak
                                            </span>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <span class="{{ $campaign->status->badgeClass() }} px-2 py-1 font-size-12">
                                                    {{ $campaign->status->label() }}
                                                </span>
                                            </div>
                                            @if ($campaign->total_recipients > 0)
                                                @php
                                                    $percent = round((($campaign->sent_count + $campaign->failed_count) / $campaign->total_recipients) * 100);
                                                @endphp
                                                <div class="progress" style="height: 5px; width: 140px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%;"
                                                        aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <small class="text-muted fs-11 mt-1 d-block">
                                                    {{ $campaign->sent_count }} terkirim • {{ $campaign->failed_count }} gagal
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted fs-13">{{ $campaign->created_at->format('d M Y, H:i') }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <button type="button"
                                                    class="btn btn-sm btn-light dropdown-toggle text-end"
                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 9999;">
                                                    <a class="dropdown-item" wire:navigate href="{{ route('admin.campaign.show', $campaign->id) }}">
                                                        <i class="mdi mdi-eye-outline me-1"></i> Detail & Monitoring
                                                    </a>
                                                    @if ($campaign->status->value === 'draft')
                                                        <a class="dropdown-item" wire:navigate href="{{ route('admin.campaign.edit', $campaign->id) }}">
                                                            <i class="mdi mdi-pencil me-1"></i> Edit Draft
                                                        </a>
                                                    @endif
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                        wire:click="confirmDelete('{{ $campaign->id }}')">
                                                        <i class="mdi mdi-trash-can me-1"></i> Hapus
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-email-open-outline font-size-24 d-block mb-1"></i>
                                            Tidak ada data campaign email yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($campaigns->hasPages())
                        <div class="mt-3 pt-3 border-top">
                            {{ $campaigns->links() }}
                        </div>
                    @endif

                </div>
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
                    <h5 class="fw-bold mb-2">Hapus Campaign?</h5>
                    <p class="text-muted fs-13 mb-3">
                        Apakah Anda yakin ingin menghapus campaign <strong>{{ $campaignToDeleteTitle }}</strong>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" wire:click="delete"
                            wire:loading.attr="disabled">
                            <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm me-1"
                                role="status"></span>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
