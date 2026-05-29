<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    
    <link rel="stylesheet" href="{{ asset('css/mobile-responsive-pro.css') }}?v=20260529-esuket-mobile-v5">
<!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    @stack('styles')

    <style>
        /* navbar */
        .floating-navbar {
            position: sticky;
            top: 12px;
            z-index: 1030;
        }

        .navbar-card {
            background: #ffffff;
            border: 1px solid #eee;
            border-radius: 6px;
            box-shadow: 0 0px 8px rgba(0, 0, 0, .2);
        }

        .navbar-card .nav-link {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: .4rem 2rem;
            font-weight: 500;
            color: #333333;
        }

        .navbar-card .nav-link.active,
        .navbar-card .nav-link:hover {
            background: #b8a57e;
            color: #fff;
        }

        .back-link {
            color: #111;
            font-weight: 500;
        }

        .back-link:hover {
            color: #b8a57e;
        }
    </style>
    @routes
</head>

<body class="min-vh-100 d-flex flex-column">
    <!-- navbar -->
    <div class="container floating-navbar">
        <nav class="navbar navbar-expand-lg navbar-card mt2 mx-4 px-4 py-3">
            <!-- Logo E-SUKET -->
            @if (auth()->user()->role_id == 2)
                <a class="navbar-brand" href="{{ url('/warga') }}">
                    <img src="{{ asset('assets/esuket.png') }}" alt="E-SUKET Logo" height="25">
                </a>
            @else
                <a class="navbar-brand" href="{{ url('/home') }}">
                    <img src="{{ asset('assets/esuket.png') }}" alt="E-SUKET Logo" height="25">
                </a>
            @endif

            <!-- toggler mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- kanan: tombol kembali -->
            <div class="ms-auto">
                @if (auth()->user()->role_id != 2)
                    <a href="{{ url()->previous() ?: url('/home') }}"
                        class="back-link d-flex align-items-center gap-1 text-decoration-none">
                        <i class="ri-arrow-go-back-line"></i>
                        <span>Kembali</span>
                    </a>
                @else
                    <a href="{{ url()->previous() ?: url('/warga') }}"
                        class="back-link d-flex align-items-center gap-1 text-decoration-none">
                        <i class="ri-arrow-go-back-line"></i>
                        <span>Kembali</span>
                    </a>
                @endif
            </div>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <main class="py-0 flex-fill">
        @yield('content')
    </main>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    @stack('scripts')

    <script src="{{ asset('js/esuket-mobile-pro.js') }}?v=20260529-esuket-mobile-v5"></script>
</body>

</html>
