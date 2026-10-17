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
                                Kelola data kontak sasaran promosi dan pengiriman email blast.
                            </p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-success" wire:click="openImportModal">
                                <i class="mdi mdi-file-excel"></i> Import Excel
                            </button>
                            <a href="{{ route('admin.campaign.create') }}" wire:navigate class="btn btn-light">
                                <i class="bx bx-send"></i> Kirim Email
                            </a>
                            <a href="{{ route('admin.contact.create') }}" wire:navigate class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Tambah
                            </a>
                        </div>
                    </div>

                    <!-- Search & Filter Controls -->
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control" placeholder="Cari nama, email, instansi..."
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

                    <!-- Tabel Kontak Target -->
                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Kontak</th>
                                    <th>Email &amp; No. HP</th>
                                    <th>Instansi / Perusahaan</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th class="text-end" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($contacts as $contact)
                                    <tr wire:key="{{ $contact->id }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span
                                                    class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center fw-bold text-uppercase"
                                                    style="width: 34px; height: 34px; font-size: 12px; flex-shrink: 0;">
                                                    {{ mb_substr($contact->name, 0, 2) }}
                                                </span>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $contact->name }}</div>
                                                    <small
                                                        class="text-muted">{{ $contact->position ?? 'PIC / Staff' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <a href="mailto:{{ $contact->email }}"
                                                    class="text-primary text-decoration-none">
                                                    {{ $contact->email }}
                                                </a>
                                            </div>
                                            <small class="text-muted">{{ $contact->phone ?: '-' }}</small>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-semibold text-dark">{{ $contact->organization_name }}</span>
                                        </td>
                                        <td>
                                            @if ($contact->category->value === 'pemerintah')
                                                <span class="badge badge-soft-info px-2 py-1">
                                                    Pemerintah
                                                </span>
                                            @else
                                                <span class="badge badge-soft-warning px-2 py-1">
                                                    Swasta
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($contact->is_active)
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aktif</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Nonaktif</span>
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
                                                        href="{{ route('admin.contact.edit', $contact->id) }}"
                                                        wire:navigate>
                                                        <i class="mdi mdi-pencil me-1"></i> Edit
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:void(0);"
                                                        wire:click="toggleStatus('{{ $contact->id }}')">
                                                        <i
                                                            class="mdi {{ $contact->is_active ? 'mdi-account-off text-warning' : 'mdi-account-check text-success' }} me-1"></i>
                                                        {{ $contact->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                        wire:click="confirmDelete('{{ $contact->id }}')">
                                                        <i class="mdi mdi-trash-can me-1"></i> Hapus
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-account-off font-size-24 d-block mb-1"></i>
                                            Tidak ada data kontak yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($contacts->hasPages())
                        <div class="mt-3 pt-3 border-top">
                            {{ $contacts->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>


    <!-- Modal Import Excel -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true" wire:ignore.self
        x-data="{ modal: null }" x-init="modal = new bootstrap.Modal($el)" @open-import-modal.window="modal.show()"
        @close-import-modal.window="modal.hide()">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="mdi mdi-file-excel text-success me-1"></i> Import Kontak dari Excel / CSV
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        wire:click="closeImportModal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Panduan & Download Format Template -->
                    <div
                        class="alert alert-info d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div>
                            <div class="fw-bold mb-1"><i class="mdi mdi-information me-1"></i> Format Kolom Excel:
                            </div>
                            <small class="d-block text-muted">
                                Kolom yang dikenali: <strong>Nama</strong>, <strong>Email</strong>,
                                <strong>Instansi</strong>, <strong>Jabatan</strong>, <strong>Telepon/WA</strong>,
                                <strong>Kategori</strong> (Pemerintah/Swasta), dan <strong>Status</strong>.
                            </small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary bg-white"
                            wire:click="downloadTemplate">
                            <i class="mdi mdi-download me-1"></i> Download Template Excel
                        </button>
                    </div>

                    <!-- Ringkasan Hasil Import -->
                    @if ($importSummary)
                        <div class="card bg-light border mb-3">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-2">Hasil Import:</h6>
                                <div class="row g-2 text-center">
                                    <div class="col-3">
                                        <div class="border rounded bg-white p-2">
                                            <div class="fs-18 fw-bold text-dark">{{ $importSummary['total'] }}</div>
                                            <small class="text-muted fs-11">Total Baris</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded bg-white p-2">
                                            <div class="fs-18 fw-bold text-success">{{ $importSummary['imported'] }}
                                            </div>
                                            <small class="text-muted fs-11">Ditambahkan</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded bg-white p-2">
                                            <div class="fs-18 fw-bold text-info">{{ $importSummary['updated'] }}</div>
                                            <small class="text-muted fs-11">Diperbarui</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded bg-white p-2">
                                            <div class="fs-18 fw-bold text-warning">{{ $importSummary['skipped'] }}
                                            </div>
                                            <small class="text-muted fs-11">Dilewati</small>
                                        </div>
                                    </div>
                                </div>

                                @if (!empty($importSummary['errors']))
                                    <div class="mt-3">
                                        <small class="fw-bold text-danger d-block mb-1">Catatan Kendala / Baris Tidak
                                            Valid:</small>
                                        <ul class="text-danger fs-12 ps-3 mb-0"
                                            style="max-height: 120px; overflow-y: auto;">
                                            @foreach ($importSummary['errors'] as $err)
                                                <li>{{ $err }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <form wire:submit="importExcel">
                        <!-- File Upload Input -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Berkas Excel / CSV <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('importFile') is-invalid @enderror"
                                wire:model="importFile" accept=".xlsx,.xls,.csv">
                            @error('importFile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mendukung berkas .xlsx, .xls, dan .csv (Maksimal 10 MB).</small>

                            <!-- Livewire Uploading Indicator -->
                            <div wire:loading wire:target="importFile" class="text-primary fs-12 mt-1">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Mengunggah berkas ke sistem...
                            </div>
                        </div>

                        <!-- Opsi Duplikasi Email -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jika Alamat Email Sudah Ada di Sistem:</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="update" id="dupUpdate"
                                        wire:model="duplicateHandling">
                                    <label class="form-check-label" for="dupUpdate">
                                        <strong>Perbarui data</strong> kontak dengan data terbaru
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="skip" id="dupSkip"
                                        wire:model="duplicateHandling">
                                    <label class="form-check-label" for="dupSkip">
                                        <strong>Lewati</strong> (jangan ubah kontak yang sudah ada)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light px-0 pb-0 pt-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                                wire:click="closeImportModal">Tutup</button>
                            <button type="submit" class="btn btn-success px-3" wire:loading.attr="disabled"
                                wire:target="importExcel,importFile">
                                <span wire:loading wire:target="importExcel"
                                    class="spinner-border spinner-border-sm me-1" role="status"></span>
                                <i class="mdi mdi-upload me-1" wire:loading.remove wire:target="importExcel"></i>
                                Mulai Import
                            </button>
                        </div>
                    </form>
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
                    <h5 class="fw-bold mb-2">Hapus Kontak?</h5>
                    <p class="text-muted fs-13 mb-3">
                        Apakah Anda yakin ingin menghapus kontak <strong>{{ $contactToDeleteName }}</strong>? Tindakan
                        ini tidak dapat dibatalkan.
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
