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
                            <a href="{{ route('admin.user.create') }}" wire:navigate class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i> Tambah
                            </a>
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

                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="selectedStatus">
                                <option value="">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex justify-content-md-end gap-2">
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
                                    <th>Status</th>
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
                                            @if ($user->is_active)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aktif</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Nonaktif</span>
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
                                                        <a class="dropdown-item" href="{{ route('admin.user.edit', $user->id) }}" wire:navigate>
                                                            <i class="mdi mdi-pencil me-1"></i> Edit
                                                        </a>
                                                        @if ($user->id !== auth()->id())
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                wire:click="toggleStatus('{{ $user->id }}')">
                                                                <i class="mdi {{ $user->is_active ? 'mdi-account-off text-warning' : 'mdi-account-check text-success' }} me-1"></i>
                                                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                            </a>
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
                                        <td colspan="7" class="text-center py-4 text-muted">
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
