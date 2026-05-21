@extends('layouts.main')

@section('title', 'Profil Instansi')

@section('content')
    @push('styles')
        <style>
            .gov-profile-page{position:relative;min-height:calc(100vh - 120px);padding-bottom:34px;background:linear-gradient(180deg,#f7fbff 0%,#ffffff 46%,#f8fafc 100%)}
            .gov-profile-page:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 8% 8%,rgba(14,165,233,.10),transparent 26%),radial-gradient(circle at 92% 0%,rgba(16,185,129,.10),transparent 28%);pointer-events:none}
            .gov-wrap{position:relative;z-index:1}.gov-hero{position:relative;overflow:hidden;border:0;border-radius:34px;background:linear-gradient(135deg,#0b3158 0%,#0d6b8b 48%,#12936f 100%);color:#fff;box-shadow:0 28px 80px rgba(11,49,88,.24)}
            .gov-hero:before{content:"";position:absolute;right:-78px;top:-90px;width:310px;height:310px;border-radius:999px;background:rgba(255,255,255,.13)}
            .gov-hero:after{content:"";position:absolute;left:52%;bottom:-130px;width:300px;height:300px;border-radius:999px;background:rgba(255,255,255,.09)}
            .gov-pattern{position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.07) 1px,transparent 1px);background-size:38px 38px;mask-image:linear-gradient(90deg,rgba(0,0,0,.45),transparent 76%)}
            .agency-seal{width:132px;height:132px;border-radius:38px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.30);display:grid;place-items:center;font-size:76px;box-shadow:inset 0 1px 0 rgba(255,255,255,.28),0 20px 48px rgba(0,0,0,.16);position:relative}.agency-seal .mini{position:absolute;right:-12px;bottom:-12px;width:52px;height:52px;border-radius:19px;background:#fff;color:#0f766e;display:grid;place-items:center;font-size:28px;box-shadow:0 16px 34px rgba(15,23,42,.20)}
            .hero-pill{display:inline-flex;align-items:center;gap:.45rem;border-radius:999px;padding:.48rem .9rem;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.27);font-weight:850;font-size:.82rem;backdrop-filter:blur(8px)}
            .gov-title{font-weight:950;letter-spacing:-.045em;line-height:1.05}.gov-sub{font-size:1rem;opacity:.78;max-width:720px}.gov-chip-row{display:flex;flex-wrap:wrap;gap:.55rem}
            .stat-card{border:0;border-radius:28px;background:#fff;box-shadow:0 18px 54px rgba(15,23,42,.085);height:100%;overflow:hidden;position:relative}.stat-card:before{content:"";position:absolute;left:0;right:0;top:0;height:5px;background:linear-gradient(90deg,#0ea5e9,#10b981,#f59e0b)}
            .stat-icon{width:58px;height:58px;border-radius:20px;background:linear-gradient(135deg,#ecfeff,#eff6ff);color:#0e7490;display:grid;place-items:center;font-size:31px;flex:none}.stat-value{font-weight:950;font-size:2.08rem;letter-spacing:-.05em;color:#0f172a;line-height:1}.stat-label{font-size:.76rem;text-transform:uppercase;letter-spacing:.09em;color:#64748b;font-weight:900;margin-top:4px}
            .panel-card{border:0;border-radius:30px;background:rgba(255,255,255,.94);box-shadow:0 18px 54px rgba(15,23,42,.085);backdrop-filter:blur(10px);overflow:hidden}.panel-head{padding:22px 24px;border-bottom:1px solid #eef2f7;background:linear-gradient(180deg,#fff,#fbfdff);display:flex;align-items:center;gap:14px}.panel-icon{width:50px;height:50px;border-radius:18px;background:linear-gradient(135deg,#ecfeff,#eff6ff);color:#0e7490;display:grid;place-items:center;font-size:27px;flex:none}.panel-title{font-weight:950;color:#0f172a;margin:0;letter-spacing:-.02em}.panel-sub{font-size:.86rem;color:#64748b;margin-top:2px}.panel-body{padding:24px}
            .info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.info-box{position:relative;border:1px solid #e8eef5;border-radius:20px;padding:16px 17px;background:linear-gradient(180deg,#fff,#fbfdff);min-height:86px}.info-label{font-size:.72rem;text-transform:uppercase;letter-spacing:.09em;color:#64748b;font-weight:900;margin-bottom:5px}.info-value{font-weight:850;color:#0f172a;word-break:break-word}.info-box.full{grid-column:1/-1}
            .contact-panel{position:relative;border-radius:30px;overflow:hidden;background:linear-gradient(135deg,#e0f2fe,#dcfce7);border:1px solid #dbeafe;height:100%}.contact-panel:before{content:"";position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(14,165,233,.13);right:-75px;top:-70px}.contact-panel:after{content:"";position:absolute;width:160px;height:160px;border-radius:50%;background:rgba(34,197,94,.16);left:-55px;bottom:-55px}.contact-content{position:relative;z-index:1;padding:24px}.contact-icon-main{width:72px;height:72px;border-radius:25px;background:#fff;color:#0f766e;display:grid;place-items:center;font-size:38px;box-shadow:0 16px 34px rgba(15,23,42,.10)}
            .mini-chip{display:inline-flex;align-items:center;gap:.38rem;border-radius:999px;padding:.48rem .76rem;background:#f0fdfa;color:#0f766e;font-weight:850;font-size:.82rem;margin:.24rem;border:1px solid #ccfbf1}.form-panel{border:1px solid #e5edf6;border-radius:26px;background:linear-gradient(180deg,#ffffff,#f8fbff);padding:22px}.form-label{font-weight:850;color:#334155}.form-control{border-radius:15px;border-color:#dbe5ef;min-height:46px}.form-control:focus{box-shadow:0 0 0 .22rem rgba(14,165,233,.12);border-color:#38bdf8}.btn-save-profile{border:0;border-radius:16px;font-weight:900;background:linear-gradient(135deg,#0ea5e9,#10b981);box-shadow:0 14px 28px rgba(16,185,129,.20)}
            .gov-note{border:1px solid #bae6fd;background:linear-gradient(135deg,#f0f9ff,#f0fdfa);border-radius:22px;color:#075985}.required-star{color:#ef4444}@media(max-width:768px){.info-grid{grid-template-columns:1fr}.agency-seal{width:96px;height:96px;border-radius:30px;font-size:54px}.gov-title{font-size:1.8rem}.panel-body,.panel-head,.contact-content{padding:18px}.stat-value{font-size:1.65rem}}
        </style>
    @endpush

    <div class="gov-profile-page">
        <div class="container-fluid py-3 gov-wrap">
            @if (session('status'))
                <div class="alert alert-success rounded-4 border-0 shadow-sm"><i class="ri-checkbox-circle-line me-1"></i>{{ session('status') }}</div>
            @endif

            <div class="gov-hero mb-4">
                <div class="gov-pattern"></div>
                <div class="card-body p-4 p-lg-5 position-relative">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="hero-pill mb-3"><i class="ri-government-line"></i>{{ $scope['label'] ?? 'Profil Instansi' }}</span>
                            <h2 class="gov-title mb-2">{{ $scope['unit_name'] ?? ($skpd->nama ?? 'Instansi') }}</h2>
                            <div class="gov-sub mb-3">Dashboard resmi instansi Pemerintahan Kota Kediri.</div>
                            <div class="gov-chip-row">
                                <span class="hero-pill"><i class="ri-map-pin-2-line"></i>Kecamatan {{ $scope['kecamatan_name'] ?? '-' }}</span>
                                @if (($scope['type'] ?? '') === 'kelurahan')
                                    <span class="hero-pill"><i class="ri-community-line"></i>Kelurahan {{ $scope['kelurahan_name'] ?? '-' }}</span>
                                @else
                                    <span class="hero-pill"><i class="ri-building-2-line"></i>{{ $counts['kelurahan'] ?? 0 }} Kelurahan</span>
                                @endif
                                <span class="hero-pill"><i class="ri-service-line"></i>Layanan Surat Digital</span>
                            </div>
                        </div>
                        <div class="col-lg-4 d-flex justify-content-lg-end justify-content-start">
                            <div class="agency-seal">
                                <i class="ri-building-4-line"></i>
                                <div class="mini"><i class="ri-verified-badge-line"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-3"><div class="stat-card"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="stat-icon"><i class="ri-user-smile-line"></i></div><div><div class="stat-value">{{ number_format($counts['warga'] ?? 0,0,',','.') }}</div><div class="stat-label">Total Warga</div></div></div></div></div>
                <div class="col-md-6 col-xl-3"><div class="stat-card"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="stat-icon"><i class="ri-road-map-line"></i></div><div><div class="stat-value">{{ number_format($counts['rw'] ?? 0,0,',','.') }}</div><div class="stat-label">Total RW</div></div></div></div></div>
                <div class="col-md-6 col-xl-3"><div class="stat-card"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="stat-icon"><i class="ri-map-pin-user-line"></i></div><div><div class="stat-value">{{ number_format($counts['rt'] ?? 0,0,',','.') }}</div><div class="stat-label">Total RT</div></div></div></div></div>
                <div class="col-md-6 col-xl-3"><div class="stat-card"><div class="card-body p-4 d-flex align-items-center gap-3"><div class="stat-icon"><i class="{{ ($scope['type'] ?? '') === 'kecamatan' ? 'ri-community-line' : 'ri-file-shield-2-line' }}"></i></div><div><div class="stat-value">{{ number_format((($scope['type'] ?? '') === 'kecamatan' ? ($counts['kelurahan'] ?? 0) : ($counts['surat'] ?? 0)),0,',','.') }}</div><div class="stat-label">{{ ($scope['type'] ?? '') === 'kecamatan' ? 'Total Kelurahan' : 'Surat Selesai' }}</div></div></div></div></div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-xl-7">
                    <div class="panel-card h-100">
                        <div class="panel-head"><div class="panel-icon"><i class="ri-profile-line"></i></div><div><h5 class="panel-title">Identitas Instansi</h5><div class="panel-sub"></div></div></div>
                        <div class="panel-body">
                            <div class="info-grid">
                                <div class="info-box"><div class="info-label">Nama Instansi</div><div class="info-value">{{ $skpd->nama ?? '-' }}</div></div>
                                <div class="info-box"><div class="info-label">Jenis Wilayah</div><div class="info-value">{{ ($scope['type'] ?? '') === 'kecamatan' ? 'Kecamatan' : 'Kelurahan' }}</div></div>
                                <div class="info-box"><div class="info-label">Kecamatan</div><div class="info-value">{{ $scope['kecamatan_name'] ?? '-' }}</div></div>
                                @if (($scope['type'] ?? '') === 'kelurahan')
                                    <div class="info-box"><div class="info-label">Kelurahan</div><div class="info-value">{{ $scope['kelurahan_name'] ?? '-' }}</div></div>
                                @endif
                                <div class="info-box full"><div class="info-label">Alamat Instansi</div><div class="info-value">{{ $skpd->instansi_alamat ?? '-' }}</div></div>
                            </div>

                            @if (($scope['type'] ?? '') === 'kecamatan')
                                <div class="mt-4">
                                    <div class="info-label mb-2">Wilayah Kelurahan</div>
                                    <div>
                                        @forelse (($scope['kelurahans'] ?? collect()) as $kel)
                                            <span class="mini-chip"><i class="ri-map-pin-line"></i>{{ $kel->nama }}</span>
                                        @empty
                                            <span class="text-muted">Belum ada kelurahan terbaca pada wilayah kecamatan ini.</span>
                                        @endforelse
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-5">
                    <div class="contact-panel">
                        <div class="contact-content">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="contact-icon-main"><i class="ri-customer-service-2-line"></i></div>
                                <div>
                                    <h5 class="fw-bold mb-1">Kontak & Layanan</h5>
                                    <div class="text-muted small">Kanal resmi pelayanan instansi.</div>
                                </div>
                            </div>
                            <div class="info-grid" style="grid-template-columns:1fr">
                                <div class="info-box"><div class="info-label">Telepon</div><div class="info-value">{{ $skpd->instansi_telp ?? '-' }}</div></div>
                                <div class="info-box"><div class="info-label">Fax</div><div class="info-value">{{ $skpd->instansi_fax ?? '-' }}</div></div>
                                <div class="info-box"><div class="info-label">Email</div><div class="info-value">{{ $skpd->instansi_email ?? '-' }}</div></div>
                                <div class="info-box"><div class="info-label">Kode Pos</div><div class="info-value">{{ $skpd->instansi_kode_pos ?? '-' }}</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-head flex-wrap justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="panel-icon"><i class="ri-edit-box-line"></i></div>
                        <div><h5 class="panel-title">Edit Profil Instansi Di Bawah Dan Simpan</h5></div>
                    </div>
                </div>
                <div class="panel-body">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4 border-0">
                            <strong>Data belum bisa disimpan.</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="form-panel">
                        <form method="POST" action="{{ route('tools.profil-instansi.update') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Alamat Instansi</label>
                                    <textarea name="instansi_alamat" rows="3" class="form-control text-uppercase" placeholder="Isi alamat instansi">{{ old('instansi_alamat', $skpd->instansi_alamat ?? '') }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Telepon <span class="required-star">*</span></label>
                                    <input type="text" inputmode="numeric" pattern="[0-9]+" required name="instansi_telp" class="form-control only-number @error('instansi_telp') is-invalid @enderror" value="{{ old('instansi_telp', $skpd->instansi_telp ?? '') }}" placeholder="Contoh: 0354771123">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fax <span class="required-star">*</span></label>
                                    <input type="text" inputmode="numeric" pattern="[0-9]+" required name="instansi_fax" class="form-control only-number @error('instansi_fax') is-invalid @enderror" value="{{ old('instansi_fax', $skpd->instansi_fax ?? '') }}" placeholder="Contoh: 0354771123">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kode Pos <span class="required-star">*</span></label>
                                    <input type="text" inputmode="numeric" pattern="[0-9]+" required name="instansi_kode_pos" class="form-control only-number @error('instansi_kode_pos') is-invalid @enderror" value="{{ old('instansi_kode_pos', $skpd->instansi_kode_pos ?? '') }}" placeholder="Contoh: 64111">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Email Layanan <span class="required-star">*</span></label>
                                    <input type="email" required name="instansi_email" class="form-control @error('instansi_email') is-invalid @enderror" value="{{ old('instansi_email', $skpd->instansi_email ?? '') }}" placeholder="contoh@kedirikota.go.id">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button class="btn btn-success btn-save-profile w-100 py-3" type="submit"><i class="ri-save-3-line me-1"></i>Simpan Profil</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.only-number').forEach(function (el) {
            el.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '');
            });
        });
        document.querySelectorAll('.text-uppercase').forEach(function (el) {
            el.addEventListener('input', function () {
                const start = this.selectionStart;
                const end = this.selectionEnd;
                this.value = this.value.toUpperCase();
                this.setSelectionRange(start, end);
            });
        });
    });
</script>
@endpush
@endsection
