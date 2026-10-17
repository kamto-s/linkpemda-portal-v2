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
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h4 class="header-title">Daftar {{ $pageTitle }}</h4>
                            <p class="sub-header">
                                Kelola koleksi template email pemasaran, info bimtek, dan format kedinasan.
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.template.create') }}" wire:navigate class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i> Tambah
                            </a>
                        </div>
                    </div>

                    <!-- Search & Filter Controls -->
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control" placeholder="Cari nama template atau subjek..."
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
                            <select class="form-select" wire:model.live="selectedCategory">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="selectedStatus">
                                <option value="">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>

                        @if ($search !== '' || $selectedCategory !== '' || $selectedStatus !== '')
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

                    <!-- Tabel Master Template Email -->
                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Template</th>
                                    <th>Subjek Default</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Tipe</th>
                                    <th class="text-end" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($templates as $item)
                                    <tr wire:key="{{ $item->id }}">
                                        <td>
                                            <a href="{{ route('admin.template.show', $item->id) }}" wire:navigate class="fw-semibold text-dark text-decoration-none hover-primary">
                                                {{ $item->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="text-muted fs-13 text-truncate d-inline-block" style="max-width: 380px;" title="{{ $item->subject }}">
                                                {{ $item->subject }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $catVal = $item->category->value;
                                            @endphp
                                            @if ($catVal === 'marketing')
                                                <span class="badge badge-soft-primary px-2 py-1">{{ $item->category->label() }}</span>
                                            @elseif ($catVal === 'info')
                                                <span class="badge badge-soft-info px-2 py-1">{{ $item->category->label() }}</span>
                                            @elseif ($catVal === 'agenda')
                                                <span class="badge badge-soft-warning px-2 py-1">{{ $item->category->label() }}</span>
                                            @elseif ($catVal === 'reminder')
                                                <span class="badge badge-soft-danger px-2 py-1">{{ $item->category->label() }}</span>
                                            @else
                                                <span class="badge badge-soft-secondary px-2 py-1">{{ $item->category->label() }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->is_active)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aktif</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->is_system)
                                                <span class="badge badge-soft-secondary px-2 py-1">Sistem</span>
                                            @else
                                                <span class="badge badge-soft-info px-2 py-1">Kustom</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <button type="button"
                                                    class="btn btn-sm btn-light dropdown-toggle text-end"
                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end shadow-sm"
                                                    style="z-index: 9999;">
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.template.show', $item->id) }}"
                                                        wire:navigate>
                                                        <i class="mdi mdi-eye me-1"></i> Pratinjau
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.template.edit', $item->id) }}"
                                                        wire:navigate>
                                                        <i class="mdi mdi-pencil me-1"></i> Edit
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.campaign.create', ['template' => $item->id]) }}"
                                                        wire:navigate>
                                                        <i class="mdi mdi-send me-1"></i> Buat Campaign
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="javascript:void(0);"
                                                        wire:click="toggleStatus('{{ $item->id }}')">
                                                        <i class="mdi {{ $item->is_active ? 'mdi-close-circle text-warning' : 'mdi-check-circle text-success' }} me-1"></i>
                                                        {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </a>
                                                    @if (!$item->is_system)
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger"
                                                            href="javascript:void(0);"
                                                            wire:click="confirmDelete('{{ $item->id }}')">
                                                            <i class="mdi mdi-trash-can me-1"></i> Hapus
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-text-box-search-outline font-size-24 d-block mb-1"></i>
                                            Tidak ada data template yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($templates->hasPages())
                        <div class="mt-3 pt-3 border-top">
                            {{ $templates->links() }}
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
                    <h5 class="fw-bold mb-2">Hapus Template?</h5>
                    <p class="text-muted fs-13 mb-3">
                        Apakah Anda yakin ingin menghapus template <strong>{{ $templateToDeleteName }}</strong>? Tindakan ini tidak dapat dibatalkan.
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
