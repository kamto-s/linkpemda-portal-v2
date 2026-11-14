<div>
    <!-- Page Header & Breadcrumb -->
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h4 class="page-title mb-0 fw-bold">{{ $pageTitle }}</h4>
                <p class="text-muted fs-13 mb-0">Pencatatan presensi kehadiran peserta bimtek &amp; pemindaian barcode QR Code</p>
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
        <div class="card border-0 shadow-sm text-center py-5 my-3" style="border-radius: 14px;">
            <div class="card-body p-4 p-md-5">
                <div class="mb-3">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px; background-color: rgba(0, 143, 131, 0.1); color: #008F83;">
                        <i class="mdi mdi-calendar-check fs-40"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-dark mb-2">Tidak Ada Kegiatan Bimtek Aktif Saat Ini</h4>
                <p class="text-muted fs-14 mx-auto mb-4" style="max-width: 520px; line-height: 1.6;">
                    Semua kegiatan bimtek sebelumnya telah selesai. Halaman absensi ini secara otomatis hanya menampilkan kegiatan yang sedang berlangsung atau yang akan datang agar panitia di lapangan tidak terganggu oleh kegiatan masa lalu.
                </p>
            </div>
        </div>
    @elseif (empty($selectedTheme))
        <!-- ========================================================================= -->
        <!-- MODE 1: TABEL DAFTAR KEGIATAN PELATIHAN (PERSIS FORMAT /admin/users)       -->
        <!-- ========================================================================= -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h4 class="card-title mb-1 fw-bold text-dark">Daftar Kegiatan Pelatihan Bimtek</h4>
                                <p class="text-muted fs-13 mb-0">
                                    Pilih salah satu kegiatan di bawah untuk membuka lembar absensi &amp; pemindaian barcode QR Code peserta.
                                </p>
                            </div>
                            <span class="badge bg-light text-dark border px-2 py-1 fs-12">
                                Total: {{ $this->eventList->count() }} Kegiatan Aktif
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <!-- Search & Filter Controls -->
                        <div class="row g-2 mb-3 align-items-center">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="mdi mdi-magnify"></i></span>
                                    <input type="text" class="form-control"
                                        placeholder="Cari tema bimtek, lokasi, gedung acara..."
                                        wire:model.live.debounce.300ms="themeSearch">
                                    @if ($themeSearch !== '')
                                        <button class="btn btn-outline-secondary" type="button" wire:click="$set('themeSearch', '')"
                                            title="Hapus pencarian">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select" wire:model.live="themeFilter">
                                    <option value="all">Semua Jadwal ({{ $this->eventList->count() }})</option>
                                    <option value="today">Hari Ini ({{ $this->eventList->where('is_today', true)->count() }})</option>
                                    <option value="this_month">Bulan Ini</option>
                                    <option value="upcoming">Mendatang (Bulan Depan dst.)</option>
                                </select>
                            </div>

                            @if ($themeSearch !== '' || $themeFilter !== 'all')
                                <div class="col-auto">
                                    <button type="button" class="btn btn-light text-danger" wire:click="resetThemeFilters">
                                        <i class="mdi mdi-filter-remove me-1"></i> Reset
                                    </button>
                                </div>
                            @endif

                            <div class="col d-flex justify-content-end align-items-center gap-2">
                                <span class="text-muted fs-12 d-none d-sm-inline">Tampilkan:</span>
                                <select class="form-select w-auto" wire:model.live="themePerPage">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tabel Daftar Kegiatan -->
                        <div class="table-responsive" style="overflow: visible;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Tema / Judul Kegiatan</th>
                                        <th>Tanggal Pelaksanaan</th>
                                        <th>Tempat / Lokasi</th>
                                        <th class="text-center" style="width: 140px;">Total Peserta</th>
                                        <th class="text-center" style="width: 160px;">Status Acara</th>
                                        <th class="text-end" style="width: 160px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($this->paginatedEventList as $index => $event)
                                        <tr wire:key="event-row-{{ md5($event->theme) }}">
                                            <th scope="row" class="text-muted">
                                                {{ ($this->paginatedEventList->currentPage() - 1) * $this->paginatedEventList->perPage() + $loop->iteration }}
                                            </th>
                                            <td>
                                                <a href="javascript:void(0);"
                                                    wire:click="selectTheme('{{ addslashes($event->theme) }}')"
                                                    class="fw-bold text-dark text-decoration-none hover-primary fs-14 d-block mb-1"
                                                    title="{{ $event->theme }}" style="max-width: 360px; line-height: 1.4;">
                                                    {{ $event->theme }}
                                                </a>
                                                <small class="text-muted d-block">
                                                    <i class="mdi mdi-clock-outline me-1"></i> {{ $event->days_count }} hari acara pelatihan
                                                </small>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">
                                                    <i class="mdi mdi-calendar-range text-primary me-1"></i>
                                                    {{ $event->certificate_date }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 220px;"
                                                    title="{{ $event->event_venue }} {{ $event->event_location }}">
                                                    <i class="mdi mdi-map-marker text-danger me-1"></i>
                                                    {{ $event->event_venue ?: 'Lokasi Kegiatan' }}{{ $event->event_location ? ', ' . $event->event_location : '' }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border font-monospace fs-12 px-2 py-1">
                                                    {{ $event->total_participants }} Peserta
                                                </span>
                                                <small class="text-muted d-block fs-11 mt-1">
                                                    Registrasi: <span class="fw-bold text-success">{{ $event->total_hadir_registrasi }}</span>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                @if ($event->is_today)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-12 fw-bold">
                                                        <i class="mdi mdi-record-circle-outline me-1"></i> Hari Ini
                                                    </span>
                                                @else
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fs-12 fw-bold">
                                                        <i class="mdi mdi-calendar-clock me-1"></i> Akan Datang
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm text-white fw-bold px-3 py-1 shadow-sm"
                                                    style="background-color: #008F83; border-color: #008F83; border-radius: 6px;"
                                                    wire:click="selectTheme('{{ addslashes($event->theme) }}')">
                                                    <i class="mdi mdi-qrcode-scan me-1"></i> Buka Presensi
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-magnify-close fs-40 text-secondary"></i>
                                                    <h5 class="fw-bold mt-2">Tidak Ada Kegiatan yang Cocok</h5>
                                                    <p class="fs-13 mb-0">Coba ubah kata kunci pencarian atau ganti filter status kegiatan.</p>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" wire:click="resetThemeFilters">
                                                        Reset Filter
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginasi Tabel Tema Kegiatan -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3 pt-3 border-top">
                            <div class="text-muted fs-12">
                                Menampilkan {{ $this->paginatedEventList->firstItem() ?? 0 }} - {{ $this->paginatedEventList->lastItem() ?? 0 }} dari {{ $this->paginatedEventList->total() }} kegiatan
                            </div>
                            <div>
                                {{ $this->paginatedEventList->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- ========================================================================= -->
        <!-- MODE 2: LEMBAR ABSENSI KEGIATAN TERPILIH (TERKUNCI KE TEMA YANG DIPILIH)  -->
        <!-- ========================================================================= -->

        <!-- Navigasi Kembali ke Tabel Tema Kegiatan -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <button type="button" class="btn btn-light border px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm"
                wire:click="clearSelectedTheme" style="border-radius: 8px;">
                <i class="mdi mdi-arrow-left fs-16"></i>
                <span>Kembali ke Daftar Kegiatan Pelatihan</span>
            </button>

            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-12 rounded-pill">
                <i class="mdi mdi-lock-check me-1"></i> Lembar Presensi Aktif
            </span>
        </div>

        @php
            $activeEvent = $this->selectedEvent;
        @endphp

        <!-- KARTU BANNER KEGIATAN TERPILIH (TANPA DROPDOWN) -->
        <div class="card mb-3 border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f9fbfb 100%);">
            <div class="card-body p-3 p-md-4">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            @if ($activeEvent?->is_today)
                                <span class="badge bg-success text-white px-2 py-1 fs-11 fw-bold">
                                    <i class="mdi mdi-record-circle-outline me-1"></i> SEDANG BERLANGSUNG HARI INI
                                </span>
                            @else
                                <span class="badge bg-info text-white px-2 py-1 fs-11 fw-bold">
                                    <i class="mdi mdi-calendar-clock me-1"></i> KEGIATAN AKAN DATANG
                                </span>
                            @endif
                            <span class="badge bg-light text-dark border px-2 py-1 fs-11">
                                {{ $totalEvent }} Peserta Terdaftar
                            </span>
                        </div>

                        <h4 class="fw-bold text-dark mb-2" style="line-height: 1.4;">
                            {{ $selectedTheme }}
                        </h4>

                        <div class="d-flex align-items-center flex-wrap gap-3 text-muted fs-13">
                            <div>
                                <i class="mdi mdi-calendar-range text-primary me-1"></i>
                                <span class="fw-medium text-dark">{{ $activeEvent?->certificate_date ?? '-' }}</span>
                            </div>
                            @if ($activeEvent?->event_venue || $activeEvent?->event_location)
                                <div>
                                    <i class="mdi mdi-map-marker text-danger me-1"></i>
                                    <span>{{ $activeEvent->event_venue }}{{ $activeEvent->event_location ? ' - ' . $activeEvent->event_location : '' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Aksi Cepat Panitia (Besar, Jelas, & Kontras) -->
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                            <button type="button" class="btn btn-success btn-lg px-4 py-2 fw-bold shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#modalScannerQr"
                                style="background-color: #008F83; border-color: #008F83; border-radius: 8px;">
                                <i class="mdi mdi-qrcode-scan me-2 fs-18"></i> SCAN QR CODE
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg px-3 py-2 fw-semibold"
                                wire:click="downloadPdf" wire:loading.attr="disabled"
                                title="Unduh berkas PDF daftar hadir resmi Linkpemda" style="border-radius: 8px;">
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

                <!-- TAB SESI PRESENSI DINAMIS -->
                <div class="border-top pt-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <span class="text-muted fw-bold fs-12 text-uppercase me-2">PILIH SESI:</span>
                            @foreach ($this->sessions as $key => $session)
                                <button type="button"
                                    class="btn btn-sm px-3 py-1 fw-bold rounded-pill {{ $activeSession === $key ? 'btn-primary shadow-sm' : 'btn-light border text-muted' }}"
                                    wire:click="$set('activeSession', '{{ $key }}')">
                                    <i class="mdi {{ $session['icon'] ?? 'mdi-calendar-check' }} me-1"></i>
                                    {{ $session['label'] }}
                                    @if (!empty($session['date_short']))
                                        <small class="opacity-75 ms-1">({{ $session['date_short'] }})</small>
                                    @endif
                                </button>
                            @endforeach
                        </div>

                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-12">
                            Sesi Aktif: <strong>{{ $this->sessions[$activeSession]['label'] ?? ucfirst($activeSession) }}</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KARTU 2: RINGKASAN KEHADIRAN (4 METRIK DALAM SATU BARIS) -->
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-2 px-3 mb-0" style="border-radius: 10px; border-left: 4px solid #4a5568 !important;">
                    <div class="text-muted fs-11 text-uppercase fw-bold">Total Peserta</div>
                    <div class="fs-22 fw-bold text-dark">{{ $totalEvent }}</div>
                    <div class="fs-11 text-muted">Terdaftar di tema ini</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-2 px-3 mb-0" style="border-radius: 10px; border-left: 4px solid #008F83 !important;">
                    <div class="text-muted fs-11 text-uppercase fw-bold text-success">Sudah Hadir</div>
                    <div class="fs-22 fw-bold" style="color: #008F83;">{{ $totalHadir }}</div>
                    <div class="fs-11 text-muted">{{ $totalEvent > 0 ? round(($totalHadir / $totalEvent) * 100) : 0 }}% kehadiran</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-2 px-3 mb-0" style="border-radius: 10px; border-left: 4px solid #f59e0b !important;">
                    <div class="text-muted fs-11 text-uppercase fw-bold text-warning">Belum Hadir</div>
                    <div class="fs-22 fw-bold text-warning">{{ $totalBelum }}</div>
                    <div class="fs-11 text-muted">Menunggu scan/check-in</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-2 px-3 mb-0" style="border-radius: 10px; border-left: 4px solid #6366f1 !important;">
                    <div class="text-muted fs-11 text-uppercase fw-bold text-info">Izin / Sakit</div>
                    <div class="fs-22 fw-bold text-info">{{ $totalIzin }}</div>
                    <div class="fs-11 text-muted">Tercatat berhalangan</div>
                </div>
            </div>
        </div>

        <!-- KARTU 3: TABEL DAFTAR PRESENSI PESERTA -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-3 p-md-4">
                <!-- Toolbar Pencarian & Filter Cepat -->
                <div class="row g-2 mb-3 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" class="form-control"
                                placeholder="Cari nama peserta, instansi, atau no. sertifikat..."
                                wire:model.live.debounce.300ms="search">
                            @if ($search !== '')
                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="all">Semua Status ({{ $totalEvent }})</option>
                            <option value="hadir">Sudah Hadir ({{ $totalHadir }})</option>
                            <option value="belum_hadir">Belum Hadir ({{ $totalBelum }})</option>
                            <option value="izin">Izin / Sakit ({{ $totalIzin }})</option>
                        </select>
                    </div>

                    <div class="col-md-4 text-md-end">
                        <div class="d-flex align-items-center justify-content-md-end gap-2">
                            <span class="text-muted fs-12">Tampilkan:</span>
                            <select class="form-select w-auto" wire:model.live="perPage">
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Bar Aksi Masal (Muncul Jika Ada yang Dicentang) -->
                @if (count($selectedParticipants) > 0)
                    <div class="alert alert-primary d-flex align-items-center justify-content-between mb-3 py-2 px-3 border-0 bg-primary-subtle text-primary-emphasis flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary fs-12 rounded-pill px-2 py-1">{{ count($selectedParticipants) }}</span>
                            <span class="fw-semibold">Peserta dipilih</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-success fw-semibold" wire:click="bulkCheckIn"
                                wire:loading.attr="disabled">
                                <i class="mdi mdi-check-all me-1"></i> Tandai Hadir Masal (Check-in)
                            </button>
                            <button type="button" class="btn btn-sm btn-link text-muted text-decoration-none" wire:click="$set('selectedParticipants', [])">
                                Batal
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Tabel Data Peserta & Kehadiran -->
                <div class="table-responsive" style="overflow: visible;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 36px;">
                                    <input type="checkbox" class="form-check-input" wire:model.live="selectAll" title="Pilih Semua">
                                </th>
                                <th>Peserta &amp; Instansi</th>
                                <th>No. Sertifikat</th>
                                <th class="text-center" style="width: 140px;">Status Kehadiran</th>
                                <th style="width: 140px;">Jam Hadir</th>
                                <th class="text-end" style="width: 180px;">Aksi Presensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($participants as $participant)
                                @php
                                    $att = $participant->attendances->first();
                                    $status = $att?->status ?? 'belum_hadir';
                                    $inTime = $att?->check_in_at ? \Carbon\Carbon::parse($att->check_in_at)->format('H:i') : null;
                                    $outTime = $att?->check_out_at ? \Carbon\Carbon::parse($att->check_out_at)->format('H:i') : null;
                                @endphp
                                <tr wire:key="row-{{ $participant->id }}-{{ $activeSession }}" class="{{ in_array((string) $participant->id, $selectedParticipants) ? 'table-active' : '' }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input" value="{{ (string) $participant->id }}" wire:model.live="selectedParticipants">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-white fs-12"
                                                style="width: 34px; height: 34px; flex-shrink: 0; background-color: {{ $status === 'hadir' ? '#008F83' : '#64748b' }};">
                                                {{ mb_substr($participant->name, 0, 2) }}
                                            </span>
                                            <div>
                                                <a href="{{ route('admin.participant.linkpemda.show', $participant->id) }}"
                                                    class="fw-bold text-dark text-decoration-none hover-primary" target="_blank">
                                                    {{ $participant->name }}
                                                </a>
                                                <div class="text-muted fs-12">
                                                    {{ $participant->institution_name ?: '-' }}
                                                    @if ($participant->position)
                                                        &bull; <span class="fst-italic">{{ $participant->position }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace fs-11">
                                            {{ $participant->certificate_number ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($status === 'hadir')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-12">
                                                <i class="mdi mdi-check-circle me-1"></i> Hadir
                                            </span>
                                        @elseif ($status === 'izin')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fs-12"
                                                title="{{ $att?->notes ?: 'Izin' }}">
                                                <i class="mdi mdi-information-outline me-1"></i> Izin
                                            </span>
                                        @elseif ($status === 'sakit')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-12"
                                                title="{{ $att?->notes ?: 'Sakit' }}">
                                                <i class="mdi mdi-hospital me-1"></i> Sakit
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 fs-12">
                                                Belum Hadir
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($inTime)
                                            <div class="fs-12 fw-semibold text-dark">
                                                <i class="mdi mdi-login text-success me-1"></i> {{ $inTime }} WIB
                                            </div>
                                            @if ($outTime)
                                                <div class="fs-11 text-muted">
                                                    <i class="mdi mdi-logout text-primary me-1"></i> {{ $outTime }} WIB
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted fs-12">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            @if ($status === 'belum_hadir')
                                                <!-- Tombol 1-Klik Hadir (Check-in) -->
                                                <button type="button" class="btn btn-sm btn-success fw-semibold px-2 py-1"
                                                    wire:click="quickCheckIn('{{ $participant->id }}')" title="Catat kehadiran sekarang">
                                                    <i class="mdi mdi-check me-1"></i> Hadir
                                                </button>
                                            @elseif ($status === 'hadir' && empty($outTime))
                                                <!-- Tombol Hadir Pulang (Check-out) -->
                                                <button type="button" class="btn btn-sm btn-outline-primary fw-semibold px-2 py-1"
                                                    wire:click="quickCheckOut('{{ $participant->id }}')" title="Catat jam pulang">
                                                    <i class="mdi mdi-logout me-1"></i> Pulang
                                                </button>
                                            @else
                                                <span class="badge bg-light text-muted border px-2 py-1 fs-11">
                                                    <i class="mdi mdi-check-all text-success me-1"></i> Lengkap
                                                </span>
                                            @endif

                                            <!-- Dropdown Menu Opsi Tambahan -->
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border dropdown-toggle px-2 py-1"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 9999;">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                            wire:click="quickCheckIn('{{ $participant->id }}')">
                                                            <i class="mdi mdi-login text-success me-2"></i> Hadir Masuk (Check-in)
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                            wire:click="quickCheckOut('{{ $participant->id }}')">
                                                            <i class="mdi mdi-logout text-primary me-2"></i> Hadir Pulang (Check-out)
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                            wire:click="openStatusModal('{{ $participant->id }}')">
                                                            <i class="mdi mdi-note-edit-outline text-warning me-2"></i> Catat Izin / Sakit
                                                        </a>
                                                    </li>
                                                    @if ($status !== 'belum_hadir')
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                                wire:click="resetAttendance('{{ $participant->id }}')"
                                                                wire:confirm="Batalkan presensi {{ $participant->name }} untuk sesi ini?">
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
                                        <div class="text-muted">
                                            <i class="mdi mdi-account-search-outline fs-40 text-secondary"></i>
                                            <h5 class="fw-bold mt-2">Tidak Ada Data Peserta</h5>
                                            <p class="fs-13 mb-0">Tidak ditemukan peserta pada filter pencarian atau kegiatan ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3 pt-3 border-top">
                    <div class="text-muted fs-12">
                        Menampilkan {{ $participants->firstItem() ?? 0 }} - {{ $participants->lastItem() ?? 0 }} dari {{ $participants->total() }} peserta
                    </div>
                    <div>
                        {{ $participants->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 1: SCANNER QR CODE (KAMERA HTML5 / BARCODE GUN USB)                 -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="modalScannerQr" tabindex="-1" aria-labelledby="modalScannerQrLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header text-white" style="background-color: #008F83; border-radius: 14px 14px 0 0;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-qrcode-scan fs-22"></i>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-white" id="modalScannerQrLabel">Pemindaian Barcode &amp; QR Code Presensi</h5>
                            <small class="text-white-50 fs-12">Arahkan kamera ke tiket QR peserta atau gunakan scanner USB</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="btnCloseQrScanner"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Area Kamera Scanner -->
                        <div class="col-md-7">
                            <div class="border rounded-3 p-2 bg-dark text-center position-relative" style="min-height: 280px; overflow: hidden;">
                                <div id="qr-reader" style="width: 100%;"></div>
                                <div id="qr-scanner-loading" class="position-absolute top-50 start-50 translate-middle text-white text-center">
                                    <div class="spinner-border text-light mb-2" role="status"></div>
                                    <div class="fs-12 text-white-50">Menyiapkan kamera...</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted fs-11">
                                    <i class="mdi mdi-camera me-1"></i> Mendukung kamera HP &amp; Webcam Laptop
                                </small>
                                <button type="button" class="btn btn-xs btn-outline-secondary" id="btnSwitchCamera">
                                    <i class="mdi mdi-camera-switch me-1"></i> Ganti Kamera
                                </button>
                            </div>
                        </div>

                        <!-- Panel Hasil Scan & Barcode USB -->
                        <div class="col-md-5 d-flex flex-column justify-content-between">
                            <!-- Box Feedback Hasil Scan Terakhir -->
                            <div class="card border bg-light-subtle mb-3">
                                <div class="card-header bg-transparent py-2">
                                    <h6 class="mb-0 fw-bold fs-12 text-uppercase text-muted">Hasil Scan Terakhir:</h6>
                                </div>
                                <div class="card-body p-3" id="scanFeedbackBox">
                                    <div class="text-center text-muted py-3" id="scanFeedbackEmpty">
                                        <i class="mdi mdi-barcode-scan fs-36 text-secondary mb-1"></i>
                                        <p class="fs-12 mb-0">Belum ada pemindaian.</p>
                                    </div>
                                    <div id="scanFeedbackSuccess" class="d-none">
                                        <div class="alert alert-success border-0 py-2 px-2 mb-2 d-flex align-items-center gap-2">
                                            <i class="mdi mdi-check-circle fs-20 text-success"></i>
                                            <div class="fs-12 fw-bold" id="scanResultActionText">Check-in Berhasil!</div>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1" id="scanResultName">-</h5>
                                        <div class="text-muted fs-12 mb-2" id="scanResultInstitution">-</div>
                                        <span class="badge bg-primary-subtle text-primary border fs-11" id="scanResultCert">-</span>
                                    </div>
                                    <div id="scanFeedbackError" class="d-none">
                                        <div class="alert alert-danger border-0 py-2 px-2 mb-0 d-flex align-items-center gap-2">
                                            <i class="mdi mdi-alert-circle fs-20 text-danger"></i>
                                            <div class="fs-12" id="scanErrorMessage">Peserta tidak ditemukan.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Manual / Scanner USB Gun Barcode -->
                            <div>
                                <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-1">
                                    <i class="mdi mdi-barcode me-1 text-primary"></i> Scanner USB Gun / Input Kode Manual:
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="manualBarcodeInput"
                                        placeholder="Scan barcode gun / ketik no. sertifikat..." autofocus>
                                    <button class="btn btn-primary" type="button" id="btnSubmitManual">
                                        <i class="mdi mdi-send"></i>
                                    </button>
                                </div>
                                <small class="text-muted fs-11 mt-1 d-block">
                                    Arahkan kursor ke input di atas jika memakai alat scanner tembak (USB Gun).
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: CATAT STATUS IZIN / SAKIT DENGAN KETERANGAN                      -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="modalEditAttendanceStatus" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header py-3">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="mdi mdi-calendar-edit text-warning me-1"></i> Update Kehadiran Manual
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted fs-12 mb-1">Nama Peserta</label>
                        <input type="text" class="form-control fw-bold bg-light" value="{{ $modalParticipantName }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Kehadiran</label>
                        <select class="form-select" wire:model="modalStatus">
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan / Alasan (Opsional)</label>
                        <textarea class="form-control" rows="3" wire:model="modalNotes"
                            placeholder="Contoh: Sakit demam, Izin ada tugas dinas luar..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary px-4" wire:click="saveStatusModal">
                        <i class="mdi mdi-content-save me-1"></i> Simpan Status
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Library HTML5 QR Code Scanner -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let html5QrCode = null;
        let isScannerRunning = false;
        let currentCameraId = null;
        let cameraList = [];
        let currentCameraIndex = 0;

        const modalEl = document.getElementById('modalScannerQr');
        const manualInput = document.getElementById('manualBarcodeInput');
        const btnSubmitManual = document.getElementById('btnSubmitManual');
        const btnSwitchCamera = document.getElementById('btnSwitchCamera');
        const loadingSpinner = document.getElementById('qr-scanner-loading');

        // Feedback Elements
        const boxEmpty = document.getElementById('scanFeedbackEmpty');
        const boxSuccess = document.getElementById('scanFeedbackSuccess');
        const boxError = document.getElementById('scanFeedbackError');
        const resultActionText = document.getElementById('scanResultActionText');
        const resultName = document.getElementById('scanResultName');
        const resultInstitution = document.getElementById('scanResultInstitution');
        const resultCert = document.getElementById('scanResultCert');
        const errorMessage = document.getElementById('scanErrorMessage');

        // Audio Beep generator untuk feedback pemindaian
        function playBeep(isSuccess = true) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.connect(gain);
                gain.connect(ctx.destination);

                if (isSuccess) {
                    osc.frequency.value = 880; // Nada tinggi A5 (sukses)
                    gain.gain.setValueAtTime(0.15, ctx.currentTime);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.15);
                } else {
                    osc.frequency.value = 220; // Nada rendah (gagal)
                    gain.gain.setValueAtTime(0.2, ctx.currentTime);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.35);
                }
            } catch (e) {
                // Browser mungkin membatasi audio sebelum interaksi
            }
        }

        // Listener saat Modal Scanner dibuka
        if (modalEl) {
            modalEl.addEventListener('shown.bs.modal', function () {
                if (manualInput) {
                    manualInput.focus();
                }
                startScanner();
            });

            modalEl.addEventListener('hidden.bs.modal', function () {
                stopScanner();
                resetFeedback();
            });
        }

        function startScanner() {
            if (isScannerRunning) return;

            if (loadingSpinner) loadingSpinner.classList.remove('d-none');

            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    cameraList = cameras;
                    currentCameraId = cameras[currentCameraIndex % cameras.length].id;

                    html5QrCode = new Html5Qrcode("qr-reader");
                    const config = {
                        fps: 10,
                        qrbox: { width: 220, height: 220 },
                        aspectRatio: 1.0
                    };

                    html5QrCode.start(
                        currentCameraId,
                        config,
                        (decodedText) => {
                            // On Success Scan
                            onScanSuccess(decodedText);
                        },
                        (error) => {
                            // Scanning frame error (ignored)
                        }
                    ).then(() => {
                        isScannerRunning = true;
                        if (loadingSpinner) loadingSpinner.classList.add('d-none');
                    }).catch(err => {
                        if (loadingSpinner) loadingSpinner.classList.add('d-none');
                        console.warn("Gagal membuka kamera:", err);
                    });
                } else {
                    if (loadingSpinner) loadingSpinner.classList.add('d-none');
                }
            }).catch(err => {
                if (loadingSpinner) loadingSpinner.classList.add('d-none');
                console.warn("Error kamera:", err);
            });
        }

        function stopScanner() {
            if (html5QrCode && isScannerRunning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    isScannerRunning = false;
                }).catch(err => console.warn(err));
            }
        }

        if (btnSwitchCamera) {
            btnSwitchCamera.addEventListener('click', function () {
                if (cameraList.length > 1) {
                    stopScanner();
                    currentCameraIndex++;
                    startScanner();
                }
            });
        }

        let isCooldown = false;
        function onScanSuccess(decodedText) {
            if (isCooldown) return;
            isCooldown = true;

            @this.scanCode(decodedText);

            setTimeout(() => {
                isCooldown = false;
            }, 1800);
        }

        // Livewire Scan Result Handlers
        window.addEventListener('scan-result-success', event => {
            playBeep(true);
            const data = event.detail[0] || event.detail;

            boxEmpty.classList.add('d-none');
            boxError.classList.add('d-none');
            boxSuccess.classList.remove('d-none');

            resultActionText.textContent = data.action === 'check_out' ? 'Check-out (Pulang) Berhasil!' : 'Check-in (Hadir) Berhasil!';
            resultName.textContent = data.name;
            resultInstitution.textContent = data.institution || '-';
            resultCert.textContent = data.certificate_number || '-';
        });

        window.addEventListener('scan-result-error', event => {
            playBeep(false);
            const data = event.detail[0] || event.detail;

            boxEmpty.classList.add('d-none');
            boxSuccess.classList.add('d-none');
            boxError.classList.remove('d-none');

            errorMessage.textContent = data.message || 'Peserta tidak ditemukan.';
        });

        function resetFeedback() {
            boxEmpty.classList.remove('d-none');
            boxSuccess.classList.add('d-none');
            boxError.classList.add('d-none');
        }

        // Barcode Gun / Manual Input Trigger
        if (manualInput) {
            manualInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    processManualInput();
                }
            });
        }

        if (btnSubmitManual) {
            btnSubmitManual.addEventListener('click', function () {
                processManualInput();
            });
        }

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
            const el = document.getElementById(modalId);
            if (el) {
                const m = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
                m.show();
            }
        });

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
