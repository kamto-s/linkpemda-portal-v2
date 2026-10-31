<!DOCTYPE html>
{{-- <html lang="en" data-bs-theme="light" data-menu-color="brand" data-topbar-color="light"> --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light" data-menu-color="brand"
    data-topbar-color="light">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>{{ isset($title) ? $title . ' | ' : '' }}{{ system_setting('app_name', 'Linkpemda') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Myra Studio" name="author" />

    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    @include('layouts.admin.style')
    @livewireStyles
    @stack('styles')
</head>

<body>
    <!-- Begin page -->
    <div class="layout-wrapper">

        @include('layouts.admin.sidebar')

        <!-- Start Page Content here -->
        <div class="page-content">

            @include('layouts.admin.header')

            <div class="px-3">
                <!-- Start Content-->
                <div class="container-fluid">
                    @if (system_setting('maintenance_mode') === 'true')
                        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mt-3 mb-0" role="alert">
                            <i class="mdi mdi-alert-octagon font-size-20 me-2 text-warning"></i>
                            <div>
                                <strong>Mode Pemeliharaan Aktif:</strong> Sistem portal saat ini berada dalam mode pemeliharaan (maintenance mode).
                            </div>
                        </div>
                    @endif
                    {{ $slot }}
                </div> <!-- container -->
            </div> <!-- content -->

            @include('layouts.admin.footer')

        </div>
        <!-- End Page content -->
    </div>
    <!-- END wrapper -->

    @include('layouts.admin.script')
    @livewireScripts
    @stack('scripts')
</body>

</html>
