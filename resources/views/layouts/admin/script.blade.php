<!-- App js -->
<script src="{{ asset('assets/admin/js/vendor.min.js') }}" data-navigate-once></script>
<script src="{{ asset('assets/admin/js/app.js') }}" data-navigate-once></script>

<script>
    document.addEventListener('livewire:navigated', () => {
        // Re-inisialisasi semua Dropdown Bootstrap 5 (termasuk profile header & tabel)
        document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(el) {
            bootstrap.Dropdown.getOrCreateInstance(el);
        });

        // Re-inisialisasi semua Collapse menu sidebar
        document.querySelectorAll('.app-menu .collapse').forEach(function(el) {
            bootstrap.Collapse.getOrCreateInstance(el, {
                toggle: false
            });
        });

        // Re-inisialisasi efek tema
        if (typeof Waves !== 'undefined') {
            Waves.init();
        }
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
