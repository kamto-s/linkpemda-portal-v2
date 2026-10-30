<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Reset Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 40px 15px;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            padding: 32px 30px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: #e0f2fe;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .body {
            padding: 36px 32px;
            color: #334155;
            line-height: 1.6;
            font-size: 15px;
        }

        .greeting {
            font-size: 17px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
        }

        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }

        .btn {
            display: inline-block;
            background-color: #0284c7;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            padding: 13px 32px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(2, 132, 199, 0.25);
        }

        .alert-box {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 13px;
            color: #92400e;
            margin-top: 24px;
        }

        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 28px 0;
        }

        .fallback {
            font-size: 12px;
            color: #64748b;
            word-break: break-all;
            line-height: 1.5;
        }

        .fallback a {
            color: #0284c7;
            text-decoration: underline;
        }

        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>PORTAL LINKPEMDA</h1>
                <p>Permintaan Reset Password</p>
            </div>

            <div class="body">
                <div class="greeting">Halo, {{ $user->name }}!</div>
                <p>
                    Kami menerima permintaan untuk mengatur ulang (*reset*) password akun Anda di <strong>Portal Linkpemda</strong>.
                </p>
                <p>
                    Silakan klik tombol di bawah ini untuk membuat password baru:
                </p>

                <div class="btn-wrapper">
                    <a href="{{ $resetUrl }}" class="btn" target="_blank">Atur Ulang Password</a>
                </div>

                <div class="alert-box">
                    <strong>Penting:</strong> Tautan ini hanya berlaku selama <strong>60 menit</strong>. Jika Anda tidak merasa meminta reset password, abaikan email ini dan password Anda saat ini tetap aman tanpa perubahan.
                </div>

                <div class="divider"></div>

                <div class="fallback">
                    Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:<br>
                    <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
                </div>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} Linkpemda. Hak Cipta Dilindungi.<br>
                Email ini dikirim secara otomatis oleh sistem, mohon untuk tidak membalas email ini.
            </div>
        </div>
    </div>
</body>

</html>
