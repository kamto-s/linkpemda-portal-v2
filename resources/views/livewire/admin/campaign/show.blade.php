<div @if(in_array($campaign->status->value, ['queued', 'processing'])) wire:poll.3s @endif>
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h4 class="page-title mb-0">Monitoring Email Blast</h4>
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
                            <p class="text-muted mb-0">
                                <strong>Subjek:</strong> {{ $campaign->subject }} •
                                <span>Dibuat: {{ $campaign->created_at->format('d M Y, H:i') }}</span>
                                @if ($campaign->sent_at)
                                    • <span>Selesai: {{ $campaign->sent_at->format('d M Y, H:i') }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a wire:navigate href="{{ route('admin.campaign.index') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i> Kembali
                            </a>
                            @if ($campaign->failed_count > 0)
                                <button type="button" class="btn btn-warning" wire:click="retryFailed">
                                    <i class="mdi mdi-refresh me-1"></i> Kirim Ulang {{ $campaign->failed_count }} yang Gagal
                                </button>
                            @endif
                        </div>
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

                    <div class="row text-center pt-2">
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
                <div class="card-header bg-light py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <h5 class="card-title m-0 fw-bold">Daftar Status Penerima</h5>
                    <div class="d-flex align-items-center gap-2">
                        <select class="form-select form-select-sm" wire:model.live="filterRecipientStatus" style="width: 160px;">
                            <option value="all">Semua Status</option>
                            <option value="sent">Hanya Terkirim</option>
                            <option value="failed">Hanya Gagal</option>
                            <option value="pending">Hanya Menunggu</option>
                        </select>
                        <div class="input-group input-group-sm" style="max-width: 240px;">
                            <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Cari nama / email..."
                                wire:model.live.debounce.300ms="searchRecipient">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Nama Penerima</th>
                                    <th>Email</th>
                                    <th>Instansi / Perusahaan</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Waktu Kirim / Catatan Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recipients as $index => $item)
                                    <tr>
                                        <td>{{ $recipients->firstItem() + $index }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->contact->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->contact->position ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <a href="mailto:{{ $item->contact->email ?? '' }}" class="text-primary">
                                                {{ $item->contact->email ?? '-' }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $item->contact->organization_name ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if ($item->contact)
                                                <span class="badge {{ $item->contact->category->badgeClass() }} rounded-pill font-size-11">
                                                    {{ $item->contact->category->label() }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="{{ $item->status->badgeClass() }} rounded-pill px-2 py-1 font-size-11">
                                                {{ $item->status->label() }}
                                            </span>
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
                                            Tidak ada data penerima yang sesuai filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $recipients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

