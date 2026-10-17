<!DOCTYPE html>
<html lang="id" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Mencegah Dark Mode mengubah warna secara otomatis -->
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $subject }}</title>

    <!-- Import Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            color-scheme: light dark;
            supported-color-schemes: light dark;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #333333;
            -webkit-font-smoothing: antialiased;
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

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f6f9;
            padding: 20px 0;
        }

        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        /* ===== HEADER BANNER ===== */
        .header-banner {
            background-color: #05190e !important;
            background: linear-gradient(135deg, #04160d 0%, #082a18 50%, #10532e 100%) !important;
            padding: 32px 24px;
            text-align: left;
        }

        /* Trik khusus Dark Mode agar teks header tetap putih */
        .brand-logo-text {
            color: #ffffff !important;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin: 0;
            line-height: 1.2;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
        }

        .brand-dot {
            color: #22c55e !important;
        }

        .brand-sub {
            color: #a7f3d0 !important;
            font-size: 12px;
            font-weight: 500;
            margin-top: 8px;
            margin-bottom: 0;
            letter-spacing: 0.2px;
            line-height: 1.5;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
        }

        /* ===== CONTENT BODY ===== */
        .content {
            padding: 30px 24px;
            font-size: 15px;
            line-height: 1.7;
            color: #374151;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .content p {
            margin-top: 0;
            margin-bottom: 16px;
        }

        .content p:last-child {
            margin-bottom: 0;
        }

        .content h1 {
            font-size: 22px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 16px;
            line-height: 1.3;
            color: #111827;
        }

        .content h2 {
            font-size: 19px;
            font-weight: 700;
            margin-top: 20px;
            margin-bottom: 14px;
            line-height: 1.35;
            color: #111827;
        }

        .content h3 {
            font-size: 16px;
            font-weight: 600;
            margin-top: 18px;
            margin-bottom: 12px;
            line-height: 1.4;
            color: #1f2937;
        }

        .content h4,
        .content h5,
        .content h6 {
            font-size: 15px;
            font-weight: 600;
            margin-top: 16px;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .content a {
            color: #0d5c31;
            text-decoration: underline;
            font-weight: 600;
        }

        .content ul,
        .content ol {
            margin-top: 0;
            margin-bottom: 16px;
            padding-left: 24px;
        }

        .content li {
            margin-bottom: 6px;
            line-height: 1.7;
        }

        .content blockquote {
            border-left: 4px solid #10b981;
            background-color: #f0fdf4;
            padding: 12px 18px;
            margin: 16px 0;
            color: #064e3b;
            font-style: italic;
            border-radius: 0 6px 6px 0;
        }

        .content hr {
            border: 0;
            border-top: 1px solid #e5e7eb;
            margin: 24px 0;
        }

        .content code {
            font-family: Consolas, Monaco, monospace;
            background-color: #f3f4f6;
            color: #be123c;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 13px;
        }

        .content pre {
            background-color: #1f2937;
            color: #f9fafb;
            padding: 14px 18px;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 13px;
            line-height: 1.6;
        }

        .content table {
            width: 100% !important;
            border-collapse: collapse;
            margin-top: 12px;
            margin-bottom: 18px;
        }

        .content table th,
        .content table td {
            border: 1px solid #d1d5db;
            padding: 9px 12px;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
            line-height: 1.6;
        }

        .content table th {
            background-color: #f8fafc;
            font-weight: 700;
            color: #0f172a;
        }

        .content table.two-column,
        .content table.three-column {
            border: none !important;
        }

        .content table.two-column td,
        .content table.three-column td {
            border: none !important;
        }

        .content a[style*="background-color"] {
            text-decoration: none !important;
        }

        .content img {
            max-width: 100% !important;
            height: auto;
            border-radius: 4px;
        }

        .content img.w-100,
        .content img[style*="width: 100%"],
        .content img[style*="width:100%"] {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        /* ===== FOOTER ===== */
        .footer {
            background-color: #05190e !important;
            color: #a7f3d0 !important;
            padding: 30px 24px;
            font-size: 12px;
            line-height: 1.6;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
        }

        .footer-title {
            color: #ffffff !important;
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 2px 0;
            letter-spacing: -0.3px;
        }

        .footer-title span {
            color: #22c55e !important;
        }

        .footer-sub {
            color: #6ee7b7 !important;
            font-size: 11px;
            font-weight: 500;
            margin: 0 0 14px 0;
        }

        .footer-address {
            color: #cbd5e1 !important;
            font-size: 12px;
            line-height: 1.7;
            margin: 0 0 18px 0;
        }

        .footer-address a {
            color: #6ee7b7 !important;
            text-decoration: underline;
        }

        .footer-links {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #114b2b;
            font-size: 11px;
            color: #6ee7b7 !important;
            line-height: 1.8;
        }

        .footer-links a {
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }

        .link-sep {
            color: #1a5e38 !important;
            margin: 0 6px;
        }

        /* ===== MOBILE RESPONSIVE ===== */
        @media screen and (max-width: 600px) {
            .wrapper {
                padding: 0 !important;
            }

            .main {
                width: 100% !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .header-banner {
                padding: 24px 18px !important;
            }

            .brand-logo-text {
                font-size: 24px !important;
            }

            .brand-sub {
                font-size: 11px !important;
            }

            .content {
                padding: 22px 18px !important;
                font-size: 14px !important;
            }

            .footer {
                padding: 24px 18px !important;
            }

            .footer-title {
                font-size: 17px !important;
            }

            .footer-address {
                font-size: 11px !important;
            }

            .footer-links {
                text-align: center !important;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <table class="main" align="center" role="presentation" width="100%" cellpadding="0" cellspacing="0"
            border="0">

            <!-- ========== HEADER BANNER ========== -->
            <tr>
                <td class="header-banner" style="background-color:#05190e !important; padding:32px 24px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td valign="middle">
                                <!-- Menggunakan div & span dengan inline style !important agar tidak terpengaruh Dark Mode Inversion -->
                                <div class="brand-logo-text"
                                    style="color:#ffffff !important; font-size:28px; font-weight:800; margin:0; line-height:1.2;">
                                    Linkpemda<span class="brand-dot" style="color:#22c55e !important;">.</span>
                                </div>
                                <p class="brand-sub"
                                    style="color:#a7f3d0 !important; margin:8px 0 0 0; font-size:12px; font-weight:500;">
                                    Lembaga Informasi Keuangan &amp; Pembangunan Daerah
                                </p>

                                <!-- ACCENT LINES -->
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                                    style="margin-top:14px;">
                                    <tr>
                                        <td width="36" height="3" bgcolor="#22c55e"
                                            style="background-color:#22c55e !important; font-size:0; line-height:0;">
                                            &nbsp;</td>
                                        <td width="6" style="font-size:0; line-height:0;">&nbsp;</td>
                                        <td width="12" height="3" bgcolor="#6ee7b7"
                                            style="background-color:#6ee7b7 !important; font-size:0; line-height:0;">
                                            &nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- ========== BODY CONTENT ========== -->
            <tr>
                <td class="content">
                    {!! $renderedContent !!}
                </td>
            </tr>

            <!-- ========== FOOTER ========== -->
            <tr>
                <td class="footer" style="background-color:#05190e !important; padding:30px 24px;">
                    <div class="footer-title" style="color:#ffffff !important; font-size:18px; font-weight:800;">
                        Linkpemda<span class="brand-dot" style="color:#22c55e !important;">.</span>
                    </div>
                    <p class="footer-sub" style="color:#6ee7b7 !important; margin:2px 0 14px 0;">
                        Lembaga Informasi Keuangan &amp; Pembangunan Daerah
                    </p>

                    <p class="footer-address" style="color:#cbd5e1 !important; margin:0 0 18px 0;">
                        Perum Rosma Indah II Blok G, Tarumajaya,<br>
                        Desa Setia Mulya Kabupaten Bekasi, Jawa Barat<br>
                        Telp: (021) 88997129 | WA: <a href="https://wa.me/6281387666605" target="_blank"
                            rel="noopener noreferrer" style="color:#6ee7b7 !important;">+6281387666605</a><br>
                        Email: <a href="mailto:info@linkpemda.com"
                            style="color:#6ee7b7 !important;">info@linkpemda.com</a>
                    </p>

                    <!-- FOOTER NAVIGATION -->
                    <div class="footer-links" style="border-top:1px solid #114b2b; color:#6ee7b7 !important;">
                        <a href="https://linkpemda.com" target="_blank" rel="noopener noreferrer"
                            style="color:#ffffff !important;">Kunjungi Website</a>
                        <span class="link-sep" style="color:#1a5e38 !important;">•</span>
                        <a href="#" style="color:#ffffff !important;">Kebijakan Privasi</a>
                        <span class="link-sep" style="color:#1a5e38 !important;">•</span>
                        <a href="#" style="color:#ffffff !important;">Pusat Bantuan</a>
                    </div>
                </td>
            </tr>

        </table>
    </div>
</body>

</html>
