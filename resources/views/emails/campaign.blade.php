<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #333333;
            -webkit-font-smoothing: antialiased;
        }

        table {
            border-spacing: 0;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        td {
            padding: 0;
        }

        img {
            border: 0;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
            height: auto;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f6f9;
            padding: 30px 0;
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

        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 30px 20px;
            text-align: center;
        }

        .header img {
            max-height: 48px;
            width: auto;
        }

        .header-title {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            margin: 12px 0 0 0;
            letter-spacing: 0.5px;
        }

        .content {
            padding: 35px 30px;
            font-size: 15px;
            line-height: 1.7;
            color: #374151;
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
            margin-top: 0;
            margin-bottom: 14px;
            line-height: 1.35;
            color: #111827;
        }

        .content h3 {
            font-size: 16px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 12px;
            line-height: 1.4;
            color: #1f2937;
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
            border-left: 4px solid #d1d5db;
            padding-left: 16px;
            margin: 0 0 16px 0;
            color: #4b5563;
            font-style: italic;
        }

        .content a {
            color: #2563eb;
            text-decoration: underline;
        }

        .content table {
            width: 100% !important;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 16px;
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
            background-color: #f9fafb;
            font-weight: 600;
            color: #111827;
        }

        .content table.two-column {
            border: none !important;
        }

        .content table.two-column td {
            border: none !important;
            padding: 8px 10px;
            width: 50%;
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

        .footer {
            background-color: #111827;
            color: #9ca3af;
            padding: 30px 25px;
            text-align: center;
            font-size: 13px;
            line-height: 1.6;
            border-top: 1px solid #1f2937;
        }

        .footer a {
            color: #60a5fa;
            text-decoration: none;
        }

        .footer-divider {
            height: 1px;
            background-color: #374151;
            margin: 18px auto;
            max-width: 80%;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <table class="main" align="center" role="presentation">
            <!-- ========== HEADER (Hardcoded Banner / Brand) ========== -->
            <tr>
                <td class="header">
                    <img src="{{ config('app.url') }}/assets/admin/images/logo-light.png" alt="Linkpemda Portal"
                        onerror="this.style.display='none'">
                    <h1 class="header-title">LINKPEMDA PORTAL</h1>
                    <p style="color: #bfdbfe; font-size: 13px; margin: 4px 0 0 0;">Solusi Integrasi Informasi Pemerintah
                        & Mitra Swasta</p>
                </td>
            </tr>

            <!-- ========== BODY CONTENT (Dynamic Content) ========== -->
            <tr>
                <td class="content">
                    {!! $renderedContent !!}
                </td>
            </tr>

            <!-- ========== FOOTER (Standard Unified Footer) ========== -->
            <tr>
                <td class="footer">
                    <p style="margin: 0 0 6px 0; font-weight: 600; color: #f3f4f6; font-size: 14px;">
                        Linkpemda Portal Indonesia
                    </p>
                    <p style="margin: 0 0 10px 0;">
                        Layanan Resmi Fasilitasi Informasi & Kolaborasi Pemerintah Daerah dan Rekanan Swasta.
                    </p>
                    <div class="footer-divider"></div>
                    <p style="margin: 0 0 4px 0; font-size: 12px; color: #9ca3af;">
                        Email ini dikirimkan secara resmi kepada <strong>{{ $contact->email }}</strong>
                        ({{ $contact->organization_name }}).
                    </p>
                    <p style="margin: 0; font-size: 11px; color: #6b7280;">
                        &copy; {{ date('Y') }} Linkpemda Portal. Hak Cipta Dilindungi Undang-Undang.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
