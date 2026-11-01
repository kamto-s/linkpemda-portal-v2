<div>
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.participant.linkpemda.index') }}" wire:navigate class="btn btn-sm btn-light border" title="Kembali ke Daftar">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <h4 class="page-title mb-0">
                        {{ $isEditing ? 'Ubah Data Peserta: ' . $participant->name : 'Tambah Data Peserta Baru' }}
                    </h4>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.participant.linkpemda.index') }}" wire:navigate>Data Peserta</a></li>
                        <li class="breadcrumb-item active">{{ $isEditing ? 'Ubah' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Error Validasi -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-alert-circle me-2 fs-18"></i>
                <div>
                    <strong>Terdapat beberapa kesalahan pengisian form:</strong>
                    <ul class="mb-0 ps-3 mt-1 fs-13">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Card Utama Form Peserta -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="header-title mb-1">
                                {{ $isEditing ? 'Form Perubahan Data Peserta' : 'Form Pendaftaran Peserta Baru' }}
                            </h4>
                            <p class="sub-header mb-0">
                                Lengkapi data identitas peserta bimtek, rincian e-sertifikat, dan dokumen kwitansi.
                            </p>
                        </div>
                        <a href="{{ route('admin.participant.linkpemda.index') }}" wire:navigate class="btn btn-sm btn-light border">
                            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>

                <div class="card-body p-3 p-lg-4">
                    <form wire:submit="save">
                        <div class="row g-3">
                            <!-- Card Kelompok 1: Informasi Peserta & Instansi -->
                            <div class="col-12">
                                <div class="card border shadow-none bg-light-subtle mb-0">
                                    <div class="card-header bg-transparent border-bottom py-2">
                                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                                            Informasi Peserta
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Jabatan</label>
                                                <input type="text" class="form-control @error('position') is-invalid @enderror" wire:model="position">
                                                @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Alamat Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                                                </div>
                                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">No. WhatsApp / HP</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bx bxl-whatsapp"></i></span>
                                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone">
                                                </div>
                                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Instansi / Perusahaan</label>
                                                <input type="text" class="form-control @error('institution_name') is-invalid @enderror" wire:model="institution_name">
                                                @error('institution_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tanggal Registrasi</label>
                                                <input type="date" class="form-control @error('registration_date') is-invalid @enderror"
                                                    wire:model="registration_date">
                                                @error('registration_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Kelompok 2: Data Kegiatan Bimtek -->
                            <div class="col-12">
                                <div class="card border shadow-none bg-light-subtle mb-0">
                                    <div class="card-header bg-transparent border-bottom py-2">
                                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                                            Data Kegiatan Pelatihan
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold">Tema / Judul Kegiatan</label>
                                                <textarea class="form-control @error('theme') is-invalid @enderror" rows="3" wire:model="theme"></textarea>
                                                @error('theme') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Rentang Tanggal: Tanggal Mulai & Tanggal Selesai -->
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                                <input type="date" class="form-control @error('event_start_date') is-invalid @enderror"
                                                    wire:model="event_start_date">
                                                @error('event_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                                <input type="date" class="form-control @error('event_end_date') is-invalid @enderror"
                                                    wire:model="event_end_date">
                                                @error('event_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tempat / Gedung Acara</label>
                                                <input type="text" class="form-control @error('event_venue') is-invalid @enderror" wire:model="event_venue">
                                                @error('event_venue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Lokasi Acara</label>
                                                <input type="text" class="form-control @error('event_location') is-invalid @enderror" wire:model="event_location">
                                                @error('event_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Kelompok 3: Kwitansi Pembayaran & Biaya Kontribusi -->
                            <div class="col-12">
                                <div class="card border shadow-none bg-light-subtle mb-0">
                                    <div class="card-header bg-transparent border-bottom py-2">
                                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark">
                                           Kwitansi Pembayaran &amp; Biaya Kontribusi
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tanggal Kwitansi</label>
                                                <input type="date" class="form-control @error('receipt_date') is-invalid @enderror"
                                                    wire:model="receipt_date">
                                                @error('receipt_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Biaya Kontribusi (Rp)</label>
                                                <div class="input-group" x-data="{
                                                    formatRupiah(val) {
                                                        if (!val) return '';
                                                        let clean = val.toString().replace(/[^0-9]/g, '');
                                                        return clean ? clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
                                                    }
                                                }">
                                                    <span class="input-group-text fw-bold">Rp</span>
                                                    <input type="text"
                                                        inputmode="numeric"
                                                        class="form-control @error('amount') is-invalid @enderror"
                                                        wire:model.live.debounce.150ms="amount"
                                                        x-on:input="$event.target.value = formatRupiah($event.target.value)">
                                                </div>
                                                @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Kelompok 4: Catatan Tambahan (Opsional) -->
                            <div class="col-12">
                                <div class="card border shadow-none bg-light-subtle mb-0">
                                    <div class="card-header bg-transparent border-bottom py-2">
                                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark">
                                            Catatan Tambahan
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <textarea class="form-control" rows="3"
                                            wire:model="notes"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Tombol Aksi Simpan -->
                        <div class="d-flex justify-content-end align-items-center gap-2 pt-3 mt-3 border-top">
                            <a href="{{ route('admin.participant.linkpemda.index') }}" wire:navigate class="btn btn-light border px-4">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="mdi mdi-content-save me-1"></i> {{ $isEditing ? 'Perbarui' : 'Simpan' }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
