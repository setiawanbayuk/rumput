<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div class="welcome-screen">
        {{-- <div class="text-center">
            <img src="{{ asset('assets/logo.png') }}" class="object-fit-contain" style="width: 100px" alt="">
        </div>
        <div class="my-5 text-center">
            <div class="h2 fw-bold text-light">E-SUKET</div>
            <div class="h5 fw-bold text-light">PEMERINTAH KOTA KEDIRI</div>
        </div>
        <div class="d-flex flex-column align-items-center justify-content-center gap-2">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                <i class="ri-login-box-line"></i>
                <span>Login</span>
            </a>
            atau Lanjutkan dengan
            <a href="{{ route('sso.login') }}" class="btn btn-warning btn-lg">
                <i class="ri-fingerprint-2-line me-1"></i>
                <span>Login SSO</span>
            </a>
            Belum Punya Akun?
            <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                <span>Register</span>
            </a>
        </div> --}}

        {{-- <div class="center-img">
            <img src="{{ asset('assets/bgcard.png') }}" style="width: 500px" alt="">
        </div> --}}
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body ">
                        <div class="my-5 text-center">
                            <div class="text-center mb-4">
                                <img src="{{ asset('assets/esuket.png') }}" class="object-fit-contain"
                                    style="height: 40px" alt="">
                            </div>
                            <div class="h5">Login untuk melanjutkan</div>
                        </div>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-floating mb-3">
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>
                                <label for="email">{{ __('Alamat Email') }}</label>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    value="{{ old('password') }}" required autocomplete="current-password" autofocus>
                                <label for="password">{{ __('Password') }}</label>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-flex flex-row justify-content-around">
                                <div class="p-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Ingat Saya') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="p-2">
                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Lupa Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                                <button type="submit" class="btn btn-warning btn-lg col-6">
                                    <i class="ri-login-box-line"></i>
                                    {{ __('Login') }}
                                </button>
                                atau Lanjutkan dengan
                                <a href="{{ route('sso.login') }}" class="btn btn-primary btn-lg col-6">
                                    <i class="ri-fingerprint-2-line me-1"></i>
                                    <span>Login SSO</span>
                                </a>
                                Belum Punya Akun?
                                <a href="{{ route('register') }}" class="btn btn-light btn-lg col-6">
                                    <span>Register</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
