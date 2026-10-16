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
                        <li class="breadcrumb-item"><a href="{{ route('admin.campaign.index') }}" wire:navigate>Email Blast</a></li>
                        <li class="breadcrumb-item active">{{ $isEditing ? 'Ubah' : 'Baru' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Stepper Navigation -->
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body p-2">
            <div class="row g-2 align-items-center">
                <!-- Step 1 -->
                <div class="col-md-4">
                    <button type="button" onclick="handleStepClick(1)"
                        class="btn w-100 text-start p-2 rounded d-flex align-items-center gap-2 border-0 {{ $currentStep === 1 ? 'bg-primary-subtle text-primary fw-bold' : ($currentStep > 1 ? 'text-success' : 'text-muted') }}">
                        <div class="wizard-circle {{ $currentStep === 1 ? 'bg-primary text-white' : ($currentStep > 1 ? 'bg-success text-white' : 'bg-light text-secondary border') }}">
                            @if ($currentStep > 1) <i class="mdi mdi-check"></i> @else 1 @endif
                        </div>
                        <span class="fs-14 fw-semibold {{ $currentStep === 1 ? 'text-primary' : 'text-dark' }}">1. Konten Email</span>
                    </button>
                </div>

                <!-- Step 2 -->
                <div class="col-md-4">
                    <button type="button" onclick="handleStepClick(2)"
                        class="btn w-100 text-start p-2 rounded d-flex align-items-center gap-2 border-0 {{ $currentStep === 2 ? 'bg-primary-subtle text-primary fw-bold' : ($currentStep > 2 ? 'text-success' : 'text-muted') }}">
                        <div class="wizard-circle {{ $currentStep === 2 ? 'bg-primary text-white' : ($currentStep > 2 ? 'bg-success text-white' : 'bg-light text-secondary border') }}">
                            @if ($currentStep > 2) <i class="mdi mdi-check"></i> @else 2 @endif
                        </div>
                        <span class="fs-14 fw-semibold {{ $currentStep === 2 ? 'text-primary' : 'text-dark' }}">
                            2. Target Penerima
                            @if (count($selectedContacts) > 0)
                                <span class="badge bg-primary ms-1 fs-11">{{ count($selectedContacts) }}</span>
                            @endif
                        </span>
                    </button>
                </div>

                <!-- Step 3 -->
                <div class="col-md-4">
                    <button type="button" onclick="handleStepClick(3)"
                        class="btn w-100 text-start p-2 rounded d-flex align-items-center gap-2 border-0 {{ $currentStep === 3 ? 'bg-primary-subtle text-primary fw-bold' : 'text-muted' }}">
                        <div class="wizard-circle {{ $currentStep === 3 ? 'bg-primary text-white' : 'bg-light text-secondary border' }}">
                            3
                        </div>
                        <span class="fs-14 fw-semibold {{ $currentStep === 3 ? 'text-primary' : 'text-dark' }}">3. Review & Kirim</span>
                    </button>
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
            <div class="card-body">
                <div class="row g-3">
                    <!-- Judul Campaign -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Judul Campaign <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                            wire:model="title" placeholder="Nama campaign">
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Subjek Email -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Subjek Email <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror"
                            wire:model="subject" placeholder="Subjek email penerima">
                        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Editor Pesan Email -->
                    <div class="col-12 mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <label class="form-label fw-bold mb-0">Isi Email <span class="text-danger">*</span></label>

                            <!-- Quick Action Buttons -->
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                <button type="button" class="btn btn-xs btn-light border py-1 px-2" onclick="insertVariable('{nama}')" title="Nama Kontak">+ {nama}</button>
                                <button type="button" class="btn btn-xs btn-light border py-1 px-2" onclick="insertVariable('{instansi}')" title="Instansi">+ {instansi}</button>
                                <button type="button" class="btn btn-xs btn-light border py-1 px-2" onclick="insertVariable('{jabatan}')" title="Jabatan">+ {jabatan}</button>
                                <button type="button" class="btn btn-xs btn-light border py-1 px-2" onclick="insertVariable('{email}')" title="Email">+ {email}</button>

                                <span class="text-muted mx-1">|</span>

                                <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2" onclick="insertTableSample()" title="Sisipkan Tabel">
                                    <i class="mdi mdi-table me-1"></i>+ Tabel
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-info py-1 px-2" onclick="insertTwoColumnSample()" title="Sisipkan 2 Kolom">
                                    <i class="mdi mdi-view-column-outline me-1"></i>+ 2 Kolom
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" onclick="setImageFullWidth()" title="Atur Gambar Menjadi 100% Lebar Penuh">
                                    <i class="mdi mdi-arrow-expand-horizontal me-1"></i>Gambar 100%
                                </button>
                            </div>
                        </div>

                        <!-- TinyMCE Wrapper -->
                        <div wire:ignore class="tinymce-wrapper border rounded">
                            <textarea id="tinymce-editor">{!! $body_content !!}</textarea>
                        </div>
                        @error('body_content')
                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Footer Aksi Langkah 1 -->
            <div class="card-footer bg-light px-3 py-3 border-top">
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
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h4 class="header-title mb-0">Target Penerima</h4>
                    <span class="badge bg-primary px-3 py-2 fs-12">
                        {{ count($selectedContacts) }} dipilih
                        @if (count($selectedContacts) > 0)
                            (Pemerintah: {{ $selectedPemerintahCount }}, Swasta: {{ $selectedSwastaCount }})
                        @endif
                    </span>
                </div>

                <!-- Filter & Search Toolbar -->
                <div class="row g-2 align-items-center mb-3">
                    <div class="col-md-5 col-lg-4">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <button type="button" class="btn {{ $contactCategory === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                wire:click="$set('contactCategory', 'all')">
                                Semua ({{ $allContactsCount }})
                            </button>
                            <button type="button" class="btn {{ $contactCategory === 'pemerintah' ? 'btn-info text-white' : 'btn-outline-info' }}"
                                wire:click="$set('contactCategory', 'pemerintah')">
                                Pemerintah ({{ $pemdaCount }})
                            </button>
                            <button type="button" class="btn {{ $contactCategory === 'swasta' ? 'btn-warning text-dark' : 'btn-outline-warning' }}"
                                wire:click="$set('contactCategory', 'swasta')">
                                Swasta ({{ $swastaCount }})
                            </button>
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-magnify"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Cari nama, instansi, email..."
                                wire:model.live.debounce.250ms="searchContact">
                            @if ($searchContact)
                                <button class="btn btn-light border" type="button" wire:click="$set('searchContact', '')">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3 col-lg-3 text-md-end">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Pilih Cepat
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item py-1 fs-13" href="javascript:void(0)" wire:click="selectAll('all')">
                                        Pilih Semua ({{ $allContactsCount }})
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1 fs-13" href="javascript:void(0)" wire:click="selectAll('pemerintah')">
                                        Pilih Semua Pemerintah ({{ $pemdaCount }})
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1 fs-13" href="javascript:void(0)" wire:click="selectAll('swasta')">
                                        Pilih Semua Swasta ({{ $swastaCount }})
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-1 fs-13 text-danger" href="javascript:void(0)" wire:click="deselectAll('all')">
                                        Kosongkan Pilihan
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                @error('selectedContacts')
                    <div class="alert alert-danger py-2 px-3 mb-3 fs-13">
                        <i class="mdi mdi-alert-circle me-1"></i> {{ $message }}
                    </div>
                @enderror

                <!-- Tabel Kontak -->
                <div class="table-responsive border rounded" style="max-height: 520px; overflow-y: auto;">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 45px;" class="text-center">
                                    <input class="form-check-input" type="checkbox" wire:click="toggleSelectVisible"
                                        {{ $isAllVisibleSelected ? 'checked' : '' }} title="Pilih Semua yang Ditampilkan">
                                </th>
                                <th>Kontak</th>
                                <th>Instansi</th>
                                <th>Jabatan</th>
                                <th>Telepon</th>
                                <th class="text-center" style="width: 100px;">Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contacts as $contact)
                                <tr class="{{ in_array($contact->id, $selectedContacts) ? 'table-primary-subtle' : '' }}">
                                    <td class="text-center">
                                        <input class="form-check-input" type="checkbox" value="{{ $contact->id }}"
                                            wire:model.live="selectedContacts" id="c_{{ $contact->id }}">
                                    </td>
                                    <td>
                                        <label class="form-check-label fw-bold d-block text-dark mb-0 cursor-pointer" for="c_{{ $contact->id }}" style="cursor: pointer;">
                                            {{ $contact->name }}
                                        </label>
                                        <small class="text-muted">{{ $contact->email }}</small>
                                    </td>
                                    <td>
                                        <span class="text-dark fs-13">{{ $contact->organization_name }}</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary fs-13">{{ $contact->position ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted fs-13">{{ $contact->phone ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $contact->category === \App\Enums\ContactCategory::Pemerintah ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }} px-2 py-1 fs-11">
                                            {{ $contact->category === \App\Enums\ContactCategory::Pemerintah ? 'Pemerintah' : 'Swasta' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Tidak ada data kontak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Aksi Langkah 2 -->
            <div class="card-footer bg-light px-3 py-3 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary" wire:click="goToStep(1)">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" wire:click="saveDraft">
                            Simpan Draft
                        </button>
                        <button type="button" class="btn btn-primary px-3" wire:click="goToStep3" {{ count($selectedContacts) === 0 ? 'disabled' : '' }}>
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
                                <span class="text-muted fw-normal fs-11">({{ $selectedPemerintahCount }} Pemerintah, {{ $selectedSwastaCount }} Swasta)</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Preview Email Mockup -->
                <div class="border rounded overflow-hidden">
                    <div class="bg-dark text-white px-3 py-2 d-flex justify-content-between align-items-center">
                        <span class="fs-13 text-light"><i class="mdi mdi-email-outline me-1"></i> Pratinjau Email</span>
                        <button type="button" class="btn btn-xs btn-outline-light py-0 px-2" wire:click="openPreview">
                            Layar Penuh
                        </button>
                    </div>

                    <div class="p-2 px-3 bg-white border-bottom fs-13">
                        <div class="row g-1">
                            <div class="col-sm-2 text-muted">Pengirim:</div>
                            <div class="col-sm-10 text-dark">Linkpemda Portal &lt;no-reply@linkpemda.id&gt;</div>

                            <div class="col-sm-2 text-muted">Penerima:</div>
                            <div class="col-sm-10 text-dark">
                                <strong>{{ $sampleContact->name }}</strong> ({{ $sampleContact->email }}) - {{ $sampleContact->organization_name }}
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
                            onclick="if(confirm('Kirim email blast ke {{ count($selectedContacts) }} kontak terpilih sekarang?')) { $wire.sendBlast(); }">
                            <i class="mdi mdi-send me-1"></i> Kirim Sekarang ({{ count($selectedContacts) }})
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
                        <button type="button" class="btn btn-sm btn-secondary" wire:click="closePreview">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .wizard-circle {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0;
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
        border-radius: 4px !important;
    }

    .tox .tox-toolbar,
    .tox .tox-toolbar__primary {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    .tox .tox-tbtn svg {
        fill: #495057 !important;
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
            height: 480,
            base_url: '{{ asset("assets/admin/libs/tinymce") }}',
            suffix: '.min',
            menubar: false,
            statusbar: false,
            plugins: 'table link lists code visualblocks image',
            toolbar: 'undo redo | blocks | bold italic underline forecolor | alignleft aligncenter alignright | bullist numlist | table | link image imgfullwidth code | removeformat',
            branding: false,
            promotion: false,
            image_title: true,
            automatic_uploads: true,
            file_picker_types: 'image',
            image_advtab: true,
            image_caption: true,
            image_dimensions: true,
            image_class_list: [
                { title: 'Normal (Ukuran Kustom)', value: '' },
                { title: 'Lebar Penuh 100% (Banner)', value: 'w-100' }
            ],
            object_resizing: 'img',
            resize_img_proportional: true,
            paste_data_images: true,
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function (resolve, reject) {
                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;
                    xhr.open('POST', '{{ route("admin.campaign.upload-image") }}');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                    xhr.upload.onprogress = function (e) {
                        progress(e.loaded / e.total * 100);
                    };

                    xhr.onload = function () {
                        if (xhr.status === 403 || xhr.status === 419) {
                            reject({ message: 'Sesi berakhir, silakan refresh halaman.', remove: true });
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

                    xhr.onerror = function () {
                        reject('Koneksi gagal saat mengupload gambar.');
                    };

                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());

                    xhr.send(formData);
                });
            },
            file_picker_callback: function (cb, value, meta) {
                if (meta.filetype === 'image') {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    input.onchange = function () {
                        const file = this.files[0];
                        if (!file) return;

                        const formData = new FormData();
                        formData.append('file', file);

                        const xhr = new XMLHttpRequest();
                        xhr.withCredentials = true;
                        xhr.open('POST', '{{ route("admin.campaign.upload-image") }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                        xhr.onload = function () {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                const json = JSON.parse(xhr.responseText);
                                if (json && json.location) {
                                    cb(json.location, { title: file.name, alt: file.name });
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
                table.two-column {
                    border: none !important;
                }
                table.two-column td {
                    border: none !important;
                    padding: 8px 10px;
                    width: 50%;
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
                    border-left: 4px solid #d1d5db;
                    padding-left: 16px;
                    margin: 0 0 16px 0;
                    color: #4b5563;
                    font-style: italic;
                }
            `,
            setup: function(editor) {
                campaignEditor = editor;

                // Tombol Toolbar 100%
                editor.ui.registry.addButton('imgfullwidth', {
                    text: '100%',
                    tooltip: 'Jadikan Gambar 100% Lebar Penuh',
                    onAction: function () {
                        const node = editor.selection.getNode();
                        const img = (node && node.nodeName.toLowerCase() === 'img') ? node : editor.dom.select('img', node)[0];
                        if (img) {
                            editor.dom.setStyle(img, 'width', '100%');
                            editor.dom.setStyle(img, 'height', 'auto');
                            editor.dom.setStyle(img, 'display', 'block');
                            editor.dom.addClass(img, 'w-100');
                            editor.dom.setAttrib(img, 'width', null);
                            editor.dom.setAttrib(img, 'height', null);
                            editor.fire('change');
                        } else {
                            editor.windowManager.alert('Pilih atau klik gambar di dalam editor terlebih dahulu.');
                        }
                    }
                });

                // Tombol Toolbar Reset
                editor.ui.registry.addButton('imgresetwidth', {
                    text: 'Auto',
                    tooltip: 'Reset ke Ukuran Asli',
                    onAction: function () {
                        const node = editor.selection.getNode();
                        const img = (node && node.nodeName.toLowerCase() === 'img') ? node : editor.dom.select('img', node)[0];
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

                // Context Toolbar saat gambar diklik
                editor.ui.registry.addContextToolbar('image_actions', {
                    predicate: function (node) {
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

    window.setImageFullWidth = function() {
        if (!campaignEditor) return;
        const node = campaignEditor.selection.getNode();
        let img = (node && node.nodeName.toLowerCase() === 'img') ? node : campaignEditor.dom.select('img', node)[0];
        if (!img) {
            const allImgs = campaignEditor.dom.select('img');
            if (allImgs.length === 1) {
                img = allImgs[0];
            }
        }

        if (img) {
            campaignEditor.dom.setStyle(img, 'width', '100%');
            campaignEditor.dom.setStyle(img, 'height', 'auto');
            campaignEditor.dom.setStyle(img, 'display', 'block');
            campaignEditor.dom.addClass(img, 'w-100');
            campaignEditor.dom.setAttrib(img, 'width', null);
            campaignEditor.dom.setAttrib(img, 'height', null);
            campaignEditor.fire('change');
            if (window.Livewire) {
                @this.set('body_content', campaignEditor.getContent(), false);
            }
        } else {
            alert('Silakan klik pada gambar di dalam editor terlebih dahulu.');
        }
    };

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

    window.insertTableSample = function() {
        if (campaignEditor) {
            const sampleTable = `
            <table style="width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 16px;">
                <thead>
                    <tr style="background-color: #f9fafb;">
                        <th style="border: 1px solid #d1d5db; padding: 9px 12px; text-align: left;">No</th>
                        <th style="border: 1px solid #d1d5db; padding: 9px 12px; text-align: left;">Program / Layanan</th>
                        <th style="border: 1px solid #d1d5db; padding: 9px 12px; text-align: left;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #d1d5db; padding: 9px 12px;">1</td>
                        <td style="border: 1px solid #d1d5db; padding: 9px 12px;">Integrasi Portal Pemerintah</td>
                        <td style="border: 1px solid #d1d5db; padding: 9px 12px;">Fasilitasi layanan informasi terpadu</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #d1d5db; padding: 9px 12px;">2</td>
                        <td style="border: 1px solid #d1d5db; padding: 9px 12px;">Pendampingan Teknis</td>
                        <td style="border: 1px solid #d1d5db; padding: 9px 12px;">Dukungan sosialisasi & implementasi menyeluruh</td>
                    </tr>
                </tbody>
            </table>
            <p></p>`;
            campaignEditor.insertContent(sampleTable);
            if (window.Livewire) {
                @this.set('body_content', campaignEditor.getContent(), false);
            }
        }
    };

    window.insertTwoColumnSample = function() {
        if (campaignEditor) {
            const sample2Col = `
            <table class="two-column" style="width: 100%; border-collapse: collapse; border: none; margin-top: 8px; margin-bottom: 16px;">
                <tr>
                    <td style="width: 50%; vertical-align: top; padding: 8px 10px; border: none;">
                        <div style="background-color: #f9fafb; padding: 14px; border-radius: 6px; border: 1px solid #e5e7eb;">
                            <strong style="color: #1e3a8a; display: block; margin-bottom: 6px;">Poin Keunggulan A</strong>
                            <p style="margin: 0; font-size: 14px;">Tuliskan deskripsi atau poin penawaran pertama di sini.</p>
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding: 8px 10px; border: none;">
                        <div style="background-color: #f9fafb; padding: 14px; border-radius: 6px; border: 1px solid #e5e7eb;">
                            <strong style="color: #1e3a8a; display: block; margin-bottom: 6px;">Poin Keunggulan B</strong>
                            <p style="margin: 0; font-size: 14px;">Tuliskan deskripsi atau poin penawaran kedua di sini.</p>
                        </div>
                    </td>
                </tr>
            </table>
            <p></p>`;
            campaignEditor.insertContent(sample2Col);
            if (window.Livewire) {
                @this.set('body_content', campaignEditor.getContent(), false);
            }
        }
    };
</script>
@endpush
