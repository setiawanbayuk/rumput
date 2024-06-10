<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">

    <title>{{ config('app.name', 'Laravel') }}</title>

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
            <aside id="sidebar">
                <div class="d-flex">
                    <button class="toggle-btn" type="button">
                        <i class="ri-grid-fill"></i>
                    </button>
                    <div class="sidebar-logo">
                        <a href="{{ url('/') }}">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>
                </div>
                <ul class="sidebar-nav">
                    {{-- <li class="sidebar-item">
                        <a href="#" class="sidebar-link">
                            <i class="lni lni-user"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link">
                            <i class="lni lni-agenda"></i>
                            <span>Task</span>
                        </a>
                    </li> --}}
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                            data-bs-target="#auth" aria-expanded="false" aria-controls="auth">
                            <i class="ri-article-line"></i>
                            <span>Pelayanan Warga</span>
                        </a>
                        <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="{{ route('sktm.index') }}" class="sidebar-link">Surat Keterangan Miskin</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('suket.index') }}" class="sidebar-link">Surat Keterangan</a>
                            </li>
                        </ul>
                    </li>
                    {{-- <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
                            data-bs-target="#multi" aria-expanded="false" aria-controls="multi">
                            <i class="lni lni-layout"></i>
                            <span>Multi Level</span>
                        </a>
                        <ul id="multi" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#multi-two" aria-expanded="false" aria-controls="multi-two">
                                    Two Links
                                </a>
                                <ul id="multi-two" class="sidebar-dropdown list-unstyled collapse">
                                    <li class="sidebar-item">
                                        <a href="#" class="sidebar-link">Link 1</a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a href="#" class="sidebar-link">Link 2</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link">
                            <i class="lni lni-popup"></i>
                            <span>Notification</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link">
                            <i class="lni lni-cog"></i>
                            <span>Setting</span>
                        </a>
                    </li> --}}
                </ul>
                <div class="sidebar-footer">
                    <a class="sidebar-link" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        <i class="ri-logout-box-line"></i>
                        <span>
                            {{ __('Logout') }}
                        </span>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </aside>
            <main class="main py-4">
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        const hamBurger = document.querySelector(".toggle-btn");

        hamBurger.addEventListener("click", function() {
            document.querySelector("#sidebar").classList.toggle("expand");
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function checkNIK() {
            let nik = document.getElementById("nik").value;
            console.log(nik);

            $.ajax({
                url: 'https://esuket.dev/api/personal?nik=' + nik,
                success: function(response) {
                    $('#kk').val(response.kk);
                    $('#name').val(response.name);
                    $('#tempat_lhr').val(response.tempat_lhr);
                    $('#tgl_lhr').val(response.tgl_lhr);
                    $('#alamat').val(response.alamat);
                    $("#gender").select2("trigger", "select", {
                        data: {
                            id: response.gender,
                            text: response.gender_nm
                        }
                    });
                    $("#status_kwn").select2("trigger", "select", {
                        data: {
                            id: response.status_kwn,
                            text: response.status_kwn_nm
                        }
                    });
                    $("#kewarganegaraan").select2("trigger", "select", {
                        data: {
                            id: response.kewarganegaraan,
                            text: response.kewarganegaraan_nm
                        }
                    });
                    $("#agama").select2("trigger", "select", {
                        data: {
                            id: response.agama,
                            text: response.agama_nm
                        }
                    });
                    $("#pendidikan").select2("trigger", "select", {
                        data: {
                            id: response.pendidikan,
                            text: response.pendidikan_nm
                        }
                    });
                    $("#pekerjaan").select2("trigger", "select", {
                        data: {
                            id: response.pekerjaan,
                            text: response.pekerjaan_nm
                        }
                    });

                    Toastify({
                        text: "Data ditemukan!",
                        duration: 1000,
                        close: true,
                        gravity: "top", // `top` or `bottom`
                        position: "center", // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        style: {
                            background: "rgba(25, 135, 84, 1)",
                        },
                    }).showToast();
                },
                error: function(xhr) {
                    const response = JSON.parse(xhr.responseText);
                    // console.log('hey error', response.message);
                    Swal.fire({
                        title: 'Ooopppsss...',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });

        }
        $(document).ready(function() {
            $('#gender').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
                minimumInputLenght: 2,
                ajax: {
                    url: route('gender.index'),
                    dataType: 'json',
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                }
            });
            $('#pekerjaan').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
                minimumInputLenght: 2,
                ajax: {
                    url: route('pekerjaan.index'),
                    dataType: 'json',
                    data: (params) => {
                        let query = {
                            q: params.term,
                            page: params.page || 1,
                        };
                        return query;
                    },
                    processResults: function(response) {

                        return {
                            results: response,
                            pagination: {
                                more: response.current_page < response.last_page,
                            }
                        };
                    },
                }
            });
            $('#pendidikan').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
                minimumInputLenght: 2,
                ajax: {
                    url: route('pendidikan.index'),
                    dataType: 'json',
                    data: (params) => {
                        let query = {
                            q: params.term,
                            page: params.page || 1,
                        };
                        return query;
                    },
                    processResults: function(response) {
                        console.log('bawah')
                        return {
                            results: response,
                            pagination: {
                                more: response.current_page < response.last_page,
                            }
                        };
                    },
                }
            });
            $('#agama').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
                minimumInputLenght: 2,
                ajax: {
                    url: route('agama.index'),
                    dataType: 'json',
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                }
            });

            $('#kewarganegaraan').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
                minimumInputLenght: 2,
                ajax: {
                    url: route('kewarganegaraan.index'),
                    dataType: 'json',
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                }
            });

            $('#status_kwn').select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
                    'style',
                placeholder: $(this).data('placeholder'),
                minimumInputLenght: 2,
                ajax: {
                    url: route('status_kwn.index'),
                    dataType: 'json',
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
