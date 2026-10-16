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
                        <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}" wire:navigate>Pengguna & Staff</a></li>
                        <li class="breadcrumb-item active">{{ $isEditing ? 'Ubah' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="header-title">
                                    {{ $isEditing ? 'Form Ubah Pengguna' : 'Form Tambah Pengguna' }}
                                </h4>
                                <p class="sub-header">
                                    Lengkapi informasi akun pengguna dan tentukan perannya.
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.user.index') }}" wire:navigate class="btn btn-light">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    <span wire:loading.remove wire:target="save"><i class="mdi mdi-content-save me-1"></i></span>
                                    {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                                </button>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Nama Lengkap -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Contoh: Budi Santoso" wire:model="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Alamat Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    placeholder="contoh: user@linkpemda.com" wire:model="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role / Peran -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Peran (Role) <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" wire:model="role">
                                    @foreach ($assignableRoles as $roleOption)
                                        <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-2 text-muted">
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
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
                                    <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengganti password.</small>
                                @endif
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Konfirmasi Password</label>
                                <input type="password" class="form-control"
                                    placeholder="Ulangi password" wire:model="password_confirmation">
                            </div>

                            <!-- Tombol Aksi Bawah -->
                            <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.user.index') }}" wire:navigate class="btn btn-light">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    <span wire:loading.remove wire:target="save"><i class="mdi mdi-content-save me-1"></i></span>
                                    {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
