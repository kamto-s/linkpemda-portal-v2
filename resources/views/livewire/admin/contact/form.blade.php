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
                        <li class="breadcrumb-item"><a href="{{ route('admin.contact.index') }}" wire:navigate>Kontak Target</a></li>
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
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <h4 class="header-title">
                                {{ $isEditing ? 'Form Ubah Kontak' : 'Form Tambah Kontak Baru' }}
                            </h4>
                            <p class="sub-header">
                                Lengkapi profil dan detail kontak penerima sasaran promosi email blast.
                            </p>
                        </div>

                        <!-- Kategori Kontak -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori Sasaran <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="pemerintah" id="catPemda" wire:model="category">
                                    <label class="form-check-label fw-semibold text-info" for="catPemda">
                                        <i class="mdi mdi-city me-1"></i> Pemerintah (OPD / Dinas / Badan)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="swasta" id="catSwasta" wire:model="category">
                                    <label class="form-check-label fw-semibold text-warning" for="catSwasta">
                                        <i class="mdi mdi-domain me-1"></i> Swasta / Perusahaan / Komunitas
                                    </label>
                                </div>
                            </div>
                            @error('category') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                        </div>

                        <!-- Nama Kontak (PIC) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Kontak (PIC) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: Budi Santoso, S.Kom" wire:model="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Nama Instansi / Perusahaan -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Instansi / Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('organization_name') is-invalid @enderror"
                                placeholder="Contoh: Bappeda Prov. Jawa Barat / PT Solusi Digital Mandiri" wire:model="organization_name">
                            @error('organization_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3">
                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Alamat Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    placeholder="nama@instansi.go.id" wire:model="email">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Jabatan -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jabatan / Posisi</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                    placeholder="Contoh: Kepala Dinas / Kabid Anggaran" wire:model="position">
                                @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Telepon / WhatsApp -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Telepon / WhatsApp</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                placeholder="Contoh: 081234567890" wire:model="phone">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Gunakan nomor aktif untuk kemudahan koordinasi.</small>
                        </div>

                        <!-- Status Kontak -->
                        <div class="mb-4">
                            <label class="form-label fw-bold d-block">Status Kontak</label>
                            <div class="form-check form-switch form-switch-md">
                                <input class="form-check-input" type="checkbox" role="switch" id="contactIsActive" wire:model="is_active">
                                <label class="form-check-label fw-semibold ms-1" for="contactIsActive">
                                    <span class="badge {{ $is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                                        {{ $is_active ? 'Aktif (Dapat menerima email blast)' : 'Nonaktif (Diabaikan saat blast)' }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('admin.contact.index') }}" wire:navigate class="btn btn-light border">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-3" wire:loading.attr="disabled">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                <i class="mdi mdi-content-save me-1" wire:loading.remove wire:target="save"></i>
                                {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Kontak' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="mdi mdi-information-outline text-info me-1"></i> Informasi Kontak</h5>
                    <ul class="text-muted fs-13 ps-3 mb-0" style="line-height: 1.7;">
                        <li>Kontak yang berstatus <strong>Aktif</strong> akan otomatis muncul sebagai penerima sasaran pada wizard pengiriman <em>Email Blast</em>.</li>
                        <li>Pastikan alamat email valid dan aktif agar reputasi pengiriman server email tetap terjaga.</li>
                        <li>Kategori <strong>Pemerintah</strong> atau <strong>Swasta</strong> membantu Anda memfilter target sasaran promosi secara spesifik.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

