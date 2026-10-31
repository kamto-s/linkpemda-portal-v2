@extends('emails.layout', ['subject' => 'Permintaan Reset Password Akun'])

@section('content')
    <h2 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 16px;">
        Halo, {{ $user->name }}!
    </h2>

    <p style="margin-bottom: 16px; color: #334155; line-height: 1.65;">
        Kami menerima permintaan untuk mengatur ulang (<em>reset</em>) kata sandi akun Anda di <strong>{{ system_setting('app_name', 'Linkpemda') }}</strong>.
    </p>

    <p style="margin-bottom: 24px; color: #334155; line-height: 1.65;">
        Silakan klik tombol di bawah ini untuk membuat kata sandi baru:
    </p>

    <div class="btn-wrapper" style="text-align: center; margin: 30px 0;">
        <a href="{{ $resetUrl }}" class="btn-action" target="_blank"
            style="display: inline-block; background-color: #0284c7 !important; background-image: linear-gradient(#0284c7, #0284c7) !important; color: #ffffff !important; font-size: 15px; font-weight: 700; text-decoration: none !important; padding: 14px 34px; border-radius: 8px; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);">
            <span class="btn-text" style="color: #ffffff !important;" data-ogsc="color: #ffffff;">Atur Ulang Kata Sandi</span>
        </a>
    </div>

    <div class="info-box"
        style="background-color: #fffbeb !important; background-image: linear-gradient(#fffbeb, #fffbeb) !important; border: 1px solid #fef3c7 !important; border-left: 4px solid #f59e0b !important; border-radius: 6px; padding: 14px 18px; margin: 24px 0; font-size: 13px; color: #92400e !important; line-height: 1.6;">
        <strong>Perhatian:</strong> Tautan ini hanya berlaku selama <strong>60 menit</strong>. Jika Anda tidak merasa meminta pengaturan ulang kata sandi, abaikan email ini dan akun Anda tetap aman.
    </div>
@endsection
