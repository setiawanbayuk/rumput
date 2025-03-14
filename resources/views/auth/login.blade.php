@extends('layouts.landing')

@if (session('error'))
    <div class="alert alert-danger w-75 text-center">
        {{ session('error') }}
    </div>
@endif

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/landing.png') }}') no-repeat center center; background-size: cover;">

        <div class="col-md-4 d-flex justify-content-center">
            <div class="card shadow-lg text-center p-4 position-relative d-flex flex-column align-items-center"
                style="border-radius: 8px; width: 100%; max-width: 380px; padding: 40px; margin-top: 60px;">

                <!-- Logo Kediri dengan Background Oval -->
                <div class="position-absolute d-flex align-items-center justify-content-center"
                    style="top: -45px; z-index: 10; width: 300px; height: 70px; background-color: white; border-radius: 35px; padding: 10px;">
                    <img src="{{ asset('assets/images/logo-pemkot.png') }}" class="img-fluid" style="max-height: 50px;"
                        alt="Logo Kediri">
                </div>



                <!-- Judul -->
                <img src="{{ asset('assets/esuket.png') }}" alt="Logo"
                    style="width: 200px; height: 30px; margin-top: 30px;">
                <p class="text-muted" style="font-size: 14px; margin-top: 15px;">Log in untuk Melanjutkan</p>

                <!-- Form Login -->
                <form method="POST" action="{{ route('login') }}" class="d-flex flex-column align-items-center w-100">
                    @csrf

                    <div class="form-floating mb-3 w-75">
                        <input type="email" id="email" name="email" class="form-control text-left"
                            placeholder="Alamat Email" required autocomplete="email" autofocus>
                        <label for="email" style="color: #AEA07A;"><i class="ri-user-line" style="color: #AEA07A;"></i>
                            Alamat Email</label>
                    </div>

                    <div class="form-floating mb-3 w-75">
                        <input type="password" id="password" name="password" class="form-control text-left"
                            placeholder="Password" required autocomplete="current-password">
                        <label for="password" style="color: #AEA07A;"><i class="ri-lock-line" style="color: #AEA07A;"></i>
                            Password</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 w-75">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Ingat Saya</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-muted" style="font-size: 12px;">Lupa
                            Password?</a>
                    </div>

                    <!-- Tombol Login -->
                    <button type="submit" class="btn text-white mb-3 py-2 w-75"
                        style="background: linear-gradient(to right, #c19a6b, #b08d57); font-size: 14px; border-radius: 6px;">
                        <i class="ri-login-box-line"></i> Log In
                    </button>
                </form>

                <!-- Separator -->
                <div class="text-center text-muted my-2" style="font-size: 12px;">atau Lanjutkan dengan</div>

                <!-- Tombol SSO -->
                <a href="{{ route('sso.login') }}" class="btn text-white mb-3 py-2 w-75"
                    style="background: linear-gradient(to right, #4A90E2, #1C3FAA); font-size: 14px; border-radius: 6px;">
                    <i class="ri-fingerprint-2-line"></i> Log In SSO
                </a>

                <!-- Registrasi -->
                <div class="text-center text-muted" style="font-size: 12px;">Belum Punya Akun?</div>

                <a href="{{ route('register') }}" class="btn border mt-2 py-2 w-75"
                    style="font-size: 14px; border-radius: 6px;">Register</a>
            </div>
        </div>
    </div>
@endsection
