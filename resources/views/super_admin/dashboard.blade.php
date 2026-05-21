@extends('layouts.main')

@section('content')
    <style>
        .super-card { border: 1px solid #eef0f4; border-radius: 22px; box-shadow: 0 16px 35px rgba(15, 23, 42, .06); }
        .super-kpi { background: linear-gradient(145deg, #fff, #f8fafc); }
        .super-kpi .icon { width: 44px; height: 44px; border-radius: 15px; display: grid; place-items: center; background: #f3f0e8; color: #756643; font-size: 22px; }
        .super-kpi .num { font-weight: 900; font-size: 30px; letter-spacing: -.03em; color: #111827; }
        .super-pill { display:inline-flex; align-items:center; border-radius:999px; padding:6px 11px; background:#f8fafc; border:1px solid #eef2f7; font-size:12px; font-weight:800; color:#64748b; }
    </style>

    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <span class="super-pill mb-2">SUPER ADMIN</span>
                <h3 class="fw-bold mb-1">Dashboard Kontrol Utama</h3>
              
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('super-admin.surat.index') }}" class="btn btn-dark rounded-4 fw-semibold">
                    <i class="ri-file-list-3-line me-1"></i> Kontrol Surat
                </a>
                <a href="{{ route('super-admin.pelayanan.index') }}" class="btn btn-outline-dark rounded-4 fw-semibold">
                    <i class="ri-file-edit-line me-1"></i> Pelayanan Warga
                </a>
                <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-dark rounded-4 fw-semibold">
                    <i class="ri-user-settings-line me-1"></i> Kontrol Akun
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @foreach ([
                ['label' => 'Total Surat', 'value' => $counts['surat'], 'icon' => 'ri-file-copy-2-line'],
                ['label' => 'Total Akun', 'value' => $counts['akun'], 'icon' => 'ri-group-line'],
                ['label' => 'Menunggu Proses', 'value' => $counts['menunggu'], 'icon' => 'ri-time-line'],
                ['label' => 'Menunggu TTE', 'value' => $counts['tte'], 'icon' => 'ri-qr-code-line'],
                ['label' => 'Selesai', 'value' => $counts['selesai'], 'icon' => 'ri-checkbox-circle-line'],
                ['label' => 'Ditolak', 'value' => $counts['ditolak'], 'icon' => 'ri-close-circle-line'],
            ] as $item)
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card super-card super-kpi h-100">
                        <div class="card-body">
                            <div class="icon mb-3"><i class="{{ $item['icon'] }}"></i></div>
                            <div class="num">{{ number_format($item['value']) }}</div>
                            <div class="text-muted fw-semibold">{{ $item['label'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card super-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Surat Terbaru</h5>
                            <a href="{{ route('super-admin.surat.index') }}" class="btn btn-sm btn-outline-dark rounded-4">Lihat Semua</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Jenis</th>
                                        <th>NIK</th>
                                        <th>Kelurahan</th>
                                        <th>Status</th>
                                        <th>Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentSurat as $row)
                                        <tr>
                                            <td class="fw-bold">#{{ $row->id }}</td>
                                            <td><span class="badge bg-info text-dark">{{ strtoupper($row->jenis_surat) }}</span></td>
                                            <td>{{ $row->nik }}</td>
                                            <td>{{ optional($row->kelurahan)->nama ?? '-' }}</td>
                                            <td><span class="badge bg-secondary">{{ $row->st['name'] ?? 'Status '.$row->status }}</span></td>
                                            <td>{{ optional($row->updated_at ?? $row->created_at)->format('d-m-Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada surat.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card super-card mb-3">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Rekap Status Surat</h5>
                        @forelse ($statusRows as $row)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ $row['label'] }}</span>
                                <b>{{ $row['total'] }}</b>
                            </div>
                        @empty
                            <div class="text-muted">Belum ada data.</div>
                        @endforelse
                    </div>
                </div>
                <div class="card super-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Rekap Akun per Role</h5>
                        @forelse ($roleRows as $row)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ $row->role_name }}</span>
                                <b>{{ $row->total }}</b>
                            </div>
                        @empty
                            <div class="text-muted">Belum ada akun.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
