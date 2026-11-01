<div>
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.participant.linkpemda.index') }}" wire:navigate class="btn btn-sm btn-light border" title="Kembali">
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

    <!-- Error Validation Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-alert-circle me-2 fs-18"></i>
                <div>
                    <strong>Terdapat kesalahan input:</strong>
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

    <form wire:submit="save">
        <div class="row g-3">
            <!-- Kolom Kiri: Data Peserta & Pelatihan -->
            <div class="col-lg-8">
                <!-- Bagian 1: Data Pribadi Peserta -->
                <div class="card mb-3">
                    <div class="card-header bg-light-subtle border-bottom py-2">
                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark">
                            <i class="bx bx-user me-1 text-primary"></i> 1. Informasi Peserta &amp; Instansi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Nama Lengkap Peserta <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Contoh: Arbayana, S.Sos., M.Si" wire:model="name">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Alamat Email Peserta</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        placeholder="peserta@gmail.com" wire:model="email">
                                </div>
                                <small class="text-muted fs-11">Diperlukan untuk mengirimkan E-Sertifikat secara digital.</small>
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No. WhatsApp / HP</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bxl-whatsapp"></i></span>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="081234567890" wire:model="phone">
                                </div>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jabatan Peserta</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror"
                                    placeholder="Contoh: Kasi Kesejahteraan Desa Kartamulia" wire:model="position">
                                @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Instansi / OPD</label>
                                <input type="text" class="form-control @error('institution_name') is-invalid @enderror"
                                    placeholder="Contoh: Kec. Sukamara Kab. Sukamara" wire:model="institution_name">
                                @error('institution_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Registrasi</label>
                                <input type="text" class="form-control @error('registration_date') is-invalid @enderror"
                                    placeholder="Contoh: 18 Februari 2025" wire:model="registration_date">
                                @error('registration_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Data E-Sertifikat & Kegiatan Bimtek -->
                <div class="card mb-3">
                    <div class="card-header bg-light-subtle border-bottom py-2">
                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark">
                            <i class="bx bx-medal me-1 text-primary"></i> 2. Data E-Sertifikat &amp; Kegiatan Bimtek
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">Nomor Sertifikat</label>
                                <div class="input-group">
                                    <input type="text" class="form-control font-monospace @error('certificate_number') is-invalid @enderror"
                                        placeholder="Contoh: LP-00170-II-2025" wire:model.live.debounce.300ms="certificate_number">
                                    <button class="btn btn-outline-primary" type="button" wire:click="generateNewCertificateNumber"
                                        title="Buat nomor otomatis berikutnya">
                                        <i class="mdi mdi-auto-fix me-1"></i> Otomatis
                                    </button>
                                </div>
                                <small class="text-muted fs-11">Pola nomor: LP-[00000]-[Romawi]-[Tahun]</small>
                                @error('certificate_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">File Gambar QR Code</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-qr"></i></span>
                                    <input type="text" class="form-control font-monospace @error('qr_code_path') is-invalid @enderror"
                                        placeholder="LP-00170-II-2025.png" wire:model="qr_code_path">
                                </div>
                                <small class="text-muted fs-11">Otomatis terisi [no_sertifikat].png</small>
                                @error('qr_code_path') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Tema / Judul Kegiatan Bimtek</label>
                                <textarea class="form-control @error('theme') is-invalid @enderror" rows="3"
                                    placeholder="Contoh: Transformasi Pelayanan Digital Kecamatan, Desa dan Kelurahan Berbasis SPBE Menuju Smart Governance"
                                    wire:model="theme"></textarea>
                                @error('theme') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Kegiatan / Sertifikat</label>
                                <input type="text" class="form-control @error('certificate_date') is-invalid @enderror"
                                    placeholder="Contoh: 19 - 20 Februari 2025" wire:model="certificate_date">
                                @error('certificate_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tempat / Gedung Acara</label>
                                <input type="text" class="form-control @error('event_venue') is-invalid @enderror"
                                    placeholder="Contoh: El Royale Hotel" wire:model="event_venue">
                                @error('event_venue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Kota Lokasi Acara</label>
                                <input type="text" class="form-control @error('event_location') is-invalid @enderror"
                                    placeholder="Contoh: Bandung" wire:model="event_location">
                                @error('event_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Kwitansi & Simpan -->
            <div class="col-lg-4">
                <!-- Bagian 3: Kwitansi Pembayaran -->
                <div class="card mb-3">
                    <div class="card-header bg-light-subtle border-bottom py-2">
                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark">
                            <i class="bx bx-receipt me-1 text-primary"></i> 3. Kwitansi Pembayaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Kwitansi</label>
                            <input type="text" class="form-control font-monospace @error('receipt_number') is-invalid @enderror"
                                placeholder="Contoh: KW-LP-2025-0170" wire:model="receipt_number">
                            @error('receipt_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Kwitansi</label>
                            <input type="text" class="form-control @error('receipt_date') is-invalid @enderror"
                                placeholder="Contoh: 18 s/d 21 Februari 2025" wire:model="receipt_date">
                            @error('receipt_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Biaya Kontribusi (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                    placeholder="5000000" wire:model="amount" min="0" step="1000">
                            </div>
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Bagian 4: Catatan Tambahan -->
                <div class="card mb-3">
                    <div class="card-header bg-light-subtle border-bottom py-2">
                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark">
                            <i class="bx bx-note me-1 text-primary"></i> Catatan Internal
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" rows="3" placeholder="Catatan internal sekretariat jika ada..." wire:model="notes"></textarea>
                    </div>
                </div>

                <!-- Tombol Aksi Simpan -->
                <div class="card">
                    <div class="card-body p-3 d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">
                                <i class="mdi mdi-content-save me-1"></i> {{ $isEditing ? 'Perbarui Peserta' : 'Simpan Peserta' }}
                            </span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...
                            </span>
                        </button>
                        <a href="{{ route('admin.participant.linkpemda.index') }}" wire:navigate class="btn btn-light border w-100">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

