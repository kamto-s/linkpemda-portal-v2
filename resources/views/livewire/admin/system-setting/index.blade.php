<div>
    <div class="py-3 py-lg-4">
        <div class="row">
            <div class="col-lg-6">
                <h4 class="page-title mb-0 d-flex align-items-center gap-2">
                    <i class="bx bx-slider-alt text-primary"></i>
                    <span>{{ $pageTitle }}</span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-11 ms-2">
                        <i class="mdi mdi-shield-lock-outline me-1"></i>Superadmin Only
                    </span>
                </h4>
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
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="card-title mb-1">Daftar Variabel & Parameter Sistem</h5>
                            <p class="text-muted fs-13 mb-0">
                                Konfigurasi parameter global aplikasi secara dinamis berbasis <code>key-value</code>.
                            </p>
                        </div>
                        <div>
                            <button type="button" wire:click="openCreateModal" class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i> Tambah Pengaturan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search & Filter Controls -->
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0"
                                    placeholder="Cari berdasarkan key, value, atau deskripsi..."
                                    wire:model.live.debounce.300ms="search">
                                @if ($search)
                                    <button class="btn btn-outline-secondary" type="button" wire:click="resetFilters">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-2 ms-auto">
                            <div class="d-flex align-items-center justify-content-md-end gap-2">
                                <label class="fs-13 text-muted text-nowrap mb-0">Tampilkan:</label>
                                <select class="form-select form-select-sm w-auto" wire:model.live="perPage">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Table List -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 200px;">Key (Kunci)</th>
                                    <th style="min-width: 240px;">Value (Nilai)</th>
                                    <th style="min-width: 260px;">Deskripsi</th>
                                    <th style="width: 170px;">Diperbarui</th>
                                    <th class="text-end" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($settings as $index => $item)
                                    @php
                                        $itemKey = $item->key ?? $item->setting_key;
                                        $itemValue = $item->value ?? $item->setting_value;
                                    @endphp
                                    <tr>
                                        <td class="text-muted fs-13">
                                            {{ $settings->firstItem() + $index }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <code class="text-primary fw-bold fs-13 px-2 py-1 bg-primary-subtle rounded border border-primary-subtle">
                                                    {{ $itemKey }}
                                                </code>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($itemValue === null || $itemValue === '')
                                                <span class="badge bg-light text-muted border">null</span>
                                            @elseif ($itemValue === 'true' || $itemValue === 'false')
                                                <span class="badge {{ $itemValue === 'true' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}">
                                                    {{ $itemValue }}
                                                </span>
                                            @elseif (strlen($itemValue) > 60)
                                                <span class="font-monospace fs-13 text-break" title="{{ $itemValue }}">
                                                    {{ Str::limit($itemValue, 60) }}
                                                </span>
                                            @else
                                                <span class="font-monospace fs-13 text-break fw-semibold text-dark">
                                                    {{ $itemValue }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fs-13 text-muted">
                                                {{ $item->description ?: '-' }}
                                            </span>
                                        </td>
                                        <td class="text-muted fs-12">
                                            <div>{{ $item->updated_at?->translatedFormat('d M Y, H:i') ?? '-' }}</div>
                                            <small class="text-secondary">{{ $item->updated_at?->diffForHumans() }}</small>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <button type="button"
                                                    class="btn btn-sm btn-light dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 9999;">
                                                    <a class="dropdown-item" href="javascript:void(0);"
                                                        wire:click="edit('{{ $item->id }}')">
                                                        <i class="mdi mdi-pencil me-1 text-primary"></i> Edit Pengaturan
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                        wire:click="confirmDelete('{{ $item->id }}')">
                                                        <i class="mdi mdi-trash-can me-1"></i> Hapus
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-tune-vertical font-size-24 d-block mb-2 text-secondary"></i>
                                            Belum ada pengaturan sistem yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($settings->hasPages())
                    <div class="card-footer bg-transparent border-top py-2">
                        {{ $settings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Form (Create / Edit) -->
    <div class="modal fade" id="settingModal" tabindex="-1" aria-hidden="true" wire:ignore.self
        x-data="{ modal: null }" x-init="modal = new bootstrap.Modal($el)"
        @open-setting-modal.window="modal.show()"
        @close-setting-modal.window="modal.hide()">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form wire:submit="save">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="bx bx-slider-alt text-primary me-1"></i>
                            {{ $isEditing ? 'Edit Pengaturan Sistem' : 'Tambah Pengaturan Sistem Baru' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Key (Kunci) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace @error('key') is-invalid @enderror"
                                wire:model="key" placeholder="contoh: site_name, maintenance_mode, api_max_limit">
                            @error('key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text fs-12">
                                Gunakan huruf kecil, angka, atau garis bawah (snake_case). Key ini digunakan di kode program via <code>SystemSetting::get('key')</code>.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Value (Nilai)</label>
                            <textarea class="form-control font-monospace @error('value') is-invalid @enderror"
                                wire:model="value" rows="4"
                                placeholder="Masukkan nilai pengaturan (teks, angka, JSON, atau boolean)"></textarea>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text fs-12">
                                Dapat diisi teks biasa, angka, boolean (true/false), atau string JSON.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi / Keterangan</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                wire:model="description" rows="2"
                                placeholder="Jelaskan fungsi dan tujuan pengaturan ini bagi sistem..."></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1" role="status"></span>
                            {{ $isEditing ? 'Simpan Perubahan' : 'Tambahkan Pengaturan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Setting -->
    <div class="modal fade" id="deleteSettingModal" tabindex="-1" aria-hidden="true" wire:ignore.self
        x-data="{ modal: null }" x-init="modal = new bootstrap.Modal($el)"
        @open-delete-setting-modal.window="modal.show()"
        @close-delete-setting-modal.window="modal.hide()">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-3">
                <div class="modal-body">
                    <div class="text-danger mb-2">
                        <i class="mdi mdi-alert-circle-outline" style="font-size: 52px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Hapus Pengaturan?</h5>
                    <p class="text-muted fs-13 mb-3">
                        Apakah Anda yakin ingin menghapus key <code class="text-danger fw-bold">{{ $settingToDeleteKey }}</code>?
                        Tindakan ini dapat berdampak pada fitur sistem yang bergantung padanya.
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
