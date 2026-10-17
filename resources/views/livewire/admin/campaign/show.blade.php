<div @if(in_array($campaign->status->value, ['queued', 'processing'])) wire:poll.3s @endif>
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.campaign.index') }}" wire:navigate class="btn btn-sm btn-light border" title="Kembali">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <h4 class="page-title mb-0">Monitoring Email Blast</h4>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a wire:navigate href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a wire:navigate href="{{ route('admin.campaign.index') }}">Email Blast</a></li>
                        <li class="breadcrumb-item active">Detail</li>
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

    @if (session()->has('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert me-1"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Campaign Header & Progress Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 pb-3 border-bottom mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h3 class="fw-bold mb-0 text-dark">{{ $campaign->title }}</h3>
                                <span class="{{ $campaign->status->badgeClass() }} rounded-pill px-3 py-1 font-size-13">
                                    {{ $campaign->status->label() }}
                                </span>
                            </div>
                            <div class="text-muted mb-0 fs-13 d-flex flex-wrap align-items-center gap-1">
                                <span><strong>Subjek:</strong> {{ $campaign->subject }}</span>
                                <span class="text-muted mx-1">•</span>
                                <span><i class="mdi mdi-play-circle-outline text-primary me-1"></i>Mulai: <strong>{{ ($campaign->started_at ?? $campaign->created_at)->format('d M Y, H:i:s') }}</strong></span>
                                @if ($campaign->sent_at)
                                    <span class="text-muted mx-1">•</span>
                                    <span><i class="mdi mdi-check-circle-outline text-success me-1"></i>Selesai: <strong>{{ $campaign->sent_at->format('d M Y, H:i:s') }}</strong></span>
                                @endif
                                @if ($campaign->delivery_duration)
                                    <span class="text-muted mx-1">•</span>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-12">
                                        <i class="mdi mdi-timer-outline me-1"></i>Durasi: {{ $campaign->delivery_duration }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if ($campaign->failed_count > 0)
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-warning" wire:click="retryFailed">
                                    <i class="mdi mdi-refresh me-1"></i> Kirim Ulang {{ $campaign->failed_count }} yang Gagal
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Progress Bar & Statistics -->
                    @php
                        $processed = $campaign->sent_count + $campaign->failed_count;
                        $percent = $campaign->total_recipients > 0 ? round(($processed / $campaign->total_recipients) * 100) : 0;
                    @endphp

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fs-13 fw-semibold">Progres Pengiriman Queue</span>
                            <span class="fs-13 fw-bold">{{ $processed }} / {{ $campaign->total_recipients }} ({{ $percent }}%)</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%;"
                                aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                                @if($percent >= 15) {{ $percent }}% @endif
                            </div>
                        </div>
                    </div>

                    <div class="row text-center pt-2 g-2">
                        <div class="col-6 col-md-3 border-end">
                            <h4 class="fw-bold mb-0 text-dark">{{ $campaign->total_recipients }}</h4>
                            <small class="text-muted text-uppercase fs-11">Total Penerima</small>
                        </div>
                        <div class="col-6 col-md-3 border-end">
                            <h4 class="fw-bold mb-0 text-success">{{ $campaign->sent_count }}</h4>
                            <small class="text-muted text-uppercase fs-11">Berhasil Terkirim</small>
                        </div>
                        <div class="col-6 col-md-3 border-end">
                            <h4 class="fw-bold mb-0 text-danger">{{ $campaign->failed_count }}</h4>
                            <small class="text-muted text-uppercase fs-11">Gagal Terkirim</small>
                        </div>
                        <div class="col-6 col-md-3">
                            <h4 class="fw-bold mb-0 text-secondary">{{ $pendingCount }}</h4>
                            <small class="text-muted text-uppercase fs-11">Menunggu Antrean</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recipient List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h4 class="header-title mb-1">Daftar Status Penerima</h4>
                            <p class="sub-header mb-0">
                                Rincian status pengiriman email blast ke masing-masing kontak target.
                            </p>
                        </div>
                        <span class="badge bg-light text-dark border px-2 py-1 fs-12">
                            Total: <strong>{{ $recipients->count() }}</strong> kontak
                        </span>
                    </div>

                    <!-- Filter & Search Toolbar -->
                    <div class="row g-2 align-items-center mb-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control" placeholder="Cari nama, email, instansi..."
                                    wire:model.live.debounce.300ms="searchRecipient">
                                @if ($searchRecipient)
                                    <button class="btn btn-outline-secondary" type="button"
                                        wire:click="$set('searchRecipient', '')" title="Hapus pencarian">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="filterRecipientStatus">
                                <option value="all">Semua Status</option>
                                <option value="sent">Terkirim</option>
                                <option value="failed">Gagal</option>
                                <option value="pending">Menunggu Antrean</option>
                            </select>
                        </div>

                        @if ($searchRecipient !== '' || $filterRecipientStatus !== 'all')
                            <div class="col-auto">
                                <button type="button" class="btn btn-light text-danger" wire:click="resetFilters">
                                    <i class="mdi mdi-filter-remove me-1"></i> Reset
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Tabel Kontak Scrollable (Los) -->
                    <div class="table-responsive border rounded" style="max-height: 540px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top" style="top: 0; z-index: 2;">
                                <tr>
                                    <th style="width: 50px;" class="text-center bg-light">#</th>
                                    <th class="bg-light">Penerima</th>
                                    <th class="bg-light">Email &amp; No. HP</th>
                                    <th class="bg-light">Instansi / Perusahaan</th>
                                    <th class="text-center bg-light" style="width: 120px;">Kategori</th>
                                    <th class="text-center bg-light" style="width: 110px;">Status</th>
                                    <th class="bg-light" style="width: 220px;">Waktu Kirim / Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recipients as $item)
                                    <tr wire:key="{{ $item->id }}">
                                        <td class="text-center text-muted fs-12">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center fw-bold text-uppercase"
                                                    style="width: 34px; height: 34px; font-size: 12px; flex-shrink: 0;">
                                                    {{ mb_substr($item->contact->name ?? '?', 0, 2) }}
                                                </span>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $item->contact->name ?? '-' }}</div>
                                                    <small class="text-muted">{{ $item->contact->position ?? 'PIC / Staff' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <a href="mailto:{{ $item->contact->email ?? '' }}" class="text-primary text-decoration-none">
                                                    {{ $item->contact->email ?? '-' }}
                                                </a>
                                            </div>
                                            <small class="text-muted">{{ $item->contact->phone ?: '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $item->contact->organization_name ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if ($item->contact)
                                                @if ($item->contact->category === \App\Enums\ContactCategory::Pemerintah || $item->contact->category?->value === 'pemerintah')
                                                    <span class="badge badge-soft-info px-2 py-1">Pemerintah</span>
                                                @else
                                                    <span class="badge badge-soft-warning px-2 py-1">Swasta</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->status->value === 'sent')
                                                <span class="badge badge-soft-success px-2 py-1">Terkirim</span>
                                            @elseif ($item->status->value === 'failed')
                                                <span class="badge badge-soft-danger px-2 py-1">Gagal</span>
                                            @else
                                                <span class="badge badge-soft-secondary px-2 py-1">Menunggu</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status->value === 'sent' && $item->sent_at)
                                                <span class="text-success fs-12"><i class="mdi mdi-check-all me-1"></i>{{ $item->sent_at->format('d/m/Y H:i:s') }}</span>
                                            @elseif ($item->status->value === 'failed')
                                                <span class="text-danger fs-12 d-block" title="{{ $item->error_message }}">
                                                    <i class="mdi mdi-alert-circle me-1"></i>{{ Str::limit($item->error_message ?: 'Gagal dikirim', 40) }}
                                                </span>
                                            @else
                                                <span class="text-muted fs-12"><i class="mdi mdi-clock-outline me-1"></i>Dalam antrean...</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="mdi mdi-account-search-outline fs-24 d-block mb-1 text-muted"></i>
                                            Tidak ada data penerima yang sesuai filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

