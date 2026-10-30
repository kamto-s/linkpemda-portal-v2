<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Aktivasi Akun</title>
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

        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #0284c7;
            border-radius: 6px;
            padding: 14px 18px;
            margin: 20px 0;
        }

        .info-card table {
            width: 100%;
            font-size: 14px;
        }

        .info-card td {
            padding: 4px 0;
        }

        .info-card .label {
            color: #64748b;
            width: 110px;
        }

        .info-card .value {
            color: #0f172a;
            font-weight: 600;
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
                <p>Undangan Aktivasi Akun Pengguna</p>
            </div>

            <div class="body">
                <div class="greeting">Halo, {{ $user->name }}!</div>
                <p>
                    Anda telah didaftarkan sebagai pengguna baru di sistem <strong>Portal Linkpemda</strong>.
                    Untuk mengaktifkan akun dan mulai menggunakan sistem, silakan buat password Anda melalui tautan di bawah ini:
                </p>

                <div class="info-card">
                    <table>
                        <tr>
                            <td class="label">Nama Akun:</td>
                            <td class="value">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td class="label">Alamat Email:</td>
                            <td class="value">{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="label">Peran (Role):</td>
                            <td class="value">{{ ucfirst($roleName) }}</td>
                        </tr>
                    </table>
                </div>

                <div class="btn-wrapper">
                    <a href="{{ $resetUrl }}" class="btn" target="_blank">Atur Password Akun</a>
                </div>

                <div class="alert-box">
                    <strong>Perhatian:</strong> Tautan pengaturan password ini hanya berlaku selama <strong>60 menit</strong>. Jika Anda tidak merasa didaftarkan, silakan abaikan email ini.
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
