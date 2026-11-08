<div>
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.invoice.linkpemda.index') }}" wire:navigate
                        class="btn btn-sm btn-light border" title="Kembali ke Daftar">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <h4 class="page-title mb-0">
                        {{ $isEditing ? 'Ubah Invoice: ' . $invoice->invoice_number : 'Buat Invoice Linkpemda' }}
                    </h4>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.invoice.linkpemda.index') }}"
                                wire:navigate>Invoice Linkpemda</a></li>
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

    <!-- Card Utama Form Invoice -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="header-title mb-1">
                                {{ $isEditing ? 'Form Perubahan Data Invoice' : 'Form Pembuatan Dokumen Invoice Linkpemda' }}
                            </h4>
                            <p class="sub-header mb-0">
                                Lengkapi identitas dokumen, instansi klien, jadwal kegiatan, daftar peserta, dan rincian
                                biaya.
                            </p>
                        </div>
                        <a href="{{ route('admin.invoice.linkpemda.index') }}" wire:navigate
                            class="btn btn-sm btn-light border">
                            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>

                <div class="card-body p-3 p-lg-4">
                    <form wire:submit="save">
                        <div class="row g-3">
                            <!-- Card Kelompok 1: Data Dokumen Invoice -->
                            <div class="col-12">
                                <div class="card border shadow-none bg-light-subtle mb-0">
                                    <div class="card-header bg-transparent border-bottom py-2">
                                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                                            Informasi Dokumen Invoice
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">No. Invoice</label>
                                                <div
                                                    class="form-control bg-light d-flex align-items-center justify-content-between py-2">
                                                    <span
                                                        class="font-monospace fw-bold text-primary fs-14">{{ $invoice_number }}</span>
                                                    <span class="badge badge-soft-info fs-11">
                                                        <i class="mdi mdi-auto-fix me-1"></i>Otomatis Sistem
                                                    </span>
                                                </div>
                                                <small class="text-muted fs-11">Nomor berurutan otomatis:
                                                    <code>LP-[No. Urut]-[Romawi Bulan]-[Tahun]</code></small>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tanggal Invoice <span
                                                        class="text-danger">*</span></label>
                                                <input type="date"
                                                    class="form-control @error('invoice_date') is-invalid @enderror"
                                                    wire:model.live="invoice_date">
                                                @error('invoice_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Menu <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select @error('menu_type') is-invalid @enderror"
                                                    wire:model="menu_type">
                                                    <option value="Offline">Offline</option>
                                                    <option value="Online">Online</option>
                                                </select>
                                                @error('menu_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Metode Pembayaran <span
                                                        class="text-danger">*</span></label>
                                                <select
                                                    class="form-select @error('payment_method') is-invalid @enderror"
                                                    wire:model="payment_method">
                                                    <option value="Transfer / Cash">Transfer / Cash</option>
                                                    <option value="Transfer">Transfer</option>
                                                    <option value="Cash">Cash</option>
                                                </select>
                                                @error('payment_method')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Status Persetujuan (ACC) <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select @error('status') is-invalid @enderror"
                                                    wire:model="status">
                                                    <option value="pending">Menunggu ACC</option>
                                                    <option value="approved">Disetujui</option>
                                                    <option value="rejected">Ditolak</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Kelompok 2: Informasi Instansi / Klien -->
                            <div class="col-12">
                                <div class="card border shadow-none bg-light-subtle mb-0">
                                    <div class="card-header bg-transparent border-bottom py-2">
                                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                                            Informasi Instansi / Klien
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold">Nama Instansi / Perusahaan <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('company_name') is-invalid @enderror"
                                                    wire:model="company_name"
                                                    placeholder="Contoh: Sekretariat Daerah Kab. Brebes">
                                                @error('company_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Alamat Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="mdi mdi-email-outline"></i></span>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        wire:model="email"
                                                        placeholder="contoh: sekretariat@brebeskab.go.id">
                                                </div>
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted fs-11">Digunakan untuk pengiriman berkas
                                                    elektronik.</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">No. WhatsApp / Telepon</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="bx bxl-whatsapp"></i></span>
                                                    <input type="text"
                                                        class="form-control @error('phone') is-invalid @enderror"
                                                        wire:model="phone" placeholder="Opsional / No. Telp">
                                                </div>
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Alamat Instansi</label>
                                                <textarea class="form-control @error('address') is-invalid @enderror"
                                                    rows="2" wire:model="address"
                                                    placeholder="Contoh: Jawa Tengah / Jl. Proklamasi No. 10"></textarea>
                                                @error('address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Kelompok 3: Data Kegiatan Pelatihan / Bimtek -->
                            <div class="col-12">
                                <div class="card border shadow-none bg-light-subtle mb-0">
                                    <div class="card-header bg-transparent border-bottom py-2">
                                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                                            Data Kegiatan Pelatihan / Bimtek
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Tema / Judul Kegiatan <span
                                                        class="text-danger">*</span></label>
                                                <textarea
                                                    class="form-control @error('activity_theme') is-invalid @enderror"
                                                    rows="3" wire:model="activity_theme"
                                                    placeholder="Contoh: Keprotokolan, Komunikasi Pimpinan, dan Kehumasan Pemerintah Daerah"></textarea>
                                                @error('activity_theme')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tanggal Mulai <span
                                                        class="text-danger">*</span></label>
                                                <input type="date"
                                                    class="form-control @error('event_start_date') is-invalid @enderror"
                                                    wire:model.live="event_start_date">
                                                @error('event_start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Tanggal Selesai <small
                                                        class="text-muted">(Bila > 1 hari)</small></label>
                                                <input type="date"
                                                    class="form-control @error('event_end_date') is-invalid @enderror"
                                                    wire:model.live="event_end_date">
                                                @error('event_end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            @if (!empty($event_start_date))
                                                <div class="col-12">
                                                    <div
                                                        class="p-2 bg-white rounded border d-flex align-items-center justify-content-between">
                                                        <span class="fs-12 text-muted">Format Jadwal di Dokumen PDF:</span>
                                                        <span
                                                            class="badge badge-soft-primary fs-12">{{ $this->formatted_event_date_preview }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Waktu Kegiatan <span
                                                        class="text-danger">*</span></label>
                                                <input type="time"
                                                    class="form-control @error('event_time') is-invalid @enderror"
                                                    wire:model="event_time">
                                                @error('event_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Lokasi Acara <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('event_location') is-invalid @enderror"
                                                    wire:model="event_location"
                                                    placeholder="Contoh: Yello Hotel Harmoni Jakarta">
                                                @error('event_location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Kelompok 4: Daftar Nama Peserta (Repeater) -->
                            <div class="col-12">
                                <div class="card border shadow-none bg-light-subtle mb-0">
                                    <div
                                        class="card-header bg-transparent border-bottom py-2 d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                                            Daftar Peserta Bimtek
                                        </h5>
                                        <span class="badge badge-soft-info px-2 py-1">
                                            Total: {{ $participant_count }} Orang
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-column gap-2">
                                            @foreach ($participants as $index => $participant)
                                                <div class="d-flex align-items-center gap-2"
                                                    wire:key="participant-row-{{ $index }}">
                                                    <span class="badge bg-light text-dark border px-2 py-2 fs-12"
                                                        style="min-width: 32px;">
                                                        {{ $index + 1 }}.
                                                    </span>
                                                    <input type="text"
                                                        class="form-control form-control-sm @error('participants.' . $index) is-invalid @enderror"
                                                        wire:model.live.debounce.300ms="participants.{{ $index }}"
                                                        placeholder="Nama Lengkap & Gelar Peserta">
                                                    <button type="button" class="btn btn-outline-danger btn-sm px-2"
                                                        wire:click="removeParticipant({{ $index }})"
                                                        @if(count($participants) <= 1) disabled @endif
                                                        title="Hapus Baris Peserta">
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-3">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                wire:click="addParticipant">
                                                <i class="mdi mdi-plus-circle me-1"></i> Tambah Peserta Lainnya
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Kelompok 5: Rincian Biaya & Pembayaran -->
                            <div class="col-12">
                                <div class="card border shadow-none bg-light-subtle mb-0">
                                    <div class="card-header bg-transparent border-bottom py-2">
                                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                                            Rincian Biaya &amp; Pembayaran
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Harga per Peserta (Rp) <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group" x-data="{
                                                    formatRupiah(val) {
                                                        if (!val) return '';
                                                        let clean = val.toString().replace(/[^0-9]/g, '');
                                                        return clean ? clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
                                                    }
                                                }">
                                                    <span class="input-group-text fw-bold">Rp</span>
                                                    <input type="text" inputmode="numeric"
                                                        class="form-control @error('price_per_participant') is-invalid @enderror"
                                                        wire:model.live.debounce.150ms="price_per_participant"
                                                        x-on:input="$event.target.value = formatRupiah($event.target.value)"
                                                        placeholder="0">
                                                </div>
                                                @error('price_per_participant')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Deposit / Uang Muka (Rp)</label>
                                                <div class="input-group" x-data="{
                                                    formatRupiah(val) {
                                                        if (!val) return '';
                                                        let clean = val.toString().replace(/[^0-9]/g, '');
                                                        return clean ? clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
                                                    }
                                                }">
                                                    <span class="input-group-text fw-bold">Rp</span>
                                                    <input type="text" inputmode="numeric"
                                                        class="form-control @error('deposit_amount') is-invalid @enderror"
                                                        wire:model.live.debounce.150ms="deposit_amount"
                                                        x-on:input="$event.target.value = formatRupiah($event.target.value)"
                                                        placeholder="0">
                                                </div>
                                                @error('deposit_amount')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Status Pembayaran <span
                                                        class="text-danger">*</span></label>
                                                <select
                                                    class="form-select @error('payment_status') is-invalid @enderror"
                                                    wire:model="payment_status">
                                                    <option value="unpaid">Belum Lunas</option>
                                                    <option value="partial">DP (Sebagian)</option>
                                                    <option value="paid">Lunas</option>
                                                </select>
                                                @error('payment_status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <div class="p-3 bg-white rounded border">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="text-muted fs-13">Jumlah Peserta:</span>
                                                        <span class="fw-bold text-dark fs-14">{{ $participant_count }}
                                                            Orang</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="text-muted fs-13">Total Biaya:</span>
                                                        <span class="fw-bold text-primary fs-16">
                                                            Rp {{ number_format($total_amount, 0, ',', '.') }},-
                                                        </span>
                                                    </div>
                                                    <div class="pt-2 border-top">
                                                        <span class="text-muted fs-11 d-block">Terbilang:</span>
                                                        <span class="fst-italic text-dark fw-medium fs-12 d-block">
                                                            "{{ $this->terbilang_total }}"
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="p-3 bg-white rounded border">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="text-muted fs-13">Deposit (DP):</span>
                                                        <span class="fw-bold text-dark fs-14">
                                                            Rp
                                                            {{ number_format((float) preg_replace('/[^\d]/', '', (string) $deposit_amount), 0, ',', '.') }},-
                                                        </span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="text-danger fw-semibold fs-13">Sisa
                                                            Pembayaran:</span>
                                                        <span class="fw-bold text-danger fs-16">
                                                            Rp {{ number_format($remaining_amount, 0, ',', '.') }},-
                                                        </span>
                                                    </div>
                                                    <div class="pt-2 border-top">
                                                        <span class="text-muted fs-11 d-block">Terbilang Sisa:</span>
                                                        <span class="fst-italic text-dark fw-medium fs-12 d-block">
                                                            "{{ $this->terbilang_remaining }}"
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Kelompok 6: Catatan Tambahan (Opsional) -->
                            <div class="col-12">
                                <div class="card border shadow-none bg-light-subtle mb-0">
                                    <div class="card-header bg-transparent border-bottom py-2">
                                        <h5 class="card-title mb-0 fs-14 fw-bold text-dark py-2">
                                            Catatan Tambahan (Opsional)
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <textarea class="form-control" rows="3" wire:model="notes"
                                            placeholder="Catatan tambahan untuk arsip / tim internal..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Tombol Aksi Simpan -->
                        <div class="d-flex justify-content-end align-items-center gap-2 pt-3 mt-3 border-top">
                            <a href="{{ route('admin.invoice.linkpemda.index') }}" wire:navigate
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
</div>