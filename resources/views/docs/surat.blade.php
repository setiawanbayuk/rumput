<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dokumentasi API Surat E-SUKET">
    <title>Dokumentasi API Surat — E-SUKET</title>
    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">
    @vite('resources/sass/app.scss')
    <link rel="stylesheet" href="{{ asset('css/mobile-responsive-pro.css') }}?v=20260624-esuket-glass-v1">
    <link rel="stylesheet" href="{{ asset('vendor/swagger-ui/swagger-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/surat-swagger.css') }}?v=20261022-2">
</head>
<body class="rumput-glass-body">
    <nav class="glass-navbar docs-navbar" aria-label="Navigasi dokumentasi">
        <div class="container-fluid docs-shell d-flex align-items-center justify-content-between gap-3">
            <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark fw-semibold">
                <span class="docs-brand-icon"><i class="ri-file-text-line" aria-hidden="true"></i></span>
                <span>E-SUKET</span>
            </a>
            <a href="{{ url('/') }}" class="docs-home-link">
                <i class="ri-arrow-left-line me-1" aria-hidden="true"></i> Kembali ke beranda
            </a>
        </div>
    </nav>

    <main class="container-fluid docs-shell surat-docs">
        <section class="glass-card docs-hero" aria-labelledby="docs-title">
            <div class="docs-hero-content">
                <div class="docs-eyebrow"><i class="ri-code-s-slash-line" aria-hidden="true"></i> REFERENSI API E-SUKET</div>
                <h1 id="docs-title">API Surat</h1>
                <p>Dokumentasi untuk pengajuan, revisi, riwayat, tracking, rating, dan berkas PDF surat.</p>
                <div class="docs-badges" aria-label="Informasi API">
                    <span><i class="ri-file-code-line" aria-hidden="true"></i> OpenAPI 3.0</span>
                    <span><i class="ri-shield-keyhole-line" aria-hidden="true"></i> Sanctum</span>
                    <span><i class="ri-global-line" aria-hidden="true"></i> Dokumentasi publik</span>
                </div>
            </div>
            <div class="docs-hero-actions">
                <a href="#endpoint-list" class="btn btn-primary">
                    <i class="ri-arrow-down-line me-1" aria-hidden="true"></i> Lihat endpoint
                </a>
                <a href="{{ asset('openapi/surat-api.json') }}" class="btn btn-outline-primary" download="surat-api.json">
                    <i class="ri-download-line me-1" aria-hidden="true"></i> Unduh spesifikasi
                </a>
            </div>
        </section>

        <section id="endpoint-list" class="glass-card docs-reference" aria-labelledby="endpoint-title">
            <div class="docs-reference-heading">
                <div>
                    <span class="docs-section-label">REFERENSI ENDPOINT</span>
                    <h2 id="endpoint-title">Jelajahi API Surat</h2>
                    <p>Pilih endpoint untuk melihat parameter, contoh respons, dan mencoba permintaan.</p>
                </div>
                <div class="docs-auth-note" role="note">
                    <i class="ri-lock-2-line" aria-hidden="true"></i>
                    <span>Akses dokumentasi tanpa login. Untuk mencoba API, masukkan token di <strong>Authorize</strong>.</span>
                </div>
            </div>
            <div id="swagger-ui" data-spec-url="{{ asset('openapi/surat-api.json') }}"></div>
        </section>
    </main>
    <script src="{{ asset('vendor/swagger-ui/swagger-ui-bundle.js') }}" defer></script>
    <script src="{{ asset('js/surat-swagger.js') }}" defer></script>
</body>
</html>
