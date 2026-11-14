<!-- ========== Left Sidebar ========== -->
<div class="main-menu">
    <!-- Brand Logo -->
    <div class="logo-box">
        <!-- Brand Logo Light -->
        <a wire:navigate class='logo-light' href='{{ route('admin.dashboard') }}'>
            <img src="{{ asset('assets/admin') }}/images/logo-light.png" alt="logo" class="logo-lg" height="28">
            <img src="{{ asset('assets/admin') }}/images/logo-sm.png" alt="small logo" class="logo-sm" height="28">
        </a>

        <!-- Brand Logo Dark -->
        <a wire:navigate class='logo-dark' href='{{ route('admin.dashboard') }}'>
            <img src="{{ asset('assets/admin') }}/images/logo-dark.png" alt="dark logo" class="logo-lg" height="28">
            <img src="{{ asset('assets/admin') }}/images/logo-sm.png" alt="small logo" class="logo-sm" height="28">
        </a>
    </div>

    <!--- Menu -->
    <div data-simplebar>
        <ul class="app-menu">
            @can('view-dashboard')
                <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a wire:navigate class='menu-link waves-effect waves-light' href='{{ route('admin.dashboard') }}'>
                        <span class="menu-icon"><i class="bx bx-home-smile"></i></span>
                        <span class="menu-text"> Dashboards </span>
                    </a>
                </li>
            @endcan

            @canany(['view-posts', 'view-categories', 'view-schedules', 'view-galleries', 'view-pages'])
                <li class="menu-title" title="CMS"><i class="mdi mdi-dots-horizontal"></i> <span>CMS</span></li>

                @php
                    $isPostActive = request()->routeIs(['admin.artikel.*', 'admin.materi.*', 'admin.category.*']);
                @endphp

                @canany(['view-posts', 'view-categories'])
                    <li class="menu-item {{ $isPostActive ? 'active' : '' }}">
                        <a href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#menuPosts"
                            aria-expanded="{{ $isPostActive ? 'true' : 'false' }}" aria-controls="menuPosts"
                            class="menu-link waves-effect waves-light {{ $isPostActive ? 'active' : '' }}">
                            <span class="menu-icon"><i class="bx bx-file"></i></span>
                            <span class="menu-text"> Posts </span>
                            <span class="menu-arrow"></span>
                        </a>

                        <div class="collapse {{ $isPostActive ? 'show' : '' }}" id="menuPosts">
                            <ul class="sub-menu">
                                @can('view-posts')
                                    <li class="menu-item {{ request()->routeIs('artikel.*') ? 'active' : '' }}">
                                        <a class='menu-link' href='#'>
                                            <span class="menu-text">Artikel</span>
                                        </a>
                                    </li>
                                    <li class="menu-item {{ request()->routeIs('materi.*') ? 'active' : '' }}">
                                        <a class='menu-link' href='#'>
                                            <span class="menu-text">Materi</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('view-categories')
                                    <li class="menu-item {{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
                                        <a wire:navigate
                                            class="menu-link {{ request()->routeIs('admin.category.*') ? 'active' : '' }}"
                                            href="{{ route('admin.category.index') }}">
                                            <span class="menu-text">Kategori</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany

                @can('view-schedules')
                    <li class="menu-item">
                        <a class='menu-link waves-effect waves-light' href='#'>
                            <span class="menu-icon"><i class="bx bx-calendar"></i></span>
                            <span class="menu-text"> Jadwal </span>
                        </a>
                    </li>
                @endcan

                @can('view-galleries')
                    <li class="menu-item">
                        <a class='menu-link waves-effect waves-light' href='#'>
                            <span class="menu-icon"><i class="bx bx-image"></i></span>
                            <span class="menu-text"> Galeri </span>
                        </a>
                    </li>
                @endcan

                @can('view-pages')
                    <li class="menu-item">
                        <a class='menu-link waves-effect waves-light' href='#'>
                            <span class="menu-icon"><i class="bx bx-layout"></i></span>
                            <span class="menu-text"> Halaman </span>
                        </a>
                    </li>
                @endcan
            @endcanany

            <!-- ============================================== -->
            <!-- PROMOSI & EMAIL BLAST -->
            <!-- ============================================== -->
            @canany(['create-campaigns', 'view-campaigns', 'view-templates', 'view-contacts'])
                <li class="menu-title" title="Email Marketing"><i class="mdi mdi-dots-horizontal"></i> <span>Email
                        Marketing</span></li>

                @can('create-campaigns')
                    <li class="menu-item {{ request()->routeIs('admin.campaign.create') ? 'active' : '' }}">
                        <a wire:navigate
                            class='menu-link waves-effect waves-light {{ request()->routeIs('admin.campaign.create') ? 'active' : '' }}'
                            href='{{ route('admin.campaign.create') }}'>
                            <span class="menu-icon"><i class="bx bx-send"></i></span>
                            <span class="menu-text"> Kirim Email </span>
                        </a>
                    </li>
                @endcan

                @can('view-campaigns')
                    <li
                        class="menu-item {{ request()->routeIs('admin.campaign.*') && !request()->routeIs('admin.campaign.create') ? 'active' : '' }}">
                        <a wire:navigate
                            class='menu-link waves-effect waves-light {{ request()->routeIs('admin.campaign.*') && !request()->routeIs('admin.campaign.create') ? 'active' : '' }}'
                            href='{{ route('admin.campaign.index') }}'>
                            <span class="menu-icon"><i class="bx bx-broadcast"></i></span>
                            <span class="menu-text"> Campaign </span>
                        </a>
                    </li>
                @endcan

                @can('view-templates')
                    <li class="menu-item {{ request()->routeIs('admin.template.*') ? 'active' : '' }}">
                        <a wire:navigate
                            class='menu-link waves-effect waves-light {{ request()->routeIs('admin.template.*') ? 'active' : '' }}'
                            href='{{ route('admin.template.index') }}'>
                            <span class="menu-icon"><i class="bx bx-layer"></i></span>
                            <span class="menu-text"> Template </span>
                        </a>
                    </li>
                @endcan

                @can('view-contacts')
                    <li class="menu-item {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
                        <a wire:navigate
                            class='menu-link waves-effect waves-light {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}'
                            href='{{ route('admin.contact.index') }}'>
                            <span class="menu-icon"><i class="bx bx-book-content"></i></span>
                            <span class="menu-text"> Kontak </span>
                        </a>
                    </li>
                @endcan
            @endcanany

            @canany(['view-certificates', 'view-participants', 'view-registrations', 'view-documents', 'view-messages'])
                <li class="menu-title" title="Administrasi"><i class="mdi mdi-dots-horizontal"></i>
                    <span>Administrasi</span>
                </li>

                <!-- 1. Linkpemda (Dropdown) -->
                @canany(['view-participants', 'view-certificates'])
                    @php
                        $isLinkpemdaActive = request()->routeIs(['admin.participant.linkpemda.*', 'admin.invoice.linkpemda.*', 'admin.attendance.linkpemda.*']);
                    @endphp
                    <li class="menu-item {{ $isLinkpemdaActive ? 'active' : '' }}">
                        <a href="#menuLinkpemda" data-bs-toggle="collapse"
                            aria-expanded="{{ $isLinkpemdaActive ? 'true' : 'false' }}"
                            class="menu-link waves-effect waves-light {{ $isLinkpemdaActive ? 'active' : '' }}">
                            <span class="menu-icon"><i class="bx bx-buildings"></i></span>
                            <span class="menu-text"> Linkpemda </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isLinkpemdaActive ? 'show' : '' }}" id="menuLinkpemda">
                            <ul class="sub-menu">
                                <li class="menu-item {{ request()->routeIs('admin.participant.linkpemda.*') ? 'active' : '' }}">
                                    <a wire:navigate
                                        class='menu-link {{ request()->routeIs('admin.participant.linkpemda.*') ? 'active' : '' }}'
                                        href='{{ route('admin.participant.linkpemda.index') }}'>
                                        <span class="menu-text">Data Peserta</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('admin.attendance.linkpemda.*') ? 'active' : '' }}">
                                    <a wire:navigate
                                        class='menu-link {{ request()->routeIs('admin.attendance.linkpemda.*') ? 'active' : '' }}'
                                        href='{{ route('admin.attendance.linkpemda.index') }}'>
                                        <span class="menu-text">Absensi</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('admin.invoice.linkpemda.*') ? 'active' : '' }}">
                                    <a wire:navigate
                                        class='menu-link {{ request()->routeIs('admin.invoice.linkpemda.*') ? 'active' : '' }}'
                                        href='{{ route('admin.invoice.linkpemda.index') }}'>
                                        <span class="menu-text">Invoice</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- 2. Tren Media Diklat (Dropdown) -->
                    @php
                        $isTrenActive = request()->routeIs(['admin.participant.trendiklat.*', 'admin.invoice.trendiklat.*']);
                    @endphp
                    <li class="menu-item {{ $isTrenActive ? 'active' : '' }}">
                        <a href="#menuTrenDiklat" data-bs-toggle="collapse"
                            aria-expanded="{{ $isTrenActive ? 'true' : 'false' }}"
                            class="menu-link waves-effect waves-light {{ $isTrenActive ? 'active' : '' }}">
                            <span class="menu-icon"><i class="mdi mdi-school"></i></span>
                            <span class="menu-text"> Tren Media </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse {{ $isTrenActive ? 'show' : '' }}" id="menuTrenDiklat">
                            <ul class="sub-menu">
                                <li
                                    class="menu-item {{ request()->routeIs('admin.participant.trendiklat.*') ? 'active' : '' }}">
                                    <a wire:navigate
                                        class='menu-link {{ request()->routeIs('admin.participant.trendiklat.*') ? 'active' : '' }}'
                                        href='{{ route('admin.participant.trendiklat.index') }}'>
                                        <span class="menu-text">Data Peserta</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('admin.invoice.trendiklat.*') ? 'active' : '' }}">
                                    <a class='menu-link' href='#'>
                                        <span class="menu-text">Invoice</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcanany

                <!-- 3. Pendaftaran Masuk -->
                @can('view-registrations')
                    <li class="menu-item">
                        <a class='menu-link waves-effect waves-light' href='#'>
                            <span class="menu-icon"><i class="bx bx-clipboard"></i></span>
                            <span class="menu-text"> Pendaftaran Kegiatan </span>
                        </a>
                    </li>
                @endcan

                <!-- 4. Dokumen & Regulasi -->
                @can('view-documents')
                    <li class="menu-item">
                        <a class='menu-link waves-effect waves-light' href='#'>
                            <span class="menu-icon"><i class="bx bx-folder"></i></span>
                            <span class="menu-text"> Dokumen </span>
                        </a>
                    </li>
                @endcan

                <!-- 5. Pesan / Surat Masuk -->
                @can('view-messages')
                    <li class="menu-item">
                        <a class='menu-link waves-effect waves-light' href='#'>
                            <span class="menu-icon"><i class="bx bx-envelope"></i></span>
                            <span class="menu-text"> Pesan Masuk </span>
                        </a>
                    </li>
                @endcan
            @endcanany

            @canany(['view-users', 'view-roles', 'view-settings', 'view-certificate-settings', 'manage-system'])
                <li class="menu-title" title="Pengaturan"><i class="mdi mdi-dots-horizontal"></i> <span>Pengaturan</span>
                </li>

                @can('view-users')
                    <li class="menu-item {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                        <a wire:navigate
                            class='menu-link waves-effect waves-light {{ request()->routeIs('admin.user.*') ? 'active' : '' }}'
                            href='{{ route('admin.user.index') }}'>
                            <span class="menu-icon"><i class="bx bx-user-check"></i></span>
                            <span class="menu-text"> User </span>
                        </a>
                    </li>
                @endcan

                @can('view-roles')
                    <li class="menu-item {{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                        <a wire:navigate
                            class='menu-link waves-effect waves-light {{ request()->routeIs('admin.role.*') ? 'active' : '' }}'
                            href='{{ route('admin.role.index') }}'>
                            <span class="menu-icon"><i class="bx bx-shield-quarter"></i></span>
                            <span class="menu-text"> Role & Hak Akses </span>
                        </a>
                    </li>
                @endcan

                @if (auth()->user()?->hasRole('superadmin'))
                    <li class="menu-item {{ request()->routeIs('admin.system-setting.*') ? 'active' : '' }}">
                        <a wire:navigate
                            class='menu-link waves-effect waves-light {{ request()->routeIs('admin.system-setting.*') ? 'active' : '' }}'
                            href='{{ route('admin.system-setting.index') }}'>
                            <span class="menu-icon"><i class="bx bx-slider-alt"></i></span>
                            <span class="menu-text"> Pengaturan Sistem </span>
                        </a>
                    </li>
                @endif

                @can('view-settings')
                    <li class="menu-item">
                        <a class='menu-link waves-effect waves-light' href='#'>
                            <span class="menu-icon"><i class="bx bx-cog"></i></span>
                            <span class="menu-text"> Identitas Portal </span>
                        </a>
                    </li>
                @endcan

                @can('view-certificate-settings')
                    <li class="menu-item">
                        <a class='menu-link waves-effect waves-light' href='#'>
                            <span class="menu-icon"><i class="bx bx-id-card"></i></span>
                            <span class="menu-text"> Konfigurasi Sertifikat </span>
                        </a>
                    </li>
                @endcan

                @can('manage-system')
                    <li class="menu-item">
                        <a class='menu-link waves-effect waves-light' href='#'>
                            <span class="menu-icon"><i class="bx bx-history"></i></span>
                            <span class="menu-text"> Log Aktivitas </span>
                        </a>
                    </li>
                @endcan
            @endcanany

        </ul>
    </div>
</div>