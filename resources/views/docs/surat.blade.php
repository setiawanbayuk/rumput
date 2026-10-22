@extends('layouts.main')

@section('title', 'Dokumentasi API Surat')

@push('styles')
    @vite('resources/js/surat-swagger.js')
@endpush

@section('content')
    <div class="container-fluid px-3 px-lg-4 surat-docs">
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
                <a href="{{ route('docs.surat.spec') }}" class="btn btn-outline-primary" download="surat-api.json">
                    <i class="ri-download-line me-1" aria-hidden="true"></i> Unduh OpenAPI
                </a>
            </div>
        </div>

        <div class="glass-card p-3 mb-4" role="note">
            <p class="mb-0"><i class="ri-shield-keyhole-line text-primary me-1" aria-hidden="true"></i>
                Semua endpoint API memerlukan token Sanctum. Tekan <strong>Authorize</strong> di bawah dan masukkan token bearer sebelum mencoba permintaan.
            </p>
        </div>

        <div class="glass-card surat-docs-panel p-2 p-lg-3">
            <div id="swagger-ui" data-spec-url="{{ route('docs.surat.spec') }}"></div>
        </div>
    </div>
@endsection
