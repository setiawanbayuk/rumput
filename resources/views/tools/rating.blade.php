@extends('layouts.main')

@section('title', 'Rating Pelayanan')

@section('content')
    @push('styles')
        <style>
            .rating-page{position:relative;min-height:calc(100vh - 120px);padding-bottom:34px;background:linear-gradient(180deg,#f8fafc 0%,#ffffff 48%,#f8fafc 100%)}
            .rating-page:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 8% 4%,rgba(250,204,21,.16),transparent 24%),radial-gradient(circle at 96% 2%,rgba(14,165,233,.12),transparent 28%);pointer-events:none}
            .rating-wrap{position:relative;z-index:1}.rating-hero{position:relative;overflow:hidden;border:0;border-radius:32px;background:linear-gradient(135deg,#172554 0%,#0f766e 55%,#ca8a04 100%);color:#fff;box-shadow:0 28px 72px rgba(15,23,42,.20)}
            .rating-hero:before{content:"";position:absolute;right:-100px;top:-120px;width:330px;height:330px;border-radius:999px;background:rgba(255,255,255,.12)}
            .rating-hero:after{content:"";position:absolute;left:42%;bottom:-150px;width:300px;height:300px;border-radius:999px;background:rgba(255,255,255,.08)}
            .hero-pattern{position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.07) 1px,transparent 1px);background-size:38px 38px;mask-image:linear-gradient(90deg,rgba(0,0,0,.56),transparent 80%)}
            .hero-content{position:relative;z-index:1}.hero-badge{display:inline-flex;align-items:center;gap:.45rem;border-radius:999px;padding:.46rem .9rem;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.28);font-weight:850;font-size:.82rem;backdrop-filter:blur(8px)}
            .hero-icon{width:118px;height:118px;border-radius:34px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.28);display:grid;place-items:center;font-size:68px;box-shadow:inset 0 1px 0 rgba(255,255,255,.24),0 18px 44px rgba(0,0,0,.14)}
            .rating-card{border:0;border-radius:26px;background:#fff;box-shadow:0 18px 54px rgba(15,23,42,.085);overflow:hidden}.rating-card-head{padding:20px 22px;border-bottom:1px solid #edf2f7;background:linear-gradient(180deg,#fff,#fbfdff)}
            .stat-card{border:0;border-radius:24px;background:#fff;box-shadow:0 16px 46px rgba(15,23,42,.08);height:100%;overflow:hidden;position:relative}.stat-card:before{content:"";position:absolute;left:0;right:0;top:0;height:5px;background:linear-gradient(90deg,#facc15,#10b981,#0ea5e9)}
            .stat-icon{width:58px;height:58px;border-radius:20px;background:linear-gradient(135deg,#fef9c3,#ecfeff);color:#b45309;display:grid;place-items:center;font-size:32px;flex:none}.stat-value{font-weight:950;font-size:2.15rem;letter-spacing:-.05em;color:#0f172a;line-height:1}.stat-label{font-size:.74rem;text-transform:uppercase;letter-spacing:.09em;color:#64748b;font-weight:900;margin-top:4px}
            .star-line{display:inline-flex;align-items:center;gap:2px;color:#f59e0b;font-size:1.1rem}.star-line.sm{font-size:.98rem}.muted-box{border:1px dashed #cbd5e1;background:#f8fafc;color:#475569;border-radius:20px;padding:18px}.filter-card{border:0;border-radius:22px;background:rgba(255,255,255,.92);box-shadow:0 14px 40px rgba(15,23,42,.07)}
            .form-select{border-radius:14px;min-height:44px}.btn-filter{border-radius:14px;font-weight:850}.table{vertical-align:middle}.table thead th{font-size:.74rem;text-transform:uppercase;letter-spacing:.08em;color:#64748b;background:#f8fafc;border-bottom:1px solid #e2e8f0}.table tbody td{border-color:#eef2f7}.badge-soft{display:inline-flex;align-items:center;gap:.35rem;border-radius:999px;padding:.38rem .65rem;background:#f8fafc;border:1px solid #e2e8f0;color:#334155;font-weight:800}.comment-box{border:1px solid #e2e8f0;background:#f8fafc;border-radius:14px;padding:10px 12px;color:#334155}.progress{height:9px;border-radius:999px;background:#f1f5f9}.progress-bar{background:#f59e0b}.rank-pill{width:38px;height:38px;border-radius:14px;background:linear-gradient(135deg,#fef3c7,#ecfeff);display:grid;place-items:center;color:#92400e;font-weight:950}.rating-number{font-weight:950;color:#0f172a}.small-muted{color:#64748b;font-size:.86rem}
            @media(max-width:768px){.hero-icon{width:86px;height:86px;font-size:48px}.stat-value{font-size:1.8rem}}
        </style>
    @endpush

    @php
        $avg = $summary['avg'] ?? null;
        $total = (int) ($summary['total'] ?? 0);
        $starCounts = $summary['star_counts'] ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $formatRating = fn ($value) => $value === null ? '-' : number_format((float) $value, 1, ',', '.');
        $unitLabel = ($roleId ?? 0) === 5 ? 'Kecamatan' : 'Kelurahan';
        $unitName = ($roleId ?? 0) === 5 ? ($scope['kecamatan_name'] ?? $scope['unit_name'] ?? '-') : ($scope['kelurahan_name'] ?? $scope['unit_name'] ?? '-');
        $maskNik = function ($nik) {
            $nik = (string) $nik;
            if (strlen($nik) <= 8) {
                return $nik ?: '-';
            }
            return substr($nik, 0, 4) . str_repeat('*', max(strlen($nik) - 8, 0)) . substr($nik, -4);
        };
    @endphp

    <div class="rating-page">
        <div class="container-fluid rating-wrap">
            <div class="card rating-hero mb-4">
                <div class="hero-pattern"></div>
                <div class="card-body p-4 p-lg-5 hero-content">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
                        <div>
                            <div class="hero-badge mb-3"><i class="ri-star-smile-line"></i> Rating Pelayanan Warga</div>
                            <h3 class="fw-bold mb-2">Rating {{ $unitLabel }} {{ $unitName }}</h3>
                            <div class="opacity-75">
                                @if (($roleId ?? 0) === 5)
                                    Rekap akumulasi rating pelayanan per kelurahan wilayah kecamatan.
                                @else
                                    Detail penilaian warga berupa bintang dan komentar untuk wilayah kelurahan.
                                @endif
                            </div>
                        </div>
                        <div class="hero-icon"><i class="ri-medal-line"></i></div>
                    </div>
                </div>
            </div>

            @if (!($ratingReady ?? false))
                <div class="muted-box mb-4">
                    <strong>Data rating belum terbaca.</strong><br>
                    Sistem belum menemukan sumber nilai <code>rating</code> pada tabel <code>surat_pengajuans</code> atau JSON <code>variable</code>. Menu sudah siap, tinggal pastikan API Super App menyimpan nilai rating ke sumber tersebut.
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="stat-icon"><i class="ri-star-fill"></i></div><div><div class="stat-value">{{ $formatRating($avg) }}</div><div class="stat-label">Rating Akumulasi</div></div></div></div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="stat-icon"><i class="ri-user-smile-line"></i></div><div><div class="stat-value">{{ number_format($total, 0, ',', '.') }}</div><div class="stat-label">Total Penilai</div></div></div></div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="stat-icon"><i class="ri-trophy-line"></i></div><div><div class="stat-value">5,0</div><div class="stat-label">Rating Terbaik</div></div></div></div>
                </div>
            </div>

            <div class="filter-card mb-4">
                <div class="card-body p-3 p-lg-4">
                    <form method="GET" action="{{ route('tools.rating') }}" class="row g-3 align-items-end">
                        <div class="col-lg-4">
                            <label class="form-label fw-semibold">Filter Jenis Surat</label>
                            <select name="jenis" class="form-select">
                                <option value="">Semua Jenis Surat</option>
                                @foreach ($jenisOptions as $key => $label)
                                    <option value="{{ $key }}" @selected(($jenisFilter ?? '') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label fw-semibold">Bulan</label>
                            <select name="bulan" class="form-select">
                                <option value="">Semua Bulan</option>
                                @foreach (($bulanOptions ?? []) as $num => $namaBulan)
                                    <option value="{{ $num }}" @selected((int) ($bulanFilter ?? 0) === (int) $num)>{{ $namaBulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label fw-semibold">Tahun</label>
                            <input type="number" name="tahun" class="form-control" min="2000" max="2100" value="{{ $tahunFilter ?? now('Asia/Jakarta')->year }}">
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-success btn-filter w-100 py-2" type="submit"><i class="ri-filter-3-line me-1"></i> Terapkan Filter</button>
                        </div>
                        <div class="col-lg-2">
                            <a href="{{ route('tools.rating') }}" class="btn btn-outline-secondary btn-filter w-100 py-2">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-4">
                    <div class="rating-card h-100">
                        <div class="rating-card-head">
                            <h5 class="fw-bold mb-1"><i class="ri-bar-chart-grouped-line me-1"></i> Sebaran Bintang</h5>
                            <div class="small-muted">Jumlah penilaian per level bintang.</div>
                        </div>
                        <div class="card-body p-4">
                            @for ($star = 5; $star >= 1; $star--)
                                @php
                                    $count = (int) ($starCounts[$star] ?? 0);
                                    $percent = $total > 0 ? round(($count / $total) * 100) : 0;
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="star-line sm">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="ri-star-{{ $i <= $star ? 'fill' : 'line' }}"></i>
                                            @endfor
                                        </div>
                                        <strong>{{ number_format($count, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="progress"><div class="progress-bar" style="width: {{ $percent }}%"></div></div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="rating-card">
                        <div class="rating-card-head d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    @if (($roleId ?? 0) === 5)
                                        <i class="ri-community-line me-1"></i> Rating Per Kelurahan
                                    @else
                                        <i class="ri-chat-smile-3-line me-1"></i> Detail Penilaian Warga
                                    @endif
                                </h5>
                                <div class="small-muted">
                                    @if (($roleId ?? 0) === 5)
                                        Camat hanya melihat akumulasi rating kelurahan, tanpa komentar warga.
                                    @else
                                        Lurah melihat detail bintang dan komentar warga wilayahnya.
                                    @endif
                                </div>
                            </div>
                            <span class="badge-soft"><i class="ri-database-2-line"></i> {{ number_format($total, 0, ',', '.') }} Data</span>
                        </div>
                        <div class="card-body p-0">
                            @if (($roleId ?? 0) === 5)
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:70px">Rank</th>
                                                <th>Kelurahan</th>
                                                <th class="text-center">Total Penilai</th>
                                                <th class="text-center">Rating</th>
                                                <th>Bintang</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($kelurahanRatings as $row)
                                                <tr>
                                                    <td><div class="rank-pill">{{ $loop->iteration }}</div></td>
                                                    <td><strong>{{ $row->nama }}</strong></td>
                                                    <td class="text-center">{{ number_format($row->total_penilai, 0, ',', '.') }}</td>
                                                    <td class="text-center"><span class="rating-number">{{ $formatRating($row->avg_rating) }}</span></td>
                                                    <td>
                                                        <span class="star-line">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <i class="ri-star-{{ $row->avg_rating !== null && $i <= round($row->avg_rating) ? 'fill' : 'line' }}"></i>
                                                            @endfor
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center p-4 text-muted">Belum ada rating warga pada wilayah kecamatan ini.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:60px">No</th>
                                                <th>Warga / Surat</th>
                                                <th>Bintang</th>
                                                <th>Komentar</th>
                                                <th>Waktu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($ratings as $item)
                                                @php
                                                    $ratingValue = (int) round((float) ($item->rating_value ?? $item->rating ?? 0));
                                                    $namaWarga = $item->penduduk->name ?? $item->kepada ?? 'Warga';
                                                    $jenisSurat = strtoupper((string) ($item->jenis_surat ?? '-'));
                                                    $komentar = trim((string) ($item->komentar_value ?? $item->komentar ?? ''));
                                                @endphp
                                                <tr>
                                                    <td>{{ ($ratings->firstItem() ?? 1) + $loop->index }}</td>
                                                    <td>
                                                        <strong>{{ $namaWarga }}</strong>
                                                        <div class="small-muted">{{ $maskNik($item->nik ?? '') }} · {{ $jenisSurat }} · No. {{ $item->no_urut_surat ?? '-' }}</div>
                                                    </td>
                                                    <td>
                                                        <div class="star-line">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <i class="ri-star-{{ $i <= $ratingValue ? 'fill' : 'line' }}"></i>
                                                            @endfor
                                                        </div>
                                                        <div class="small-muted">{{ $ratingValue }}/5</div>
                                                    </td>
                                                    <td><div class="comment-box">{{ $komentar !== '' ? $komentar : '-' }}</div></td>
                                                    <td>{{ optional($item->updated_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center p-4 text-muted">Belum ada penilaian warga untuk kelurahan ini.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if (method_exists($ratings, 'links'))
                                    <div class="p-3 border-top">{{ $ratings->links() }}</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
