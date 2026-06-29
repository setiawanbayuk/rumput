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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @stack('styles')

    <style>
        .rating-star i {
            font-size: 28px;
            color: #c2c2c2;
            cursor: pointer;
        }
        .rating-star i.active {
            color: #e2b84c;
        }
        /* navbar */
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

        /* OFFCANVAS WIDTH */
        #offcanvasSuket {
            width: 280px;
        }
        @media (max-width: 576px) {
            #offcanvasSuket {
                width: 60% !important;
            }
        }

        /* SEMBUNYIKAN SIDEBAR DESKTOP DI MOBILE */
        @media (max-width: 991.98px) {
            .sidebar-desktop {
                display: none !important;
            }
        }

        /* SEMBUNYIKAN OFFCANVAS DI DESKTOP */
        @media (min-width: 992px) {
            #offcanvasSuket {
                display: none !important;
            }
        }

        /* PERBAIKI GRID KONTEN DI MOBILE */
        @media (max-width: 991.98px) {
            .col-md-7 {
                width: 100% !important;
            }
        }
    </style>
    @routes
</head>

<body class="rumput-glass-body min-vh-100 d-flex flex-column">
    <!-- navbar -->
    <div class="container floating-navbar pb-2">
        <nav class="navbar navbar-expand-lg navbar-card mt2 mx-4 px-4 py-3">
            <!-- TOMBOL TOGGLE SIDEBAR MOBILE (HANYA MOBILE) -->
            <button class="btn d-lg-none me-2" 
                    type="button" 
                    data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasSuket">
                <i class="ri-menu-line" style="font-size: 26px;"></i>
            </button>
            
            <!-- Logo E-SUKET -->
            <a class="navbar-brand" href="{{ url('/warga') }}">
                <img src="{{ asset('assets/esuket.png') }}" alt="E-SUKET Logo" height="25">
            </a>

            <!-- toggler mobile untuk menu navbar (bootstrap) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarMain" aria-controls="navbarMain"
                    aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>

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

    <!-- SIDEBAR MOBILE (OFFCANVAS) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSuket" aria-labelledby="offcanvasSuketLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold ms-1" id="offcanvasSuketLabel">
                <img src="{{ asset('assets/images/icon.png') }}" alt="" class="me-2 small-icon">
                Jenis Surat
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">
            <div class="suket-list">
                @foreach ($surat as $item)
                    <a href="{{ route($item['jenis'] . '.warga') }}" class="suket-item has-thumb">
                        <div class="suket-item-title">
                            {{ strtoupper($item['name'] ?? $item['jenis']) }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>


    <div class="d-flex flex-row flex-fill justify-content-center ">
        {{-- Sidebar Jenis Surat --}}
        <div class="col-md-3 sidebar-desktop">
            <div class="card suket-sidebar shadow-sm rounded-3 mb-4" 
                style="border: 2px solid #eee; margin-left: 35px; margin-top: 40px;">
                <div class="card-body">
                    <h6 class="suket-title mb-4 ms-2">
                        <img src="{{ asset('assets/images/icon.png') }}" alt="Icon Jenis Surat" class="me-4 small-icon">
                        Jenis Surat
                    </h6>

                    <div class="suket-list">
                        @foreach ($surat as $item)
                            @php
                                $clean = "assets/images/{$item['jenis']}-clean.png";
                                $default = "assets/images/{$item['assets']}";
                                $img = file_exists(public_path($clean)) ? $clean : $default;
                            @endphp

                            <a href="{{ route($item['jenis'] . '.warga') }}" class="suket-item has-thumb">
                                <div class="suket-thumb">
                                    <img src="{{ asset($img) }}" alt="{{ $item['nama'] ?? $item['jenis'] }}">
                                </div>

                                <span class="suket-item-title">
                                    {{ $item['name'] ?? \Illuminate\Support\Str::headline($item['jenis']) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <main class="py-4 mx-4 flex-fill">
                @yield('content')
            </main>
        </div>
    </div>

    <footer class="py-3" style="background-color: #AB9C71;">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @stack('scripts')
    <script>
        var selectedRating = 0;

        // highlight bintang
        $(document).on('click', '.rating-star i', function () {
            selectedRating = $(this).data('value');
            $(".rating-star i").removeClass('active');
            for (let i = 1; i <= selectedRating; i++) {
                $(".rating-star i[data-value='" + i + "']").addClass('active');
            }
        });

        // buka modal nilai
        function openModalNilai(alias, id, noSurat, judul) {
            $("#modalNilai").data('alias', alias);
            $("#modalNilai").data('id', id);
            $("#judulSurat").text(judul);
            $("#noSurat").text(noSurat);

            selectedRating = 0;
            $(".rating-star i").removeClass('active');
            $("#komentar").val('');

            $("#modalNilai").modal('show');
        }

        // kirim penilaian
        $("#btnKirimNilai").on("click", function () {
            let modal  = $("#modalNilai");
            let alias  = modal.data('alias');
            let id     = modal.data('id');
            let komentar = $("#komentar").val();

            if (selectedRating === 0) {
                return Swal.fire("Oops!", "Silakan beri rating terlebih dahulu", "warning");
            }

            $.ajax({
                url: "/warga/" + alias + "/" + id + "/nilai",
                type: "POST",
                data: {
                    rating: selectedRating,
                    komentar: komentar,
                    _token: "{{ csrf_token() }}"
                },
                success: function () {
                    $("#modalNilai").modal("hide");
                    Swal.fire("Berhasil!", "Terima kasih atas penilaian Anda!", "success");
                    location.reload();
                },
                error: function (res) {
                    console.log(res.responseText);
                    Swal.fire("Gagal", "Terjadi kesalahan", "error");
                }
            });
        });
    </script>
    <script>
        function lihatPenilaian(alias, id) {
            $.ajax({
                url: "/warga/" + alias + "/" + id + "/nilai",
                method: "GET",
                success: function (res) {
                    console.log("HASIL:", res);   // ← Tambahkan ini
                    
                    let stars = "";
                    for (let i = 1; i <= 5; i++) {
                        stars += `<i class="ri-star-${i <= res.rating ? 'fill' : 'line'}"></i>`;
                    }
                    $("#lihatStars").html(stars);

                    $("#lihatKomentar").text(res.komentar || "-");
                    $("#lihatTanggalNilai").text(res.tanggal || "-");

                    $("#modalLihatNilai").modal('show');
                },
                error: function () {
                    Swal.fire("Gagal", "Tidak dapat mengambil data penilaian.", "error");
                }
            });
        }
    </script>
    <script src="{{ asset('js/esuket-mobile-pro.js') }}?v=20260529-esuket-mobile-v5"></script>
</body>
</html>
