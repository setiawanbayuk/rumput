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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    @stack('styles')
    @routes
</head>

<body class="min-vh-100 d-flex flex-column">

    <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
        <div class="container">

            <!-- Logo E-SUKET -->
            <a class="navbar-brand" href="{{ url('/warga') }}">
                <img src="{{ asset('assets/esuket.png') }}" alt="E-SUKET Logo" height="30">
            </a>

            {{-- <li class="nav-item dropdown" style="display: block">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                    href="{{ route('profile') }}">
                    <span class="img-circle"><i class="ri-user-fill"></i>
                    </span>
                    <span class="d-none d-md-inline pl-2">{{ Auth::user()->name }}</span>
                </a>
            </li> --}}
            <li class="nav-item dropdown" style="display: block">
                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    <div class="user-info bg-success me-2"></div>
                    <span>{{ Auth::user()->name }}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('profile') }}">
                        <i class="ri-user-fill me-2"></i>
                        <span>Profile</span>
                    </a>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="ri-logout-circle-r-line me-2"></i>
                        <span>Logout</span>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>

        </div>
    </nav>

    <main class="py-4 flex-fill">
        @yield('content')
    </main>

    <footer class="py-3" style="background-color: rgb(150, 104, 19);">
        <div class="container d-flex justify-content-between align-items-center text-white">
            <p class="mb-0">© 2025 Pemerintah Kota Kediri</p>
            <p class="mb-0">Support by <a href="#" class="text-white text-decoration-none">Dinas Komunikasi
                    dan
                    Informatika Kota Kediri</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')
</body>

</html>
