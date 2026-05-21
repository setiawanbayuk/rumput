@extends('layouts.main')

@section('content')
<style>
    .city-hero{position:relative;overflow:hidden;border-radius:30px;background:linear-gradient(135deg,#0f172a,#1e293b 48%,#312e81);color:#fff;box-shadow:0 28px 70px rgba(15,23,42,.24)}
    .city-hero:before{content:"";position:absolute;inset:-80px -120px auto auto;width:360px;height:360px;border-radius:50%;background:radial-gradient(circle,rgba(99,102,241,.75),rgba(99,102,241,0));animation:pulseCity 4s ease-in-out infinite}
    .city-hero:after{content:"";position:absolute;left:-90px;bottom:-120px;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(14,165,233,.5),rgba(14,165,233,0));animation:pulseCity 5s ease-in-out infinite reverse}
    @keyframes pulseCity{0%,100%{transform:scale(.92);opacity:.65}50%{transform:scale(1.08);opacity:1}}
    .city-card{border:1px solid #e2e8f0;border-radius:24px;box-shadow:0 18px 42px rgba(15,23,42,.07);background:#fff}
    .city-stat{border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.12);border-radius:22px;padding:18px;backdrop-filter:blur(12px)}
    .stat-num{font-size:34px;font-weight:950;letter-spacing:-.05em}.stat-label{font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;font-weight:900}
    .info-box{border:1px solid #e2e8f0;border-radius:18px;padding:14px;background:#f8fafc}.info-label{font-size:12px;text-transform:uppercase;letter-spacing:.07em;font-weight:900;color:#64748b}.info-value{font-size:18px;font-weight:900;color:#0f172a}
</style>

<div class="container-fluid">
    <div class="city-hero p-4 p-xl-5 mb-4">
        <div class="position-relative" style="z-index:2">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-3" style="background:rgba(255,255,255,.13);font-weight:900">
                <i class="ri-government-line"></i> PROFIL INSTANSI SUPER ADMIN
            </div>
            <div class="row align-items-end g-4">
                <div class="col-xl-7">
                    <h1 class="fw-bold mb-2" style="font-size:clamp(34px,5vw,68px);letter-spacing:-.06em;line-height:.95">{{ $skpd->nama ?? 'KOTA KEDIRI' }}</h1>
                    
                </div>
                <div class="col-xl-5">
                    <div class="row g-2">
                        <div class="col-6"><div class="city-stat"><div class="stat-num">{{ number_format($counts['kecamatan'] ?? 0) }}</div><div class="stat-label">Kecamatan</div></div></div>
                        <div class="col-6"><div class="city-stat"><div class="stat-num">{{ number_format($counts['kelurahan'] ?? 0) }}</div><div class="stat-label">Kelurahan</div></div></div>
                        <div class="col-6"><div class="city-stat"><div class="stat-num">{{ number_format($counts['rw'] ?? 0) }}</div><div class="stat-label">RW</div></div></div>
                        <div class="col-6"><div class="city-stat"><div class="stat-num">{{ number_format($counts['rt'] ?? 0) }}</div><div class="stat-label">RT</div></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success rounded-4">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger rounded-4">
            <strong>Data belum bisa disimpan.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="city-card p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-4 d-grid place-items-center" style="width:56px;height:56px;background:#eef2ff;color:#3730a3;font-size:28px"><i class="ri-building-4-line"></i></div>
                    <div>
                        <h4 class="fw-bold mb-0">Identitas Instansi</h4>
                        
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><div class="info-box"><div class="info-label">Nama Instansi</div><div class="info-value">{{ $skpd->nama ?? 'KOTA KEDIRI' }}</div></div></div>
                    <div class="col-md-6"><div class="info-box"><div class="info-label">Jenis Wilayah</div><div class="info-value">Kota Kediri</div></div></div>

                    <div class="col-12"><div class="info-box"><div class="info-label">Alamat Instansi</div><div class="info-value">{{ $skpd->instansi_alamat ?? '-' }}</div></div></div>
                    <div class="col-md-6"><div class="info-box"><div class="info-label">Telepon</div><div class="info-value">{{ $skpd->instansi_telp ?? '-' }}</div></div></div>
                    <div class="col-md-6"><div class="info-box"><div class="info-label">Email</div><div class="info-value">{{ $skpd->instansi_email ?? '-' }}</div></div></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="city-card p-4 h-100">
                <h4 class="fw-bold mb-1">Edit Kontak Kota</h4>
                
                <form method="POST" action="{{ route('super-admin.profil-instansi.update') }}" class="row g-3">
                    @csrf
                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat Instansi</label>
                        <textarea name="instansi_alamat" rows="3" class="form-control rounded-4 text-uppercase">{{ old('instansi_alamat', $skpd->instansi_alamat ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Telepon</label>
                        <input type="text" name="instansi_telp" value="{{ old('instansi_telp', $skpd->instansi_telp ?? '') }}" class="form-control rounded-4 only-number" inputmode="numeric" pattern="[0-9]*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fax</label>
                        <input type="text" name="instansi_fax" value="{{ old('instansi_fax', $skpd->instansi_fax ?? '') }}" class="form-control rounded-4 only-number" inputmode="numeric" pattern="[0-9]*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Pos</label>
                        <input type="text" name="instansi_kode_pos" value="{{ old('instansi_kode_pos', $skpd->instansi_kode_pos ?? '') }}" class="form-control rounded-4 only-number" inputmode="numeric" pattern="[0-9]*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="instansi_email" value="{{ old('instansi_email', $skpd->instansi_email ?? '') }}" class="form-control rounded-4">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-dark rounded-4 fw-bold w-100 py-3"><i class="ri-save-3-line me-1"></i>Simpan Profil Instansi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.only-number').forEach(function (el) {
        el.addEventListener('input', function () { this.value = this.value.replace(/\D/g, ''); });
    });
    document.querySelectorAll('.text-uppercase').forEach(function (el) {
        el.addEventListener('input', function () {
            const start = this.selectionStart, end = this.selectionEnd;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(start, end);
        });
    });
});
</script>
@endpush
