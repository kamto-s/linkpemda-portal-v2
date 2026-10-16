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
                                Atur izin akses (permissions) untuk setiap tingkatan pengguna di dalam portal.
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.role.create') }}" wire:navigate class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i> Tambah Role
                            </a>
                        </div>
                    </div>

                    <!-- Search & Filter Controls -->
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control" placeholder="Cari nama role..."
                                    wire:model.live.debounce.300ms="search">
                                @if ($search !== '')
                                    <button class="btn btn-outline-secondary" type="button" wire:click="resetFilters"
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

                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    {{-- <th style="width: 60px;">#</th> --}}
                                    <th>Role</th>
                                    <th>Pengguna</th>
                                    <th>Hak Akses</th>
                                    <th class="text-end" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $index => $roleItem)
                                    <tr wire:key="{{ $roleItem->id }}">
                                        {{-- <th scope="row">{{ $roles->firstItem() + $index }}</th> --}}
                                        <td>
                                            @if ($roleItem->name === 'superadmin')
                                                <span class="badge badge-soft-danger px-2 py-1">Superadmin</span>
                                            @elseif ($roleItem->name === 'admin')
                                                <span class="badge badge-soft-primary px-2 py-1">Admin</span>
                                            @elseif ($roleItem->name === 'staff')
                                                <span class="badge badge-soft-info px-2 py-1">Staff</span>
                                            @else
                                                <span
                                                    class="badge badge-soft-secondary px-2 py-1 text-capitalize">{{ $roleItem->name }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $roleItem->users_count }} akun</td>
                                        <td>
                                            @if ($roleItem->name === 'superadmin')
                                                <span
                                                    class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                    Semua Hak Akses (Penuh)
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                    {{ $roleItem->permissions->count() }} Hak Akses
                                                </span>
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
                                                        href="{{ route('admin.role.edit', $roleItem->id) }}"
                                                        wire:navigate>
                                                        <i class="mdi mdi-pencil me-1"></i> Edit
                                                    </a>
                                                    @if (!in_array(strtolower($roleItem->name), ['superadmin', 'admin', 'staff']))
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                            wire:click="confirmDelete({{ $roleItem->id }})">
                                                            <i class="mdi mdi-trash-can me-1"></i> Hapus
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-shield-off font-size-24 d-block mb-1"></i>
                                            Tidak ada data role yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($roles->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <small class="text-muted">
                                Menampilkan {{ $roles->firstItem() }} - {{ $roles->lastItem() }} dari
                                {{ $roles->total() }} data
                            </small>
                            <div>
                                {{ $roles->links() }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Role -->
    <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-hidden="true" wire:ignore.self
        x-data="{ modal: null }" x-init="modal = new bootstrap.Modal($el)" @open-delete-role-modal.window="modal.show()"
        @close-delete-role-modal.window="modal.hide()">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-3">
                <div class="modal-body">
                    <div class="text-danger mb-2">
                        <i class="mdi mdi-alert-circle-outline" style="font-size: 54px;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Hapus Role?</h5>
                    <p class="text-muted fs-13 mb-3">
                        Apakah Anda yakin ingin menghapus role <strong>{{ $roleToDeleteName }}</strong>?
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
