@extends('layouts.warga')

@push('styles')
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">


    <style>
        /* latar lembut seperti desain */
        .section-soft {
            background: #EEE9DB;
        }

        /* judul dinamis di atas slider */
        .section-title {
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.25rem;
            letter-spacing: .3px;
        }

        /* Kontainer slider: jadi acuan posisi absolute & beri ruang tombol */
        .news {
            position: relative;
            /* supaya tombol yang keluar tak kepotong */
            padding: 0 60px 56px;
            /* 60px ruang kiri/kanan untuk tombol, 56px bawah untuk pagination */
        }

        /* Tombol kanan–kiri menempel di tengah vertikal */
        .news .swiper-button-prev,
        .news .swiper-button-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .95);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
            z-index: 10;
        }

        .news .swiper-button-prev {
            left: 12px;
        }

        /* jangan pakai 425px fixed */
        .news .swiper-button-next {
            right: 12px;
        }

        .news .swiper-button-prev::after,
        .news .swiper-button-next::after {
            font-size: 18px;
            color: #333;
        }

        /* Bullet di bawah tengah, tidak absolute di atas kartu */
        .news .swiper-pagination {
            position: absolute !important;
            left: 0;
            right: 0;
            bottom: 12px;
            /* muncul di bawah slider */
        }

        /* Opsional: di layar kecil, pindah tombol ke bawah atau kecilkan jarak */
        @media (max-width: 575.98px) {
            .news {
                padding: 0 40px 56px;
            }

            .news .swiper-button-prev {
                left: 6px;
            }

            .news .swiper-button-next {
                right: 6px;
            }
        }


        .btn-surat {
            padding: 0;
            /* hilangkan padding bawaan button */
            border-radius: 10px;
            /* biar ada sudut rounded */
            overflow: hidden;
            /* biar gambar nggak keluar kotak */
        }

        .btn-surat img {
            width: 100%;
            /* atur tinggi tetap */
            object-fit: cover;
            /* isi penuh tanpa distorsi */
        }

        .btn-surat:hover img {
            transform: scale(.99);
            /* efek zoom halus saat hover */
        }
    </style>
@endpush
@section('content')
    <!-- HEADER & WELCOME SECTION -->
    <section class="position-relative"
        style="background: url('{{ asset('assets/landing.png') }}') no-repeat center center; background-size: cover; min-height: 100vh; margin-top: -73px;">

        <!-- WELCOME SECTION -->
        <div class="d-flex flex-column justify-content-center align-items-center text-white pt-5 text-center"
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 600px;">
            <h1 class="fw-bold"
                style="font-size: 3rem; text-shadow: 2px 2px 4px #000000; letter-spacing: 1.5px;
                    max-width: 100%; overflow-wrap: break-word; word-break: break-word;">
                SELAMAT DATANG
            </h1>
            <h2 class="fw-normal"
                style="font-size: 1.3rem; text-shadow: 2px 2px 4px #000000; letter-spacing: 1.5px;
                    max-width: 100%; overflow-wrap: break-word; word-break: break-word;">
                {{ auth()->user()->name }}
            </h2>
        </div>
        <div class="position-absolute bottom-0 w-100">
            <div class="text-center py-2 fs-5"
                style="background-color: rgba(165, 146, 109, 0.7); color:white;text-shadow: 2px 2px 4px #000000;">
                <i class="ri-megaphone-line"></i> Pelayanan terkait pengesahan dan pengambilan Surat Keterangan dilakukan
                pada
                jam kerja senin-jumat 08.00 s/d 15.00 WIB
            </div>

            <!-- INFORMASI LAYANAN -->
            <section class="py-4" style="background-color: #b8a57ed0; font-family: 'Kumbh Sans', sans-serif;">
                <div class="container">
                    <div class="row align-items-center">
                        <!-- Logo & Deskripsi Kiri -->
                        <div class="col-md-6 d-flex align-items-center">
                            <img src="{{ asset('assets/logo.png') }}" alt="Logo"
                                style="width: 60px; margin-right: 15px;">
                            <div>
                                <h6 class="mb-1" style="color: #FFFFFF; font-weight: bold;">Sistem Pelayanan Terpadu
                                    Kelurahan
                                </h6>
                                <h6 class="mb-2" style="color: #FFFFFF;">Pemerintah Kota Kediri</h6>
                                <p style="color: #EDE6D3; font-size: 14px;">
                                    Merupakan media untuk warga khususnya masyarakat Kota Kediri dalam membuat surat
                                    keterangan
                                    secara online.
                                </p>
                            </div>
                        </div>
                        <!-- Informasi Kanan -->
                        <div class="col-md-6 text-end">
                            <h6 class="mb-1" style="color: #FFFFFF; font-weight: bold;">NGURUS SURAT, <br> NGGAK PAKAI
                                RIBET
                            </h6>
                            <p style="color: #EDE6D3; font-size: 14px;">
                                E-Suket mempermudah dalam mengajukan berbagai surat keterangan di Kelurahan berbasis NIK
                                Nasional
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <section class="container py-4 rounded-4 mt-4" style="background-color:rgba(174, 160, 122, .25)"
        aria-label="Berita Unggulan">
        <div class="berita mb-3">Berita Kota Kediri</div>
        @if (count($berita) === 10)
            <div class="swiper news px-4" style="max-width:1200px; margin:auto;">
                <div class="swiper-wrapper">
                    @foreach ($berita as $item)
                        <div class="swiper-slide">
                            <a href="https://kedirikota.go.id/p/{{ $item['kategoriurl'] }}/{{ $item['idpost'] }}/{{ $item['judulurl'] }}"
                                target="_blank" class="text-decoration-none text-dark d-block h-100">
                                <article class="card border-0 shadow-sm overflow-hidden h-100" style="border-radius: 12px">
                                    <div class="ratio ratio-16x9">
                                        <img src="{{ $item['linkgambar'] }}" alt="{{ $item['judul'] }}"
                                            class="w-100 h-100 object-fit-cover" loading="lazy">
                                    </div>
                                    <div class="card-body">
                                        <h3 class="h6">
                                            {{ strlen($item['judul']) > 40 ? substr($item['judul'], 0, 60) . '…' : $item['judul'] }}
                                        </h3>
                                        <p class="small text-muted">
                                            {{ strlen($item['deskripsi']) > 60 ? substr($item['deskripsi'], 0, 60) . '…' : $item['deskripsi'] }}
                                        </p>
                                    </div>
                                </article>
                            </a>
                        </div>
                    @endforeach 
                </div>

                {{-- Posisikan tombol & pagination di dalam .news, bukan di .d-flex --}}
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            </div>
        @else
            {{-- fallback --}}
            <div class="row g-3">
                @for ($i = 0; $i < 3; $i++)
                    <div class="col-12 col-md-4">
                        <div class="card border-0 shadow-sm placeholder-glow" style="border-radius: 12px">
                            <div class="ratio ratio-16x9 bg-body-secondary"></div>
                            <div class="card-body">
                                <div class="placeholder col-8 mb-2"></div>
                                <div class="placeholder col-6"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        @endif
    </section>


    <!-- PANDUAN & PROFIL -->
    <section class="container py-4">
        <div class="row justify-content-center align-items-start">
            <!-- Panduan Mengakses E-SUKET -->
            <div class="col-md-6 mb-3">
                <div class="bg-white p-4 rounded shadow-sm text-center" style="border: 2px solid #E1E1E1;">
                    <h5>
                        <span class="me-2"
                            style="background-color: #A3936F; border-radius: 50%; padding: 8px; color:white;">
                            <i class="ri-video-on-line"></i>
                        </span>
                        Panduan Mengakses E-SUKET
                    </h5>
                    <div class="d-flex justify-content-center mt-3">
                        <!-- Kotak Panduan Kosong -->
                        <div class="mx-2 my-2 content-box"></div>
                        <div class="mx-2 my-2 content-box"></div>
                        <div class="mx-2 my-2 content-box"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm" style="border: 2px solid #E1E1E1;">
                    <div class="d-flex text-center align-items-center justify-content-center mb-3">
                        {{-- KOTAK FOTO --}}
                        @php
                            $avatar = Auth::user()->avatar 
                                    ? asset('storage/' . Auth::user()->avatar) 
                                    : asset('assets/default-avatar.png');
                        @endphp

                        <div class="id-photo-box me-3 mb-1">
                            <img id="avatarPreview"
                                src="{{ $avatar }}"
                                alt="Foto profil" 
                                style="width:100%; height:100%; object-fit:cover;">
                        </div>

                        {{-- NIK --}}
                        <div class="flex-fill p-2 rounded" style="background-color:#F8F8F8; border:1px solid #E1E1E1;">
                            <p class="mb-0 fw-bold">{{ auth()->user()->nik }}</p>
                            <small class="text-muted">Nomor Induk Kependudukan</small>
                        </div>
                    </div>
                    {{-- Nama & Kelurahan --}}
                    <h5 class="fw-bold text-uppercase">{{ auth()->user()->name }}</h5>
                    <div class="d-flex justify-content-between">
                        <p class="text-start text-muted mb-0">
                            {{ $skpd->nama ? 'KELURAHAN ' . $skpd->nama : 'Kelurahan tidak tersedia' }}
                        </p>
                        <p class="text-end text-muted mb-0">
                            RW/RT {{ auth()->user()->id_rw }}/{{ auth()->user()->id_rt }}
                        </p>
                    </div>
                </div>
            </div>
    </section>

    <section class="container py-4">
        <div class="row g-3">
            @foreach ($surat as $item)
                <div class=" col-md-4 d-flex justify-content-center">
                    <a href="{{ url('/warga/' . $item['jenis']) }}" class="btn-surat">
                        <img src="{{ asset('assets/images/' . $item['assets']) }}" class="img-fluid rounded"
                            alt="Surat Keterangan">
                    </a>
                </div>
            @endforeach
        </div>
    </section>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script>
            const beritaLength = {{ count($berita) }};
            const initial = Math.floor(beritaLength / 2); // posisi tengah
            const swiper = new Swiper('.news', {
                effect: 'coverflow',
                loop: true, // loop aktif
                grabCursor: true,
                centeredSlides: true,
                slidesPerView: 1,
                initialSlide: initial, // mulai dari tengah
                coverflowEffect: {
                    rotate: 0,
                    stretch: -40,
                    depth: 200,
                    modifier: 1.2,
                    slideShadows: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2
                    },
                    1024: {
                        slidesPerView: 3
                    },
                }
            }); 
        </script>
    @endpush
@endsection
