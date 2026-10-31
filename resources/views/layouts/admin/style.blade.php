<link rel="shortcut icon" href="{{ asset('assets/admin/images/logo-sm.png') }}">

<link href="{{ asset('assets/admin/css/style.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/admin/css/icons.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/admin/libs/tinymce/skins/ui/oxide/skin.min.css') }}" rel="stylesheet" type="text/css" data-navigate-once>
<script src="{{ asset('assets/admin/js/config.js') }}"></script>

<style>
    /* Indikator titik tiga (...) untuk menu-title saat sidebar di-minimize */
    .app-menu .menu-title i {
        display: none;
    }
    html.sidebar-enable:not([data-sidebar-size="full"]) .main-menu .app-menu .menu-title,
    html[data-sidebar-size="sm"] .main-menu .app-menu .menu-title,
    html[data-sidebar-size="condensed"] .main-menu .app-menu .menu-title {
        display: flex !important;
        justify-content: center;
        align-items: center;
        padding: 12px 0 6px;
        font-size: 0;
        line-height: 1;
        letter-spacing: normal;
        cursor: default;
    }
    html.sidebar-enable:not([data-sidebar-size="full"]) .main-menu .app-menu .menu-title span,
    html[data-sidebar-size="sm"] .main-menu .app-menu .menu-title span,
    html[data-sidebar-size="condensed"] .main-menu .app-menu .menu-title span {
        display: none !important;
    }
    html.sidebar-enable:not([data-sidebar-size="full"]) .main-menu .app-menu .menu-title i,
    html[data-sidebar-size="sm"] .main-menu .app-menu .menu-title i,
    html[data-sidebar-size="condensed"] .main-menu .app-menu .menu-title i {
        display: inline-block !important;
        font-size: 16px;
        line-height: 1;
        color: var(--bs-menu-item-color);
        opacity: 0.6;
    }
    html.sidebar-enable:not([data-sidebar-size="full"]) .main-menu .app-menu .menu-title:not(:has(i))::before,
    html[data-sidebar-size="sm"] .main-menu .app-menu .menu-title:not(:has(i))::before,
    html[data-sidebar-size="condensed"] .main-menu .app-menu .menu-title:not(:has(i))::before {
        content: "\F01D8";
        font-family: "Material Design Icons";
        font-size: 16px;
        line-height: 1;
        color: var(--bs-menu-item-color);
        opacity: 0.6;
    }
</style>
