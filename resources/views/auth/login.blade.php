@extends('layouts.landing')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/landing.png') }}') no-repeat center center; background-size: cover;">

        <div class="col-md-4 d-flex justify-content-center">
            <div class="card shadow-lg text-center p-4 position-relative d-flex flex-column align-items-center"
                style="border-radius: 8px; width: 100%; max-width: 380px; padding: 40px; margin-top: 60px; z-index:2;">
                <div class="position-absolute d-flex align-items-center justify-content-center shadow-sm"
                    style="top: -45px; z-index: 10; width: 300px; height: 70px; background-color: white; border-radius: 35px; padding: 10px;">
                    <img src="{{ asset('assets/images/logo-pemkot.png') }}" class="img-fluid" style="max-height: 50px;"
                        alt="Logo Kediri">
                </div>

                <!-- Judul -->
                <img src="{{ asset('assets/esuket.png') }}" alt="Logo"
                    style="width: 200px; height: 30px; margin-top: 30px;">
                <p class="text-dark" style="font-size: 12px; margin-top: 30px;">Log in untuk Melanjutkan</p>

                @if (session('error'))
                    <div class="mb-3">
                        <div class="py-2 px-3 rounded-1 text-center text-danger" style="background: #F9DCE3; font-size: 13px;">
                            {{ session('error') }}</div>
                    </div>
                @endif
                <!-- Form Login -->
                <form method="POST" action="{{ route('login') }}" class="d-flex flex-column align-items-center w-100">
                    @csrf

                    <div class="form-floating mb-3 w-75">
                        <input type="email" id="email" value="{{ old('email') }}" name="email"
                            class="form-control text-left" style="border: 1px solid #AEA07A;" placeholder="Alamat Email"
                            required autocomplete="email" autofocus>
                        <label for="email" style="color: #AEA07A;"><i class="ri-user-line" style="color: #AEA07A;"></i>
                            Alamat Email</label>
                    </div>

                    <div class="form-floating mb-2 w-75">
                        <input type="password" id="password" name="password" class="form-control text-left"
                            style="border: 1px solid #AEA07A;" placeholder="Password" required
                            autocomplete="current-password">
                        <label for="password" style="color: #AEA07A;"><i class="ri-lock-line" style="color: #AEA07A;"></i>
                            Password</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 w-75">
                        <div class="form-check">
                            <input class="form-check-input border-dark" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-dark" style="font-size: 12px;" for="remember">Ingat
                                Saya</label>
                        </div>
                    </div>

                    <!-- Tombol Login -->
                    <button type="submit" class="btn-login">
                        <i class="ri-login-box-line"></i> Log In
                    </button>
                </form>

                <!-- Separator -->
                <div class="text-center my-2" style="font-size: 11px;">atau Lanjutkan dengan</div>

                <!-- Tombol SSO -->
                <a href="{{ route('sso.login') }}" class="btn-sso">
                    <i class="ri-fingerprint-2-line"></i> Log In SSO
                </a>

                <img src="{{ asset('assets/bgcard-clean.png') }}" alt="Petugas" class="login-figure d-none d-lg-block">
            </div>
        </div>
    @endsection
