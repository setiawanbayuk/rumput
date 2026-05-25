@extends('layouts.main')

@section('title', 'Rekap Surat')

@section('content')
    @push('styles')
        <style>
            .tools-hero{border:0;border-radius:28px;overflow:hidden;background:linear-gradient(135deg,#0f766e,#0891b2);color:#fff;box-shadow:0 22px 55px rgba(8,145,178,.22)}
            .tools-card{border:0;border-radius:24px;box-shadow:0 16px 42px rgba(15,23,42,.08)}
            .soft-badge{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);border-radius:999px;padding:.38rem .78rem;font-weight:700}
            .form-select,.form-control{border-radius:14px;min-height:45px}.btn-download{border-radius:14px;font-weight:800;box-shadow:0 10px 22px rgba(22,163,74,.18)}
            .notice-box{border:1px dashed #99f6e4;background:#f0fdfa;color:#115e59;border-radius:18px;padding:16px}
            .warning-box{border:1px solid #fde68a;background:#fffbeb;color:#92400e;border-radius:18px;padding:16px}
        </style>
    @endpush

    <div class="container-fluid">
        @if (session('error'))
            <div class="alert alert-danger rounded-4 border-0 shadow-sm">{{ session('error') }}</div>
        @endif

        <div class="card tools-hero mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <div class="soft-badge d-inline-flex align-items-center gap-2 mb-3"><i class="ri-file-excel-2-line"></i> Export Excel Aman</div>
                        <h3 class="fw-bold mb-2">Rekap Surat {{ ($scope['type'] ?? '') === 'kecamatan' ? 'Kecamatan' : 'Kelurahan' }}</h3>
                        <div class="opacity-75"></div>
                    </div>
                    <div class="display-4 opacity-75"><i class="ri-download-cloud-2-line"></i></div>
                </div>
            </div>
        </div>

        <div class="card tools-card">
            <div class="card-body p-4">
                @if (($scope['type'] ?? '') === 'kecamatan')
                    <div class="warning-box mb-4">
                        <strong>Khusus Sekcam/Camat:</strong> menu Rekap hanya mengizinkan download data <strong>SKTM</strong>. Rekap surat non-SKTM tidak tersedia untuk wilayah kecamatan.
                    </div>
                @endif

                <form method="GET" action="{{ route('tools.rekap.export') }}" class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label fw-semibold">Jenis Surat</label>
                        <select name="jenis" id="jenis" class="form-select" required>
                            <option value="">- Pilih Jenis Surat -</option>
                            @foreach ($jenisSurat as $key => $item)
                                <option value="{{ $key }}">{{ $item['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label fw-semibold">Bulan</label>
                        <select name="bulan" id="bulan" class="form-select">
                            <option value="">Semua Bulan</option>
                            @foreach (($bulanOptions ?? []) as $num => $namaBulan)
                                <option value="{{ $num }}">{{ $namaBulan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label fw-semibold">Tahun</label>
                        <input type="number" name="tahun" id="tahun" class="form-control" min="2000" max="2100" value="{{ $currentYear ?? now('Asia/Jakarta')->year }}">
                    </div>

                    <div class="col-lg-2 d-none" id="skbnFilterWrap">
                        <label class="form-label fw-semibold">Filter Khusus SKBN</label>
                        <select name="skbn_kategori" id="skbn_kategori" class="form-select">
                            <option value="menikah">Menikah</option>
                            <option value="lainnya">Keperluan Lainnya</option>
                        </select>
                        <div class="form-text">Filter ini hanya berlaku untuk SKBN.</div>
                    </div>

                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-success btn-download w-100 py-2">
                            <i class="ri-file-excel-2-line me-1"></i> Download Excel
                        </button>
                    </div>
                </form>


            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const jenis = document.getElementById('jenis');
                const wrap = document.getElementById('skbnFilterWrap');
                const skbnKategori = document.getElementById('skbn_kategori');

                function toggleSkbn() {
                    if (jenis && jenis.value === 'skbn') {
                        wrap.classList.remove('d-none');
                        skbnKategori.disabled = false;
                    } else {
                        wrap.classList.add('d-none');
                        skbnKategori.disabled = true;
                    }
                }

                if (jenis) {
                    jenis.addEventListener('change', toggleSkbn);
                    toggleSkbn();
                }
            });
        </script>
    @endpush
@endsection
