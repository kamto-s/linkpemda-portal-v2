<div>
    <div class="text-center mb-4">
        <a class="d-inline-block mb-3" href="{{ route('home') }}">
            <img src="{{ asset('assets/admin') }}/images/logo-dark.png" alt="Logo Linkpemda" height="34" onerror="this.src='{{ asset('assets/admin') }}/images/logo-sm.png'">
        </a>
        <h4 class="fw-bold text-dark mb-1 fs-18">Lupa Password?</h4>
        <p class="text-muted fs-13 mb-0">Masukkan email akun Anda untuk menerima tautan pemulihan</p>
    </div>

    @if ($emailSent)
        <div class="text-center py-2">
            <div class="avatar-lg mx-auto mb-3">
                <span class="avatar-title rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width: 52px; height: 52px; background: rgba(0, 143, 131, 0.12); color: #008F83;">
                    <i class="mdi mdi-email-check fs-22"></i>
                </span>
            </div>
            <h5 class="fw-bold text-dark mb-2">Tautan Telah Dikirim!</h5>
            <p class="text-muted fs-13 mb-4">
                Kami telah mengirimkan tautan reset password ke <strong class="text-dark">{{ $email }}</strong>. Silakan periksa kotak masuk atau spam email Anda.
            </p>

            <div class="d-grid gap-2">
                <a href="{{ route('login') }}" wire:navigate class="btn btn-auth-submit text-center">
                    Kembali ke Halaman Login
                </a>
                <button type="button" wire:click="sendResetLink" wire:loading.attr="disabled" class="btn btn-link link-green fs-13 text-decoration-none">
                    <span wire:loading.remove wire:target="sendResetLink">Belum menerima email? Kirim ulang</span>
                    <span wire:loading wire:target="sendResetLink">
                        <span class="spinner-border spinner-border-sm me-1"></span> Mengirim ulang...
                    </span>
                </button>
            </div>
        </div>
    @else
        <form wire:submit="sendResetLink">
            <div class="form-group mb-4">
                <label class="form-label" for="email">Alamat Email</label>
                <input class="form-control @error('email') is-invalid @enderror" type="email"
                    id="email" placeholder="nama@email.com" wire:model="email" autofocus>
                @error('email')
                    <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <button class="btn btn-auth-submit" type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="sendResetLink">Kirim Tautan Reset Password</span>
                    <span wire:loading wire:target="sendResetLink">
                        <span class="spinner-border spinner-border-sm me-1"></span> Mengirim...
                    </span>
                </button>
            </div>

            <div class="text-center pt-2">
                <p class="text-muted fs-13 mb-0">
                    Sudah ingat password Anda?
                    <a class="link-green ms-1" href="{{ route('login') }}" wire:navigate>
                        Masuk di sini
                    </a>
                </p>
            </div>
        </form>
    @endif
</div>
