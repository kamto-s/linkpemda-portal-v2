<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-4 col-md-5">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-center w-75 mx-auto auth-logo mb-4">
                        <a class="logo-dark" href="{{ route('home') }}">
                            <span><img src="{{ asset('assets/admin') }}/images/logo-dark.png" alt="Logo" height="34"></span>
                        </a>

                        <a class="logo-light" href="{{ route('home') }}">
                            <span><img src="{{ asset('assets/admin') }}/images/logo-light.png" alt="Logo" height="34"></span>
                        </a>
                        <p class="text-muted fs-13 mt-4">Masukkan email akun Anda untuk mendapatkan tautan pemulihan</p>
                    </div>

                    @if ($emailSent)
                        <div class="text-center py-3">
                            <div class="avatar-lg mx-auto mb-3">
                                <span class="avatar-title rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center"
                                    style="width: 64px; height: 64px;">
                                    <i class="mdi mdi-email-check fs-24"></i>
                                </span>
                            </div>
                            <h5 class="fw-bold text-success mb-2">Tautan Telah Dikirim!</h5>
                            <p class="text-muted fs-14 mb-4">
                                Kami telah mengirimkan tautan reset password ke <br>
                                <strong class="text-dark">{{ $email }}</strong>.<br>
                                Silakan periksa kotak masuk atau folder spam email Anda.
                            </p>

                            <div class="d-grid gap-2">
                                <a href="{{ route('login') }}" wire:navigate class="btn btn-primary py-2 fw-semibold">
                                    <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Halaman Login
                                </a>
                                <button type="button" wire:click="sendResetLink" wire:loading.attr="disabled" class="btn btn-link text-muted fs-13">
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
                                <label class="form-label fw-semibold" for="email">Alamat Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email"
                                    id="email" wire:model="email" autofocus>

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-0 text-center">
                                <button class="btn btn-primary w-100 py-2 fw-semibold" type="submit" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="sendResetLink">
                                        <i class="mdi mdi-email-send-outline me-1"></i> Kirim Tautan Reset Password
                                    </span>
                                    <span wire:loading wire:target="sendResetLink">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Mengirim...
                                    </span>
                                </button>
                            </div>

                        </form>
                    @endif

                </div>
            </div>

            @if (!$emailSent)
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="text-white">
                            Sudah ingat password Anda?
                            <a class="text-white fw-bold ms-1 text-decoration-underline" href="{{ route('login') }}" wire:navigate>
                                Masuk di sini
                            </a>
                        </p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
