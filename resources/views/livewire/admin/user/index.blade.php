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
                                Kelola pengguna, peran (role), dan hak akses sistem portal.
                            </p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="create">
                                <i class="mdi mdi-plus me-1"></i> Tambah
                            </button>
                        </div>
                    </div>

                    <!-- Search & Filter Controls -->
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control" placeholder="Cari nama atau email..."
                                    wire:model.live.debounce.300ms="search">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="selectedRole">
                                <option value="">Semua Peran (Role)</option>
                                @foreach ($roles as $roleItem)
                                    <option value="{{ $roleItem->value }}">{{ $roleItem->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-5 d-flex justify-content-md-end gap-2">
                            <select class="form-select w-auto" wire:model.live="perPage">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Pengguna</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Terdaftar</th>
                                    <th class="text-end" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $index => $user)
                                    <tr wire:key="{{ $user->id }}">
                                        <th scope="row">{{ $users->firstItem() + $index }}</th>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span
                                                    class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center fw-bold text-uppercase"
                                                    style="width: 34px; height: 34px; font-size: 12px;">
                                                    {{ $user->initials() }}
                                                </span>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $user->name }}
                                                        @if ($user->id === auth()->id())
                                                            <span class="badge bg-primary">Akun Anda</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @php
                                                $primaryRole = $user->roles->first()?->name;
                                            @endphp
                                            @if ($primaryRole === 'superadmin')
                                                <span class="badge badge-soft-danger px-2 py-1">Superadmin</span>
                                            @elseif ($primaryRole === 'admin')
                                                <span class="badge badge-soft-primary px-2 py-1">Admin</span>
                                            @else
                                                <span class="badge badge-soft-info px-2 py-1">Staff</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $user->created_at->translatedFormat('d M Y, H:i') }}</small>
                                        </td>
                                        <td class="text-end">
                                            @php
                                                $canManageThisUser = $isSuperadmin || $primaryRole !== 'superadmin';
                                            @endphp
                                            <div class="btn-group">
                                                <button type="button"
                                                    class="btn btn-sm btn-light dropdown-toggle text-end"
                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end shadow-sm"
                                                    style="z-index: 9999;">
                                                    @if ($canManageThisUser)
                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                            wire:click="edit('{{ $user->id }}')">
                                                            <i class="mdi mdi-pencil me-1"></i> Edit
                                                        </a>
                                                        @if ($user->id !== auth()->id())
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                                wire:click="confirmDelete('{{ $user->id }}')">
                                                                <i class="mdi mdi-trash-can me-1"></i> Hapus
                                                            </a>
                                                        @endif
                                                    @else
                                                        <span class="dropdown-item text-muted disabled py-1 fs-12">
                                                            <i class="mdi mdi-shield-lock me-1 text-danger"></i> Terproteksi (Superadmin)
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-account-off font-size-24 d-block mb-1"></i>
                                            Tidak ada data pengguna yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($users->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <small class="text-muted">
                                Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari
                                {{ $users->total() }} data
                            </small>
                            <div>
                                {{ $users->links() }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form (Tambah / Edit Pengguna) -->
    <div class="modal fade" id="userFormModal" tabindex="-1" aria-labelledby="userFormModalLabel" aria-hidden="true"
        wire:ignore.self
        x-data="{ modal: null }"
        x-init="modal = new bootstrap.Modal($el)"
        @open-user-modal.window="modal.show()"
        @close-user-modal.window="modal.hide()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit="save">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userFormModalLabel">
                            <i class="mdi {{ $isEditing ? 'mdi-account-edit' : 'mdi-account-plus' }} me-1 text-primary"></i>
                            {{ $isEditing ? 'Ubah Data Pengguna' : 'Tambah Pengguna Baru' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Nama Lengkap -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: Budi Santoso" wire:model="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="contoh: user@linkpemda.com" wire:model="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role / Peran -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Peran (Role) <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" wire:model="role">
                                @foreach ($assignableRoles as $roleOption)
                                    <option value="{{ $roleOption->value }}">{{ $roleOption->label() }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-3 text-muted">

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Password
                                @if (!$isEditing)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                placeholder="{{ $isEditing ? 'Kosongkan jika tidak ingin mengubah password' : 'Min. 8 karakter' }}"
                                wire:model="password">
                            @if ($isEditing)
                                <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah password.</small>
                            @endif
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" class="form-control"
                                placeholder="Ulangi password di atas" wire:model="password_confirmation">
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <span wire:loading.remove wire:target="save"><i class="mdi mdi-content-save me-1"></i></span>
                            {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true"
        wire:ignore.self
        x-data="{ modal: null }"
        x-init="modal = new bootstrap.Modal($el)"
        @open-delete-modal.window="modal.show()"
        @close-delete-modal.window="modal.hide()">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-3">
                <div class="modal-body">
                    <div class="text-danger mb-2">
                        <i class="mdi mdi-alert-circle-outline font-size-48" style="font-size: 54px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Hapus Pengguna?</h5>
                    <p class="text-muted fs-13 mb-3">
                        Apakah Anda yakin ingin menghapus akun <strong>{{ $userToDeleteName }}</strong>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" wire:click="delete" wire:loading.attr="disabled">
                            <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
