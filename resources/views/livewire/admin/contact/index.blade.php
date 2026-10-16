<div>
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h4 class="page-title mb-0">{{ $pageTitle }}</h4>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a wire:navigate href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Category Summary Cards -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card mb-3 mb-md-0 shadow-sm border-0 cursor-pointer {{ $filterCategory === 'all' ? 'border border-primary' : '' }}"
                wire:click="$set('filterCategory', 'all')" style="cursor: pointer;">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded bg-light-primary text-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.1);">
                        <i class="mdi mdi-account-group font-size-24 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fs-12 mb-1">Semua Kontak</h6>
                        <h4 class="mb-0 fw-bold">{{ $countTotal }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3 mb-md-0 shadow-sm border-0 cursor-pointer {{ $filterCategory === 'pemerintah' ? 'border border-info' : '' }}"
                wire:click="$set('filterCategory', 'pemerintah')" style="cursor: pointer;">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded text-info d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(6, 182, 212, 0.1);">
                        <i class="mdi mdi-city font-size-24 text-info"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fs-12 mb-1">Pemerintah</h6>
                        <h4 class="mb-0 fw-bold text-info">{{ $countPemerintah }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 cursor-pointer {{ $filterCategory === 'swasta' ? 'border border-warning' : '' }}"
                wire:click="$set('filterCategory', 'swasta')" style="cursor: pointer;">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm rounded text-warning d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.1);">
                        <i class="mdi mdi-domain font-size-24 text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fs-12 mb-1">Swasta / Perusahaan</h6>
                        <h4 class="mb-0 fw-bold text-warning">{{ $countSwasta }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-primary" wire:click="openCreateModal">
                                <i class="mdi mdi-plus me-1"></i> Tambah Kontak
                            </button>
                            <a wire:navigate href="{{ route('admin.campaign.create') }}" class="btn btn-outline-success">
                                <i class="mdi mdi-email-fast-outline me-1"></i> Buat Email Blast
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group" style="max-width: 280px;">
                                <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control border-start-0" placeholder="Cari nama, email, pemda..."
                                    wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Nama & Jabatan</th>
                                    <th>Email & Telepon</th>
                                    <th>Instansi / Perusahaan</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th class="text-end" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($contacts as $index => $contact)
                                    <tr>
                                        <td>{{ $contacts->firstItem() + $index }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $contact->name }}</div>
                                            <small class="text-muted">{{ $contact->position ?? 'Staff / PIC' }}</small>
                                        </td>
                                        <td>
                                            <div><a href="mailto:{{ $contact->email }}" class="text-primary">{{ $contact->email }}</a></div>
                                            <small class="text-muted">{{ $contact->phone ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $contact->organization_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $contact->category->badgeClass() }} rounded-pill px-2 py-1">
                                                {{ $contact->category->label() }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm {{ $contact->is_active ? 'btn-soft-success' : 'btn-soft-secondary' }} py-0 px-2"
                                                wire:click="toggleActive('{{ $contact->id }}')" title="Klik untuk ubah status">
                                                {{ $contact->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-info me-1" wire:click="edit('{{ $contact->id }}')" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirm('Yakin ingin menghapus kontak ini?') || event.stopImmediatePropagation()"
                                                wire:click="delete('{{ $contact->id }}')" title="Hapus">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-account-off-outline font-size-36 d-block mb-1"></i>
                                            Belum ada data kontak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $contacts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create / Edit Contact -->
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">
                            {{ $contactId ? 'Edit Kontak' : 'Tambah Kontak Baru' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kategori Kontak <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="pemerintah" id="catPemda" wire:model="category">
                                        <label class="form-check-label fw-semibold text-info" for="catPemda">
                                            <i class="mdi mdi-city me-1"></i> Pemerintah
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="swasta" id="catSwasta" wire:model="category">
                                        <label class="form-check-label fw-semibold text-warning" for="catSwasta">
                                            <i class="mdi mdi-domain me-1"></i> Swasta
                                        </label>
                                    </div>
                                </div>
                                @error('category') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Kontak (PIC) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name"
                                    placeholder="Contoh: Budi Santoso, S.Kom">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Instansi / Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('organization_name') is-invalid @enderror" wire:model="organization_name"
                                    placeholder="Contoh: Bappeda Prov. Jawa Barat / PT Solusi Digital">
                                @error('organization_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email"
                                        placeholder="nama@instansi.go.id">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Jabatan</label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror" wire:model="position"
                                        placeholder="Contoh: Kepala Dinas">
                                    @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">No. Telepon / WhatsApp</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone"
                                    placeholder="Contoh: 081234567890">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isActiveSwitch" wire:model="is_active">
                                <label class="form-check-label" for="isActiveSwitch">Kontak Aktif (Bisa menerima email)</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

