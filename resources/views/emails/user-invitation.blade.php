@extends('emails.layout', ['subject' => 'Undangan Aktivasi Akun Pengguna'])

@section('content')
    <h2 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 16px;">
        Halo, {{ $user->name }}!
    </h2>

    <p style="margin-bottom: 16px; color: #334155; line-height: 1.65;">
        Anda telah diundang untuk bergabung dan mengelola <strong>{{ system_setting('app_name', 'Linkpemda') }}</strong>.
    </p>

    <div style="background-color: #f8fafc !important; background-image: linear-gradient(#f8fafc, #f8fafc) !important; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin: 20px 0;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 14px;">
            <tr>
                <td style="color: #64748b; padding: 4px 0; width: 120px;">Email Akun:</td>
                <td style="color: #0f172a; font-weight: 600; padding: 4px 0;">{{ $user->email }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; padding: 4px 0;">Role / Peran:</td>
                <td style="padding: 4px 0;">
                    <span style="display: inline-block; background-color: #e0f2fe; color: #0369a1; font-weight: 600; font-size: 12px; padding: 2px 10px; border-radius: 9999px;">
                        {{ ucfirst($roleName) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <p style="margin-bottom: 24px; color: #334155; line-height: 1.65;">
        Untuk mulai menggunakan portal, silakan klik tombol di bawah ini guna mengaktifkan akun dan membuat kata sandi Anda:
    </p>

    <div class="btn-wrapper" style="text-align: center; margin: 30px 0;">
        <a href="{{ $resetUrl }}" class="btn-action" target="_blank"
            style="display: inline-block; background-color: #0284c7 !important; background-image: linear-gradient(#0284c7, #0284c7) !important; color: #ffffff !important; font-size: 15px; font-weight: 700; text-decoration: none !important; padding: 14px 34px; border-radius: 8px; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);">
            <span class="btn-text" style="color: #ffffff !important;" data-ogsc="color: #ffffff;">Aktivasi Akun &amp; Buat Password</span>
        </a>
    </div>

    <div class="info-box"
        style="background-color: #f0fdf4 !important; background-image: linear-gradient(#f0fdf4, #f0fdf4) !important; border: 1px solid #dcfce7 !important; border-left: 4px solid #22c55e !important; border-radius: 6px; padding: 14px 18px; margin: 24px 0; font-size: 13px; color: #166534 !important; line-height: 1.6;">
        <strong>Informasi Keamanan:</strong> Tautan undangan ini hanya berlaku selama <strong>24 jam</strong>. Setelah akun diaktifkan, Anda dapat langsung login ke dalam sistem portal.
    </div>
@endsection
