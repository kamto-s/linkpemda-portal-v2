<div>
    <div class="text-center mb-4">
        <a class="d-inline-block mb-3" href="{{ route('home') }}">
            <img src="{{ asset('assets/admin') }}/images/logo-dark.png" alt="Logo Linkpemda" height="34" onerror="this.src='{{ asset('assets/admin') }}/images/logo-sm.png'">
        </a>
        <h4 class="fw-bold text-dark mb-1 fs-18">{{ $isInitialSetup ? 'Buat Password Akun' : 'Atur Ulang Password' }}</h4>
        <p class="text-muted fs-13 mb-0">{{ $isInitialSetup ? 'Silakan tentukan password untuk mengaktifkan akun Anda' : 'Silakan masukkan password baru untuk akun Anda' }}</p>
    </div>

    @if ($errors->has('token'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="mdi mdi-alert-circle fs-18"></i>
            <div class="fs-13">
                {{ $errors->first('token') }}
            </div>
        </div>
    @endif

    <form wire:submit="savePassword">
        <!-- Email -->
        <div class="form-group mb-3">
            <label class="form-label" for="email">Alamat Email</label>
            <input class="form-control bg-light @error('email') is-invalid @enderror" type="email"
                id="email" wire:model="email" {{ !empty($email) ? 'readonly' : '' }}>
            @error('email')
                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group mb-3">
            <label class="form-label" for="password">
                {{ $isInitialSetup ? 'Password' : 'Password Baru' }} <span class="text-danger">*</span>
            </label>
            <input class="form-control @error('password') is-invalid @enderror" type="password"
                id="password" placeholder="Minimal 8 karakter" wire:model="password" autofocus>
            @error('password')
                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Konfirmasi Password -->
        <div class="form-group mb-4">
            <label class="form-label" for="password_confirmation">
                {{ $isInitialSetup ? 'Konfirmasi Password' : 'Konfirmasi Password Baru' }} <span class="text-danger">*</span>
            </label>
            <input class="form-control" type="password"
                id="password_confirmation" placeholder="Ulangi password" wire:model="password_confirmation">
        </div>

        <div class="form-group mb-3">
            <button class="btn btn-auth-submit" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="savePassword">
                    {{ $isInitialSetup ? 'Simpan & Aktifkan Akun' : 'Perbarui Password & Masuk' }}
                </span>
                <span wire:loading wire:target="savePassword">
                    <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                </span>
            </button>
        </div>

        @if (!$isInitialSetup)
            <div class="text-center pt-2">
                <p class="text-muted fs-13 mb-0">
                    Sudah ingat password Anda?
                    <a class="link-green ms-1" href="{{ route('login') }}" wire:navigate>
                        Masuk di sini
                    </a>
                </p>
            </div>
        @endif
    </form>
</div>
