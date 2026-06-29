<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">
    <title>{{ $title ?? config('app.name', 'E-SUKET') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="{{ asset('css/mobile-responsive-pro.css') }}?v=20260529-esuket-mobile-v5">
@stack('styles')
    <style>
        :root {
            --primary-color: #14b8a6;
        }

        html,
        body {
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #000;
        }

        /* Navbar Baru: Minimalis & Mewah */
        .floating-navbar {
            position: absolute;
            top: 0;
            width: 100%;
            z-index: 1050;
            padding: 20px 0;
        }

        .navbar-minimal {
            background: rgba(255, 255, 255, 0.05);
            /* Sangat transparan */
            backdrop-filter: blur(15px);
            /* Efek kaca */
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            /* Bentuk kapsul */
            padding: 10px 25px;
            transition: 0.3s ease;
        }

        .navbar-minimal:hover {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .footer-landing {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 0;
            font-size: 0.8rem;
            z-index: 10;
        }

        /* Button Login Petugas di Navbar */
        .btn-login-petugas {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 8px 20px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-login-petugas:hover {
            background: #fff;
            color: var(--primary-color);
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <header class="floating-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center navbar-minimal">
                <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none">
                    <img src="{{ asset('assets/esuket.png') }}" alt="Logo" height="30">
                </a>

                <a href="{{ route('login') }}" class="btn btn-login-petugas">
                    <i class="ri-admin-line me-1"></i> Login Petugas
                </a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer-landing">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-7 d-flex align-items-center">
                    <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="me-3"
                        style="width: 45px;">
                    <div class="x-small">
                        <h6 class="fw-bold mb-0" style="font-size: 0.85rem">Sistem Pelayanan Terpadu Kelurahan</h6>
                        <p class="mb-0 opacity-75">Pemerintah Kota Kediri</p>
                    </div>
                </div>
                <div class="col-md-5 text-md-end text-center mt-3 mt-md-0">
                    <h6 class="fw-bold mb-0" style="font-size: 0.85rem">NGURUS SURAT, NGGAK PAKAI RIBET!</h6>
                    <small class="opacity-50">© 2026 Kota Kediri. All Rights Reserved.</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('js/esuket-mobile-pro.js') }}?v=20260529-esuket-mobile-v5"></script>
</body>

</html>
