@extends('layouts.landing')

@section('content')
<div class="welcome-screen">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body ">
                    <div class="my-5 text-center">
                        {{-- <div class="text-center mb-4">
                            <img src="{{ asset('assets/esuket.png') }}" class="object-fit-contain"
                                style="height: 40px" alt="">
                        </div> --}}
                        <div class="h5">{{ config('app.name', 'Laravel') }}</div>
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

                        <div class="form-floating">
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

                        <div class="d-flex flex-row justify-content-between align-items-center mb-3">
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
@endsection
