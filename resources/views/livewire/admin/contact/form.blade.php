<div>
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.contact.index') }}" wire:navigate class="btn btn-sm btn-light border">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <h4 class="page-title mb-0">{{ $pageTitle }}</h4>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.contact.index') }}" wire:navigate>Kontak
                            </a></li>
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
        <div class="col-lg-12">
            <div class="card">
                <form wire:submit="save">
                    <div class="card-header">
                        <h4 class="header-title">
                            {{ $isEditing ? 'Form Ubah Kontak' : 'Form Tambah Kontak Baru' }}
                        </h4>
                        <p class="sub-header mb-0">
                            Lengkapi profil dan detail kontak email.
                        </p>
                    </div>

                    <div class="card-body">
                        <!-- Kategori Kontak -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="pemerintah" id="catPemda"
                                        wire:model="category">
                                    <label class="form-check-label fw-semibold text-info" for="catPemda">
                                        Pemerintah
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="swasta" id="catSwasta"
                                        wire:model="category">
                                    <label class="form-check-label fw-semibold text-warning" for="catSwasta">
                                        Swasta
                                    </label>
                                </div>
                            </div>
                            @error('category')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nama Kontak (PIC) <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jabatan -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nama Instansi / Perusahaan <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('organization_name') is-invalid @enderror"
                                    wire:model="organization_name">
                                @error('organization_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Alamat Email <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jabatan -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jabatan / Posisi</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                    wire:model="position">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nomor Telepon / WhatsApp</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    wire:model="phone">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold d-block">Status Kontak</label>
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" role="switch" id="contactIsActive"
                                        wire:model="is_active">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-muted">
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('admin.contact.index') }}" wire:navigate
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
                </form>
            </div>
        </div>
    </div>
</div>
