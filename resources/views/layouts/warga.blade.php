<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">

    <title>{{ config('app.name', 'E-SUKET') }}</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    
    <link rel="stylesheet" href="{{ asset('css/mobile-responsive-pro.css') }}?v=20260529-esuket-mobile-v5">
<!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @stack('styles')

    <style>
        .floating-navbar {
            position: sticky;
            top: 12px;
            z-index: 1030;
        }

        .navbar-card {
            background: var(--rumput-glass-bg);
            backdrop-filter: blur(var(--rumput-glass-blur));
            -webkit-backdrop-filter: blur(var(--rumput-glass-blur));
            border: 1px solid var(--rumput-glass-border);
            border-radius: var(--rumput-radius-lg);
            box-shadow: var(--rumput-glass-shadow);
        }

        .navbar-card .nav-link {
            border: 1px solid var(--rumput-glass-border);
            border-radius: 8px;
            padding: .4rem 2rem;
            font-weight: 500;
            color: var(--rumput-ink);
        }

        .navbar-card .nav-link.active,
        .navbar-card .nav-link:hover {
            background: var(--rumput-teal);
            border-color: var(--rumput-teal);
            color: #fff;
        }

        @media (max-width: 576px) {
            .navbar .navbar-brand {
                margin-left: auto;
                margin-right: auto;
            }

            .navbar .navbar-brand img {
                height: 18px;   /* dari 25px → 18px */
            }

            .navbar-card .nav-link {
                padding: .35rem 1rem;
                font-size: .75rem;
                border-radius: 6px;
            }

            .navbar .nav-link i {
                font-size: 16px ;
            }

            .dropdown-menu {
                font-size: .8rem ;
                padding: .3rem .2rem ;
            }

            .dropdown-item {
                padding: .3rem .7rem ;
            }

            .navbar-nav {
                margin-left: 8px ;
            }

            .navbar-card {
                padding-top: 8px ;
                padding-bottom: 8px ;
            }
        }
    </style>
    @routes
</head>

<body class="rumput-glass-body min-vh-100 d-flex flex-column">
    <!-- navbar -->
    <div class="container floating-navbar">
        <nav class="navbar navbar-expand-lg navbar-card mt2 mx-4 px-4 py-3">

            <!-- toggler mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarMain" aria-controls="navbarMain"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Logo E-SUKET -->
            <a class="navbar-brand" href="{{ url('/warga') }}">
                <img src="{{ asset('assets/esuket.png') }}" alt="E-SUKET Logo" height="25">
            </a>

            <!-- menu tengah -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <div class="d-flex justify-content-center w-100 gap-4">
                    <a class="nav-link {{ request()->is('warga') ? 'active' : '' }}" href="{{ url('/warga') }}">
                        Beranda
                    </a>
                    <a class="nav-link {{ request()->is('warga/ajukan') ? 'active' : '' }}"
                        href="{{ url('/warga/ajukan') }}">
                        Ajukan Surat
                    </a>
                </div>

                <!-- kanan: dropdown user -->
                <ul class="navbar-nav ms-lg-3">
                    @auth
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                            role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-user-fill me-2"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="ri-user-fill me-2"></i> {{ Auth::user()->name }}
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ri-logout-circle-r-line me-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf</form>
                        </div>
                    </li>
                    @endauth
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    @endguest
                </ul>
            </div>
        </nav>
    </div>

    <!-- SIDEBAR OFFCANVAS -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSuket" aria-labelledby="offcanvasSuketLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold ms-4" id="offcanvasSuketLabel">
                <img src="{{ asset('assets/images/icon.png') }}" alt="Icon" class="me-2 small-icon">
                Jenis Surat
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="list-group">
                @foreach ($surat as $item)
                    <a href="{{ route($item['jenis'] . '.warga') }}" class="list-group-item">
                        <img src="{{ asset('assets/images/' . $item['assets']) }}" alt="Surat"
                            class="img-fluid rounded me-2">
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="py-0 flex-fill">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="py-3" style="background-color: #AB9C71">
        <div class="container d-flex justify-content-between align-items-center text-white">
            <p class="mb-0">© 2025 Pemerintah Kota Kediri</p>
            <p class="mb-0">Support by <a href="#" class="text-white text-decoration-none">Dinas Kominfo Kota
                    Kediri</a></p>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('js/esuket-mobile-pro.js') }}?v=20260529-esuket-mobile-v5"></script>
</body>

</html>
