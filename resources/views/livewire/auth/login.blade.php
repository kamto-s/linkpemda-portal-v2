<div>
    <!-- Logo & Header Form -->
    <div class="text-center mb-4">
        <a class="d-inline-block mb-3" href="{{ route('home') }}">
            <img src="{{ asset('assets/admin') }}/images/logo-dark.png" alt="Logo Linkpemda" height="34"
                onerror="this.src='{{ asset('assets/admin') }}/images/logo-sm.png'">
        </a>
        <p class="text-muted fs-13 mb-0">Silakan masukkan email dan password Anda</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form wire:submit="login">
        <!-- Input Email -->
        <div class="form-group mb-3">
            <label class="form-label" for="email">Alamat Email</label>
            <input class="form-control @error('email') is-invalid @enderror" type="email" id="email"
                placeholder="nama@email.com" wire:model="email" autofocus>
            @error('email')
                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Input Password -->
        <div class="form-group mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0" for="password">Password</label>
                <a class="link-green fs-12" href="{{ route('password.request') }}" wire:navigate>
                    Lupa password?
                </a>
            </div>
            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password"
                placeholder="••••••••" wire:model="password">
            @error('password')
                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me Checkbox -->
        <div class="form-group mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" wire:model="remember">
                <label class="form-check-label text-muted fs-13" for="remember">Ingat saya di perangkat ini</label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-group mb-0">
            <button class="btn btn-auth-submit" type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="login">Masuk</span>
                <span wire:loading wire:target="login">
                    <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                </span>
            </button>
        </div>
    </form>
</div>