<div>
    <!-- Page Title & Actions -->
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.template.index') }}" wire:navigate class="btn btn-sm btn-light border" title="Kembali ke Daftar">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="page-title mb-0 d-flex align-items-center gap-2 flex-wrap">
                            <span>{{ $template->name }}</span>
                            <span class="badge {{ $template->category->badgeClass() }} fs-11">
                                <i class="{{ $template->category->icon() }} me-1"></i> {{ $template->category->label() }}
                            </span>
                            @if ($template->is_system)
                                <span class="badge bg-secondary-subtle text-secondary fs-10 border">Sistem</span>
                            @else
                                <span class="badge bg-info-subtle text-info fs-10 border border-info-subtle">Kustom</span>
                            @endif
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-lg-end mt-2 mt-lg-0">
                <div class="d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                    <a href="{{ route('admin.campaign.create', ['template' => $template->id]) }}" wire:navigate class="btn btn-sm btn-success fw-semibold">
                        <i class="mdi mdi-send me-1"></i> Buat Campaign
                    </a>
                    <a href="{{ route('admin.template.edit', $template->id) }}" wire:navigate class="btn btn-sm btn-primary fw-semibold">
                        <i class="mdi mdi-pencil me-1"></i> Edit Template
                    </a>
                    @if (!$template->is_system)
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="confirm('Yakin ingin menghapus template ini?') || event.stopImmediatePropagation()"
                            wire:click="delete">
                            <i class="mdi mdi-trash-can-outline me-1"></i> Hapus
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kolom Kiri: Metadata & Detail Informasi -->
        <div class="col-lg-5">
            <!-- Informasi Template -->
            <div class="card mb-4">
                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title fw-bold text-dark mb-0">
                        <i class="mdi mdi-information-outline text-primary me-1"></i> Rincian Informasi Template
                    </h6>
                    <button type="button" class="btn btn-xs {{ $template->is_active ? 'btn-success' : 'btn-secondary' }}" 
                        wire:click="toggleActive" title="Klik untuk mengubah status aktif">
                        {{ $template->is_active ? 'Status: Aktif' : 'Status: Nonaktif' }}
                    </button>
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless align-middle mb-0 fs-13">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 140px;">Kategori:</td>
                                <td class="fw-semibold">
                                    <span class="badge {{ $template->category->badgeClass() }}">
                                        <i class="{{ $template->category->icon() }} me-1"></i> {{ $template->category->label() }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Subjek Default:</td>
                                <td class="fw-medium text-dark">{{ $template->subject }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Template:</td>
                                <td>
                                    @if ($template->is_system)
                                        <span class="text-muted"><i class="mdi mdi-lock-outline me-1"></i> Bawaan Sistem (Permanen)</span>
                                    @else
                                        <span class="text-info"><i class="mdi mdi-account-edit-outline me-1"></i> Kustom Pengguna</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Dibuat Pada:</td>
                                <td class="text-muted">
                                    {{ $template->created_at ? $template->created_at->format('d M Y, H:i') : '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Variabel Dinamis -->
            <div class="card mb-4">
                <div class="card-header bg-light py-2 border-bottom">
                    <h6 class="card-title fw-bold text-dark mb-0 fs-13">
                        <i class="mdi mdi-code-tags text-info me-1"></i> Variabel Dinamis &amp; Data Dummy
                    </h6>
                </div>
                <div class="card-body p-3">
                    <p class="text-muted fs-12 mb-2">
                        Pratinjau di samping menggunakan data kontak acuan berikut untuk menggantikan placeholder:
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0 fs-12">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 110px;">Tag Variabel</th>
                                    <th>Nilai Dummy di Pratinjau</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>{nama}</code></td>
                                    <td>{{ $sampleContact->name }}</td>
                                </tr>
                                <tr>
                                    <td><code>{instansi}</code></td>
                                    <td>{{ $sampleContact->organization_name }}</td>
                                </tr>
                                <tr>
                                    <td><code>{jabatan}</code></td>
                                    <td>{{ $sampleContact->position }}</td>
                                </tr>
                                <tr>
                                    <td><code>{email}</code></td>
                                    <td>{{ $sampleContact->email }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kode Sumber HTML -->
            <div class="card" x-data="{ copied: false, open: false }">
                <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-center cursor-pointer"
                    @click="open = !open">
                    <h6 class="card-title fw-bold text-dark mb-0 fs-13">
                        <i class="mdi mdi-xml text-warning me-1"></i> Kode Sumber HTML Template
                    </h6>
                    <div>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2"
                            @click.stop="
                                navigator.clipboard.writeText($refs.htmlCode.value);
                                copied = true;
                                setTimeout(() => copied = false, 2000);
                            ">
                            <span x-show="!copied"><i class="mdi mdi-content-copy me-1"></i> Salin</span>
                            <span x-show="copied" class="text-success"><i class="mdi mdi-check me-1"></i> Tersalin!</span>
                        </button>
                        <i class="mdi ms-2" :class="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"></i>
                    </div>
                </div>
                <div class="card-body p-3" x-show="open" x-transition>
                    <textarea x-ref="htmlCode" class="form-control font-monospace fs-11 bg-light" rows="12" readonly>{{ $template->body_content }}</textarea>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pratinjau Tampilan Email -->
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="card-title fw-bold text-dark mb-0 fs-13">
                            <i class="mdi mdi-eye-outline text-success me-1"></i> Pratinjau Tampilan Email
                        </h6>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" 
                            class="btn {{ $previewDevice === 'desktop' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                            wire:click="setDevice('desktop')" title="Tampilan Desktop">
                            <i class="mdi mdi-monitor me-1"></i> Desktop
                        </button>
                        <button type="button" 
                            class="btn {{ $previewDevice === 'mobile' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                            wire:click="setDevice('mobile')" title="Tampilan Mobile / Smartphone">
                            <i class="mdi mdi-cellphone me-1"></i> Mobile
                        </button>
                    </div>
                </div>

                <!-- Header Email Mockup -->
                <div class="p-3 bg-white border-bottom fs-12">
                    <div class="row g-1">
                        <div class="col-sm-2 text-muted fw-semibold">Subjek:</div>
                        <div class="col-sm-10 fw-bold text-dark">{{ $template->subject }}</div>
                        <div class="col-sm-2 text-muted fw-semibold">Kepada:</div>
                        <div class="col-sm-10 text-muted">{{ $sampleContact->name }} &lt;{{ $sampleContact->email }}&gt;</div>
                    </div>
                </div>

                <!-- Frame Email -->
                <div class="card-body p-3" style="background-color: #f1f3f5; min-height: 640px;">
                    <div class="mx-auto transition-all" style="{{ $previewDevice === 'mobile' ? 'max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border-radius: 20px; border: 8px solid #2b2d42; overflow: hidden;' : 'width: 100%;' }}">
                        <iframe srcdoc="{{ $previewHtml }}"
                            style="width: 100%; height: 680px; border: none; background-color: #ffffff; display: block;"
                            title="Pratinjau Template Email">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
