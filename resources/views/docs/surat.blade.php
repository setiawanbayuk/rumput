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
    <link rel="stylesheet" href="{{ asset('css/surat-swagger.css') }}">
</head>
<body class="rumput-glass-body">
    <nav class="glass-navbar py-3 mb-4" aria-label="Navigasi dokumentasi">
        <div class="container-fluid px-3 px-lg-4 d-flex align-items-center justify-content-between gap-3">
            <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark fw-semibold">
                <i class="ri-file-text-line fs-4 text-primary" aria-hidden="true"></i>
                <span>E-SUKET</span>
            </a>
            <a href="{{ url('/') }}" class="btn btn-outline-primary btn-sm">
                <i class="ri-home-2-line me-1" aria-hidden="true"></i> Beranda
            </a>
        </div>
    </nav>

    <main class="container-fluid px-3 px-lg-4 pb-5 surat-docs">
        <div class="glass-card p-3 p-lg-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-semibold">
                        <i class="ri-book-open-line fs-4" aria-hidden="true"></i>
                        <span>Dokumentasi Pengembang</span>
                    </div>
                    <h1 class="h3 mb-2">API Surat E-SUKET</h1>
                    <p class="mb-0 text-secondary">Endpoint pengajuan, revisi, riwayat, tracking, rating, dan PDF surat.</p>
                </div>
                <a href="{{ asset('openapi/surat-api.json') }}" class="btn btn-outline-primary" download="surat-api.json">
                    <i class="ri-download-line me-1" aria-hidden="true"></i> Unduh OpenAPI
                </a>
            </div>
        </div>

        <div class="glass-card p-3 mb-4" role="note">
            <p class="mb-0"><i class="ri-shield-keyhole-line text-primary me-1" aria-hidden="true"></i>
                Dokumentasi ini dapat dibuka tanpa login. Untuk mencoba endpoint API, tekan <strong>Authorize</strong> dan masukkan token bearer Sanctum.
            </p>
        </div>

        <div class="glass-card surat-docs-panel p-2 p-lg-3">
            <div id="swagger-ui" data-spec-url="{{ asset('openapi/surat-api.json') }}"></div>
        </div>
    </main>
    <script src="{{ asset('vendor/swagger-ui/swagger-ui-bundle.js') }}" defer></script>
    <script src="{{ asset('js/surat-swagger.js') }}" defer></script>
</body>
</html>
