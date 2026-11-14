<div>
    <!-- Page Header & Breadcrumb -->
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h4 class="page-title mb-0 fw-bold">{{ $pageTitle }}</h4>
                <p class="text-muted fs-13 mb-0">Pencatatan presensi kehadiran peserta</p>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Administrasi</li>
                        <li class="breadcrumb-item">Linkpemda</li>
                        <li class="breadcrumb-item active">Absensi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="mdi mdi-check-circle me-1 fs-16"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($this->eventList->isEmpty())
        <!-- KONDISI KOSONG: TIDAK ADA KEGIATAN AKTIF SAAT INI -->
        <div class="card border-0 text-center py-5 my-3" style="border-radius: 14px;">
            <div class="card-body p-4 p-md-5">
                <div class="mb-3">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px; background-color: rgba(0, 143, 131, 0.1); color: #008F83;">
                        <i class="mdi mdi-calendar-check fs-40"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-dark mb-2">Tidak Ada Kegiatan Aktif Saat Ini</h4>
                <p class="text-muted fs-14 mx-auto mb-4" style="max-width: 520px; line-height: 1.6;">
                    Semua kegiatan bimtek sebelumnya telah selesai. Halaman absensi ini secara otomatis hanya menampilkan kegiatan yang sedang berlangsung atau yang akan datang agar panitia di lapangan tidak terganggu dengan kegiatan masa lalu.
                </p>
                <!-- <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="{{ route('admin.participant.linkpemda.create') }}" wire:navigate class="btn btn-success px-4 py-2 fw-semibold">
                        <i class="mdi mdi-plus-circle me-1"></i> Tambah Peserta Kegiatan Baru
                    </a>
                    <a href="{{ route('admin.participant.linkpemda.index') }}" wire:navigate class="btn btn-outline-secondary px-3 py-2 fw-semibold">
                        <i class="mdi mdi-account-group me-1"></i> Buka Data Seluruh Peserta
                    </a>
                </div> -->
            </div>
        </div>
    @else
        <!-- KONDISI ADA KEGIATAN AKTIF / MENDATANG -->

        <!-- KARTU 1: PANEL KONTROL KEGIATAN & SESI (RINGKAS & BERSIH) -->
        <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-3 p-md-4">
                <div class="row g-3 align-items-center">
                    <!-- Dropdown Kegiatan Aktif -->
                    <div class="col-lg-7">
                        <label class="form-label fw-bold text-muted fs-11 text-uppercase mb-1 tracking-wide">
                            <i class="mdi mdi-bookmark-check me-1 text-success"></i> PILIH KEGIATAN BIMTEK AKTIF:
                        </label>
                        <select class="form-select form-select-lg fs-14 fw-bold border-2 border-primary-subtle text-dark"
                            wire:model.live="selectedTheme">
                            @foreach ($this->eventList as $event)
                                <option value="{{ $event->theme }}">
                                    {{ $event->is_today ? '🟢 [HARI INI] ' : '🔵 [MENDATANG] ' }}
                                    {{ \Illuminate\Support\Str::limit($event->theme, 55) }}
                                    {{ $event->certificate_date ? ' (' . $event->certificate_date . ')' : '' }}
                                    — [{{ $event->total_participants }} Peserta]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tombol Aksi Cepat Panitia (Besar, Jelas, & Kontras) -->
                    <div class="col-lg-5 text-lg-end">
                        <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                            <button type="button" class="btn btn-success btn-lg px-4 py-2 fw-bold shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#modalScannerQr"
                                style="background-color: #008F83; border-color: #008F83;">
                                <i class="mdi mdi-qrcode-scan me-2 fs-18"></i> SCAN QR CODE
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg px-3 py-2 fw-semibold"
                                wire:click="downloadPdf" wire:loading.attr="disabled"
                                title="Unduh berkas PDF daftar hadir resmi Linkpemda">
                                <span wire:loading.remove wire:target="downloadPdf">
                                    <i class="mdi mdi-file-pdf-box me-1 text-danger fs-18"></i> Unduh PDF
                                </span>
                                <span wire:loading wire:target="downloadPdf">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Menyiapkan...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tab Sesi Hari (Registrasi, Hari 1, Hari 2, dst) -->
                @if (!empty($this->sessions))
                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                <span class="text-muted fs-11 fw-bold me-2 text-uppercase">PILIH SESI:</span>
                                @foreach ($this->sessions as $sessKey => $s)
                                    <button type="button"
                                        class="btn btn-sm px-3 py-1 fw-bold rounded-pill {{ $activeSession === $sessKey ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }}"
                                        style="{{ $activeSession === $sessKey ? 'background-color: #008F83; border-color: #008F83;' : '' }}"
                                        wire:click="$set('activeSession', '{{ $sessKey }}')">
                                        {{ $s['label'] }}
                                        @if (!empty($s['sub']))
                                            <small class="fw-normal opacity-75 ms-1">{{ $s['sub'] }}</small>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                            <div class="text-muted fs-12">
                                <i class="mdi mdi-clock-outline me-1"></i> Waktu Server: <span class="fw-semibold text-dark">{{ now()->translatedFormat('d M Y, H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- KARTU 2: RINGKASAN STATISTIK KOMPAK (1 BARIS, TIDAK MAKAN TEMPAT) -->
        <div class="card mb-3 border-0 shadow-sm" style="border-radius: 10px;">
            <div class="card-body py-2 px-3">
                <div class="row text-center g-2 align-items-center">
                    <div class="col-6 col-md-3 border-end-md">
                        <div class="py-1">
                            <span class="text-muted fs-12 d-block">Total Peserta Terdaftar</span>
                            <h3 class="mb-0 fw-bold text-dark fs-20">{{ number_format($totalEvent) }}</h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 border-end-md">
                        <div class="py-1">
                            <span class="text-success fs-12 fw-semibold d-block">
                                <i class="mdi mdi-check-circle me-1"></i> Sudah Hadir
                            </span>
                            <h3 class="mb-0 fw-bold text-success fs-20">{{ number_format($totalHadir) }}</h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 border-end-md">
                        <div class="py-1">
                            <span class="text-danger fs-12 fw-semibold d-block">
                                <i class="mdi mdi-close-circle me-1"></i> Belum Hadir
                            </span>
                            <h3 class="mb-0 fw-bold text-danger fs-20">{{ number_format($totalBelum) }}</h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="py-1">
                            <span class="text-warning fs-12 fw-semibold d-block">
                                <i class="mdi mdi-information me-1"></i> Izin / Sakit
                            </span>
                            <h3 class="mb-0 fw-bold text-warning fs-20">{{ number_format($totalIzin) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KARTU 3: TABEL DAFTAR PESERTA & AKSI LAPANGAN -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-3">
                <!-- Baris Filter & Pencarian Cepat -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                            <input type="text" class="form-control border-start-0"
                                placeholder="Ketik nama peserta atau instansi..."
                                wire:model.live.debounce.300ms="search">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="all">Semua Status Kehadiran</option>
                            <option value="belum_hadir">🔴 Khusus Belum Hadir</option>
                            <option value="hadir">🟢 Khusus Sudah Hadir</option>
                            <option value="izin">🟡 Izin</option>
                            <option value="sakit">🔴 Sakit</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" wire:model.live="perPage">
                            <option value="15">15 per halaman</option>
                            <option value="30">30 per halaman</option>
                            <option value="50">50 per halaman</option>
                            <option value="100">100 per halaman</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-md-end">
                        <span class="text-muted fs-12">Menampilkan <b>{{ $participants->total() }}</b> orang</span>
                    </div>
                </div>

                <!-- Bar Aksi Masal (Bulk Check-in) -->
                @if (count($selectedParticipants) > 0)
                    <div class="alert alert-primary d-flex align-items-center justify-content-between mb-3 py-2 px-3 border-0 bg-primary-subtle text-primary-emphasis flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary fs-12 rounded-pill px-2 py-1">{{ count($selectedParticipants) }}</span>
                            <span class="fw-semibold">Peserta terpilih</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-primary fw-semibold" wire:click="bulkCheckIn">
                                <i class="mdi mdi-check-all me-1"></i> Tandai Check-in Masal ({{ count($selectedParticipants) }})
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Tabel Daftar Peserta & Kehadiran -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" wire:model.live="selectAll">
                                </th>
                                <th style="width: 45px;">No</th>
                                <th>Peserta &amp; Instansi</th>
                                <th class="text-center" style="width: 140px;">Status</th>
                                <th class="text-center" style="width: 150px;">Jam Presensi</th>
                                <th class="text-center" style="width: 160px;">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($participants as $index => $participant)
                                @php
                                    $att = $participant->attendances->first();
                                    $isHadir = $att && $att->status === 'hadir';
                                    $hasCheckIn = $att && !empty($att->check_in_at);
                                    $hasCheckOut = $att && !empty($att->check_out_at);
                                @endphp
                                <tr class="{{ $isHadir ? 'table-success-subtle' : '' }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input" value="{{ $participant->id }}"
                                            wire:model.live="selectedParticipants">
                                    </td>
                                    <td>{{ $participants->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold text-dark fs-14">{{ $participant->name }}</div>
                                        <div class="text-muted fs-12">
                                            {{ $participant->institution_name ?: '-' }}
                                            @if ($participant->position)
                                                • <span class="opacity-75">{{ $participant->position }}</span>
                                            @endif
                                        </div>
                                        <div class="text-muted fs-11 font-monospace mt-1">
                                            <i class="mdi mdi-ticket-outline me-1 text-primary"></i>{{ $participant->certificate_number ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($isHadir)
                                            <span class="badge bg-success-subtle text-success fs-12 px-2 py-1">
                                                <i class="mdi mdi-check-circle me-1"></i> Hadir
                                            </span>
                                        @elseif ($att && $att->status === 'izin')
                                            <span class="badge bg-warning-subtle text-warning fs-12 px-2 py-1">
                                                <i class="mdi mdi-information me-1"></i> Izin
                                            </span>
                                        @elseif ($att && $att->status === 'sakit')
                                            <span class="badge bg-danger-subtle text-danger fs-12 px-2 py-1">
                                                <i class="mdi mdi-hospital-box me-1"></i> Sakit
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border fs-12 px-2 py-1">
                                                Belum Hadir
                                            </span>
                                        @endif

                                        @if ($att && !empty($att->notes))
                                            <div class="text-muted fs-11 mt-1 fst-italic" title="{{ $att->notes }}">
                                                "{{ \Illuminate\Support\Str::limit($att->notes, 20) }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($hasCheckIn)
                                            <div class="fs-12 text-success fw-semibold">
                                                <i class="mdi mdi-login me-1"></i> In: {{ $att->check_in_at->format('H:i') }}
                                            </div>
                                        @endif
                                        @if ($hasCheckOut)
                                            <div class="fs-12 text-primary fw-semibold">
                                                <i class="mdi mdi-logout me-1"></i> Out: {{ $att->check_out_at->format('H:i') }}
                                            </div>
                                        @elseif ($hasCheckIn)
                                            <div class="fs-11 text-muted">Out: --:--</div>
                                        @else
                                            <span class="text-muted fs-12">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @if (!$hasCheckIn)
                                                <button type="button" class="btn btn-sm btn-success fw-bold px-3 shadow-sm"
                                                    wire:click="checkIn('{{ $participant->id }}')"
                                                    title="Check-in Kehadiran">
                                                    <i class="mdi mdi-check me-1"></i> Check-in
                                                </button>
                                            @elseif (!$hasCheckOut)
                                                <button type="button" class="btn btn-sm btn-primary fw-bold px-3 shadow-sm"
                                                    wire:click="checkOut('{{ $participant->id }}')"
                                                    title="Check-out Selesai">
                                                    <i class="mdi mdi-logout me-1"></i> Check-out
                                                </button>
                                            @else
                                                <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2 fs-12">
                                                    <i class="mdi mdi-check-all me-1"></i> Lengkap
                                                </span>
                                            @endif

                                            <div class="dropdown d-inline-block">
                                                <button type="button" class="btn btn-sm btn-light border px-2"
                                                    data-bs-toggle="dropdown" aria-expanded="false" title="Pilihan Lain">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm fs-12">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                            wire:click="openStatusModal('{{ $participant->id }}')">
                                                            <i class="mdi mdi-pencil me-2 text-primary"></i> Ubah Status (Izin/Sakit)...
                                                        </a>
                                                    </li>
                                                    @if ($att)
                                                        <li><hr class="dropdown-divider my-1"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                                wire:click="resetAttendance('{{ $participant->id }}')"
                                                                onclick="return confirm('Batalkan presensi kehadiran peserta ini?')">
                                                                <i class="mdi mdi-undo me-2"></i> Batalkan / Reset Presensi
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="mdi mdi-account-search-outline text-muted fs-36 d-block mb-2"></i>
                                        <p class="text-muted mb-0">Tidak ada peserta yang ditemukan untuk filter ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($participants->hasPages())
                    <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                        <span class="text-muted fs-12">
                            Menampilkan {{ $participants->firstItem() ?? 0 }} - {{ $participants->lastItem() ?? 0 }} dari {{ $participants->total() }} peserta
                        </span>
                        <div>
                            {{ $participants->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- MODAL 1: SCANNER QR CODE (KAMERA & USB SCANNER) -->
    <div class="modal fade" id="modalScannerQr" tabindex="-1" aria-labelledby="modalScannerQrLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white py-2" style="background-color: #008F83 !important;">
                    <h5 class="modal-title fs-15 fw-bold" id="modalScannerQrLabel">
                        <i class="mdi mdi-qrcode-scan me-1"></i> Scan QR Code - {{ $sessions[$activeSession]['label'] ?? 'Presensi' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="btnCloseScanner"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <!-- Area Kamera Video -->
                    <div id="reader" style="width: 100%; min-height: 250px; background: #000; border-radius: 12px; overflow: hidden;" class="mb-3">
                        <div class="d-flex align-items-center justify-content-center h-100 text-white-50 p-4">
                            <span><i class="mdi mdi-camera fs-24 d-block mb-1"></i> Memuat kamera scanner...</span>
                        </div>
                    </div>

                    <!-- Notifikasi Hasil Scan Real-Time -->
                    <div id="scanFeedback" class="alert alert-info py-2 px-3 fs-13 mb-3 d-none">
                        <span id="scanFeedbackText">Arahkan QR Code peserta ke kamera</span>
                    </div>

                    <!-- Input Barcode Scanner Gun Manual (Bisa juga diketik) -->
                    <div class="mt-2 text-start">
                        <label class="form-label fs-12 text-muted fw-bold mb-1">
                            <i class="mdi mdi-barcode-scan me-1"></i> Atau scan pakai Barcode Gun USB / Ketik Nomor:
                        </label>
                        <div class="input-group">
                            <input type="text" id="manualBarcodeInput" class="form-control"
                                placeholder="Klik disini &amp; scan dengan barcode gun...">
                            <button type="button" class="btn btn-primary" id="btnSubmitManualBarcode">
                                Proses
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <small class="text-muted me-auto fs-11">
                        <i class="mdi mdi-information-outline me-1"></i> Scan pertama otomatis Check-in, scan kedua Check-out.
                    </small>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: EDIT STATUS KEHADIRAN MANUAL -->
    <div class="modal fade" id="modalEditAttendanceStatus" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header py-2">
                    <h5 class="modal-title fs-14 fw-bold">Ubah Status Presensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <p class="text-muted fs-12 mb-3">Peserta: <strong class="text-dark">{{ $modalParticipantName }}</strong></p>

                    <div class="mb-3">
                        <label class="form-label fs-12 fw-bold text-muted">Status Kehadiran:</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="hadir" id="stHadir" wire:model="modalStatus">
                                <label class="form-check-label text-success fw-semibold" for="stHadir">Hadir</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="izin" id="stIzin" wire:model="modalStatus">
                                <label class="form-check-label text-warning fw-semibold" for="stIzin">Izin</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="sakit" id="stSakit" wire:model="modalStatus">
                                <label class="form-check-label text-danger fw-semibold" for="stSakit">Sakit</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="alpha" id="stAlpha" wire:model="modalStatus">
                                <label class="form-check-label text-muted fw-semibold" for="stAlpha">Tanpa Keterangan</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fs-12 fw-bold text-muted">Catatan (Opsional):</label>
                        <textarea class="form-control fs-12" rows="2" wire:model="modalNotes" placeholder="Misal: Izin rapat kedinasan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary btn-sm" wire:click="saveStatusModal">Simpan Status</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Library HTML5-QRCode untuk Camera Scanning -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let html5QrCode = null;
        const feedbackDiv = document.getElementById('scanFeedback');
        const feedbackText = document.getElementById('scanFeedbackText');
        const manualInput = document.getElementById('manualBarcodeInput');
        const btnSubmitManual = document.getElementById('btnSubmitManualBarcode');
        const modalElement = document.getElementById('modalScannerQr');

        // Suara Beep Sukses
        function playSuccessBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime); // 880 Hz
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.25);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.25);
            } catch (e) {}
        }

        // Suara Buzzer Gagal
        function playErrorBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, ctx.currentTime); // 220 Hz low buzz
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.4);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.4);
            } catch (e) {}
        }

        // Ketika modal scanner dibuka
        modalElement.addEventListener('shown.bs.modal', function () {
            manualInput.focus();

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            const config = { fps: 10, qrbox: { width: 220, height: 220 } };

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    if (decodedText) {
                        feedbackDiv.classList.remove('d-none', 'alert-danger', 'alert-info');
                        feedbackDiv.classList.add('alert-success');
                        feedbackText.innerText = "Memproses scan: " + decodedText;

                        @this.scanCode(decodedText);
                    }
                },
                (errorMessage) => {}
            ).catch((err) => {
                feedbackDiv.classList.remove('d-none', 'alert-info');
                feedbackDiv.classList.add('alert-warning');
                feedbackText.innerText = "Kamera tidak dapat diakses atau diblokir. Anda tetap bisa menggunakan Barcode Gun / Input Manual di bawah.";
            });
        });

        // Ketika modal scanner ditutup
        modalElement.addEventListener('hidden.bs.modal', function () {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                }).catch(() => {});
            }
        });

        // Event listener hasil scan sukses dari Livewire
        window.addEventListener('scan-result-success', event => {
            playSuccessBeep();
            const data = event.detail[0] || event.detail;
            feedbackDiv.classList.remove('d-none', 'alert-danger', 'alert-info');
            feedbackDiv.classList.add('alert-success');
            feedbackText.innerHTML = `<strong>${data.message}</strong> (${data.institution})`;
        });

        // Event listener hasil scan gagal dari Livewire
        window.addEventListener('scan-result-error', event => {
            playErrorBeep();
            const data = event.detail[0] || event.detail;
            feedbackDiv.classList.remove('d-none', 'alert-success', 'alert-info');
            feedbackDiv.classList.add('alert-danger');
            feedbackText.innerHTML = `<strong>Gagal:</strong> ${data.message}`;
        });

        // Input manual barcode scanner gun (enter key)
        manualInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                processManualInput();
            }
        });

        btnSubmitManual.addEventListener('click', function () {
            processManualInput();
        });

        function processManualInput() {
            const val = manualInput.value.trim();
            if (val) {
                @this.scanCode(val);
                manualInput.value = '';
                manualInput.focus();
            }
        }

        // Livewire modal open / close handler
        window.addEventListener('open-modal', event => {
            const modalId = event.detail.modal;
            const m = bootstrap.Modal.getInstance(document.getElementById(modalId)) || new bootstrap.Modal(document.getElementById(modalId));
            m.show();
        });

        window.addEventListener('close-modal', event => {
            const modalId = event.detail.modal;
            const m = bootstrap.Modal.getInstance(document.getElementById(modalId));
            if (m) m.hide();
        });
    });
</script>
@endpush
