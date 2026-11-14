<div>
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.participant.linkpemda.index') }}" wire:navigate
                        class="btn btn-sm btn-light border" title="Kembali ke Daftar">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <h4 class="page-title mb-0">
                        {{ $isEditing ? 'Ubah Data Peserta: ' . $participant->name : 'Tambah Data Peserta Linkpemda' }}
                    </h4>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.participant.linkpemda.index') }}"
                                wire:navigate>Data Peserta</a></li>
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
                                {{ $isEditing ? 'Form Perubahan Data Peserta' : 'Form Pendaftaran Peserta Linkpemda' }}
                            </h4>
                            <p class="sub-header mb-0">
                                Lengkapi data identitas peserta bimtek, rincian Sertifikat, dan dokumen kwitansi.
                            </p>
                        </div>
                        <a href="{{ route('admin.participant.linkpemda.index') }}" wire:navigate
                            class="btn btn-sm btn-light border">
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
                                                <label class="form-label fw-semibold">Nama Lengkap <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    wire:model="name">
                                                @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Jabatan</label>
                                                <input type="text"
                                                    class="form-control @error('position') is-invalid @enderror"
                                                    wire:model="position">
                                                @error('position')
                                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Alamat Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="mdi mdi-email-outline"></i></span>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        wire:model="email">
                                                </div>
                                                @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">No. WhatsApp / HP</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="bx bxl-whatsapp"></i></span>
                                                    <input type="text"
                                                        class="form-control @error('phone') is-invalid @enderror"
                                                        wire:model="phone">
                                                </div>
                                                @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Instansi / Perusahaan</label>
                                                <input type="text"
                                                    class="form-control @error('institution_name') is-invalid @enderror"
                                                    wire:model="institution_name">
                                                @error('institution_name')
                                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tanggal Registrasi</label>
                                                <input type="date"
                                                    class="form-control @error('registration_date') is-invalid @enderror"
                                                    wire:model="registration_date">
                                                @error('registration_date')
                                                <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <label class="form-label fw-semibold mb-0">Tema / Judul Kegiatan</label>
                                                    <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 fs-11"
                                                        data-bs-toggle="modal" data-bs-target="#modalSearchTheme">
                                                        <i class="mdi mdi-magnify me-1"></i> Cari &amp; Pilih dari Tema yang Ada
                                                    </button>
                                                </div>
                                                <textarea class="form-control @error('theme') is-invalid @enderror"
                                                    rows="3" wire:model="theme" placeholder="Ketik tema kegiatan pelatihan atau gunakan tombol 'Cari & Pilih' di atas untuk auto-fill jadwal & lokasi..."></textarea>
                                                @error('theme')
                                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <!-- Rentang Tanggal: Tanggal Mulai & Tanggal Selesai -->
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                                <input type="date"
                                                    class="form-control @error('event_start_date') is-invalid @enderror"
                                                    wire:model="event_start_date">
                                                @error('event_start_date')
                                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                                <input type="date"
                                                    class="form-control @error('event_end_date') is-invalid @enderror"
                                                    wire:model="event_end_date">
                                                @error('event_end_date')
                                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tempat / Gedung Acara</label>
                                                <input type="text"
                                                    class="form-control @error('event_venue') is-invalid @enderror"
                                                    wire:model="event_venue">
                                                @error('event_venue')
                                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Lokasi Acara</label>
                                                <input type="text"
                                                    class="form-control @error('event_location') is-invalid @enderror"
                                                    wire:model="event_location">
                                                @error('event_location')
                                                <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                                <input type="date"
                                                    class="form-control @error('receipt_date') is-invalid @enderror"
                                                    wire:model="receipt_date">
                                                @error('receipt_date')
                                                <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                                    <input type="text" inputmode="numeric"
                                                        class="form-control @error('amount') is-invalid @enderror"
                                                        wire:model.live.debounce.150ms="amount"
                                                        x-on:input="$event.target.value = formatRupiah($event.target.value)">
                                                </div>
                                                @error('amount')
                                                <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
                                        <textarea class="form-control" rows="3" wire:model="notes"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Tombol Aksi Simpan -->
                        <div class="d-flex justify-content-end align-items-center flex-wrap gap-2 pt-3 mt-3 border-top">
                            <a href="{{ route('admin.participant.linkpemda.index') }}" wire:navigate
                                class="btn btn-light border px-4">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="mdi mdi-content-save me-1"></i> {{ $isEditing ? 'Perbarui' : 'Simpan' }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pencarian & Pemilihan Tema yang Sudah Pernah Ada -->
    <div class="modal fade" id="modalSearchTheme" tabindex="-1" aria-labelledby="modalSearchThemeLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header bg-light border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-bookmark-check text-primary fs-20"></i>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="modalSearchThemeLabel">Pilih Tema Kegiatan Pelatihan</h5>
                            <small class="text-muted fs-12">Pilih tema yang pernah ada untuk auto-fill jadwal &amp; lokasi tanpa salah ketik</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-md-4">
                    <!-- Input Pencarian Realtime -->
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white"><i class="mdi mdi-magnify fs-16"></i></span>
                        <input type="text" class="form-control"
                            placeholder="Ketik kata kunci tema kegiatan, lokasi, gedung acara, atau tanggal..."
                            wire:model.live.debounce.250ms="themeSearchQuery" autofocus>
                        @if ($themeSearchQuery !== '')
                            <button class="btn btn-outline-secondary" type="button" wire:click="$set('themeSearchQuery', '')">
                                <i class="mdi mdi-close"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Daftar Hasil Pencarian Tema -->
                    <div class="list-group" style="max-height: 380px; overflow-y: auto;">
                        @forelse ($this->existingThemes as $ex)
                            <div class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1 fs-14" style="line-height: 1.4;">{{ $ex->theme }}</h6>
                                    <div class="d-flex align-items-center flex-wrap gap-2 text-muted fs-12 mt-1">
                                        <span>
                                            <i class="mdi mdi-calendar-range text-primary me-1"></i> {{ $ex->certificate_date ?: '-' }}
                                        </span>
                                        @if ($ex->event_venue || $ex->event_location)
                                            <span>&bull;</span>
                                            <span>
                                                <i class="mdi mdi-map-marker text-danger me-1"></i>
                                                {{ $ex->event_venue }}{{ $ex->event_location ? ', ' . $ex->event_location : '' }}
                                            </span>
                                        @endif
                                        <span>&bull;</span>
                                        <span class="badge bg-light text-dark border">
                                            {{ $ex->total_participants }} Peserta
                                        </span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary fw-semibold flex-shrink-0 px-3"
                                    wire:click="selectExistingTheme('{{ addslashes($ex->theme) }}')">
                                    <i class="mdi mdi-check me-1"></i> Gunakan
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="mdi mdi-magnify-close fs-36 text-secondary mb-1"></i>
                                <div class="fw-semibold">Tidak ada tema yang cocok dengan pencarian</div>
                                <small class="text-muted">Anda bisa menutup modal ini dan mengetik tema baru secara manual.</small>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.addEventListener('close-modal', event => {
            const modalId = event.detail.modal;
            const el = document.getElementById(modalId);
            if (el) {
                const m = bootstrap.Modal.getInstance(el);
                if (m) m.hide();
            }
        });
    });
</script>
@endpush