<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">

    <title>{{ config('app.name', 'E-SUKET') }}</title>

    <!-- Fonts -->
    {{-- <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet"> --}}

    {{-- <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" /> --}}
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @stack('styles')

    <style>
        .nav-link {
            border: 1px solid #fff;
            border-radius: 8px;
            padding: .4rem .4rem;
            font-weight: 500;
            color: #fff;
        }

        .nav-link.active,
        .nav-link:hover {
            border: 1px solid #fff;
            color: #fff;
        }
    </style>
    @routes
</head>

<body>
    <div id="app">
        {{-- <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main> --}}

        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <div class="sidebar-body">
                    <ul class="sidebar-nav">
                        {{-- BERANDA --}}
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ url('home') }}">
                                <i class="ri-home-2-line"></i>
                                <span class="sidebar-text">Beranda</span>
                            </a>
                        </li>
                        {{-- SECTION: MENU LAYANAN --}}
                        @if (auth()->user()->role_id != 2)
                            <li class="sidebar-item">
                                <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                                    data-bs-target="#pelayanan" aria-expanded="false" aria-controls="pelayanan">
                                    <i class="ri-file-edit-line"></i>
                                    <span class="sidebar-text">Pelayanan Warga</span>
                                </a>

                                <ul id="pelayanan" class="sidebar-dropdown list-unstyled collapse"
                                    data-bs-parent="#sidebar">
                                    @if (auth()->user()->role_id == 5 || auth()->user()->role_id == 6)
                                        {{-- SKTM KHUSUS ROLE 5 & 6 --}}
                                        <li class="sidebar-item">
                                            <a href="{{ route('sktm.index') }}" class="sidebar-link">
                                                <span class="nav-icon-wrapper">
                                                    <img src="{{ asset('assets/icons/sktm.svg') }}"
                                                        class="nav-icon nav-icon-default" alt="SKTM">
                                                    <img src="{{ asset('assets/icons/sktm-hover.svg') }}"
                                                        class="nav-icon nav-icon-hover" alt="">
                                                </span>
                                                <span class="sidebar-text">Surat Keterangan Miskin</span>
                                            </a>
                                        </li>
                                    @else
                                        <li class="sidebar-item">
                                            <a href="{{ route('skbn.index') }}" class="sidebar-link">
                                                <span class="nav-icon-wrapper">
                                                    <img src="{{ asset('assets/icons/skbn.svg') }}"
                                                        class="nav-icon nav-icon-default" alt="SKBN">
                                                    <img src="{{ asset('assets/icons/skbn-hover.svg') }}"
                                                        class="nav-icon nav-icon-hover" alt="">
                                                </span>
                                                <span class="sidebar-text">Surat Ket. Belum Menikah</span>
                                            </a>
                                        </li>

                                        <li class="sidebar-item">
                                            <a href="{{ route('skboro.index') }}" class="sidebar-link">
                                                <span class="nav-icon-wrapper">
                                                    <img src="{{ asset('assets/icons/skboro.svg') }}"
                                                        class="nav-icon nav-icon-default" alt="SKBORO">
                                                    <img src="{{ asset('assets/icons/skboro-hover.svg') }}"
                                                        class="nav-icon nav-icon-hover" alt="">
                                                </span>
                                                <span class="sidebar-text">Surat Keterangan Boro</span>
                                            </a>
                                        </li>

                                        <li class="sidebar-item">
                                            <a href="{{ route('skdom.index') }}" class="sidebar-link">
                                                <span class="nav-icon-wrapper">
                                                    <img src="{{ asset('assets/icons/skdom.svg') }}"
                                                        class="nav-icon nav-icon-default" alt="SKDOM">
                                                    <img src="{{ asset('assets/icons/skdom-hover.svg') }}"
                                                        class="nav-icon nav-icon-hover" alt="">
                                                </span>
                                                <span class="sidebar-text">Surat Keterangan Domisili</span>
                                            </a>
                                        </li>

                                        <li class="sidebar-item">
                                            <a href="{{ route('skkelahiran.index') }}" class="sidebar-link">
                                                <span class="nav-icon-wrapper">
                                                    <img src="{{ asset('assets/icons/skkelahiran.svg') }}"
                                                        class="nav-icon nav-icon-default" alt="SKKELAHIRAN">
                                                    <img src="{{ asset('assets/icons/skkelahiran-hover.svg') }}"
                                                        class="nav-icon nav-icon-hover" alt="">
                                                </span>
                                                <span class="sidebar-text">Surat Keterangan Kelahiran</span>
                                            </a>
                                        </li>

                                        <li class="sidebar-item">
                                            <a href="{{ route('skkematian.index') }}" class="sidebar-link">
                                                <span class="nav-icon-wrapper">
                                                    <img src="{{ asset('assets/icons/skkematian.svg') }}"
                                                        class="nav-icon nav-icon-default" alt="SKKEMATIAN">
                                                    <img src="{{ asset('assets/icons/skkematian-hover.svg') }}"
                                                        class="nav-icon nav-icon-hover" alt="">
                                                </span>
                                                <span class="sidebar-text">Surat Keterangan Kematian</span>
                                            </a>
                                        </li>

                                        <li class="sidebar-item">
                                            <a href="{{ route('sktm.index') }}" class="sidebar-link">
                                                <span class="nav-icon-wrapper">
                                                    <img src="{{ asset('assets/icons/sktm.svg') }}"
                                                        class="nav-icon nav-icon-default" alt="SKTM">
                                                    <img src="{{ asset('assets/icons/sktm-hover.svg') }}"
                                                        class="nav-icon nav-icon-hover" alt="">
                                                </span>
                                                <span class="sidebar-text">Surat Keterangan Miskin</span>
                                            </a>
                                        </li>

                                        <li class="sidebar-item">
                                            <a href="{{ route('skhsl.index') }}" class="sidebar-link">
                                                <span class="nav-icon-wrapper">
                                                    <img src="{{ asset('assets/icons/skhsl.svg') }}"
                                                        class="nav-icon nav-icon-default" alt="SKHSL">
                                                    <img src="{{ asset('assets/icons/skhsl-hover.svg') }}"
                                                        class="nav-icon nav-icon-hover" alt="">
                                                </span>
                                                <span class="sidebar-text">Surat Keterangan Penghasilan</span>
                                            </a>
                                        </li>

                                        <li class="sidebar-item">
                                            <a href="{{ route('skusaha.index') }}" class="sidebar-link">
                                                <span class="nav-icon-wrapper">
                                                    <img src="{{ asset('assets/icons/skusaha.svg') }}"
                                                        class="nav-icon nav-icon-default" alt="SKUASAHA">
                                                    <img src="{{ asset('assets/icons/skusaha-hover.svg') }}"
                                                        class="nav-icon nav-icon-hover" alt="">
                                                </span>
                                                <span class="sidebar-text">Surat Keterangan Usaha</span>
                                            </a>
                                        </li>

                                        <li class="sidebar-item">
                                            <a href="{{ route('suket.index') }}" class="sidebar-link">
                                                <span class="nav-icon-wrapper">
                                                    <img src="{{ asset('assets/icons/suket.svg') }}"
                                                        class="nav-icon nav-icon-default" alt="SUKET">
                                                    <img src="{{ asset('assets/icons/suket-hover.svg') }}"
                                                        class="nav-icon nav-icon-hover" alt="">
                                                </span>
                                                <span class="sidebar-text">Surat Keterangan</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        {{-- SECTION: PENGATURAN / TOOLS --}}
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                                data-bs-target="#tools" aria-expanded="false" aria-controls="tools">
                                <i class="ri-tools-fill"></i>
                                <span class="sidebar-text">Tools</span>
                            </a>
                            <ul id="tools" class="sidebar-dropdown list-unstyled collapse"
                                data-bs-parent="#sidebar">
                                <li class="sidebar-item">
                                    <a href="{{ url('/profile') }}" class="sidebar-link">
                                        <i class="ri-profile-fill"></i>
                                        <span class="sidebar-text">Profil</span>
                                    </a>
                                </li>
                                @if (auth()->user()->role_id != 2)
                                    <li class="sidebar-item">
                                        <a href="{{ url('/') }}" class="sidebar-link">
                                            <i class="ri-file-list-line"></i>
                                            <span class="sidebar-text">Rekap</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a href="{{ url('/') }}" class="sidebar-link">
                                            <i class="ri-building-fill"></i>
                                            <span class="sidebar-text">Profil Instansi</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a href="{{ url('/template') }}" class="sidebar-link">
                                            <i class="ri-file-2-line"></i>
                                            <span class="sidebar-text">Template Surat</span>
                                        </a>
                                    </li>
                                @endif
                                @if (auth()->user()->role_id == 7)
                                    <li class="sidebar-item">
                                        <a href="{{ url('/jenis') }}" class="sidebar-link">
                                            <i class="ri-folder-2-line"></i>
                                            <span class="sidebar-text">Jenis Surat</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>

                        <li class="sidebar-item">
                            <a href="{{ route('warga') }}" class="sidebar-link">
                                <i class="ri-user-shared-fill"></i>
                                <span class="sidebar-text">Masuk Sebagai Warga</span>
                            </a>
                        </li>
                    </ul>
                </div>
                {{-- FOOTER SIDEBAR --}}
                <div class="sidebar-footer">
                    <a class="sidebar-link" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ri-logout-box-line"></i>
                        <span class="sidebar-text">{{ auth()->user()->name }}</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </aside>

            {{-- KONTEN UTAMA (TOPBAR + MAIN) --}}
            <div class="content-wrapper">
                {{-- NAVBAR ATAS --}}
                <header class="topbar shadow-sm">
                    <div class="topbar-left">
                        <button class="toggle-btn" type="button">
                            <i class="ri-menu-line"></i>
                        </button>
                        <a href="{{ route('home') }}" class="sidebar-brand">
                            <img src="{{ asset('assets/esuket-white.png') }}" class="object-fit-contain"
                                style="height: 20px" alt="">
                        </a>
                    </div>

                    <div class="topbar-right">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center text-white"
                            href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
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
                    </div>
                </header>

                <main class="main py-4" style="background-color: #f9f9f9">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    <script>
        const sidebar = document.querySelector("#sidebar");
        const hamBurger = document.querySelector(".toggle-btn");

        hamBurger.addEventListener("click", function () {
            sidebar.classList.toggle("expand");

            const chartContainer = document.getElementById("chartSuratDrill");
            if (chartContainer) chartContainer.classList.add("animating");
        });

        sidebar.addEventListener("transitionend", (e) => {
            if (e.propertyName === "width") {

                setTimeout(() => {
                    if (window.chartSurat) {
                        window.chartSurat.reflow();
                        window.chartSurat.redraw();
                    }
                }, 20);

                const chartContainer = document.getElementById("chartSuratDrill");
                if (chartContainer) chartContainer.classList.remove("animating");
            }
        });

    </script>

    <script>
        document.querySelectorAll("#sidebar .sidebar-item").forEach(item => {
            item.addEventListener("mouseenter", function() {
                const rect = this.getBoundingClientRect();
                this.style.setProperty("--item-top", `${rect.top}px`);
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')
    @stack('modals')
</body>

</html>
