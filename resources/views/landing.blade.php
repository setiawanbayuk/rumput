@extends('layouts.home')

@section('content')
    <section class="h-100 w-100 d-flex align-items-center"
        style="background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)), url('{{ asset('assets/landing.png') }}') no-repeat center center; background-size: cover; padding-top: 50px;">

        <div class="container mt-4">
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3 text-primary">
                                <i class="ri-information-line me-2"></i>Tentang E-Suket
                            </h4>
                            <p class="text-muted leading-relaxed">
                                E-Suket adalah platform digital inovatif yang dirancang untuk mempermudah warga Kota Kediri
                                dalam mendapatkan berbagai layanan surat keterangan dari kelurahan secara cepat, transparan,
                                dan akuntabel.
                            </p>
                            <hr class="my-4 opacity-50">
                            <h5 class="fw-bold mb-3">Cara Pengajuan Mandiri:</h5>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="p-3 border rounded-3 bg-light h-100">
                                        <i class="ri-download-cloud-2-line fs-3 text-primary mb-2 d-block"></i>
                                        <small class="fw-bold d-block mb-1">1. Unduh Aplikasi</small>
                                        <span class="small text-muted">Download Super App Kota Kediri di Store.</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded-3 bg-light h-100">
                                        <i class="ri-user-shared-line fs-3 text-primary mb-2 d-block"></i>
                                        <small class="fw-bold d-block mb-1">2. Login SSO</small>
                                        <span class="small text-muted">Masuk menggunakan akun SSO Kediri Anda.</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded-3 bg-light h-100">
                                        <i class="ri-file-list-3-line fs-3 text-primary mb-2 d-block"></i>
                                        <small class="fw-bold d-block mb-1">3. Pilih Layanan</small>
                                        <span class="small text-muted">Pilih jenis surat dan lengkapi data yang
                                            diminta.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-4 p-3 shadow-sm" style="background-color:rgba(255, 255, 255, 0.9)">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small">Berita Terkini</span>
                            <span class="badge bg-primary">Kediri Kota</span>
                        </div>

                        @if (count($berita) > 0)
                            <div class="swiper news position-relative px-2">
                                <div class="swiper-wrapper">
                                    @foreach ($berita as $item)
                                        <div class="swiper-slide h-auto">
                                            <a href="https://kedirikota.go.id/p/{{ $item['kategoriurl'] }}/{{ $item['idpost'] }}/{{ $item['judulurl'] }}"
                                                target="_blank" class="text-decoration-none">
                                                <div class="card border-0 shadow-none h-100"
                                                    style="border-radius: 10px; background: #f8f9fa;">
                                                    <div class="ratio ratio-16x9">
                                                        <img src="{{ $item['linkgambar'] }}"
                                                            class="object-fit-cover rounded-top" loading="lazy">
                                                    </div>
                                                    <div class="p-2">
                                                        <div class="fw-bold text-dark line-clamp-1"
                                                            style="font-size: 0.75rem;">{{ $item['judul'] }}</div>
                                                        <div class="text-muted line-clamp-2" style="font-size: 0.65rem;">
                                                            {{ $item['deskripsi'] }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination position-relative mt-2"></div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4 d-flex flex-column gap-3">
                    <div class="card border-0 shadow-lg" style="border-radius: 25px; background: #fff; overflow:hidden;">
                        <div class="card-body p-4 d-flex flex-column h-100">

                            <div class="text-center mb-4">
                                <h5 class="fw-bold text-dark mb-3">Unduh Super App</h5>

                                <div class="qr-container">
                                    <ul class="nav nav-pills nav-justified mb-3 bg-light rounded-pill p-1 shadow-sm"
                                        id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active rounded-pill py-2 small fw-bold"
                                                data-bs-toggle="pill" data-bs-target="#android-qr" type="button"
                                                role="tab">
                                                <i class="ri-android-fill me-1"></i>Android
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link rounded-pill py-2 small fw-bold" data-bs-toggle="pill"
                                                data-bs-target="#ios-qr" type="button" role="tab">
                                                <i class="ri-apple-fill me-1"></i>iOS
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content py-2">
                                        <div class="tab-pane fade show active" id="android-qr" role="tabpanel">
                                            <div
                                                class="bg-white p-2 rounded-4 d-inline-block border shadow-sm transition-up">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=https://play.google.com/store/apps/details?id=com.diskominfo.superapp"
                                                    style="width:125px" alt="QR Play Store">
                                            </div>
                                            <p class="x-small text-muted mt-2 mb-0">Scan untuk <strong>Play Store</strong>
                                            </p>
                                        </div>
                                        <div class="tab-pane fade" id="ios-qr" role="tabpanel">
                                            <div
                                                class="bg-white p-2 rounded-4 d-inline-block border shadow-sm transition-up">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=https://apps.apple.com/id/app/super-app-kota-kediri/id6756762936"
                                                    style="width:125px" alt="QR App Store">
                                            </div>
                                            <p class="x-small text-muted mt-2 mb-0">Scan untuk <strong>App Store</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center gap-2 mt-3 border-top pt-3">
                                    <a href="https://play.google.com/store/apps/details?id=com.diskominfo.superapp"
                                        target="_blank" class="transition-up">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg"
                                            height="32">
                                    </a>
                                    <a href="https://apps.apple.com/id/app/super-app-kota-kediri/id6756762936"
                                        target="_blank" class="transition-up">
                                        <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg"
                                            height="32">
                                    </a>
                                </div>
                            </div>

                            <div class="mt-auto pt-2">
                                <a href="https://sso.kedirikota.go.id" target="_blank"
                                    class="d-flex align-items-center bg-light rounded-4 p-3 text-decoration-none border border-light transition-up shadow-sm">
                                    <div class="bg-white p-2 rounded-3 me-3 shadow-sm border">
                                        <i class="ri-fingerprint-line fs-4 text-primary"></i>
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-bold text-dark small">Daftar SSO Kediri</div>
                                        <div class="x-small text-muted" style="font-size: 0.65rem;">Satu akun untuk semua
                                            layanan</div>
                                    </div>
                                    <i class="ri-arrow-right-s-line ms-auto text-muted"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-4 p-3 text-center shadow-sm">
                        <div class="small fw-bold text-warning mb-1">
                            <i class="ri-time-line me-1"></i> Jam Layanan Kelurahan
                        </div>
                        <div class="small text-light opacity-75 fw-medium" style="font-size: 0.75rem;">
                            Senin - Jumat | 08.00 - 15.00 WIB
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .transition-up:hover {
            transform: translateY(-5px);
            transition: 0.3s ease;
        }

        .nav-pills .nav-link {
            color: #666;
        }

        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .shadow-inner {
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .x-small {
            font-size: 0.7rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        new Swiper('.news', {
            slidesPerView: 1,
            spaceBetween: 20,
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            breakpoints: {
                768: {
                    slidesPerView: 2
                },
                1200: {
                    slidesPerView: 3
                }
            },
            autoplay: {
                delay: 4500
            }
        });
    </script>
@endpush
