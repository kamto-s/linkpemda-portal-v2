<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <meta charset="utf-8" />
    <title>{{ isset($title) ? $title . ' | ' : '' }}{{ function_exists('system_setting') ? system_setting('app_name', 'Linkpemda') : config('app.name', 'Linkpemda') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="{{ asset('assets/admin') }}/images/logo-sm.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="{{ asset('assets/admin') }}/css/style.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/admin') }}/css/icons.min.css" rel="stylesheet" type="text/css">
    <script src="{{ asset('assets/admin') }}/js/config.js"></script>

    <style>
        :root {
            --brand-green: #008F83;
            --brand-green-hover: #007369;
            --brand-green-glow: rgba(0, 143, 131, 0.22);
        }

        * {
            box-sizing: border-box;
        }

        body.auth-centered-body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #011d1a;
            background: radial-gradient(circle at 50% 30%, #004d46 0%, #011412 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            margin: 0;
        }

        /* Ambient Glow di Tengah */
        .auth-ambient-glow {
            position: absolute;
            width: 580px;
            height: 580px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--brand-green-glow) 0%, rgba(0, 143, 131, 0.03) 60%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        /* Pattern Kotak-Kotak */
        .auth-grid-pattern {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.045) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.045) 1px, transparent 1px);
            background-size: 34px 34px;
            mask-image: radial-gradient(ellipse 75% 75% at 50% 50%, #000 40%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 75% 75% at 50% 50%, #000 40%, transparent 100%);
            pointer-events: none;
            z-index: 0;
        }

        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
        }

        /* Card Form Putih di Tengah (Shadow Halus & Lembut) */
        .auth-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.22), 0 0 1px rgba(0, 0, 0, 0.08);
            padding: 38px 32px;
        }

        .auth-card .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .auth-card .form-control {
            height: 44px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 10px 14px;
            font-size: 14px;
            color: #0f172a;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }

        .auth-card .form-control:focus {
            border-color: var(--brand-green);
            box-shadow: 0 0 0 3px rgba(0, 143, 131, 0.18);
            background-color: #ffffff;
        }

        .auth-card .form-control::placeholder {
            color: #94a3b8;
            font-size: 13.5px;
        }

        .btn-auth-submit {
            background: linear-gradient(135deg, #008F83 0%, #007369 100%) !important;
            border: none !important;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 14px;
            height: 44px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 143, 131, 0.25);
            transition: all 0.2s ease;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-auth-submit:hover {
            background: linear-gradient(135deg, #007b71 0%, #005a52 100%) !important;
            box-shadow: 0 4px 12px rgba(0, 143, 131, 0.35);
            color: #ffffff !important;
            transform: translateY(-1px);
        }

        .form-check-input:checked {
            background-color: var(--brand-green) !important;
            border-color: var(--brand-green) !important;
        }

        .link-green {
            color: var(--brand-green);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s ease;
        }

        .link-green:hover {
            color: var(--brand-green-hover);
            text-decoration: underline;
        }

        .auth-copyright {
            margin-top: 20px;
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body class="auth-centered-body">

    <!-- Ambient Glow & Pattern Kotak-Kotak -->
    <div class="auth-ambient-glow"></div>
    <div class="auth-grid-pattern"></div>

    <div class="auth-wrapper">
        <div class="auth-card">
            {{ $slot }}
        </div>
        <div class="auth-copyright">
            &copy; {{ date('Y') }} {{ function_exists('system_setting') ? system_setting('app_name', 'Linkpemda') : config('app.name', 'Linkpemda') }}. All rights reserved.
        </div>
    </div>

    <script src="{{ asset('assets/admin') }}/js/vendor.min.js"></script>
    <script src="{{ asset('assets/admin') }}/js/app.js"></script>
</body>
</html>
