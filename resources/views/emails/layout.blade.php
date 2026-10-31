<!DOCTYPE html>
<html lang="id" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Wajib: 'light only' untuk mengunci Gmail App & Apple Mail agar TIDAK menginversi warna (Anti Dark Mode) -->
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light only">
    <title>{{ $subject ?? system_setting('app_name', 'Linkpemda') }}</title>

    <!-- Import Font Bersih & Modern -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style type="text/css">
        :root {
            color-scheme: light only !important;
            supported-color-schemes: light only !important;
        }

        /* Reset & Base Canvas */
        body,
        html {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background-color: #ffffff !important;
            background-image: linear-gradient(#ffffff, #ffffff) !important;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            color: #1e293b !important;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        td {
            padding: 0;
        }

        img {
            border: 0;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        /* Container & Wrapper */
        .email-wrapper {
            width: 100% !important;
            background-color: #ffffff !important;
            background-image: linear-gradient(#ffffff, #ffffff) !important;
            padding: 16px 0;
            margin: 0 auto;
        }

        .email-container {
            width: 100% !important;
            max-width: 580px !important;
            margin: 0 auto !important;
            background-color: #ffffff !important;
            background-image: linear-gradient(#ffffff, #ffffff) !important;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        /* Header Bersih */
        .email-header {
            padding: 28px 24px 20px 24px;
            text-align: center;
            background-color: #ffffff !important;
            background-image: linear-gradient(#ffffff, #ffffff) !important;
            border-bottom: 1px solid #f1f5f9;
        }

        .brand-logo-img {
            max-height: 48px;
            max-width: 220px;
            height: auto;
            display: inline-block;
            border: 0;
            outline: none;
            margin: 0 auto 6px auto;
        }

        .brand-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a !important;
            line-height: 1.2;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .brand-sub {
            font-size: 13px;
            font-weight: 500;
            color: #64748b !important;
            margin: 6px 0 0 0;
            line-height: 1.4;
        }

        /* Content Area */
        .email-body {
            padding: 28px 24px;
            font-size: 15px;
            line-height: 1.65;
            color: #334155 !important;
            background-color: #ffffff !important;
            background-image: linear-gradient(#ffffff, #ffffff) !important;
        }

        .email-body h1,
        .email-body h2,
        .email-body h3,
        .email-body h4 {
            color: #0f172a !important;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 14px;
            letter-spacing: -0.3px;
        }

        .email-body p {
            margin: 0 0 14px 0;
            color: #334155 !important;
        }

        .email-body a {
            color: #0284c7 !important;
            text-decoration: underline;
        }

        /* Button Style */
        .btn-wrapper {
            text-align: center;
            margin: 26px 0;
        }

        .btn-action {
            display: inline-block;
            background-color: #0284c7 !important;
            background-image: linear-gradient(#0284c7, #0284c7) !important;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none !important;
            padding: 13px 32px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
            text-align: center;
        }

        .btn-text {
            color: #ffffff !important;
        }

        /* Alert / Highlight Box */
        .info-box {
            background-color: #fffbeb !important;
            background-image: linear-gradient(#fffbeb, #fffbeb) !important;
            border: 1px solid #fef3c7 !important;
            border-left: 4px solid #f59e0b !important;
            border-radius: 6px;
            padding: 14px 16px;
            margin: 20px 0;
            font-size: 13px;
            color: #92400e !important;
            line-height: 1.6;
        }

        /* Footer Bersih */
        .email-footer {
            padding: 22px 24px 28px 24px;
            text-align: center;
            font-size: 12px;
            line-height: 1.6;
            color: #64748b !important;
            background-color: #ffffff !important;
            background-image: linear-gradient(#ffffff, #ffffff) !important;
            border-top: 1px solid #f1f5f9;
        }

        .footer-brand {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b !important;
            margin-bottom: 4px;
        }

        .footer-links a {
            color: #64748b !important;
            text-decoration: underline;
            margin: 0 6px;
        }

        /* ================= Responsif Layar HP (Mencegah Tampilan Mengkerut) ================= */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 0 !important;
                width: 100% !important;
            }

            .email-container {
                width: 100% !important;
                max-width: 100% !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .email-header {
                padding: 22px 16px 16px 16px !important;
            }

            .email-body {
                padding: 18px 16px !important;
                font-size: 15px !important;
                line-height: 1.6 !important;
            }

            .email-footer {
                padding: 18px 16px 24px 16px !important;
            }

            .brand-title {
                font-size: 20px !important;
            }

            .btn-wrapper {
                margin: 20px 0 !important;
            }

            .btn-action {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
                padding: 13px 16px !important;
                text-align: center !important;
            }

            .info-box {
                padding: 12px 14px !important;
                margin: 16px 0 !important;
            }
        }

        /* ================= Gmail Mobile Specific Anti-Dark Mode Hack ================= */
        u + .body,
        u + .body .email-wrapper,
        u + .body .email-container,
        u + .body .email-header,
        u + .body .email-body,
        u + .body .email-footer,
        u + .body table,
        u + .body td {
            background-color: #ffffff !important;
            background-image: linear-gradient(#ffffff, #ffffff) !important;
            background: #ffffff !important;
            color: #1e293b !important;
        }

        u + .body .brand-title,
        u + .body h1,
        u + .body h2,
        u + .body h3,
        u + .body h4,
        u + .body strong {
            color: #0f172a !important;
        }

        u + .body .brand-sub,
        u + .body .email-footer,
        u + .body .footer-brand {
            color: #64748b !important;
        }

        u + .body .btn-action {
            background-color: #0284c7 !important;
            background-image: linear-gradient(#0284c7, #0284c7) !important;
            color: #ffffff !important;
        }

        u + .body .btn-text {
            color: #ffffff !important;
        }

        /* Outlook & App Anti-Inversion Directives */
        [data-ogsc] .email-text,
        [data-ogsc] p,
        [data-ogsc] h1,
        [data-ogsc] h2,
        [data-ogsc] h3,
        [data-ogsc] strong {
            color: #0f172a !important;
        }

        [data-ogsc] .btn-text {
            color: #ffffff !important;
        }

        [data-ogsb] body,
        [data-ogsb] .email-wrapper,
        [data-ogsb] .email-container,
        [data-ogsb] .email-header,
        [data-ogsb] .email-body,
        [data-ogsb] .email-footer {
            background-color: #ffffff !important;
            background-image: linear-gradient(#ffffff, #ffffff) !important;
        }
    </style>
</head>

<body class="body" bgcolor="#ffffff"
    style="background-color: #ffffff !important; background-image: linear-gradient(#ffffff, #ffffff) !important; margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <!-- Wrapper Luar (Lebar Penuh Tanpa Padding Samping yang Mempersempit Layar HP) -->
    <div class="email-wrapper" bgcolor="#ffffff"
        style="width: 100% !important; background-color: #ffffff !important; background-image: linear-gradient(#ffffff, #ffffff) !important; padding: 16px 0; margin: 0 auto;">
        
        <table class="email-container" role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
            align="center" bgcolor="#ffffff"
            style="width: 100% !important; max-width: 580px !important; margin: 0 auto !important; background-color: #ffffff !important; background-image: linear-gradient(#ffffff, #ffffff) !important; border: 1px solid #f1f5f9; border-radius: 10px; overflow: hidden;">

            <!-- ================= HEADER BERSAMA ================= -->
            <tr>
                <td class="email-header" align="center" bgcolor="#ffffff"
                    style="padding: 28px 24px 20px 24px; text-align: center; background-color: #ffffff !important; background-image: linear-gradient(#ffffff, #ffffff) !important; border-bottom: 1px solid #f1f5f9;">
                    @php
                        $emailLogo = system_setting('email_header_logo');
                        $appName = system_setting('app_name', 'Linkpemda');
                        $appTagline = system_setting('app_tagline', 'Lembaga Informasi Keuangan & Pembangunan Daerah');
                    @endphp

                    @if ($emailLogo)
                        <div style="margin-bottom: 6px;">
                            <img src="{{ str_starts_with($emailLogo, 'http') ? $emailLogo : asset($emailLogo) }}"
                                alt="{{ $appName }}" class="brand-logo-img"
                                style="max-height: 48px; max-width: 220px; height: auto; display: inline-block; border: 0; outline: none;"
                                height="48" border="0">
                        </div>
                    @else
                        <div class="brand-title" style="font-size: 22px; font-weight: 800; color: #0f172a !important; line-height: 1.2; margin: 0;">
                            {{ $appName }}<span style="color: #22c55e;">.</span>
                        </div>
                    @endif

                    @if ($appTagline)
                        <p class="brand-sub"
                            style="font-size: 13px; font-weight: 500; color: #64748b !important; margin: 6px 0 0 0; line-height: 1.4;">
                            {{ $appTagline }}
                        </p>
                    @endif
                </td>
            </tr>

            <!-- ================= KONTEN EMAIL ================= -->
            <tr>
                <td class="email-body" bgcolor="#ffffff"
                    style="padding: 28px 24px; font-size: 15px; line-height: 1.65; color: #334155 !important; background-color: #ffffff !important; background-image: linear-gradient(#ffffff, #ffffff) !important;">
                    @yield('content')
                </td>
            </tr>

            <!-- ================= FOOTER BERSAMA ================= -->
            <tr>
                <td class="email-footer" align="center" bgcolor="#ffffff"
                    style="padding: 22px 24px 28px 24px; text-align: center; font-size: 12px; line-height: 1.6; color: #64748b !important; background-color: #ffffff !important; background-image: linear-gradient(#ffffff, #ffffff) !important; border-top: 1px solid #f1f5f9;">
                    <div class="footer-brand" style="font-size: 14px; font-weight: 700; color: #1e293b !important; margin-bottom: 4px;">
                        {{ $appName }}
                    </div>

                    <div style="margin: 6px 0 12px 0; color: #64748b !important;">
                        Perum Rosma Indah II Blok G, Tarumajaya, Kab. Bekasi, Jawa Barat<br>
                        Telp: (021) 88997129 | WA: <a href="https://wa.me/6281387666605" target="_blank"
                            style="color: #0284c7 !important; text-decoration: none;">+6281387666605</a> |
                        Email: <a href="mailto:{{ system_setting('contact_support_email', 'info@linkpemda.com') }}"
                            style="color: #0284c7 !important; text-decoration: none;">{{ system_setting('contact_support_email', 'info@linkpemda.com') }}</a>
                    </div>

                    <div class="footer-links" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #f1f5f9; color: #64748b !important;">
                        &copy; {{ date('Y') }} {{ $appName }}. Hak Cipta Dilindungi.<br>
                        <span style="font-size: 11px; color: #94a3b8 !important;">Email ini dikirim secara otomatis oleh sistem, mohon untuk tidak membalas langsung ke alamat ini.</span>
                    </div>
                </td>
            </tr>

        </table>
    </div>
</body>

</html>
