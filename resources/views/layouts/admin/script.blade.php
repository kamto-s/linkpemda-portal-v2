<!-- App js -->
<script src="{{ asset('assets/admin/js/vendor.min.js') }}" data-navigate-once></script>
<script src="{{ asset('assets/admin/js/app.js') }}" data-navigate-once></script>

<script data-navigate-once>
    // 1. Sidebar Toggle (Mendukung Desktop Collapse & Mobile Off-canvas Drawer)
    function toggleSidebarMenu() {
        const html = document.documentElement;
        const isMobile = window.innerWidth <= 1200 || html.getAttribute('data-sidebar-size') === 'full';

        if (isMobile) {
            const isOpen = html.classList.toggle('sidebar-enable');
            let backdrop = document.getElementById('custom-backdrop');

            if (isOpen) {
                if (!backdrop) {
                    backdrop = document.createElement('div');
                    backdrop.id = 'custom-backdrop';
                    backdrop.className = 'offcanvas-backdrop fade show';
                    document.body.appendChild(backdrop);
                    document.body.style.overflow = 'hidden';

                    backdrop.addEventListener('click', function() {
                        html.classList.remove('sidebar-enable');
                        if (backdrop && backdrop.parentNode) {
                            backdrop.parentNode.removeChild(backdrop);
                        }
                        document.body.style.overflow = '';
                    });
                }
            } else {
                if (backdrop && backdrop.parentNode) {
                    backdrop.parentNode.removeChild(backdrop);
                }
                document.body.style.overflow = '';
            }
        } else {
            // Desktop: toggle class sidebar-enable untuk memperkecil (icon-only) / memperlebar menu
            const isCollapsed = html.classList.toggle('sidebar-enable');
            sessionStorage.setItem('sidebarCollapsed', isCollapsed ? 'true' : 'false');
        }
    }

    // 2. Theme Mode Toggle (Dark / Light)
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        sessionStorage.setItem('themeMode', theme);

        const themeIcon = document.querySelector('#theme-mode i');
        if (themeIcon) {
            if (theme === 'dark') {
                themeIcon.className = 'bx bx-sun font-size-24';
            } else {
                themeIcon.className = 'bx bx-moon font-size-24';
            }
        }
    }

    function toggleThemeMode() {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
    }

    // 3. Fullscreen Toggle
    function toggleFullScreen() {
        if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullscreen) {
                document.documentElement.webkitRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }
    }

    // Global Singleton Registration
    if (!window.__adminScriptInitialized) {
        window.__adminScriptInitialized = true;

        document.addEventListener('fullscreenchange', function() {
            if (document.fullscreenElement) {
                document.body.classList.add('fullscreen-enable');
            } else {
                document.body.classList.remove('fullscreen-enable');
            }
        });

        // Event delegation global pada document:
        // Selalu aktif dan tidak akan pernah terlepas saat berpindah halaman via wire:navigate
        document.addEventListener('click', function(e) {
            // Toggle Sidebar
            const toggleBtn = e.target.closest('.button-toggle-menu');
            if (toggleBtn) {
                e.preventDefault();
                toggleSidebarMenu();
                return;
            }

            // Toggle Theme Mode
            const themeBtn = e.target.closest('#theme-mode');
            if (themeBtn) {
                e.preventDefault();
                toggleThemeMode();
                return;
            }

            // Toggle Fullscreen
            const fullscreenBtn = e.target.closest('[data-bs-toggle="fullscreen"]');
            if (fullscreenBtn) {
                e.preventDefault();
                toggleFullScreen();
                return;
            }

            // Sidebar Submenu Collapse Toggle
            const submenuToggle = e.target.closest('.app-menu [data-bs-toggle="collapse"]');
            if (submenuToggle) {
                e.preventDefault();
                const targetSelector = submenuToggle.getAttribute('data-bs-target') || submenuToggle
                    .getAttribute('href');
                if (targetSelector && targetSelector.startsWith('#')) {
                    const targetEl = document.querySelector(targetSelector);
                    if (targetEl) {
                        const bsCollapse = bootstrap.Collapse.getOrCreateInstance(targetEl, {
                            toggle: false
                        });
                        bsCollapse.toggle();
                    }
                }
                return;
            }

            // Dismiss Notification Item
            const notiCloseBtn = e.target.closest('.noti-close-btn');
            if (notiCloseBtn) {
                e.preventDefault();
                e.stopPropagation();
                const item = notiCloseBtn.closest('.notify-item');
                if (item) {
                    item.remove();
                    const badge = document.querySelector('.noti-icon-badge');
                    if (badge) {
                        const remaining = document.querySelectorAll(
                            '.notification-list .notify-item:not(.border-top)').length;
                        if (remaining > 0) {
                            badge.textContent = remaining;
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                }
                return;
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 1200) {
                const backdrop = document.getElementById('custom-backdrop');
                if (backdrop && backdrop.parentNode) {
                    backdrop.parentNode.removeChild(backdrop);
                    document.body.style.overflow = '';
                }
                const isCollapsed = sessionStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    document.documentElement.classList.add('sidebar-enable');
                }
            } else {
                document.documentElement.classList.remove('sidebar-enable');
            }
        });

        document.addEventListener('DOMContentLoaded', syncUIState);
        document.addEventListener('livewire:navigated', syncUIState);
    }

    function syncUIState() {
        // 1. Sinkronisasi tema dan ikon (default: light)
        const savedTheme = sessionStorage.getItem('themeMode') || 'light';
        applyTheme(savedTheme);

        // 2. Sinkronisasi status collapse sidebar desktop
        if (window.innerWidth > 1200) {
            const isCollapsed = sessionStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.documentElement.classList.add('sidebar-enable');
            } else {
                document.documentElement.classList.remove('sidebar-enable');
            }
        } else {
            document.documentElement.classList.remove('sidebar-enable');
        }

        // 3. Bersihkan backdrop jika ada saat berpindah halaman
        const backdrop = document.getElementById('custom-backdrop');
        if (backdrop && backdrop.parentNode) {
            backdrop.parentNode.removeChild(backdrop);
            document.body.style.overflow = '';
        }

        // 4. Re-inisialisasi semua Dropdown Bootstrap 5 (termasuk profile header & tabel)
        document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(el) {
            bootstrap.Dropdown.getOrCreateInstance(el);
        });

        // 5. Re-inisialisasi semua Collapse menu sidebar
        document.querySelectorAll('.app-menu .collapse').forEach(function(el) {
            bootstrap.Collapse.getOrCreateInstance(el, {
                toggle: false
            });
        });

        // 6. Re-inisialisasi efek tema
        if (typeof Waves !== 'undefined') {
            Waves.init();
        }
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
</script>
