<!-- ========== Left Sidebar ========== -->
<div class="main-menu">
    <!-- Brand Logo -->
    <div class="logo-box">
        <!-- Brand Logo Light -->
        <a class='logo-light' href='{{ route('admin.dashboard') }}'>
            <img src="{{ asset('assets/admin') }}/images/logo-light.png" alt="logo" class="logo-lg" height="28">
            <img src="{{ asset('assets/admin') }}/images/logo-sm.png" alt="small logo" class="logo-sm" height="28">
        </a>

        <!-- Brand Logo Dark -->
        <a class='logo-dark' href='{{ route('admin.dashboard') }}'>
            <img src="{{ asset('assets/admin') }}/images/logo-dark.png" alt="dark logo" class="logo-lg" height="28">
            <img src="{{ asset('assets/admin') }}/images/logo-sm.png" alt="small logo" class="logo-sm" height="28">
        </a>
    </div>

    <!--- Menu -->
    <div data-simplebar>
        <ul class="app-menu">
            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='{{ route('admin.dashboard') }}'>
                    <span class="menu-icon"><i class="bx bx-home-smile"></i></span>
                    <span class="menu-text"> Dashboards </span>
                </a>
            </li>

            <li class="menu-title">CMS</li>

            <li class="menu-item">
                <a href="#menuExpages" data-bs-toggle="collapse" class="menu-link waves-effect waves-light">
                    <span class="menu-icon"><i class="bx bx-file"></i></span>
                    <span class="menu-text"> Posts </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="menuExpages">
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <a class='menu-link' href='pages-starter.html'>
                                <span class="menu-text">Artikel</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class='menu-link' href='pages-invoice.html'>
                                <span class="menu-text">Materi</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-calendar"></i></span>
                    <span class="menu-text"> Jadwal </span>
                </a>
            </li>


            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-image"></i></span>
                    <span class="menu-text"> Galeri </span>
                </a>
            </li>

            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-layout"></i></span>
                    <span class="menu-text"> Pages </span>
                </a>
            </li>

            <!-- ============================================== -->
            <!-- PROMOSI & EMAIL BLAST -->
            <!-- ============================================== -->
            <li class="menu-title">Marketing & Promosi</li>

            <li class="menu-item">
                <a href="#menuEmailBlast" data-bs-toggle="collapse" class="menu-link waves-effect waves-light">
                    <span class="menu-icon"><i class="bx bx-broadcast"></i></span>
                    <span class="menu-text"> Email Blast </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="menuEmailBlast">
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <a class='menu-link' href='#'>
                                <span class="menu-text">Kirim Promosi Baru</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class='menu-link' href='#'>
                                <span class="menu-text">Template Brosur Email</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class='menu-link' href='#'>
                                <span class="menu-text">Database Kontak / OPD</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class='menu-link' href='#'>
                                <span class="menu-text">Riwayat Pengiriman</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-title">Administrasi</li>

            <!-- 1. Sertifikat (Sudah Anda buat) -->
            <li class="menu-item">
                <a href="#menuSertifikat" data-bs-toggle="collapse" class="menu-link waves-effect waves-light">
                    <span class="menu-icon"><i class="bx bx-medal"></i></span>
                    <span class="menu-text"> Sertifikat </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="menuSertifikat">
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <a class='menu-link' href='#'>
                                <span class="menu-text">Linkpemda</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class='menu-link' href='#'>
                                <span class="menu-text">Tren Media Diklat</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- 2. Data Peserta / OPD -->
            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-group"></i></span>
                    <span class="menu-text"> Data Peserta </span>
                </a>
            </li>

            <!-- 3. Pendaftaran Masuk -->
            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-clipboard"></i></span>
                    <span class="menu-text"> Pendaftaran Kegiatan </span>
                </a>
            </li>

            <!-- 4. Dokumen & Regulasi -->
            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-folder"></i></span>
                    <span class="menu-text"> Dokumen </span>
                </a>
            </li>

            <!-- 5. Pesan / Surat Masuk -->
            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-envelope"></i></span>
                    <span class="menu-text"> Pesan Masuk </span>
                </a>
            </li>


            <li class="menu-title">Pengaturan</li>

            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-cog"></i></span>
                    <span class="menu-text"> Identitas Portal </span>
                </a>
            </li>

            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-user-check"></i></span>
                    <span class="menu-text"> Pengguna & Staff </span>
                </a>
            </li>

            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-id-card"></i></span>
                    <span class="menu-text"> Konfigurasi Sertifikat </span>
                </a>
            </li>

            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-shield-quarter"></i></span>
                    <span class="menu-text"> Role & Hak Akses </span>
                </a>
            </li>

            <li class="menu-item">
                <a class='menu-link waves-effect waves-light' href='#'>
                    <span class="menu-icon"><i class="bx bx-history"></i></span>
                    <span class="menu-text"> Log Aktivitas </span>
                </a>
            </li>

        </ul>
    </div>
</div>
