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
                        <h4 class="mt-3 mb-1 fw-bold text-dark">{{ $isInitialSetup ? 'Buat Password Akun' : 'Atur Ulang Password' }}</h4>
                        <p class="text-muted fs-13 mt-2">{{ $isInitialSetup ? 'Silakan tentukan password untuk mengaktifkan akun Anda' : 'Silakan masukkan password baru untuk akun Anda' }}</p>
                    </div>

                    @if ($errors->has('token'))
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                            <i class="mdi mdi-alert-circle fs-18"></i>
                            <div>
                                {{ $errors->first('token') }}
                            </div>
                        </div>
                    @endif

                    <form wire:submit="savePassword">

                        <!-- Email -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold" for="email">Alamat Email</label>
                            <input class="form-control bg-light @error('email') is-invalid @enderror" type="email"
                                id="email" wire:model="email" {{ !empty($email) ? 'readonly' : '' }}>

                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-semibold" for="password">{{ $isInitialSetup ? 'Password' : 'Password Baru' }} <span class="text-danger">*</span></label>
                            <input class="form-control @error('password') is-invalid @enderror" type="password"
                                id="password" placeholder="Minimal 8 karakter" wire:model="password" autofocus>

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold" for="password_confirmation">{{ $isInitialSetup ? 'Konfirmasi Password' : 'Konfirmasi Password Baru' }} <span class="text-danger">*</span></label>
                            <input class="form-control" type="password"
                                id="password_confirmation" placeholder="Ulangi password" wire:model="password_confirmation">
                        </div>

                        <div class="form-group mb-0 text-center">
                            <button class="btn btn-primary w-100 py-2 fw-semibold" type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="savePassword">
                                    <i class="mdi mdi-check-circle-outline me-1"></i> {{ $isInitialSetup ? 'Simpan & Aktifkan Akun' : 'Perbarui Password & Masuk' }}
                                </span>
                                <span wire:loading wire:target="savePassword">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                                </span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            @if (!$isInitialSetup)
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
