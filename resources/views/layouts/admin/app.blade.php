<!DOCTYPE html>
{{-- <html lang="en" data-bs-theme="light" data-menu-color="brand" data-topbar-color="light"> --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light" data-menu-color="brand"
    data-topbar-color="light">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>{{ isset($title) ? $title . ' | ' : '' }}Linkpemda</title>
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
