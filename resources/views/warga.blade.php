@extends('layouts.warga')
<style>
    .card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin: 10px;
        padding: 15px;
        width: 250px;
        height: 220px;
        text-align: center;
        transition: transform 0.4s ease, opacity 0.4s ease;
        opacity: 0.6;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card.active {
        transform: scale(1.1);
        opacity: 1;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .card img {
        max-width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 10px;
    }

    .card h3 {
        font-size: 14px;
        font-weight: bold;
        color: #333;
        margin: 10px 0 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .card p {
        font-size: 12px;
        color: #666;
        margin: 0;
        line-height: 1.4;
    }

    .button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: #c4b695;
        color: white;
        border: none;
        cursor: pointer;
        padding: 12px;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        font-size: 18px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s;
    }

    .button:hover {
        background: #a08d6f;
    }

    .button.left {
        left: -50px;
    }

    .button.right {
        right: -50px;
    }
</style>
@section('content')
    <!-- HEADER & WELCOME SECTION -->
    <section class="position-relative"
        style="background: url('{{ asset('assets/landing.png') }}') no-repeat center center; background-size: cover; min-height: 100vh;">

        <!-- WELCOME SECTION -->
        <div class="d-flex flex-column justify-content-center align-items-center text-white pt-5 text-center"
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 600px;">
            <h1 class="fw-bold"
                style="text-shadow: 2px 2px 4px #000000; letter-spacing: 1.5px;
                   max-width: 100%; overflow-wrap: break-word; word-break: break-word;">
                SELAMAT DATANG
            </h1>
            <h2 class="fw-bold"
                style="font-size: 2.5rem; text-shadow: 2px 2px 4px #000000; letter-spacing: 1.5px;
                   max-width: 100%; overflow-wrap: break-word; word-break: break-word;">
                {{ auth()->user()->name }}
            </h2>
        </div>
    </section>

    <div class="text-center py-2 fs-5"
        style="background-color: rgb(165, 146, 109); color:white;text-shadow: 2px 2px 4px #000000;">
        <i class="ri-megaphone-line"></i> Pelayanan terkait pengesahan dan pengambilan Surat Keterangan dilakukan pada
        jam kerja senin-jumat 08.00 s/d 15.00 WIB
    </div>

    <!-- INFORMASI LAYANAN -->
    <section class="py-4" style="background-color: #B8A57E; font-family: 'Kumbh Sans', sans-serif;">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo & Deskripsi Kiri -->
                <div class="col-md-6 d-flex align-items-center">
                    <img src="{{ asset('assets/logo.png') }}" alt="Logo" style="width: 60px; margin-right: 15px;">
                    <div>
                        <h6 class="mb-1" style="color: #FFFFFF; font-weight: bold;">Sistem Pelayanan Terpadu Kelurahan
                        </h6>
                        <h6 class="mb-2" style="color: #FFFFFF;">Pemerintah Kota Kediri</h6>
                        <p style="color: #EDE6D3; font-size: 14px;">
                            Merupakan media untuk warga khususnya masyarakat Kota Kediri dalam membuat surat keterangan
                            secara online.
                        </p>
                    </div>
                </div>
                <!-- Informasi Kanan -->
                <div class="col-md-6 text-end">
                    <h6 class="mb-1" style="color: #FFFFFF; font-weight: bold;">NGURUS SURAT, <br> NGGAK PAKAI RIBET</h6>
                    <p style="color: #EDE6D3; font-size: 14px;">
                        E-Suket mempermudah dalam mengajukan berbagai surat keterangan di Kelurahan berbasis NIK Nasional
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="berita">Berita Kota Kediri</div>

    <div class="carousel-container">
        <button class="button left" onclick="moveSlide(-1)">&#10094;</button>

        <div class="carousel">
            <div class="card-container">
                @foreach ($berita as $index => $item)
                    <div class="card card-carousel {{ $index === 1 ? 'active' : '' }}">
                        <img src="{{ $item['linkgambar'] }}" alt="{{ $item['judul'] }}">
                        <h3 title="{{ $item['judul'] }}">
                            {{ strlen($item['judul']) > 40 ? substr($item['judul'], 0, 40) . '...' : $item['judul'] }}
                        </h3>
                        <p>
                            {{ strlen($item['deskripsi']) > 80 ? substr($item['deskripsi'], 0, 80) . '...' : $item['deskripsi'] }}
                        </p>
                        <a href="https://kedirikota.go.id/p/{{ $item['kategoriurl'] }}/{{ $item['idpost'] }}/{{ $item['judulurl'] }}"
                            target="_blank" className="btn btn-primary btn-sm" role="button">
                            Selengkapnya
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <button class="button right" onclick="moveSlide(1)">&#10095;</button>
    </div>

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
                        <div class="mx-2 content-box"></div>
                        <div class="mx-2 content-box"></div>
                        <div class="mx-2 content-box"></div>
                        <div class="mx-2 content-box"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-white rounded shadow-sm text-center" style="border: 2px solid #E1E1E1;">
                    <div class="mb-3"
                        style="width: 100px; height: 130px; background: url('https://via.placeholder.com/100x130') center center / cover; border-radius: 10px; margin: 0 auto; border: 1px solid #E1E1E1;">
                    </div>
                    <div class="p-2 rounded" style="background-color: #F8F8F8; border: 1px solid #E1E1E1;">
                        <p class="mb-0 fw-bold">{{ auth()->user()->nik }}</p>
                        <small class="text-muted">Nomor Induk Kependudukan</small>
                    </div>
                    <h5 class="mt-3 fw-bold text-uppercase">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-0">
                        {{ auth()->user()->kelurahan->nama_kelurahan ?? 'Kelurahan tidak tersedia' }}</p>
                </div>
            </div>

        </div>
    </section>

    <section class="container py-4">
        <div class="row g-3">
            @foreach ($surat as $item)
                <div class="col-md-4 d-flex justify-content-center">
                    <a href="{{ url('/warga/' . $item['jenis']) }}" class="btn btn-light shadow-sm w-100">
                        <img src="{{ asset('assets/images/' . $item['assets']) }}" class="img-fluid rounded"
                            alt="Surat Keterangan">
                    </a>
                </div>
            @endforeach
        </div>
    </section>
    <script>
        let currentIndex = 1; // Fokus awal di tengah

        function moveSlide(direction) {
            const cards = document.querySelectorAll('.card-carousel');
            const totalCards = cards.length;

            currentIndex = (currentIndex + direction + totalCards) % totalCards;
            const cardWidth = cards[0].offsetWidth + 20;
            document.querySelector('.card-container').style.transform = `translateX(-${(currentIndex - 1) * cardWidth}px)`;

            document.querySelectorAll('.card-carousel').forEach((card, index) => {
                card.classList.toggle('active', index === currentIndex);
            });
        }

        setInterval(() => {
            moveSlide(1);
        }, 3000); // Auto-slide setiap 3 detik
    </script>
@endsection
