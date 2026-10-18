<div>
    <!-- Page Header -->
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.campaign.index') }}" wire:navigate class="btn btn-sm btn-light border">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <h4 class="page-title mb-0">
                        {{ $isEditing ? 'Ubah Email: ' . $campaign->title : 'Buat Email Blast' }}
                    </h4>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.campaign.index') }}" wire:navigate>Email
                                Blast</a></li>
                        <li class="breadcrumb-item active">{{ $isEditing ? 'Ubah' : 'Baru' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Stepper Navigation (Modern Top-Icon & Progress Line Design) -->
    <div class="stepper-modern-wrapper mb-4 py-3 px-2 px-md-4">
        <!-- Row of Icons -->
        <div class="row g-0 mb-3">
            <!-- Step 1 Icon -->
            <div class="col-4">
                <div class="step-icon-box {{ $currentStep >= 1 ? 'step-icon-active' : 'step-icon-pending' }}"
                    onclick="handleStepClick(1)" title="Langkah 1: Konten Email">
                    <i class="mdi {{ $currentStep > 1 ? 'mdi-check' : 'mdi-email-edit-outline' }}"></i>
                </div>
            </div>

            <!-- Step 2 Icon -->
            <div class="col-4">
                <div class="step-icon-box {{ $currentStep >= 2 ? 'step-icon-active' : 'step-icon-pending' }}"
                    onclick="handleStepClick(2)" title="Langkah 2: Target Penerima">
                    <i class="mdi {{ $currentStep > 2 ? 'mdi-check' : 'mdi-account-group-outline' }}"></i>
                </div>
            </div>

            <!-- Step 3 Icon -->
            <div class="col-4">
                <div class="step-icon-box {{ $currentStep >= 3 ? 'step-icon-active' : 'step-icon-pending' }}"
                    onclick="handleStepClick(3)" title="Langkah 3: Review & Kirim">
                    <i class="mdi mdi-send-check-outline"></i>
                </div>
            </div>
        </div>

        <!-- Progress Track Bar -->
        <div class="step-progress-track mb-3">
            <div class="step-progress-fill"
                style="width: {{ $currentStep === 1 ? '33.33%' : ($currentStep === 2 ? '66.66%' : '100%') }};"></div>
        </div>

        <!-- Row of Titles & Subtitles -->
        <div class="row g-0">
            <!-- Step 1 Text -->
            <div class="col-4 pe-4 pe-lg-5">
                <div class="step-text-box" onclick="handleStepClick(1)">
                    <div class="step-title {{ $currentStep >= 1 ? 'text-active' : 'text-pending' }}">Konten Email</div>
                    <div class="step-subtitle">Subjek & Pesan</div>
                </div>
            </div>

            <!-- Step 2 Text -->
            <div class="col-4 pe-4 pe-lg-5">
                <div class="step-text-box" onclick="handleStepClick(2)">
                    <div class="step-title {{ $currentStep >= 2 ? 'text-active' : 'text-pending' }}">
                        Target Penerima
                        @if (count($selectedContacts) > 0)
                            <span
                                class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 ms-1">{{ count($selectedContacts) }}</span>
                        @endif
                    </div>
                    <div class="step-subtitle">Pilih Kontak</div>
                </div>
            </div>

            <!-- Step 3 Text -->
            <div class="col-4 pe-3">
                <div class="step-text-box" onclick="handleStepClick(3)">
                    <div class="step-title {{ $currentStep >= 3 ? 'text-active' : 'text-pending' }}">Review & Kirim
                    </div>
                    <div class="step-subtitle">Konfirmasi & Kirim Blast</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-alert-circle me-2 fs-16"></i>
                <ul class="mb-0 ps-2 fs-13">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ========================================================================================= -->
    <!-- LANGKAH 1: KONTEN EMAIL (Preserved in DOM to protect TinyMCE instance)                    -->
    <!-- ========================================================================================= -->
    <div style="{{ $currentStep === 1 ? '' : 'display: none;' }}">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">
                    {{ $isEditing ? 'Form Ubah Email: ' . $campaign->title : 'Form Konten Email Blast' }}
                </h4>
                <p class="sub-header mb-0">
                    Tentukan judul campaign, subjek email, dan susun pesan penawaran.
                </p>
            </div>

            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12 mb-3">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label fw-bold">Judul Campaign <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    wire:model="title" placeholder="Nama campaign">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Subjek Email <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                    wire:model="subject" placeholder="Subjek email penerima">
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <label class="form-label fw-bold mb-0">Isi Email <span class="text-danger">*</span></label>

                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                <button type="button"
                                    class="btn btn-xs btn-outline-primary py-1 px-2 d-inline-flex align-items-center gap-1 fw-semibold"
                                    data-bs-toggle="modal" data-bs-target="#templateModal"
                                    title="Pilih Template Siap Pakai">
                                    <i class="mdi mdi-file-document-outline"></i> Pilih Template
                                </button>

                                <span class="text-muted mx-1">|</span>

                                <button type="button" class="btn btn-xs btn-light border py-1 px-2"
                                    onclick="insertVariable('{nama}')" title="Nama Kontak">+ {nama}</button>
                                <button type="button" class="btn btn-xs btn-light border py-1 px-2"
                                    onclick="insertVariable('{instansi}')" title="Instansi">+ {instansi}</button>
                                <button type="button" class="btn btn-xs btn-light border py-1 px-2"
                                    onclick="insertVariable('{jabatan}')" title="Jabatan">+ {jabatan}</button>
                                <button type="button" class="btn btn-xs btn-light border py-1 px-2"
                                    onclick="insertVariable('{email}')" title="Email">+ {email}</button>
                            </div>
                        </div>

                        <div wire:ignore class="tinymce-wrapper border rounded">
                            <textarea id="tinymce-editor" placeholder="Tulis isi pesan email di sini atau pilih template siap pakai di atas...">{!! $body_content !!}</textarea>
                        </div>
                        @error('body_content')
                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Footer Aksi Langkah 1 -->
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.campaign.index') }}" wire:navigate class="btn btn-light border">
                        Batal
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="handleSaveDraft()">
                            Simpan Draft
                        </button>
                        <button type="button" class="btn btn-primary px-3" onclick="handleGoToStep2()">
                            Lanjut ke Penerima <i class="mdi mdi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================================= -->
    <!-- LANGKAH 2: TARGET PENERIMA                                                               -->
    <!-- ========================================================================================= -->
    @if ($currentStep === 2)
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <div>
                        <h4 class="">Target Penerima</h4>
                        <p class="mb-0">
                            Pilih kontak sasaran yang akan menerima pengiriman email blast ini.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-3 py-2 fs-12">
                            {{ count($selectedContacts) }} dipilih
                            @if (count($selectedContacts) > 0)
                                (Pemerintah: {{ $selectedPemerintahCount }}, Swasta: {{ $selectedSwastaCount }})
                            @endif
                        </span>
                        @if (count($selectedContacts) > 0)
                            <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 fs-12"
                                wire:click="deselectAll('all')" title="Kosongkan Pilihan">
                                <i class="mdi mdi-close me-1"></i>Kosongkan
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body">

                <!-- Filter & Bulk Actions Toolbar -->
                <div class="row g-2 align-items-center mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" class="form-control" placeholder="Cari nama, email, instansi..."
                                wire:model.live.debounce.300ms="searchContact">
                            @if ($searchContact)
                                <button class="btn btn-outline-secondary" type="button"
                                    wire:click="$set('searchContact', '')" title="Hapus pencarian">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="contactCategory">
                            <option value="all">Semua Kategori ({{ $allContactsCount }})</option>
                            <option value="pemerintah">Pemerintah ({{ $pemdaCount }})</option>
                            <option value="swasta">Swasta ({{ $swastaCount }})</option>
                        </select>
                    </div>

                    @if ($searchContact !== '' || $contactCategory !== 'all')
                        <div class="col-auto">
                            <button type="button" class="btn btn-light text-danger" wire:click="resetFilters">
                                <i class="mdi mdi-filter-remove me-1"></i> Reset
                            </button>
                        </div>
                    @endif

                    <div class="col d-flex justify-content-end align-items-center">
                        <span class="text-muted fs-13">
                            Total: <strong>{{ $contacts->count() }}</strong> kontak
                        </span>
                    </div>
                </div>

                @error('selectedContacts')
                    <div class="alert alert-danger py-2 px-3 mb-3 fs-13">
                        <i class="mdi mdi-alert-circle me-1"></i> {{ $message }}
                    </div>
                @enderror

                <!-- Tabel Kontak Scrollable -->
                <div class="table-responsive border rounded" style="max-height: 520px; overflow-y: auto;">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light sticky-top" style="top: 0; z-index: 2;">
                            <tr>
                                <th style="width: 45px;" class="text-center bg-light">
                                    <input class="form-check-input" type="checkbox" wire:model.live="selectAll"
                                        wire:key="header-checkbox-{{ $selectAll ? '1' : '0' }}"
                                        id="selectAllCheckbox" @disabled($contacts->count() === 0)
                                        title="Pilih Semua yang Ditampilkan">
                                </th>
                                <th class="bg-light">Kontak</th>
                                <th class="bg-light">Email &amp; No. HP</th>
                                <th class="bg-light">Instansi / Perusahaan</th>
                                <th class="text-center bg-light" style="width: 130px;">Kategori</th>
                                <th class="text-center bg-light" style="width: 100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contacts as $contact)
                                <tr wire:key="{{ $contact->id }}"
                                    class="{{ in_array($contact->id, $selectedContacts) ? 'table-primary-subtle' : '' }}">
                                    <td class="text-center">
                                        <input class="form-check-input" type="checkbox" value="{{ $contact->id }}"
                                            wire:model.live="selectedContacts" id="c_{{ $contact->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span
                                                class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center fw-bold text-uppercase"
                                                style="width: 34px; height: 34px; font-size: 12px; flex-shrink: 0;">
                                                {{ mb_substr($contact->name, 0, 2) }}
                                            </span>
                                            <div>
                                                <label
                                                    class="form-check-label fw-bold d-block text-dark mb-0 cursor-pointer"
                                                    for="c_{{ $contact->id }}" style="cursor: pointer;">
                                                    {{ $contact->name }}
                                                </label>
                                                <small
                                                    class="text-muted">{{ $contact->position ?? 'PIC / Staff' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <a href="mailto:{{ $contact->email }}"
                                                class="text-primary text-decoration-none">
                                                {{ $contact->email }}
                                            </a>
                                        </div>
                                        <small class="text-muted">{{ $contact->phone ?: '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $contact->organization_name }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($contact->category === \App\Enums\ContactCategory::Pemerintah || $contact->category?->value === 'pemerintah')
                                            <span class="badge badge-soft-info px-2 py-1">
                                                Pemerintah
                                            </span>
                                        @else
                                            <span class="badge badge-soft-warning px-2 py-1">
                                                Swasta
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($contact->is_active)
                                            <span class="badge badge-soft-success px-2 py-1">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-soft-secondary px-2 py-1">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="mdi mdi-account-search-outline fs-24 d-block mb-1 text-muted"></i>
                                        Tidak ada data kontak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Aksi Langkah 2 -->
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary" wire:click="goToStep(1)">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" wire:click="saveDraft">
                            Simpan Draft
                        </button>
                        <button type="button" class="btn btn-primary px-3" wire:click="goToStep3"
                            {{ count($selectedContacts) === 0 ? 'disabled' : '' }}>
                            Lanjut ke Review <i class="mdi mdi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================================= -->
    <!-- LANGKAH 3: REVIEW & KIRIM                                                                -->
    <!-- ========================================================================================= -->
    @if ($currentStep === 3)
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="header-title">
                    Review &amp; Konfirmasi Pengiriman
                </h4>
                <p class="sub-header mb-0">
                    Periksa kembali data kampanye dan pratinjau tampilan email sebelum dikirim.
                </p>
            </div>

            <div class="card-body">
                <!-- Ringkasan Singkat -->
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <div class="p-2 px-3 bg-light rounded border">
                            <small class="text-muted d-block fs-11">Judul Campaign</small>
                            <span class="fw-bold text-dark text-truncate d-block">{{ $title }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 px-3 bg-light rounded border">
                            <small class="text-muted d-block fs-11">Subjek Email</small>
                            <span class="fw-bold text-dark text-truncate d-block">{{ $subject }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 px-3 bg-light rounded border">
                            <small class="text-muted d-block fs-11">Penerima</small>
                            <span class="fw-bold text-primary d-block">
                                {{ count($selectedContacts) }} Kontak
                                <span class="text-muted fw-normal fs-11">({{ $selectedPemerintahCount }} Pemerintah,
                                    {{ $selectedSwastaCount }} Swasta)</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Preview Email Mockup -->
                <div class="border rounded overflow-hidden">
                    <div class="bg-dark text-white px-3 py-2 d-flex justify-content-between align-items-center">
                        <span class="fs-13 text-light"><i class="mdi mdi-email-outline me-1"></i> Pratinjau
                            Email</span>
                        <button type="button" class="btn btn-xs btn-outline-light py-0 px-2"
                            wire:click="openPreview">
                            Layar Penuh
                        </button>
                    </div>

                    <div class="p-2 px-3 bg-white border-bottom fs-13">
                        <div class="row g-1">
                            <div class="col-sm-2 text-muted">Pengirim:</div>
                            <div class="col-sm-10 text-dark">Linkpemda Portal &lt;no-reply@linkpemda.id&gt;</div>

                            <div class="col-sm-2 text-muted">Penerima:</div>
                            <div class="col-sm-10 text-dark">
                                <strong>{{ $sampleContact->name }}</strong> ({{ $sampleContact->email }}) -
                                {{ $sampleContact->organization_name }}
                            </div>

                            <div class="col-sm-2 text-muted">Subjek:</div>
                            <div class="col-sm-10 text-dark fw-bold">{{ $subject }}</div>
                        </div>
                    </div>

                    <div class="p-2" style="background-color: #f4f6f9;">
                        <iframe srcdoc="{{ $previewHtml }}"
                            style="width: 100%; height: 560px; border: none; border-radius: 4px; background-color: #ffffff;"
                            title="Pratinjau Email">
                        </iframe>
                    </div>
                </div>
            </div>

            <!-- Footer Aksi Langkah 3 -->
            <div class="card-footer bg-light px-3 py-3 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary" wire:click="goToStep(2)">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" wire:click="saveDraft">
                            Simpan Draft
                        </button>
                        <button type="button" class="btn btn-success px-4"
                            wire:confirm="Kirim email blast ke {{ count($selectedContacts) }} kontak terpilih sekarang?"
                            wire:click="sendBlast" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="sendBlast">
                                <i class="mdi mdi-send me-1"></i> Kirim Sekarang ({{ count($selectedContacts) }})
                            </span>
                            <span wire:loading wire:target="sendBlast">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengirim...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Layar Penuh Pratinjau Email -->
    @if ($showPreview)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.6);" role="dialog">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header py-2 bg-dark text-white">
                        <h6 class="modal-title text-white mb-0">Pratinjau Email</h6>
                        <button type="button" class="btn-close btn-close-white" wire:click="closePreview"></button>
                    </div>
                    <div class="modal-body p-0" style="background: #f1f3f5;">
                        <div class="p-3">
                            <iframe srcdoc="{{ $previewHtml }}"
                                style="width: 100%; height: 680px; border: none; background-color: #f4f6f9;"
                                title="Pratinjau Email Modal">
                            </iframe>
                        </div>
                    </div>
                    <div class="modal-footer py-2 bg-white">
                        <button type="button" class="btn btn-sm btn-secondary"
                            wire:click="closePreview">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Pemilihan Template Email -->
    <div wire:ignore.self class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header py-3 bg-light border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="templateModalLabel">
                            <i class="mdi mdi-email-newsletter text-primary me-1"></i> Katalog Template Email Pelatihan
                            &amp; Bimtek
                        </h5>
                        <p class="text-muted fs-12 mb-0">Pilih template siap pakai dengan gaya komunikasi santai,
                            ramah, dan persuasif untuk penawaran kelas pelatihan atau update seputar bimtek.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-light-subtle" style="max-height: 72vh; overflow-y: auto;">
                    <!-- Toolbar Pencarian & Filter Kategori -->
                    <div class="row g-2 align-items-center mb-3">
                        <div class="col-md-7 col-lg-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="mdi mdi-magnify text-muted"></i></span>
                                <input type="text" id="templateSearchInput" class="form-control border-start-0"
                                    placeholder="Cari template (misal: early bird, tips, in-house, webinar, kalender, reminder)..."
                                    oninput="filterTemplates()">
                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                    onclick="clearTemplateSearch()" id="btnClearTemplateSearch"
                                    style="display: none;">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-6 text-md-end">
                            <span class="badge bg-white text-muted border px-2 py-1 fs-12" id="templateCountBadge">
                                Menampilkan {{ $emailTemplates->count() }} template
                            </span>
                        </div>
                    </div>

                    <!-- Kategori Filter Pills -->
                    <div class="d-flex gap-1 flex-wrap mb-3" id="templateCategoryFilters">
                        <button type="button" class="btn btn-sm btn-primary py-1 px-2 fs-12 template-cat-btn active"
                            data-cat="all" onclick="filterTemplateCategory('all', this)">
                            Semua ({{ $emailTemplates->count() }})
                        </button>
                        @foreach ($templateCategories as $cat)
                            @php $cCount = $emailTemplates->where('category', $cat)->count(); @endphp
                            @if ($cCount > 0)
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary py-1 px-2 fs-12 template-cat-btn"
                                    data-cat="{{ $cat->value }}"
                                    onclick="filterTemplateCategory('{{ $cat->value }}', this)">
                                    <i class="{{ $cat->icon() }} me-1"></i>{{ $cat->label() }}
                                    ({{ $cCount }})
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <!-- Pesan jika pencarian kosong -->
                    <div id="templateEmptyState" class="text-center py-5" style="display: none;">
                        <i class="mdi mdi-text-box-search-outline text-muted" style="font-size: 40px;"></i>
                        <p class="text-muted fs-14 mt-2 mb-1">Tidak ada template yang cocok dengan pencarian Anda.</p>
                        <button type="button" class="btn btn-sm btn-light border mt-2"
                            onclick="clearTemplateSearch()">Reset Pencarian</button>
                    </div>

                    <!-- Grid Card Template (Dynamic from Database) -->
                    <div class="row g-3" id="templateCardContainer">
                        @foreach ($emailTemplates as $tpl)
                            <div class="col-md-6 col-lg-6 template-item-col"
                                data-category="{{ $tpl->category->value }}">
                                <div class="card h-100 border shadow-sm template-card">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge {{ $tpl->category->badgeClass() }} fs-11">
                                                <i
                                                    class="{{ $tpl->category->icon() }} me-1"></i>{{ $tpl->category->label() }}
                                            </span>
                                        </div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ $tpl->name }}</h6>
                                        <p class="text-muted fs-12 mb-3 flex-grow-1 text-truncate"
                                            title="{{ $tpl->subject }}">
                                            <i class="mdi mdi-email-outline me-1"></i>{{ $tpl->subject }}
                                        </p>
                                        <div class="d-flex gap-2 mt-auto">
                                            <button type="button" class="btn btn-primary btn-sm w-100 fw-semibold"
                                                onclick="applyEmailTemplate('{{ $tpl->id }}')">
                                                <i class="mdi mdi-check-circle-outline me-1"></i> Gunakan Template Ini
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer py-2 bg-white d-flex justify-content-between align-items-center">
                    <span class="text-muted fs-12">
                        <i class="mdi mdi-information-outline me-1"></i> Template dapat disesuaikan kembali setelah
                        dimasukkan ke editor.
                    </span>
                    <button type="button" class="btn btn-sm btn-light border px-3"
                        data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Modern Segmented / Underline Stepper (matching user design) */
        .stepper-modern-wrapper {
            user-select: none;
        }

        .step-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .step-icon-active {
            background-color: #ede9fe;
            color: #5b50e6;
            border: 1.5px solid #ddd6fe;
        }

        .step-icon-active:hover {
            background-color: #e0e7ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(91, 80, 230, 0.12);
        }

        .step-icon-pending {
            background-color: #f8fafc;
            color: #94a3b8;
            border: 1.5px solid #e2e8f0;
        }

        .step-icon-pending:hover {
            background-color: #f1f5f9;
        }

        .step-progress-track {
            width: 100%;
            height: 4.5px;
            background-color: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
            position: relative;
        }

        .step-progress-fill {
            height: 100%;
            background-color: #5b50e6;
            border-radius: 3px;
            transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-text-box {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .step-text-box:hover .step-title {
            color: #5b50e6 !important;
        }

        .step-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 2px;
            line-height: 1.3;
            transition: color 0.2s ease;
        }

        .step-title.text-active {
            color: #1e293b;
        }

        .step-title.text-pending {
            color: #64748b;
        }

        .step-subtitle {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.4;
        }

        .template-card {
            transition: all 0.25s ease;
            border-radius: 10px;
            background: #ffffff;
        }

        .template-card:hover {
            transform: translateY(-2px);
            border-color: #5b50e6 !important;
            box-shadow: 0 8px 16px rgba(91, 80, 230, 0.1) !important;
        }

        .template-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .template-cat-btn {
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .template-cat-btn.active {
            box-shadow: 0 2px 6px rgba(91, 80, 230, 0.25);
        }

        .fs-10 {
            font-size: 10px;
        }

        .table-primary-subtle {
            background-color: #eff6ff !important;
        }

        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .fs-11 {
            font-size: 11px;
        }

        .fs-12 {
            font-size: 12px;
        }

        .fs-13 {
            font-size: 13px;
        }

        .fs-14 {
            font-size: 14px;
        }

        .btn-xs {
            padding: 0.15rem 0.45rem;
            font-size: 0.75rem;
            border-radius: 0.2rem;
        }

        .tox-tinymce {
            border-color: #dee2e6 !important;
            border-radius: 6px !important;
        }

        .tox .tox-menubar {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #e9ecef !important;
            padding: 4px 6px !important;
        }

        .tox .tox-mbtn {
            color: #374151 !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            border-radius: 4px !important;
        }

        .tox .tox-mbtn:hover {
            background-color: #e5e7eb !important;
        }

        .tox .tox-toolbar,
        .tox .tox-toolbar__primary,
        .tox .tox-toolbar__overflow {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e9ecef !important;
        }

        .tox .tox-tbtn svg {
            fill: #374151 !important;
        }

        .tox .tox-statusbar {
            background-color: #f8f9fa !important;
            border-top: 1px solid #e9ecef !important;
            color: #6b7280 !important;
            font-size: 12px !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        let campaignEditor = null;

        function initTinyMCE() {
            const textareaEl = document.getElementById('tinymce-editor');
            if (!textareaEl) {
                campaignEditor = null;
                return;
            }

            if (typeof tinymce === 'undefined') {
                setTimeout(initTinyMCE, 100);
                return;
            }

            if (tinymce.get('tinymce-editor')) {
                tinymce.get('tinymce-editor').remove();
            }

            tinymce.init({
                selector: '#tinymce-editor',
                license_key: 'gpl',
                placeholder: 'Tulis isi pesan email Anda di sini atau pilih template siap pakai di atas...',
                height: 550,
                base_url: '{{ asset('assets/admin/libs/tinymce') }}',
                suffix: '.min',
                menubar: 'file edit view insert format tools table',
                menu: {
                    file: {
                        title: 'File',
                        items: 'newdocument'
                    },
                    edit: {
                        title: 'Edit',
                        items: 'undo redo | cut copy paste pastetext | selectall | searchreplace'
                    },
                    view: {
                        title: 'View',
                        items: 'code visualaid visualchars visualblocks | preview fullscreen'
                    },
                    insert: {
                        title: 'Insert',
                        items: 'image link media inserttable | charmap emoticons hr | pagebreak nonbreaking anchor'
                    },
                    format: {
                        title: 'Format',
                        items: 'bold italic underline strikethrough superscript subscript codeformat | formats blockformats fontfamily fontsize align lineheight | forecolor backcolor | removeformat'
                    },
                    tools: {
                        title: 'Tools',
                        items: 'code wordcount'
                    },
                    table: {
                        title: 'Table',
                        items: 'inserttable | cell row column | tableprops deletetable'
                    }
                },
                plugins: 'accordion advlist anchor autolink autosave charmap code directionality emoticons fullscreen image insertdatetime link lists media nonbreaking pagebreak preview quickbars searchreplace table visualblocks visualchars wordcount',
                toolbar: [
                    'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | removeformat',
                    'alignleft aligncenter alignright alignjustify | lineheight | bullist numlist | outdent indent | table link image imgfullwidth components | hr emoticons | code fullscreen'
                ],
                statusbar: true,
                elementpath: true,
                font_family_formats: 'System UI=-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif; Plus Jakarta Sans="Plus Jakarta Sans",-apple-system,sans-serif; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Tahoma=tahoma,arial,helvetica,sans-serif; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva',
                font_size_formats: '8pt 9pt 10pt 11pt 12pt 13pt 14pt 16pt 18pt 20pt 24pt 28pt 32pt 36pt 48pt',
                line_height_formats: '1 1.15 1.25 1.4 1.5 1.75 2 2.5',
                emoticons_database_url: '{{ asset('assets/admin/libs/tinymce/plugins/emoticons/js/emojis.min.js') }}',
                emoticons_images_url: '{{ asset('assets/admin/libs/tinymce/plugins/emoticons/js/emojiimages.min.js') }}',
                quickbars_selection_toolbar: 'bold italic underline strikethrough | forecolor backcolor | quicklink blockquote',
                quickbars_insert_toolbar: false,
                branding: false,
                promotion: false,
                image_title: true,
                automatic_uploads: true,
                file_picker_types: 'image',
                image_advtab: true,
                image_caption: true,
                image_dimensions: true,
                image_class_list: [{
                        title: 'Normal (Ukuran Kustom)',
                        value: ''
                    },
                    {
                        title: 'Lebar Penuh 100% (Banner)',
                        value: 'w-100'
                    }
                ],
                object_resizing: 'img',
                resize_img_proportional: true,
                paste_data_images: true,
                images_upload_handler: function(blobInfo, progress) {
                    return new Promise(function(resolve, reject) {
                        const xhr = new XMLHttpRequest();
                        xhr.withCredentials = true;
                        xhr.open('POST', '{{ route('admin.campaign.upload-image') }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                        xhr.upload.onprogress = function(e) {
                            progress(e.loaded / e.total * 100);
                        };

                        xhr.onload = function() {
                            if (xhr.status === 403 || xhr.status === 419) {
                                reject({
                                    message: 'Sesi berakhir, silakan refresh halaman.',
                                    remove: true
                                });
                                return;
                            }

                            if (xhr.status < 200 || xhr.status >= 300) {
                                reject('Gagal mengupload gambar (HTTP ' + xhr.status + ')');
                                return;
                            }

                            let json;
                            try {
                                json = JSON.parse(xhr.responseText);
                            } catch (err) {
                                reject('Respon server tidak valid');
                                return;
                            }

                            if (!json || typeof json.location !== 'string') {
                                reject(json.message || 'Gagal memproses gambar');
                                return;
                            }

                            resolve(json.location);
                        };

                        xhr.onerror = function() {
                            reject('Koneksi gagal saat mengupload gambar.');
                        };

                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());

                        xhr.send(formData);
                    });
                },
                file_picker_callback: function(cb, value, meta) {
                    if (meta.filetype === 'image') {
                        const input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');

                        input.onchange = function() {
                            const file = this.files[0];
                            if (!file) return;

                            const formData = new FormData();
                            formData.append('file', file);

                            const xhr = new XMLHttpRequest();
                            xhr.withCredentials = true;
                            xhr.open('POST', '{{ route('admin.campaign.upload-image') }}');
                            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                            xhr.onload = function() {
                                if (xhr.status >= 200 && xhr.status < 300) {
                                    const json = JSON.parse(xhr.responseText);
                                    if (json && json.location) {
                                        cb(json.location, {
                                            title: file.name,
                                            alt: file.name
                                        });
                                    }
                                }
                            };
                            xhr.send(formData);
                        };

                        input.click();
                    }
                },
                content_style: `
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                    font-size: 15px;
                    line-height: 1.7;
                    color: #374151;
                    padding: 18px 24px;
                }
                .mce-content-body[data-mce-placeholder]:not(.mce-visualblocks)::before {
                    color: #94a3b8 !important;
                    font-style: normal !important;
                }
                img {
                    max-width: 100%;
                    height: auto;
                    border-radius: 4px;
                    display: inline-block;
                }
                img.w-100,
                img[style*="width: 100%"],
                img[style*="width:100%"] {
                    width: 100% !important;
                    max-width: 100% !important;
                    height: auto !important;
                    display: block;
                }
                p {
                    margin-top: 0;
                    margin-bottom: 16px;
                }
                p:last-child {
                    margin-bottom: 0;
                }
                h1 { font-size: 22px; font-weight: 700; margin-top: 0; margin-bottom: 16px; line-height: 1.3; color: #111827; }
                h2 { font-size: 19px; font-weight: 700; margin-top: 0; margin-bottom: 14px; line-height: 1.35; color: #111827; }
                h3 { font-size: 16px; font-weight: 600; margin-top: 0; margin-bottom: 12px; line-height: 1.4; color: #1f2937; }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 8px;
                    margin-bottom: 16px;
                }
                th, td {
                    border: 1px solid #d1d5db;
                    padding: 9px 12px;
                    text-align: left;
                    vertical-align: top;
                    font-size: 14px;
                    line-height: 1.6;
                }
                th {
                    background-color: #f9fafb;
                    font-weight: 600;
                    color: #111827;
                }
                table.two-column, table.three-column {
                    border: none !important;
                }
                table.two-column td, table.three-column td {
                    border: none !important;
                }
                a[style*="background-color"] {
                    text-decoration: none !important;
                }
                ul, ol {
                    margin-top: 0;
                    margin-bottom: 16px;
                    padding-left: 24px;
                }
                li {
                    margin-bottom: 6px;
                    line-height: 1.7;
                }
                a {
                    color: #2563eb;
                    text-decoration: underline;
                }
                blockquote {
                    border-left: 4px solid #10b981;
                    background-color: #f0fdf4;
                    padding: 12px 18px;
                    margin: 16px 0;
                    color: #064e3b;
                    font-style: italic;
                    border-radius: 0 6px 6px 0;
                }
                hr {
                    border: 0;
                    border-top: 1px solid #d1d5db;
                    margin: 20px 0;
                }
                code {
                    font-family: Consolas, Monaco, monospace;
                    background-color: #f3f4f6;
                    color: #be123c;
                    padding: 2px 6px;
                    border-radius: 4px;
                    font-size: 13px;
                }
                pre {
                    background-color: #1f2937;
                    color: #f9fafb;
                    padding: 14px 18px;
                    border-radius: 6px;
                    overflow-x: auto;
                    font-size: 13px;
                    line-height: 1.6;
                }
            `,
                setup: function(editor) {
                    campaignEditor = editor;

                    // Tombol Toolbar 100%
                    editor.ui.registry.addButton('imgfullwidth', {
                        text: '100%',
                        tooltip: 'Jadikan Gambar 100% Lebar Penuh',
                        onAction: function() {
                            const node = editor.selection.getNode();
                            const img = (node && node.nodeName.toLowerCase() === 'img') ? node :
                                editor.dom.select('img', node)[0];
                            if (img) {
                                editor.dom.setStyle(img, 'width', '100%');
                                editor.dom.setStyle(img, 'height', 'auto');
                                editor.dom.setStyle(img, 'display', 'block');
                                editor.dom.addClass(img, 'w-100');
                                editor.dom.setAttrib(img, 'width', null);
                                editor.dom.setAttrib(img, 'height', null);
                                editor.fire('change');
                            } else {
                                editor.windowManager.alert(
                                    'Pilih atau klik gambar di dalam editor terlebih dahulu.');
                            }
                        }
                    });

                    // Tombol Toolbar Reset
                    editor.ui.registry.addButton('imgresetwidth', {
                        text: 'Auto',
                        tooltip: 'Reset ke Ukuran Asli',
                        onAction: function() {
                            const node = editor.selection.getNode();
                            const img = (node && node.nodeName.toLowerCase() === 'img') ? node :
                                editor.dom.select('img', node)[0];
                            if (img) {
                                editor.dom.setStyle(img, 'width', null);
                                editor.dom.setStyle(img, 'height', null);
                                editor.dom.setStyle(img, 'display', null);
                                editor.dom.removeClass(img, 'w-100');
                                editor.dom.setAttrib(img, 'width', null);
                                editor.dom.setAttrib(img, 'height', null);
                                editor.fire('change');
                            }
                        }
                    });

                    // Tombol Menu Komponen Desain & Card
                    editor.ui.registry.addMenuButton('components', {
                        text: 'Komponen',
                        icon: 'table',
                        tooltip: 'Sisipkan Komponen Desain, Card, Tombol, dan Tabel',
                        fetch: function(callback) {
                            const items = [{
                                    type: 'nestedmenuitem',
                                    text: 'Tata Letak & Card Kolom',
                                    icon: 'table-insert-column-after',
                                    getSubmenuItems: function() {
                                        return [{
                                                type: 'menuitem',
                                                text: 'Card 2 Kolom (Berdampingan & Berjarak)',
                                                onAction: function() {
                                                    const html = `
<table class="two-column" style="width: 100%; border-collapse: separate; border-spacing: 0; border: none; margin: 16px 0;">
    <tr>
        <td style="width: 50%; vertical-align: top; padding: 0 8px 0 0; border: none;">
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 18px;">
                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 15px; font-weight: 700;">Judul Card 1</h4>
                <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #475569;">Tuliskan isi materi, poin penawaran, atau penjelasan kolom pertama di sini.</p>
            </div>
        </td>
        <td style="width: 50%; vertical-align: top; padding: 0 0 0 8px; border: none;">
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 18px;">
                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 15px; font-weight: 700;">Judul Card 2</h4>
                <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #475569;">Tuliskan isi materi, poin penawaran, atau penjelasan kolom kedua di sini.</p>
            </div>
        </td>
    </tr>
</table>
<p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            },
                                            {
                                                type: 'menuitem',
                                                text: 'Card 3 Kolom (Fitur / Layanan)',
                                                onAction: function() {
                                                    const html = `
<table class="three-column" style="width: 100%; border-collapse: separate; border-spacing: 0; border: none; margin: 16px 0;">
    <tr>
        <td style="width: 33.33%; vertical-align: top; padding: 0 6px 0 0; border: none;">
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; text-align: center;">
                <div style="font-size: 24px; margin-bottom: 6px;">🏛️</div>
                <h4 style="margin: 0 0 6px 0; color: #1e3a8a; font-size: 14px; font-weight: 700;">Layanan A</h4>
                <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #475569;">Keterangan singkat layanan pertama.</p>
            </div>
        </td>
        <td style="width: 33.33%; vertical-align: top; padding: 0 3px 0 3px; border: none;">
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; text-align: center;">
                <div style="font-size: 24px; margin-bottom: 6px;">📊</div>
                <h4 style="margin: 0 0 6px 0; color: #1e3a8a; font-size: 14px; font-weight: 700;">Layanan B</h4>
                <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #475569;">Keterangan singkat layanan kedua.</p>
            </div>
        </td>
        <td style="width: 33.33%; vertical-align: top; padding: 0 0 0 6px; border: none;">
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; text-align: center;">
                <div style="font-size: 24px; margin-bottom: 6px;">⚡</div>
                <h4 style="margin: 0 0 6px 0; color: #1e3a8a; font-size: 14px; font-weight: 700;">Layanan C</h4>
                <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #475569;">Keterangan singkat layanan ketiga.</p>
            </div>
        </td>
    </tr>
</table>
<p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            },
                                            {
                                                type: 'menuitem',
                                                text: 'Card 1 Kolom Penuh (Hero / Kotak Utama)',
                                                onAction: function() {
                                                    const html = `
<div style="background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 20px 24px; margin: 18px 0;">
    <h3 style="margin: 0 0 10px 0; color: #0f172a; font-size: 18px; font-weight: 700;">Judul Pokok Bahasan</h3>
    <p style="margin: 0; font-size: 14px; line-height: 1.7; color: #334155;">Tuliskan ringkasan pokok materi atau pengantar penting program di sini. Kotak ini sangat cocok untuk menarik perhatian penerima di awal email.</p>
</div>
<p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            }
                                        ];
                                    }
                                },
                                {
                                    type: 'nestedmenuitem',
                                    text: 'Kotak Catatan & Sorotan',
                                    icon: 'info',
                                    getSubmenuItems: function() {
                                        return [{
                                                type: 'menuitem',
                                                text: 'Info / Pengumuman (Biru)',
                                                onAction: function() {
                                                    const html = `
<div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 0 8px 8px 0; padding: 14px 18px; margin: 16px 0;">
    <strong style="color: #1d4ed8; font-size: 14px; display: block; margin-bottom: 4px;">ℹ️ Informasi / Pengumuman</strong>
    <p style="margin: 0; font-size: 14px; color: #1e40af; line-height: 1.6;">Tuliskan catatan penting, pengumuman, atau sosialisasi materi di sini.</p>
</div>
<p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            },
                                            {
                                                type: 'menuitem',
                                                text: 'Peringatan / Batas Waktu (Kuning)',
                                                onAction: function() {
                                                    const html = `
<div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 14px 18px; margin: 16px 0;">
    <strong style="color: #b45309; font-size: 14px; display: block; margin-bottom: 4px;">⚠️ Perhatian / Batas Waktu</strong>
    <p style="margin: 0; font-size: 14px; color: #92400e; line-height: 1.6;">Tuliskan hal-hal yang perlu diperhatikan khusus atau batas waktu penyampaian data.</p>
</div>
<p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            },
                                            {
                                                type: 'menuitem',
                                                text: 'Sukses / Rekomendasi (Hijau)',
                                                onAction: function() {
                                                    const html = `
<div style="background-color: #f0fdf4; border-left: 4px solid #10b981; border-radius: 0 8px 8px 0; padding: 14px 18px; margin: 16px 0;">
    <strong style="color: #047857; font-size: 14px; display: block; margin-bottom: 4px;">✅ Manfaat & Rekomendasi</strong>
    <p style="margin: 0; font-size: 14px; color: #065f46; line-height: 1.6;">Tuliskan manfaat langsung, poin rekomendasi, atau pencapaian program di sini.</p>
</div>
<p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            },
                                            {
                                                type: 'menuitem',
                                                text: 'Kutipan Pimpinan / Regulasi (Quote Box)',
                                                onAction: function() {
                                                    const html = `
<div style="background-color: #f9fafb; border-left: 4px solid #6b7280; border-radius: 0 8px 8px 0; padding: 16px 20px; margin: 18px 0; font-style: italic;">
    <p style="margin: 0 0 8px 0; font-size: 14px; color: #374151; line-height: 1.7;">"Tuliskan pernyataan penting pimpinan, amanat regulasi, atau kutipan peraturan perundang-undangan di sini."</p>
    <small style="display: block; font-style: normal; font-weight: 600; color: #4b5563;">— Nama Pejabat / Dasar Regulasi</small>
</div>
<p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            }
                                        ];
                                    }
                                },
                                {
                                    type: 'nestedmenuitem',
                                    text: 'Tombol Aksi (Call-to-Action)',
                                    icon: 'bookmark',
                                    getSubmenuItems: function() {
                                        return [{
                                                type: 'menuitem',
                                                text: 'Tombol Utama (Hijau)',
                                                onAction: function() {
                                                    const html = `
<table role="presentation" style="margin: 22px auto; border-collapse: separate; border: none;">
    <tr>
        <td style="border-radius: 6px; background-color: #0d5c31; text-align: center; padding: 0; border: none;">
            <a href="https://" target="_blank" style="background-color: #0d5c31; border: 1px solid #0d5c31; border-radius: 6px; color: #ffffff !important; display: inline-block; font-size: 14px; font-weight: 600; padding: 12px 28px; text-decoration: none;">Pelajari Selengkapnya &rarr;</a>
        </td>
    </tr>
</table>
<p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            },
                                            {
                                                type: 'menuitem',
                                                text: 'Tombol Layanan (Biru)',
                                                onAction: function() {
                                                    const html = `
                                                                <table role="presentation" style="margin: 22px auto; border-collapse: separate; border: none;">
                                                                    <tr>
                                                                        <td style="border-radius: 6px; background-color: #2563eb; text-align: center; padding: 0; border: none;">
                                                                            <a href="https://" target="_blank" style="background-color: #2563eb; border: 1px solid #2563eb; border-radius: 6px; color: #ffffff !important; display: inline-block; font-size: 14px; font-weight: 600; padding: 12px 28px; text-decoration: none;">Akses Portal Layanan &rarr;</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            }
                                        ];
                                    }
                                },
                                {
                                    type: 'nestedmenuitem',
                                    text: 'Format Khusus & Dokumen',
                                    icon: 'table',
                                    getSubmenuItems: function() {
                                        return [{
                                                type: 'menuitem',
                                                text: 'Tabel Agenda / Jadwal Kegiatan',
                                                onAction: function() {
                                                    const html = `
                                                                <table style="width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px;">
                                                                    <thead>
                                                                        <tr style="background-color: #f1f5f9;">
                                                                            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; font-weight: 600; color: #0f172a; width: 150px;">Waktu / Tanggal</th>
                                                                            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; font-weight: 600; color: #0f172a;">Agenda Kegiatan</th>
                                                                            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; font-weight: 600; color: #0f172a;">Keterangan / Tempat</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px; font-weight: 600;">Hari ke-1 (09.00 WIB)</td>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Pembukaan & Sosialisasi Terpadu</td>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Online via Zoom / Ruang Rapat</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px; font-weight: 600;">Hari ke-2 (09.00 WIB)</td>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Bimbingan Teknis & Praktik Terarah</td>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Tim Teknis Pendamping</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            },
                                            {
                                                type: 'menuitem',
                                                text: 'Tabel Data Modern',
                                                onAction: function() {
                                                    const html = `
                                                                <table style="width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px;">
                                                                    <thead>
                                                                        <tr style="background-color: #f1f5f9;">
                                                                            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: center; font-weight: 600; color: #0f172a; width: 60px;">No</th>
                                                                            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; font-weight: 600; color: #0f172a;">Program / Layanan</th>
                                                                            <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; font-weight: 600; color: #0f172a;">Keterangan</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: center;">1</td>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Integrasi Portal Pemerintah</td>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Fasilitasi layanan informasi terpadu</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: center;">2</td>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Pendampingan Sosialisasi</td>
                                                                            <td style="border: 1px solid #cbd5e1; padding: 10px 14px;">Dukungan sosialisasi & implementasi</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            },
                                            {
                                                type: 'menuitem',
                                                text: 'Daftar Poin Keunggulan (Ceklis)',
                                                onAction: function() {
                                                    const html = `
                                                                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px; margin: 16px 0;">
                                                                    <h4 style="margin: 0 0 12px 0; color: #0f172a; font-size: 15px; font-weight: 700;">Poin Utama & Manfaat Program:</h4>
                                                                    <div style="margin-bottom: 8px; font-size: 14px; color: #334155; line-height: 1.6;">
                                                                        <span style="color: #10b981; font-weight: bold; margin-right: 8px;">✔</span>
                                                                        <strong>Akses Terpusat:</strong> Memudahkan integrasi seluruh data dalam satu pintu layanan.
                                                                    </div>
                                                                    <div style="margin-bottom: 8px; font-size: 14px; color: #334155; line-height: 1.6;">
                                                                        <span style="color: #10b981; font-weight: bold; margin-right: 8px;">✔</span>
                                                                        <strong>Efisiensi Waktu:</strong> Mengurangi proses birokrasi berulang dengan otomatisasi sistem.
                                                                    </div>
                                                                    <div style="margin-bottom: 0; font-size: 14px; color: #334155; line-height: 1.6;">
                                                                        <span style="color: #10b981; font-weight: bold; margin-right: 8px;">✔</span>
                                                                        <strong>Dukungan Penuh:</strong> Didampingi langsung oleh tenaga ahli yang berpengalaman.
                                                                    </div>
                                                                </div>
                                                                <p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            },
                                            {
                                                type: 'menuitem',
                                                text: 'Blok Kontak & Helpdesk Layanan',
                                                onAction: function() {
                                                    const html = `
                                                                <div style="background-color: #f1f5f9; border-radius: 8px; padding: 18px 22px; margin: 20px 0; border: 1px solid #e2e8f0;">
                                                                    <h4 style="margin: 0 0 8px 0; color: #0f172a; font-size: 15px; font-weight: 700;">Layanan Bantuan & Informasi Lebih Lanjut</h4>
                                                                    <p style="margin: 0 0 10px 0; font-size: 14px; color: #475569; line-height: 1.5;">Apabila Bapak/Ibu membutuhkan koordinasi atau konsultasi teknis lebih lanjut, silakan hubungi tim kami:</p>
                                                                    <div style="font-size: 14px; color: #334155; line-height: 1.8;">
                                                                        <div>📞 <strong>WhatsApp / Telp:</strong> +62 812-xxxx-xxxx</div>
                                                                        <div>✉️ <strong>Email Resmi:</strong> info@pemerintah.go.id</div>
                                                                        <div>🕒 <strong>Jam Operasional:</strong> Senin – Jumat (08.00 – 16.00 WIB)</div>
                                                                    </div>
                                                                </div>
                                                                <p></p>`;
                                                    editor.insertContent(html);
                                                }
                                            }
                                        ];
                                    }
                                }
                            ];
                            callback(items);
                        }
                    });

                    // Context Toolbar saat gambar diklik
                    editor.ui.registry.addContextToolbar('image_actions', {
                        predicate: function(node) {
                            return node && node.nodeName.toLowerCase() === 'img';
                        },
                        items: 'imgfullwidth imgresetwidth | alignleft aligncenter alignright | image',
                        position: 'node',
                        scope: 'node'
                    });

                    editor.on('change keyup NodeChange', function() {
                        if (window.Livewire) {
                            @this.set('body_content', editor.getContent(), false);
                        }
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initTinyMCE);
        document.addEventListener('livewire:navigated', initTinyMCE);

        window.getEditorContent = function() {
            if (campaignEditor) {
                return campaignEditor.getContent();
            }
            const el = document.getElementById('tinymce-editor');
            return el ? el.value : '';
        };

        window.handleGoToStep2 = function() {
            const content = getEditorContent();
            @this.call('goToStep2', content);
        };

        window.handleStepClick = function(step) {
            const content = getEditorContent();
            @this.call('goToStep', step, content);
        };

        window.handleSaveDraft = function() {
            const content = getEditorContent();
            @this.call('saveDraft', content);
        };

        window.insertVariable = function(tag) {
            if (campaignEditor) {
                campaignEditor.insertContent(tag);
                if (window.Livewire) {
                    @this.set('body_content', campaignEditor.getContent(), false);
                }
            }
        };

        const emailTemplates = {
            @foreach ($emailTemplates as $tpl)
                @json($tpl->id): {!! json_encode($tpl->body_content) !!},
            @endforeach
        };

        const defaultSubjects = {
            @foreach ($emailTemplates as $tpl)
                @json($tpl->id): {!! json_encode($tpl->subject) !!},
            @endforeach
        };

        let currentTemplateCategory = 'all';

        window.filterTemplateCategory = function(category, btnEl) {
            currentTemplateCategory = category;

            document.querySelectorAll('.template-cat-btn').forEach(btn => {
                btn.classList.remove('btn-primary', 'active');
                btn.classList.add('btn-outline-secondary');
            });
            btnEl.classList.remove('btn-outline-secondary');
            btnEl.classList.add('btn-primary', 'active');

            filterTemplates();
        };

        window.clearTemplateSearch = function() {
            const input = document.getElementById('templateSearchInput');
            if (input) {
                input.value = '';
                const btnClear = document.getElementById('btnClearTemplateSearch');
                if (btnClear) btnClear.style.display = 'none';
                filterTemplates();
            }
        };

        window.filterTemplates = function() {
            const input = document.getElementById('templateSearchInput');
            const query = input ? input.value.toLowerCase().trim() : '';
            const btnClear = document.getElementById('btnClearTemplateSearch');
            if (btnClear) {
                btnClear.style.display = query.length > 0 ? 'inline-block' : 'none';
            }

            const items = document.querySelectorAll('.template-item-col');
            let visibleCount = 0;

            items.forEach(item => {
                const itemCat = item.getAttribute('data-category');
                const matchesCategory = (currentTemplateCategory === 'all' || itemCat ===
                    currentTemplateCategory);
                const textContent = item.textContent.toLowerCase();
                const matchesSearch = (!query || textContent.includes(query));

                if (matchesCategory && matchesSearch) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            const emptyState = document.getElementById('templateEmptyState');
            if (emptyState) {
                emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            const badge = document.getElementById('templateCountBadge');
            if (badge) {
                badge.textContent = `Menampilkan ${visibleCount} template`;
            }
        };

        window.applyEmailTemplate = function(templateKey) {
            if (!campaignEditor) return;

            const currentContent = campaignEditor.getContent().trim();
            if (currentContent !== '' && currentContent !== '<p></p>') {
                if (!confirm(
                        'Editor saat ini sudah memiliki konten. Apakah Anda yakin ingin mengganti isi email dengan template ini?'
                    )) {
                    return;
                }
            }

            const html = emailTemplates[templateKey] || '';
            campaignEditor.setContent(html);
            campaignEditor.fire('change');

            if (window.Livewire) {
                @this.set('body_content', html, false);

                // Auto-fill subject if currently empty
                const subjectVal = @this.get('subject');
                if (!subjectVal || subjectVal.trim() === '') {
                    @this.set('subject', defaultSubjects[templateKey] || '', false);
                }
            }

            // Close Modal
            const modalEl = document.getElementById('templateModal');
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modalInstance.hide();
            }
        };

        const templateModalEl = document.getElementById('templateModal');
        if (templateModalEl) {
            templateModalEl.addEventListener('show.bs.modal', function() {
                if (typeof clearTemplateSearch === 'function') {
                    clearTemplateSearch();
                }
                const firstFilterBtn = document.querySelector('.template-cat-btn[data-cat="all"]');
                if (firstFilterBtn) {
                    filterTemplateCategory('all', firstFilterBtn);
                }
            });
        }
    </script>
@endpush
