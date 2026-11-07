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
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <div>
                            <h4 class="">Daftar {{ $pageTitle }}</h4>
                            <p class="mb-0">Kelola konfigurasi parameter dan variabel global sistem aplikasi.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search & Controls -->
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control" placeholder="Cari key, value, deskripsi..."
                                    wire:model.live.debounce.300ms="search">
                                @if ($search !== '')
                                    <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')"
                                        title="Hapus pencarian">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if ($search !== '')
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

                    <!-- Tabel Pengaturan Sistem -->
                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Value</th>
                                    <th>Deskripsi</th>
                                    <th>Diperbarui</th>
                                    <th class="text-end" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($settings as $item)
                                    @php
                                        $itemKey = $item->key ?? $item->setting_key;
                                        $itemValue = $item->value ?? $item->setting_value;
                                    @endphp
                                    <tr wire:key="{{ $item->id }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge badge-soft-danger px-2 py-1">{{ $itemKey }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="font-monospace fs-13 text-break" style="max-width: 320px;">
                                                @if ($itemValue === null || $itemValue === '')
                                                    <span class="badge bg-light text-muted border">null</span>
                                                @elseif ($itemValue === 'true' || $itemValue === 'false')
                                                    <span
                                                        class="badge {{ $itemValue === 'true' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} px-2 py-1">
                                                        {{ $itemValue }}
                                                    </span>
                                                @elseif (strlen($itemValue) > 60)
                                                    <span class="text-dark" title="{{ $itemValue }}">
                                                        {{ Str::limit($itemValue, 60) }}
                                                    </span>
                                                @else
                                                    <span class="text-dark fw-semibold">{{ $itemValue }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted d-block" style="max-width: 320px;">
                                                {{ $item->description ?: '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="text-dark">
                                                {{ $item->updated_at?->translatedFormat('d M Y') ?? '-' }}
                                            </div>
                                            <small
                                                class="text-muted">{{ $item->updated_at?->translatedFormat('H:i') ?? '' }}</small>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-soft-primary"
                                                wire:click="edit('{{ $item->id }}')" title="Edit Nilai Pengaturan">
                                                <i class="bx bx-cog"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-cog-off-outline font-size-24 d-block mb-1"></i>
                                            Tidak ada data pengaturan sistem yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($settings->hasPages())
                    <div class="card-footer mb-0">
                        <div class="pagination-rounded" x-data
                            x-effect="$el.querySelectorAll('.pagination').forEach(el => el.classList.add('pagination-rounded'))">
                            {{ $settings->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Form Edit Nilai Pengaturan -->
    <div class="modal fade" id="settingModal" tabindex="-1" aria-hidden="true" wire:ignore.self x-data="{ modal: null }"
        x-init="modal = bootstrap.Modal.getOrCreateInstance($el)" @open-setting-modal.window="modal.show()"
        @close-setting-modal.window="modal.hide()">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <form wire:submit="save">
                    <div class="modal-header py-3 px-4 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span
                                class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px; font-size: 15px;">
                                <i class="mdi mdi-tune"></i>
                            </span>
                            <div>
                                <h5 class="modal-title fw-bold mb-0 text-dark">Edit Pengaturan</h5>
                                <small class="text-muted">Perbarui nilai parameter sistem</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        <!-- Box Info Kunci (Key) Terkunci -->
                        <div class="bg-light p-3 rounded border mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-muted fw-semibold text-uppercase"
                                    style="font-size: 11px; letter-spacing: 0.5px;">Key</span>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"
                                    style="font-size: 10px;">
                                </span>
                            </div>
                            <div class="font-monospace fw-bold text-dark fs-14 text-break">
                                {{ $key }}
                            </div>
                        </div>

                        <!-- Nilai Pengaturan -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">
                                Value
                            </label>
                            <textarea class="form-control font-monospace fs-13 @error('value') is-invalid @enderror"
                                wire:model="value" rows="3"
                                placeholder="Masukkan nilai atau teks konfigurasi..."></textarea>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">
                                Nilai dapat berupa teks, path file, angka, atau nilai boolean (<code>true</code> /
                                <code>false</code>).
                            </small>
                        </div>

                        <!-- Deskripsi / Keterangan -->
                        <div class="mb-0">
                            <label class="form-label fw-bold text-dark">
                                Deskripsi
                            </label>
                            <textarea class="form-control fs-13 @error('description') is-invalid @enderror"
                                wire:model="description" rows="3"
                                placeholder="Keterangan singkat fungsi pengaturan ini..."></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer px-4 py-2 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-3" wire:loading.attr="disabled">
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"
                                role="status"></span>
                            <i class="mdi mdi-check me-1" wire:loading.remove wire:target="save"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>