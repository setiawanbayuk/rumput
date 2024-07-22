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
      <div class="text-center">
        <img src="{{ asset('assets/logo.png') }}" class="object-fit-contain" style="width: 100px" alt="">
      </div>
      <div class="my-5 text-center">
        <div class="h2 fw-bold text-light">E-SUKET</div>
        <div class="h5 fw-bold text-light">PEMERINTAH KOTA KEDIRI</div>
      </div>
      <div class="d-flex align-items-center justify-content-center gap-2">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
          <i class="ri-fingerprint-line me-1"></i>
          <span>Login</span>
        </a>
        <a href="{{ route('register') }}" class="btn btn-light btn-lg">
          <span>Register</span>
        </a>
      </div>
    </div>
  </body>
</html>
