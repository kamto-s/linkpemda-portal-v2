<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="brand" data-topbar-color="light">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>{{ isset($title) ? $title . ' | ' : '' }}Linkpemda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Myra Studio" name="author" />

    <link rel="shortcut icon" href="{{ asset('assets/admin') }}/images/logo-sm.png">


    <link href="{{ asset('assets/admin') }}/css/style.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin') }}/css/icons.min.css" rel="stylesheet" type="text/css">
    <script src="{{ asset('assets/admin') }}/js/config.js"></script>
</head>

<body class="bg-info d-flex justify-content-center align-items-center min-vh-100 p-5">

    {{ $slot }}

    <script src="{{ asset('assets/admin') }}/js/vendor.min.js"></script>
    <script src="{{ asset('assets/admin') }}/js/app.js"></script>
</body>

</html>
