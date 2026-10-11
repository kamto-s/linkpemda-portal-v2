<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-4 col-md-5">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-center w-75 mx-auto auth-logo mb-4">
                        <a class='logo-dark' href='index.html'>
                            <span><img src="{{ asset('assets/admin') }}/images/logo-dark.png" alt=""
                                    height="34"></span>
                        </a>

                        <a class='logo-light' href='index.html'>
                            <span><img src="{{ asset('assets/admin') }}/images/logo-light.png" alt=""
                                    height="34"></span>
                        </a>
                        <p class="text-muted fs-13 mt-3">Silahkan login menggunakan akun Anda</p>
                    </div>

                    <form wire:submit="login">

                        <div class="form-group mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" type="email"
                                id="email" placeholder="Masukkan Email" wire:model="email">

                            @error('email')
                                <div class="invalid-feedback text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input class="form-control @error('password') is-invalid @enderror" type="password"
                                required="" id="password" placeholder="Masukkan Password" wire:model="password">

                            @error('password')
                                <div class="invalid-feedback text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="">
                                <input class="form-check-input" type="checkbox" id="checkbox-signin" checked
                                    id="remember" wire:model="remember">
                                <label class="form-check-label ms-2" for="checkbox-signin">Remember me</label>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-center">
                            <button class="btn btn-primary w-100" type="submit">
                                <span wire:loading.remove wire:target="login"> Login</span>
                                <span wire:loading wire:target="login">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                                </span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12 text-center">
                    <p class="text-white-50"> <a class='text-white-50 ms-1' href='pages-register.html'>Forgot your
                            password?</a></p>
                </div>
            </div>

        </div>
    </div>
</div>
