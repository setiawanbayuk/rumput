@extends('layouts.main')

@section('content')
<style>
    .monitor-page{position:relative;overflow:hidden;background:linear-gradient(180deg,#f8fafc 0%,#eef2ff 42%,#f8fafc 100%);border-radius:30px;padding:18px}.monitor-page:before{content:"";position:absolute;right:-130px;top:-160px;width:520px;height:520px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,.22),rgba(37,99,235,0));animation:glowMove 7s ease-in-out infinite}.monitor-page:after{content:"";position:absolute;left:-170px;top:420px;width:460px;height:460px;border-radius:50%;background:radial-gradient(circle,rgba(168,85,247,.18),rgba(168,85,247,0));animation:glowMove 9s ease-in-out infinite reverse}@keyframes glowMove{0%,100%{transform:translateY(0) scale(.96)}50%{transform:translateY(28px) scale(1.06)}}
    .hero-super{position:relative;overflow:hidden;border-radius:34px;background:linear-gradient(135deg,#020617 0%,#111827 38%,#1e1b4b 68%,#075985 100%);box-shadow:0 32px 90px rgba(15,23,42,.30);color:#fff}.hero-super:before{content:"";position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.08) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.08) 1px,transparent 1px);background-size:42px 42px;mask-image:linear-gradient(to bottom,rgba(0,0,0,.9),rgba(0,0,0,.18))}.hero-super:after{content:"";position:absolute;right:-70px;bottom:-90px;width:360px;height:360px;border-radius:50%;background:radial-gradient(circle,rgba(56,189,248,.42),rgba(56,189,248,0));animation:spinGlow 12s linear infinite}@keyframes spinGlow{from{transform:rotate(0deg) translateX(12px)}to{transform:rotate(360deg) translateX(12px)}}
    .super-badge{display:inline-flex;align-items:center;gap:9px;padding:10px 15px;border-radius:999px;background:rgba(255,255,255,.13);border:1px solid rgba(255,255,255,.16);backdrop-filter:blur(14px);font-weight:950;font-size:12px;letter-spacing:.09em}.pulse-live{width:10px;height:10px;border-radius:50%;background:#22c55e;box-shadow:0 0 0 rgba(34,197,94,.65);animation:pulseLive 1.3s infinite}@keyframes pulseLive{0%{box-shadow:0 0 0 0 rgba(34,197,94,.65)}70%{box-shadow:0 0 0 13px rgba(34,197,94,0)}100%{box-shadow:0 0 0 0 rgba(34,197,94,0)}}
    .hero-title{font-size:clamp(38px,5vw,78px);line-height:.9;font-weight:950;letter-spacing:-.08em}.hero-sub{color:#cbd5e1;font-size:17px;max-width:780px}.glass-kpi{border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.11);backdrop-filter:blur(18px);border-radius:26px;padding:18px}.kpi-value{font-size:32px;font-weight:950;letter-spacing:-.05em}.kpi-label{font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#bae6fd;font-weight:900}
    .robot-wrap{height:330px;display:grid;place-items:center;position:relative}.robot-aura{position:absolute;width:310px;height:310px;border-radius:50%;background:radial-gradient(circle,rgba(59,130,246,.26),rgba(59,130,246,0));animation:aura 2.8s ease-in-out infinite}@keyframes aura{0%,100%{transform:scale(.92);opacity:.65}50%{transform:scale(1.08);opacity:1}}.robot{position:relative;width:210px;height:240px;animation:robotFloat 3.4s ease-in-out infinite}.robot-head{position:absolute;left:30px;top:18px;width:150px;height:118px;border-radius:36px;background:linear-gradient(145deg,#f8fafc,#cbd5e1);box-shadow:0 25px 48px rgba(0,0,0,.30),inset 0 0 0 5px rgba(255,255,255,.45)}.robot-head:before{content:"";position:absolute;left:68px;top:-30px;width:14px;height:30px;background:#93c5fd;border-radius:999px}.robot-head:after{content:"";position:absolute;left:57px;top:-48px;width:36px;height:36px;border-radius:999px;background:#38bdf8;box-shadow:0 0 32px #38bdf8;animation:blinkLight 1.4s ease-in-out infinite}.robot-eye{position:absolute;top:44px;width:24px;height:24px;border-radius:50%;background:#0f172a;box-shadow:0 0 15px #38bdf8}.eye-l{left:41px}.eye-r{right:41px}.robot-mouth{position:absolute;left:48px;bottom:27px;width:55px;height:9px;border-radius:999px;background:#38bdf8;box-shadow:0 0 18px rgba(56,189,248,.85);animation:talk 1.1s ease-in-out infinite}.robot-body{position:absolute;left:47px;top:145px;width:116px;height:78px;border-radius:26px;background:linear-gradient(145deg,#60a5fa,#4338ca);box-shadow:0 22px 42px rgba(0,0,0,.24)}.robot-body:before{content:"";position:absolute;left:43px;top:22px;width:30px;height:30px;border-radius:50%;background:#fff;box-shadow:0 0 24px rgba(255,255,255,.8)}.robot-arm{position:absolute;top:158px;width:40px;height:15px;background:#93c5fd;border-radius:999px}.arm-l{left:4px;transform-origin:right center;animation:armWave 1.7s ease-in-out infinite}.arm-r{right:4px;transform-origin:left center;animation:armWave 1.7s ease-in-out infinite reverse}@keyframes robotFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-17px)}}@keyframes blinkLight{0%,100%{transform:scale(.88);opacity:.7}50%{transform:scale(1.1);opacity:1}}@keyframes talk{0%,100%{width:55px;left:48px}50%{width:30px;left:61px}}@keyframes armWave{0%,100%{transform:rotate(0deg)}50%{transform:rotate(-18deg)}}
    .metric-card{position:relative;border:1px solid #e2e8f0;border-radius:28px;background:rgba(255,255,255,.9);box-shadow:0 18px 45px rgba(15,23,42,.08);overflow:hidden}.metric-card:after{content:"";position:absolute;right:-45px;top:-45px;width:115px;height:115px;border-radius:50%;background:rgba(59,130,246,.08)}.metric-icon{width:54px;height:54px;border-radius:20px;display:grid;place-items:center;font-size:28px;background:#eff6ff;color:#1d4ed8}.metric-num{font-size:34px;font-weight:950;letter-spacing:-.06em;color:#0f172a}.metric-label{font-size:12px;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.06em}.metric-sub{font-size:12px;color:#94a3b8;font-weight:700}
    .panel{border:1px solid #e2e8f0;border-radius:30px;background:rgba(255,255,255,.94);box-shadow:0 18px 45px rgba(15,23,42,.07);position:relative;overflow:hidden}.panel-title{font-weight:950;letter-spacing:-.035em;color:#0f172a}.panel-sub{font-size:13px;color:#64748b}.bar-track{height:14px;background:#eef2ff;border-radius:999px;overflow:hidden}.bar-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,#2563eb,#7c3aed,#06b6d4);animation:barAlive 2s ease-in-out infinite}.bar-fill.orange{background:linear-gradient(90deg,#f59e0b,#f97316,#ef4444)}.bar-fill.green{background:linear-gradient(90deg,#10b981,#22c55e,#84cc16)}.bar-fill.purple{background:linear-gradient(90deg,#7c3aed,#d946ef,#06b6d4)}@keyframes barAlive{0%,100%{filter:saturate(1)}50%{filter:saturate(1.45) brightness(1.08)}}
    .donut{width:190px;height:190px;border-radius:50%;background:conic-gradient(#22c55e 0 var(--done),#f59e0b var(--done) var(--process-end),#ef4444 var(--process-end) var(--reject-end),#e2e8f0 var(--reject-end) 100%);display:grid;place-items:center;box-shadow:inset 0 0 0 18px rgba(255,255,255,.9),0 18px 38px rgba(15,23,42,.14)}.donut-center{width:112px;height:112px;border-radius:50%;background:#fff;display:grid;place-items:center;text-align:center}.donut-num{font-size:28px;font-weight:950;color:#0f172a;letter-spacing:-.05em}.legend-dot{width:10px;height:10px;border-radius:50%;display:inline-block}.table-monitor th{font-size:12px;text-transform:uppercase;letter-spacing:.06em;color:#64748b}.table-monitor td{vertical-align:middle}.svg-line{width:100%;height:250px}.mini-chip{display:inline-flex;align-items:center;gap:7px;padding:7px 11px;border-radius:999px;background:#f8fafc;border:1px solid #e2e8f0;font-size:12px;font-weight:850;color:#334155}.status-badge{display:inline-flex;align-items:center;border-radius:999px;padding:6px 10px;background:#eff6ff;color:#1d4ed8;font-size:12px;font-weight:900}.quick-btn{display:flex;align-items:center;gap:10px;padding:14px 16px;border:1px solid #e2e8f0;border-radius:22px;text-decoration:none;color:#0f172a;background:#fff;font-weight:900;box-shadow:0 10px 26px rgba(15,23,42,.05)}.quick-btn:hover{color:#1d4ed8;border-color:#bfdbfe;background:#eff6ff}.quick-btn i{font-size:24px}
</style>

@php
    $maxStatus = max(1, collect($statusRows)->max('total') ?: 1);
    $maxJenis = max(1, collect($jenisRows)->max('total') ?: 1);
    $maxWilayah = max(1, collect($wilayahRows)->max('total') ?: 1);
    $maxKec = max(1, collect($kecamatanRows)->max('total') ?: 1);
    $maxRole = max(1, collect($roleRows)->max('total') ?: 1);
    $statusMap = collect($statusRows)->keyBy('status');
    $total = max(1, (int) $kpis['total_surat']);
    $donePct = round(((int) $kpis['selesai'] / $total) * 100, 1);
    $processPct = round(((int) $kpis['menunggu'] / $total) * 100, 1);
    $rejectPct = round(((int) $kpis['ditolak'] / $total) * 100, 1);
    $processEndPct = min(100, $donePct + $processPct);
    $rejectEndPct = min(100, $processEndPct + $rejectPct);

    $monthly = collect($monthlyRows)->values();
    $maxMonthly = max(1, $monthly->max('total') ?: 1);
    $chartW = 640;
    $chartH = 210;
    $padX = 28;
    $padY = 24;
    $points = [];
    foreach ($monthly as $i => $row) {
        $count = max(1, $monthly->count());
        $x = $padX + ($count === 1 ? 0 : ($i * (($chartW - ($padX * 2)) / ($count - 1))));
        $y = $chartH - $padY - (((int) $row['total']) / $maxMonthly * ($chartH - ($padY * 2)));
        $points[] = round($x, 2) . ',' . round($y, 2);
    }
    $polylinePoints = implode(' ', $points);
    $areaPoints = $polylinePoints ? ($padX . ',' . ($chartH - $padY) . ' ' . $polylinePoints . ' ' . ($chartW - $padX) . ',' . ($chartH - $padY)) : '';
@endphp

<div class="container-fluid monitor-page">
    <div class="hero-super p-4 p-xl-5 mb-4">
        <div class="row align-items-center g-4 position-relative" style="z-index:2">
            <div class="col-xl-7">
                <div class="super-badge mb-4"><span class="pulse-live"></span> LIVE ALL INFORMATION CENTER</div>
                <h1 class="hero-title mb-3">BERANDA<br>SUPER ADMIN</h1>
                <p class="hero-sub mb-4">Layar Pantau Seluruh Informasi E-Suket Kota Kediri.<b> Super Admin</b>.</p>
                <div class="row g-2">
                    <div class="col-sm-4"><div class="glass-kpi"><div class="kpi-value">{{ number_format($kpis['total_surat']) }}</div><div class="kpi-label">Total Surat</div></div></div>
                    <div class="col-sm-4"><div class="glass-kpi"><div class="kpi-value">{{ number_format($kpis['menunggu_tte']) }}</div><div class="kpi-label">Total Menunggu TTE</div></div></div>
                    <div class="col-sm-4"><div class="glass-kpi"><div class="kpi-value">{{ number_format($kpis['selesai']) }}</div><div class="kpi-label">Selesai/Final</div></div></div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="robot-wrap">
                    <div class="robot-aura"></div>
                    <div class="robot">
                        <div class="robot-head"><span class="robot-eye eye-l"></span><span class="robot-eye eye-r"></span><span class="robot-mouth"></span></div>
                        <div class="robot-arm arm-l"></div><div class="robot-arm arm-r"></div><div class="robot-body"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['label'=>'Surat Hari Ini','value'=>$kpis['hari_ini'],'icon'=>'ri-calendar-event-line','sub'=>'pengajuan masuk hari ini'],
            ['label'=>'Surat Bulan Ini','value'=>$kpis['bulan_ini'],'icon'=>'ri-bar-chart-grouped-line','sub'=>'akumulasi bulan berjalan'],
            ['label'=>'Menunggu Proses','value'=>$kpis['menunggu'],'icon'=>'ri-loader-4-line','sub'=>'pengajuan/proses/verifikasi'],
            ['label'=>'TTE Lurah','value'=>$kpis['tte_lurah'],'icon'=>'ri-qr-code-line','sub'=>'menunggu tanda tangan lurah'],
            ['label'=>'TTE Camat','value'=>$kpis['tte_camat'],'icon'=>'ri-shield-check-line','sub'=>'khusus SKTM camat'],
            ['label'=>'TTD Basah','value'=>$kpis['manual'],'icon'=>'ri-file-paper-2-line','sub'=>'manual/upload bukti'],
            ['label'=>'Total Akun','value'=>$kpis['total_akun'],'icon'=>'ri-group-line','sub'=>'semua user sistem'],
            ['label'=>'Total Warga','value'=>$kpis['total_warga'],'icon'=>'ri-user-heart-line','sub'=>'role warga/client'],
            ['label'=>'Akun Perangkat','value'=>$kpis['total_pejabat'],'icon'=>'ri-user-star-line','sub'=>'admin, sekkel, lurah, sekcam, camat'],
            ['label'=>'Kecamatan','value'=>$kpis['total_kecamatan'],'icon'=>'ri-building-2-line','sub'=>'wilayah kecamatan'],
            ['label'=>'Kelurahan','value'=>$kpis['total_kelurahan'],'icon'=>'ri-community-line','sub'=>'wilayah kelurahan'],
            ['label'=>'RW / RT','value'=>number_format($kpis['total_rw']).' / '.number_format($kpis['total_rt']),'icon'=>'ri-road-map-line','sub'=>'data rt/rw database'],
        ] as $item)
            <div class="col-xxl-2 col-xl-3 col-md-4 col-sm-6">
                <div class="metric-card h-100 p-3">
                    <div class="d-flex justify-content-between gap-2 position-relative" style="z-index:2">
                        <div>
                            <div class="metric-label mb-2">{{ $item['label'] }}</div>
                            <div class="metric-num">{{ is_numeric($item['value']) ? number_format($item['value']) : $item['value'] }}</div>
                            <div class="metric-sub">{{ $item['sub'] }}</div>
                        </div>
                        <div class="metric-icon"><i class="{{ $item['icon'] }}"></i></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="panel p-4 h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h4 class="panel-title mb-1">Grafik Surat 6 Bulan Terakhir</h4>
                        <div class="panel-sub">Tren jumlah pengajuan berdasarkan tanggal dibuat.</div>
                    </div>
                    <span class="mini-chip"><i class="ri-line-chart-line"></i> Real data surat_pengajuans</span>
                </div>
                <svg class="svg-line" viewBox="0 0 {{ $chartW }} {{ $chartH }}" preserveAspectRatio="none" role="img" aria-label="Grafik surat bulanan">
                    <defs>
                        <linearGradient id="areaGradient" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#2563eb" stop-opacity="0.28" />
                            <stop offset="100%" stop-color="#2563eb" stop-opacity="0" />
                        </linearGradient>
                        <linearGradient id="lineGradient" x1="0" x2="1" y1="0" y2="0">
                            <stop offset="0%" stop-color="#2563eb" />
                            <stop offset="55%" stop-color="#7c3aed" />
                            <stop offset="100%" stop-color="#06b6d4" />
                        </linearGradient>
                    </defs>
                    @for ($i = 0; $i < 5; $i++)
                        @php $gy = $padY + ($i * (($chartH - ($padY * 2)) / 4)); @endphp
                        <line x1="{{ $padX }}" y1="{{ $gy }}" x2="{{ $chartW - $padX }}" y2="{{ $gy }}" stroke="#e2e8f0" stroke-width="1" />
                    @endfor
                    <polygon points="{{ $areaPoints }}" fill="url(#areaGradient)"></polygon>
                    <polyline points="{{ $polylinePoints }}" fill="none" stroke="url(#lineGradient)" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></polyline>
                    @foreach ($points as $idx => $point)
                        @php [$cx, $cy] = explode(',', $point); @endphp
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="7" fill="#fff" stroke="#2563eb" stroke-width="4"></circle>
                    @endforeach
                </svg>
                <div class="row g-2 mt-1">
                    @foreach ($monthly as $row)
                        <div class="col">
                            <div class="text-center rounded-4 p-2" style="background:#f8fafc;border:1px solid #e2e8f0">
                                <div class="fw-bold text-dark">{{ number_format($row['total']) }}</div>
                                <div class="small text-muted">{{ $row['label'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="panel p-4 h-100">
                <h4 class="panel-title mb-1">Ringkasan Status</h4>
                <div class="panel-sub mb-4">Komposisi selesai, proses, dan ditolak dari seluruh surat.</div>
                <div class="d-flex justify-content-center mb-3">
                    <div class="donut" style="--done: {{ $donePct }}%; --process-end: {{ $processEndPct }}%; --reject-end: {{ $rejectEndPct }}%;">
                        <div class="donut-center">
                            <div>
                                <div class="donut-num">{{ $donePct }}%</div>
                                <div class="small text-muted fw-bold">Selesai</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <div class="d-flex justify-content-between"><span><i class="legend-dot me-2" style="background:#22c55e"></i>Selesai/Final</span><b>{{ number_format($kpis['selesai']) }}</b></div>
                    <div class="d-flex justify-content-between"><span><i class="legend-dot me-2" style="background:#f59e0b"></i>Menunggu Proses</span><b>{{ number_format($kpis['menunggu']) }}</b></div>
                    <div class="d-flex justify-content-between"><span><i class="legend-dot me-2" style="background:#ef4444"></i>Ditolak</span><b>{{ number_format($kpis['ditolak']) }}</b></div>
                    <div class="d-flex justify-content-between"><span><i class="legend-dot me-2" style="background:#e2e8f0"></i>Lainnya</span><b>{{ number_format(max(0, $kpis['total_surat'] - $kpis['selesai'] - $kpis['menunggu'] - $kpis['ditolak'])) }}</b></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="panel p-4 h-100">
                <h4 class="panel-title mb-1">Detail Semua Status Surat</h4>
                <div class="panel-sub mb-3">Semua status yang ada pada database surat.</div>
                <div class="d-grid gap-3">
                    @forelse ($statusRows as $row)
                        @php $pct = round(($row['total'] / $maxStatus) * 100, 1); @endphp
                        <div>
                            <div class="d-flex justify-content-between mb-1"><span class="fw-bold">{{ $row['label'] }}</span><span class="status-badge">{{ number_format($row['total']) }}</span></div>
                            <div class="bar-track"><div class="bar-fill" style="width: {{ $pct }}%"></div></div>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada data status surat.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="panel p-4 h-100">
                <h4 class="panel-title mb-1">Detail Jenis Surat</h4>
                <div class="panel-sub mb-3">Jumlah pengajuan per jenis surat dari seluruh wilayah.</div>
                <div class="d-grid gap-3">
                    @forelse ($jenisRows as $row)
                        @php $pct = round(($row->total / $maxJenis) * 100, 1); @endphp
                        <div>
                            <div class="d-flex justify-content-between mb-1"><span class="fw-bold text-uppercase">{{ $row->jenis_surat ?: 'TIDAK DIKETAHUI' }}</span><span class="status-badge">{{ number_format($row->total) }}</span></div>
                            <div class="bar-track"><div class="bar-fill purple" style="width: {{ $pct }}%"></div></div>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada data jenis surat.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4">
            <div class="panel p-4 h-100">
                <h4 class="panel-title mb-1">Wilayah Teraktif</h4>
                <div class="panel-sub mb-3">Top kelurahan berdasarkan jumlah surat.</div>
                <div class="d-grid gap-3">
                    @forelse ($wilayahRows as $row)
                        @php $pct = round(($row->total / $maxWilayah) * 100, 1); @endphp
                        <div>
                            <div class="d-flex justify-content-between mb-1"><span class="fw-bold">{{ $row->id_skpd ? $row->id_skpd.' - ' : '' }}{{ $row->nama ?: 'Tidak Diketahui' }}</span><b>{{ number_format($row->total) }}</b></div>
                            <div class="bar-track"><div class="bar-fill green" style="width: {{ $pct }}%"></div></div>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada data wilayah.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="panel p-4 h-100">
                <h4 class="panel-title mb-1">Rekap Kecamatan</h4>
                <div class="panel-sub mb-3">Jumlah surat per kecamatan Kota Kediri.</div>
                <div class="d-grid gap-3">
                    @forelse ($kecamatanRows as $row)
                        @php $pct = round(($row->total / $maxKec) * 100, 1); @endphp
                        <div>
                            <div class="d-flex justify-content-between mb-1"><span class="fw-bold">{{ $row->nama }}</span><b>{{ number_format($row->total) }}</b></div>
                            <div class="bar-track"><div class="bar-fill orange" style="width: {{ $pct }}%"></div></div>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada data kecamatan.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="panel p-4 h-100">
                <h4 class="panel-title mb-1">Akun Per Role</h4>
                <div class="panel-sub mb-3">Jumlah user berdasarkan role.</div>
                <div class="d-grid gap-3">
                    @forelse ($roleRows as $row)
                        @php $pct = round(($row->total / $maxRole) * 100, 1); @endphp
                        <div>
                            <div class="d-flex justify-content-between mb-1"><span class="fw-bold">{{ $row->role_id }} - {{ $row->role_name }}</span><b>{{ number_format($row->total) }}</b></div>
                            <div class="bar-track"><div class="bar-fill" style="width: {{ $pct }}%"></div></div>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada data role.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="panel p-4 h-100">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h4 class="panel-title mb-1">Aktivitas Surat Terbaru</h4>
                        <div class="panel-sub">12 data terakhir berdasarkan updated_at/created_at.</div>
                    </div>
                    <a href="{{ route('super-admin.surat.index') }}" class="btn btn-dark rounded-4 fw-bold"><i class="ri-file-search-line me-1"></i>Lihat Kontrol Surat</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-monitor mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Jenis</th>
                                <th>Wilayah</th>
                                <th>Pemohon/NIK</th>
                                <th>Status</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentRows as $row)
                                @php
                                    $statusText = data_get($statusMap->get((int) $row->status), 'label', 'Status '.$row->status);
                                    $waktu = $row->updated_at ?? $row->created_at;
                                @endphp
                                <tr>
                                    <td><b>#{{ $row->id }}</b></td>
                                    <td class="text-uppercase fw-bold">{{ $row->jenis_surat }}</td>
                                    <td>{{ optional($row->kelurahan)->nama ?? '-' }}</td>
                                    <td>{{ $row->kepada ?: ($row->nik ?: '-') }}</td>
                                    <td><span class="status-badge">{{ $statusText }}</span></td>
                                    <td>{{ $waktu ? \Carbon\Carbon::parse($waktu)->format('d-m-Y H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada aktivitas surat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="panel p-4 h-100">
                <h4 class="panel-title mb-1">Akses Cepat Super Admin</h4>
                <div class="panel-sub mb-3">Shortcut untuk masuk ke pusat kontrol.</div>
                <div class="d-grid gap-3">
                    <a class="quick-btn" href="{{ route('super-admin.dashboard') }}"><i class="ri-shield-keyhole-line"></i><span>Pusat Kontrol Super Admin</span></a>
                    <a class="quick-btn" href="{{ route('super-admin.surat.index') }}"><i class="ri-file-shield-2-line"></i><span>Kontrol Semua Surat</span></a>
                    <a class="quick-btn" href="{{ route('super-admin.pelayanan.index') }}"><i class="ri-file-edit-line"></i><span>Pelayanan Warga</span></a>
                    <a class="quick-btn" href="{{ route('super-admin.users.index') }}"><i class="ri-user-settings-line"></i><span>Kontrol Akun</span></a>
                    <a class="quick-btn" href="{{ route('super-admin.profil-instansi') }}"><i class="ri-government-line"></i><span>Profil Instansi Kota</span></a>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
