<div>
    <!-- Page Title & Breadcrumb -->
    <div class="py-3 py-lg-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.template.index') }}" wire:navigate class="btn btn-sm btn-light border"
                        title="Kembali ke Daftar">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>
                    <h4 class="page-title mb-0">
                        {{ $isEditing ? 'Ubah Template: ' . $template->name : 'Tambah Template Email Baru' }}
                    </h4>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-none d-lg-block">
                    <ol class="breadcrumb m-0 float-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.template.index') }}" wire:navigate>Template
                                Email</a></li>
                        <li class="breadcrumb-item active">{{ $isEditing ? 'Ubah' : 'Tambah Baru' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form onsubmit="event.preventDefault(); handleSaveTemplate();">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="header-title">
                                    {{ $isEditing ? 'Form Ubah Template Email' : 'Form Tambah Template Email' }}
                                </h4>
                                <p class="sub-header">
                                    Tentukan nama, subjek default, dan susun konten email yang dapat digunakan
                                    berkali-kali.
                                </p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Nama Template -->
                            <div class="col-md-8">
                                <label class="form-label fw-bold fs-13">Nama Template <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model="name" placeholder="Contoh: Undangan Bimtek Pengelolaan Keuangan Daerah">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kategori -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold fs-13">Kategori Template <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror"
                                    wire:model="category">
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Subjek Email Default -->
                            <div class="col-12">
                                <label class="form-label fw-bold fs-13">Subjek Email Default <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                    wire:model="subject"
                                    placeholder="Contoh: Undangan Eksklusif: Bimtek Tata Kelola Keuangan untuk {instansi}">
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Isi Konten Email (TinyMCE Visual Editor) -->
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                    <label class="form-label fw-bold fs-13 mb-0">
                                        Isi Konten Email (Editor Visual) <span class="text-danger">*</span>
                                    </label>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="text-muted fs-11 me-1">Sisipkan Variabel:</span>
                                        <button type="button" class="btn btn-xs btn-light border py-1 px-2 fs-11"
                                            onclick="insertVariable('{nama}')" title="Nama Kontak">+ {nama}</button>
                                        <button type="button" class="btn btn-xs btn-light border py-1 px-2 fs-11"
                                            onclick="insertVariable('{instansi}')" title="Nama Dinas / Instansi">+
                                            {instansi}</button>
                                        <button type="button" class="btn btn-xs btn-light border py-1 px-2 fs-11"
                                            onclick="insertVariable('{jabatan}')" title="Jabatan Kontak">+
                                            {jabatan}</button>
                                        <button type="button" class="btn btn-xs btn-light border py-1 px-2 fs-11"
                                            onclick="insertVariable('{email}')" title="Email Kontak">+ {email}</button>
                                    </div>
                                </div>

                                <div wire:ignore class="tinymce-wrapper border rounded">
                                    <textarea id="tinymce-template-editor">{!! $body_content !!}</textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted fs-11">
                                        <i class="mdi mdi-information-outline me-1"></i> Variabel <code>{nama}</code>,
                                        <code>{instansi}</code>, <code>{jabatan}</code>, dan <code>{email}</code> akan
                                        otomatis digantikan sesuai data kontak penerima saat campaign dikirim.
                                    </small>
                                </div>
                                @error('body_content')
                                    <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status Aktif Switch -->
                            <div class="col-12 pt-2 border-top">
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" role="switch" id="templateIsActive"
                                        wire:model.live="is_active">
                                    <label class="form-check-label fw-semibold ms-1 fs-13" for="templateIsActive">
                                        Status Aktif
                                    </label>
                                </div>
                                <small class="text-muted fs-11 d-block mt-1">
                                    Template aktif akan tersedia untuk dipilih saat pembuatan Campaign Email Blast.
                                </small>
                            </div>
                        </div>

                        <!-- Tombol Aksi Bawah -->
                        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.template.index') }}" wire:navigate class="btn btn-light px-3">
                                Batal
                            </a>
                            <button type="button" class="btn btn-primary px-4 fw-semibold"
                                onclick="handleSaveTemplate()" wire:loading.attr="disabled">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"
                                    role="status"></span>
                                <span wire:loading.remove wire:target="save"><i
                                        class="mdi mdi-content-save-outline me-1"></i></span>
                                {{ $isEditing ? 'Simpan Perubahan' : 'Buat Template' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
    <style>
        .tinymce-wrapper {
            position: relative;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .tox-tinymce {
            border: none !important;
            border-radius: 8px !important;
        }

        .tox .tox-toolbar-overlord,
        .tox .tox-toolbar,
        .tox .tox-toolbar__primary {
            background-color: #f8f9fa !important;
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
        let templateEditor = null;

        function initTemplateTinyMCE() {
            const textareaEl = document.getElementById('tinymce-template-editor');
            if (!textareaEl) {
                templateEditor = null;
                return;
            }

            if (typeof tinymce === 'undefined') {
                setTimeout(initTemplateTinyMCE, 100);
                return;
            }

            if (tinymce.get('tinymce-template-editor')) {
                tinymce.get('tinymce-template-editor').remove();
            }

            tinymce.init({
                selector: '#tinymce-template-editor',
                license_key: 'gpl',
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
                p { margin-top: 0; margin-bottom: 16px; }
                p:last-child { margin-bottom: 0; }
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
                table.two-column, table.three-column { border: none !important; }
                table.two-column td, table.three-column td { border: none !important; }
                a[style*="background-color"] { text-decoration: none !important; }
                ul, ol { margin-top: 0; margin-bottom: 16px; padding-left: 24px; }
                li { margin-bottom: 6px; line-height: 1.7; }
                a { color: #2563eb; text-decoration: underline; }
                blockquote {
                    border-left: 4px solid #10b981;
                    background-color: #f0fdf4;
                    padding: 12px 18px;
                    margin: 16px 0;
                    color: #064e3b;
                    font-style: italic;
                    border-radius: 0 6px 6px 0;
                }
                hr { border: 0; border-top: 1px solid #d1d5db; margin: 20px 0; }
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
                    templateEditor = editor;

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

        document.addEventListener('DOMContentLoaded', initTemplateTinyMCE);
        document.addEventListener('livewire:navigated', initTemplateTinyMCE);

        window.insertVariable = function(tag) {
            if (templateEditor) {
                templateEditor.insertContent(tag);
                if (window.Livewire) {
                    @this.set('body_content', templateEditor.getContent(), false);
                }
            } else {
                const textarea = document.getElementById('tinymce-template-editor');
                if (textarea) {
                    textarea.value += tag;
                    if (window.Livewire) {
                        @this.set('body_content', textarea.value, false);
                    }
                }
            }
        };

        window.handleSaveTemplate = function() {
            let content = '';
            if (templateEditor) {
                content = templateEditor.getContent();
            } else {
                const el = document.getElementById('tinymce-template-editor');
                content = el ? el.value : '';
            }
            @this.call('save', content);
        };
    </script>
@endpush
