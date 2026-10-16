<div>
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h4 class="page-title mb-0">{{ $pageTitle }}</h4>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a wire:navigate href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $pageTitle }}</li>
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

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <a wire:navigate href="{{ route('admin.campaign.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i> Buat Email Blast Baru
                            </a>
                            <a wire:navigate href="{{ route('admin.contact.index') }}" class="btn btn-outline-info">
                                <i class="mdi mdi-account-multiple-outline me-1"></i> Kelola Kontak Target
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select" wire:model.live="filterStatus" style="width: 170px;">
                                <option value="all">Semua Status</option>
                                <option value="draft">Draft</option>
                                <option value="queued">Dalam Antrean</option>
                                <option value="processing">Sedang Mengirim</option>
                                <option value="completed">Selesai</option>
                                <option value="failed">Gagal</option>
                            </select>
                            <div class="input-group" style="max-width: 260px;">
                                <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control border-start-0" placeholder="Cari judul / subjek..."
                                    wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Judul Campaign & Subjek</th>
                                    <th>Target Penerima</th>
                                    <th>Status & Progres</th>
                                    <th>Tanggal Dibuat</th>
                                    <th class="text-end" style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($campaigns as $index => $campaign)
                                    <tr>
                                        <td>{{ $campaigns->firstItem() + $index }}</td>
                                        <td>
                                            <div class="fw-bold text-dark font-size-15">{{ $campaign->title }}</div>
                                            <small class="text-muted"><i class="mdi mdi-email-outline me-1"></i>{{ $campaign->subject }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 font-size-13">
                                                <i class="mdi mdi-account-group me-1 text-primary"></i> {{ $campaign->total_recipients }} Kontak
                                            </span>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <span class="{{ $campaign->status->badgeClass() }} rounded-pill px-2 py-1 font-size-12">
                                                    {{ $campaign->status->label() }}
                                                </span>
                                            </div>
                                            @if ($campaign->total_recipients > 0)
                                                @php
                                                    $percent = $campaign->total_recipients > 0 ? round((($campaign->sent_count + $campaign->failed_count) / $campaign->total_recipients) * 100) : 0;
                                                @endphp
                                                <div class="progress" style="height: 6px; width: 140px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%;"
                                                        aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <small class="text-muted fs-11 mt-1 d-block">
                                                    {{ $campaign->sent_count }} terkirim • {{ $campaign->failed_count }} gagal
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $campaign->created_at->format('d M Y, H:i') }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a wire:navigate href="{{ route('admin.campaign.show', $campaign->id) }}" class="btn btn-sm btn-info me-1" title="Monitoring Detail">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </a>
                                            @if ($campaign->status->value === 'draft')
                                                <a wire:navigate href="{{ route('admin.campaign.edit', $campaign->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Edit Draft">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="confirm('Hapus campaign ini?') || event.stopImmediatePropagation()"
                                                wire:click="delete('{{ $campaign->id }}')" title="Hapus">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-email-open-outline font-size-48 d-block mb-2 text-muted"></i>
                                            <h5>Belum ada Campaign Email</h5>
                                            <p class="text-muted mb-3">Mulai buat email blast promosi untuk mitra pemerintah dan swasta Anda sekarang.</p>
                                            <a wire:navigate href="{{ route('admin.campaign.create') }}" class="btn btn-primary">
                                                <i class="mdi mdi-plus me-1"></i> Buat Campaign Baru
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $campaigns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

