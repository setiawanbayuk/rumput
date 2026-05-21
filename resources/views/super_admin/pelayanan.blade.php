@extends('layouts.main')

@section('content')
<style>
    .super-card { border: 1px solid #eef0f4; border-radius: 22px; box-shadow: 0 16px 35px rgba(15, 23, 42, .06); }
    .super-pill { display:inline-flex; align-items:center; border-radius:999px; padding:6px 11px; background:#f8fafc; border:1px solid #eef2f7; font-size:12px; font-weight:800; color:#64748b; }
    .jenis-btn { border:1px solid #e5e7eb; border-radius:18px; padding:14px; text-decoration:none; color:#111827; background:#fff; display:block; height:100%; transition:.15s; }
    .jenis-btn:hover { transform: translateY(-2px); box-shadow:0 14px 30px rgba(15,23,42,.08); color:#111827; }
</style>
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="super-pill mb-2">SUPER ADMIN / PELAYANAN WARGA</span>
            <h3 class="fw-bold mb-1">Pelayanan Warga Semua Kelurahan</h3>
            <div class="text-muted">Pilih kecamatan dan kelurahan, lalu buat surat seperti Admin Kelurahan tanpa mengubah alur lama.</div>
        </div>
        <a href="{{ route('super-admin.surat.index') }}" class="btn btn-outline-dark rounded-4 fw-semibold">
            <i class="ri-shield-check-line me-1"></i> Kontrol Semua Surat
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif
    @if (session('status'))
        <div class="alert alert-success rounded-4">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger rounded-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card super-card mb-3">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">Kecamatan</label>
                    <select name="id_kec" class="form-select rounded-4" onchange="this.form.submit()">
                        <option value="">Semua Kecamatan</option>
                        @foreach ($kecamatans as $kec)
                            <option value="{{ $kec->id }}" @selected((string) request('id_kec') === (string) $kec->id)>Kec. {{ $kec->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold">Kelurahan</label>
                    <select name="id_kel" class="form-select rounded-4">
                        <option value="">Pilih Kelurahan</option>
                        @foreach ($kelurahans as $kel)
                            <option value="{{ $kel->id }}" @selected((string) request('id_kel') === (string) $kel->id)>{{ $kel->super_label ?? $kel->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold">Jenis</label>
                    <select name="jenis" class="form-select rounded-4">
                        <option value="">Semua</option>
                        @foreach ($jenisOptions as $kode => $label)
                            <option value="{{ $kode }}" @selected(request('jenis') === $kode)>{{ strtoupper($kode) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select rounded-4">
                        <option value="">Semua</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected((string) request('status') === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-12 d-flex gap-2">
                    <button class="btn btn-dark rounded-4 fw-semibold w-100" type="submit">Filter</button>
                    <a href="{{ route('super-admin.pelayanan.index') }}" class="btn btn-outline-secondary rounded-4">Reset</a>
                </div>
                <div class="col-12">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-4" placeholder="Cari ID, NIK, nama/kepada, atau peruntukan">
                </div>
            </form>
        </div>
    </div>

    <div class="card super-card mb-3">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Tambah Surat</h5>
                    <div class="text-muted">Wajib pilih kelurahan terlebih dahulu.</div>
                </div>
                @if ($selectedKelurahan)
                    <span class="badge bg-dark rounded-pill px-3 py-2">Kelurahan: {{ $selectedKelurahan->id }} - {{ $selectedKelurahan->nama }}</span>
                @else
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Belum pilih kelurahan</span>
                @endif
            </div>
            <div class="row g-2">
                @foreach ($jenisOptions as $kode => $label)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        @if ($selectedKelurahan)
                            <a class="jenis-btn" href="{{ route('super-admin.pelayanan.create', ['jenis' => $kode, 'id_kel' => $selectedKelurahan->id]) }}">
                                <div class="fw-bold">{{ strtoupper($kode) }}</div>
                                <small class="text-muted">{{ $label }}</small>
                            </a>
                        @else
                            <div class="jenis-btn opacity-50" title="Pilih kelurahan dahulu">
                                <div class="fw-bold">{{ strtoupper($kode) }}</div>
                                <small class="text-muted">{{ $label }}</small>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card super-card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Daftar Surat Pelayanan</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr><th>ID</th><th>Jenis</th><th>NIK</th><th>Kepada</th><th>Kelurahan</th><th>Status</th><th>Update</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                    @forelse ($surats as $row)
                        <tr>
                            <td class="fw-bold">#{{ $row->id }}</td>
                            <td><span class="badge bg-info text-dark">{{ strtoupper($row->jenis_surat) }}</span></td>
                            <td>{{ $row->nik }}</td>
                            <td>{{ $row->kepada ?: '-' }}</td>
                            <td>{{ optional($row->kelurahan)->nama ?? '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $row->st['name'] ?? 'Status '.$row->status }}</span></td>
                            <td>{{ optional($row->updated_at ?? $row->created_at)->format('d-m-Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.surat.edit', $row->id) }}" class="btn btn-sm btn-warning rounded-3" title="Edit"><i class="ri-edit-2-line"></i></a>
                                <a href="{{ route('admin.surat.preview', $row->id) }}" class="btn btn-sm btn-info rounded-3" title="Preview" target="_blank"><i class="ri-eye-line"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Data surat tidak ditemukan.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $surats->links() }}</div>
        </div>
    </div>
</div>
@endsection
