<div>
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.user.index') }}" wire:navigate class="btn btn-sm btn-light border">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <h4 class="page-title mb-0">
                        {{ $isEditing ? 'Ubah Pengguna: ' . $user->name : 'Tambah Pengguna Baru' }}
                    </h4>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}" wire:navigate>Pengguna &
                                Staff</a></li>
                        <li class="breadcrumb-item active">{{ $isEditing ? 'Ubah' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <form wire:submit="save">
                    <div class="card-header">
                        <h4 class="header-title">
                            {{ $isEditing ? 'Form Ubah Pengguna' : 'Form Tambah Pengguna Baru' }}
                        </h4>
                        <p class="sub-header mb-0">
                            Lengkapi informasi akun pengguna dan tentukan perannya.
                        </p>
                    </div>

                    <div class="card-body">

                        <div class="row g-3">
                            <!-- Nama Lengkap -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Contoh: Budi Santoso" wire:model="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Alamat Email <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    placeholder="contoh: user@linkpemda.com" wire:model="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role / Peran -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Peran (Role) <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" wire:model="role">
                                    @foreach ($assignableRoles as $roleOption)
                                        <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status Akun (Aktif / Nonaktif) -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold d-block">Status Akun</label>
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" role="switch" id="userIsActive"
                                        wire:model.live="isActive"
                                        {{ $isEditing && $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <label class="form-check-label fw-semibold ms-1" for="userIsActive">
                                        <span
                                            class="badge {{ $isActive ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                                            {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </label>
                                </div>
                                @if ($isEditing && $user->id === auth()->id())
                                    <small class="text-muted d-block mt-1">Anda tidak dapat menonaktifkan akun
                                        sendiri.</small>
                                @endif
                            </div>

                            <div class="col-12">
                                <hr class="my-2 text-muted">
                            </div>

                            <!-- Password -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">
                                    Password
                                    @if (!$isEditing)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    placeholder="{{ $isEditing ? 'Kosongkan jika tidak diubah' : 'Min. 8 karakter' }}"
                                    wire:model="password">
                                @if ($isEditing)
                                    <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengganti
                                        password.</small>
                                @endif
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Konfirmasi Password</label>
                                <input type="password" class="form-control" placeholder="Ulangi password"
                                    wire:model="password_confirmation">
                            </div>

                        </div>
                    </div>

                    <div class="card-footer text-muted">
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('admin.user.index') }}" wire:navigate class="btn btn-light border me-2">
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
                </form>
            </div>
        </div>
    </div>
</div>
