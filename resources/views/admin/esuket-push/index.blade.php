@extends('layouts.main')

@section('title', 'Log Push SSO')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1">Monitoring Push SSO / FCM</h3>
                <p class="text-muted mb-0">Riwayat pengiriman notifikasi dan perangkat FCM yang terdaftar.</p>
            </div>
            <form method="GET" action="{{ route('admin.esuket-push.index') }}" class="d-flex gap-2">
                <input type="search" name="q" value="{{ $search }}" class="form-control"
                    placeholder="Cari nama, NIK, email, perangkat..." aria-label="Cari log atau perangkat">
                <button class="btn btn-primary text-nowrap" type="submit">
                    <i class="ri-search-line me-1"></i> Cari
                </button>
                @if ($search !== '')
                    <a class="btn btn-outline-secondary" href="{{ route('admin.esuket-push.index') }}">Reset</a>
                @endif
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card glass-card border-0 rounded-4 h-100">
                    <div class="card-body">
                        <div class="small text-muted">Perangkat dengan token</div>
                        <div class="fs-3 fw-bold">{{ number_format($deviceCount) }}</div>
                    </div>
                </div>
            </div>
            @foreach (['sent' => 'Terkirim', 'retry' => 'Perlu dicoba ulang', 'permanent_fail' => 'Gagal permanen', 'pending' => 'Menunggu'] as $status => $label)
                <div class="col-sm-6 col-xl-3">
                    <div class="card glass-card border-0 rounded-4 h-100">
                        <div class="card-body">
                            <div class="small text-muted">{{ $label }}</div>
                            <div class="fs-3 fw-bold">{{ number_format((int) ($deliveryCounts[$status] ?? 0)) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card glass-card border-0 rounded-4 mb-4">
            <div class="card-header glass-accent bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-1"><i class="ri-time-line me-1"></i> Riwayat Pengiriman</h5>
                <p class="small text-muted mb-0">Status terakhir setiap pengiriman per perangkat. Jumlah percobaan mencakup retry antrean.</p>
            </div>
            <div class="card-body px-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Waktu pembaruan</th>
                                <th>Pemohon / akun</th>
                                <th>Surat</th>
                                <th>Event</th>
                                <th>Perangkat</th>
                                <th>Status</th>
                                <th>Percobaan</th>
                                <th>HTTP</th>
                                <th>Kode error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($deliveries as $delivery)
                                @php
                                    $device = $delivery->device;
                                    $user = $device?->user;
                                    $statusClass = match ($delivery->status) {
                                        'sent' => 'success',
                                        'retry' => 'warning',
                                        'permanent_fail' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <tr>
                                    <td class="text-nowrap">{{ $delivery->updated_at?->format('d/m/Y H:i:s') ?? '-' }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $user?->name ?? 'Akun tidak tersedia' }}</div>
                                        <small class="text-muted">{{ $user?->nik ?? '-' }}</small>
                                    </td>
                                    <td>#{{ $delivery->surat_pengajuan_id }}<br><small class="text-muted">{{ $delivery->surat?->jenis_surat ?? '-' }}</small></td>
                                    <td>{{ $delivery->event_name }}</td>
                                    <td>{{ $device?->platform ?? '-' }}<br><small class="text-muted">{{ $device?->device_id ?? '-' }}</small></td>
                                    <td><span class="badge text-bg-{{ $statusClass }}">{{ str_replace('_', ' ', $delivery->status) }}</span></td>
                                    <td>{{ $delivery->attempts }}</td>
                                    <td>{{ $delivery->http_status ?? '-' }}</td>
                                    <td>{{ $delivery->error_code ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">Belum ada riwayat pengiriman yang cocok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $deliveries->links() }}
            </div>
        </div>

        <div class="card glass-card border-0 rounded-4">
            <div class="card-header glass-accent bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-1"><i class="ri-smartphone-line me-1"></i> Akun dan Token FCM</h5>
                <p class="small text-muted mb-0">
                    Token ditampilkan lengkap hanya di halaman admin ini. Perlakukan token sebagai data sensitif dan jangan bagikan.
                </p>
            </div>
            <div class="card-body px-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Akun</th>
                                <th>Perangkat</th>
                                <th>Platform</th>
                                <th>Token FCM</th>
                                <th>Terdaftar / diperbarui</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($devices as $device)
                                @php
                                    $token = $device->fcm_token;
                                    $maskedToken = strlen($token) > 20
                                        ? substr($token, 0, 10) . '...' . substr($token, -8)
                                        : $token;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $device->user?->name ?? 'Akun tidak tersedia' }}</div>
                                        <small class="text-muted">NIK: {{ $device->user?->nik ?? '-' }}</small>
                                    </td>
                                    <td>{{ $device->device_id }}</td>
                                    <td><span class="badge text-bg-light">{{ strtoupper($device->platform) }}</span></td>
                                    <td style="min-width: 280px">
                                        <details>
                                            <summary class="font-monospace small text-break">{{ $maskedToken }}</summary>
                                            <code class="d-block text-break mt-2 user-select-all">{{ $token }}</code>
                                        </details>
                                    </td>
                                    <td class="text-nowrap">
                                        <div>{{ $device->created_at?->format('d/m/Y H:i') ?? '-' }}</div>
                                        <small class="text-muted">Update: {{ $device->updated_at?->format('d/m/Y H:i') ?? '-' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada perangkat FCM terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $devices->links() }}
            </div>
        </div>
    </div>
@endsection
