<div>
    <div class="py-3 py-lg-4">
        <div class="row">
            <div class="col-lg-6">
                <h4 class="page-title mb-0">{{ $isEditing ? 'Hak Akses: ' . ucfirst($name) : 'Tambah Role' }}</h4>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.role.index') }}" wire:navigate>Role & Hak
                                Akses</a></li>
                        <li class="breadcrumb-item active">{{ $isEditing ? 'Ubah' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if ($isEditing && strtolower($name) === 'superadmin')
        <div class="alert alert-info py-2 px-3 mb-3 fs-13">
            <i class="mdi mdi-shield-crown text-primary me-1"></i>
            <strong>Superadmin:</strong> Seluruh hak akses aktif permanen.
        </div>
    @endif

    <form wire:submit="save">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">
                            {{ $isEditing ? 'Form Ubah Role: ' . ucfirst($name) : 'Form Tambah Role Baru' }}
                        </h4>
                        <p class="sub-header mb-0">
                            Tentukan hak akses untuk role ini dengan saklar di bawah.
                        </p>
                    </div>

                    <div class="card-body">

                        <!-- Baris Input Nama Role & Tombol Cepat -->
                        <div class="row g-2 mb-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold mb-1">Nama Role <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Contoh: editor, keuangan" wire:model="name"
                                    {{ $isEditing && in_array(strtolower($name), ['superadmin', 'admin', 'staff']) ? 'readonly' : '' }}>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8 d-flex justify-content-md-end align-items-center gap-2">
                                <span class="badge bg-light text-dark border px-3 py-2 fs-13">
                                    <strong>{{ $totalSelected }}</strong> / {{ $totalAllPermissions }} Izin Aktif
                                </span>
                                @if (!($isEditing && strtolower($name) === 'superadmin'))
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        wire:click="selectAll">
                                        Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        wire:click="deselectAll">
                                        Reset
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Tabel Matriks CRUD Murni Sederhana -->
                        <div class="table-responsive" style="overflow: visible;">
                            <table class="table table-hover align-middle mb-0 text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-start ps-3" style="width: 30%;">Fitur / Modul</th>
                                        <th style="width: 14%;">Lihat</th>
                                        <th style="width: 14%;">Tambah</th>
                                        <th style="width: 14%;">Ubah</th>
                                        <th style="width: 14%;">Hapus</th>
                                        <th style="width: 14%;">Semua</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($matrixFeatures as $feat)
                                        @php
                                            $rowKeys = array_values(
                                                array_filter([
                                                    $feat['view'],
                                                    $feat['create'],
                                                    $feat['edit'],
                                                    $feat['delete'],
                                                ]),
                                            );
                                            $selectedInRow = count(array_intersect($rowKeys, $selectedPermissions));
                                            $allRowSelected = count($rowKeys) > 0 && $selectedInRow === count($rowKeys);
                                            $isSuper = $isEditing && strtolower($name) === 'superadmin';
                                        @endphp
                                        <tr>
                                            <!-- Nama Fitur -->
                                            <td class="text-start ps-3 fw-semibold text-dark">
                                                {{ $feat['name'] }}
                                            </td>

                                            <!-- Lihat -->
                                            <td>
                                                @if ($feat['view'])
                                                    <div class="form-check form-switch d-inline-block mb-0">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            value="{{ $feat['view'] }}"
                                                            wire:model="selectedPermissions"
                                                            style="cursor: pointer; width: 2.2em; height: 1.15em;"
                                                            {{ $isSuper ? 'disabled checked' : '' }}>
                                                    </div>
                                                @else
                                                    <span class="text-muted fw-bold">-</span>
                                                @endif
                                            </td>

                                            <!-- Tambah -->
                                            <td>
                                                @if ($feat['create'])
                                                    <div class="form-check form-switch d-inline-block mb-0">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            value="{{ $feat['create'] }}"
                                                            wire:model="selectedPermissions"
                                                            style="cursor: pointer; width: 2.2em; height: 1.15em;"
                                                            {{ $isSuper ? 'disabled checked' : '' }}>
                                                    </div>
                                                @else
                                                    <span class="text-muted fw-bold">-</span>
                                                @endif
                                            </td>

                                            <!-- Ubah -->
                                            <td>
                                                @if ($feat['edit'])
                                                    <div class="form-check form-switch d-inline-block mb-0">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            value="{{ $feat['edit'] }}"
                                                            wire:model="selectedPermissions"
                                                            style="cursor: pointer; width: 2.2em; height: 1.15em;"
                                                            {{ $isSuper ? 'disabled checked' : '' }}>
                                                    </div>
                                                @else
                                                    <span class="text-muted fw-bold">-</span>
                                                @endif
                                            </td>

                                            <!-- Hapus -->
                                            <td>
                                                @if ($feat['delete'])
                                                    <div class="form-check form-switch d-inline-block mb-0">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            value="{{ $feat['delete'] }}"
                                                            wire:model="selectedPermissions"
                                                            style="cursor: pointer; width: 2.2em; height: 1.15em;"
                                                            {{ $isSuper ? 'disabled checked' : '' }}>
                                                    </div>
                                                @else
                                                    <span class="text-muted fw-bold">-</span>
                                                @endif
                                            </td>

                                            <!-- Semua di Baris Ini -->
                                            <td>
                                                @if (!($isEditing && strtolower($name) === 'superadmin') && count($rowKeys) > 0)
                                                    <button type="button"
                                                        class="btn btn-sm {{ $allRowSelected ? 'btn-soft-secondary' : 'btn-soft-primary' }} px-2 py-1 fs-12"
                                                        wire:click="toggleFeatureRow(@js($rowKeys))">
                                                        {{ $allRowSelected ? 'Batal' : 'Pilih' }}
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="card-footer text-muted">
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('admin.role.index') }}" wire:navigate
                                class="btn btn-light border me-2">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-3" wire:loading.attr="disabled">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"
                                    role="status"></span>
                                <i class="mdi mdi-content-save me-1" wire:loading.remove wire:target="save"></i>
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
