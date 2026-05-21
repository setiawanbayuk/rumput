@extends('layouts.main')

@section('title', 'Profil Pengguna')

@section('content')
@push('styles')
<style>
    .user-profile-page{background:linear-gradient(135deg,#f8fafc 0%,#eef4ff 45%,#fff8e7 100%);border-radius:28px;padding:24px;min-height:calc(100vh - 130px)}
    .user-hero{position:relative;overflow:hidden;border-radius:28px;background:linear-gradient(135deg,#1f3b57 0%,#7896B2 52%,#B6A16B 100%);box-shadow:0 20px 45px rgba(31,59,87,.18);color:#fff;padding:28px}
    .user-hero:before{content:"";position:absolute;right:-90px;top:-90px;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.16)}
    .user-hero:after{content:"";position:absolute;left:40%;bottom:-120px;width:320px;height:320px;border-radius:50%;background:rgba(255,255,255,.08)}
    .hero-content{position:relative;z-index:2}.avatar-modern{width:110px;height:110px;border-radius:30px;background:rgba(255,255,255,.18);border:3px solid rgba(255,255,255,.45);display:flex;align-items:center;justify-content:center;font-size:52px;box-shadow:0 18px 35px rgba(0,0,0,.18);overflow:hidden}.avatar-modern img{width:100%;height:100%;object-fit:cover}.badge-soft{display:inline-flex;align-items:center;gap:6px;padding:8px 13px;border-radius:999px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);font-weight:600;font-size:12px}.profile-card-modern{border:0;border-radius:24px;box-shadow:0 14px 35px rgba(15,23,42,.08);overflow:hidden}.info-tile{height:100%;border:1px solid #eef2f7;border-radius:20px;background:#fff;padding:18px;transition:.2s}.info-tile:hover{transform:translateY(-2px);box-shadow:0 12px 24px rgba(15,23,42,.07)}.tile-icon{width:44px;height:44px;border-radius:15px;background:linear-gradient(135deg,#eef4ff,#fff7dd);display:flex;align-items:center;justify-content:center;color:#1f3b57;font-size:22px}.tile-label{font-size:12px;text-transform:uppercase;letter-spacing:.07em;color:#8391a2;font-weight:700}.tile-value{font-size:16px;font-weight:800;color:#1f2937;word-break:break-word}.btn-modern-primary{background:linear-gradient(135deg,#1f3b57,#7896B2);border:0;color:#fff;border-radius:14px;box-shadow:0 10px 20px rgba(31,59,87,.18)}.btn-modern-primary:hover{color:#fff;filter:brightness(.96)}.btn-modern-gold{background:linear-gradient(135deg,#B6A16B,#D8C58A);border:0;color:#fff;border-radius:14px;box-shadow:0 10px 20px rgba(182,161,107,.22)}.btn-modern-gold:hover{color:#fff;filter:brightness(.96)}
</style>
@endpush

<div class="container-fluid user-profile-page">
    @if (session('status'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-3">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-3">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-3">
            <div class="fw-bold mb-1">Data belum bisa disimpan:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $fotoPath = $user->foto ?? $user->avatar ?? null;
        $avatar = $fotoPath ? asset('storage/' . $fotoPath) : null;
        $roleName = optional($user->user_role)->name ?? ('Role ' . ($user->role_id ?? '-'));
        $canEditPhotoOnly = in_array((int) ($user->role_id ?? 0), [3, 4, 5, 6], true);
        $isAdminKelurahan = (int) ($user->role_id ?? 0) === 1 && $skpd && strlen((string) ($skpd->id_region ?? '')) === 13;
        $namaInstansi = $skpd->nama ?? 'Instansi belum tersedia';
        $namaKecamatan = optional($skpd->kecamatan ?? null)->nama ?? '-';
        $namaKelurahan = optional($skpd->kelurahan ?? null)->nama ?? ($skpd->nama ?? '-');
    @endphp

    <div class="user-hero mb-4">
        <div class="hero-content d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-4">
                <div class="avatar-modern">
                    @if($avatar)
                        <img src="{{ $avatar }}" alt="Foto Profil">
                    @else
                        <i class="ri-user-3-line"></i>
                    @endif
                </div>
                <div>
                    <div class="badge-soft mb-3"><i class="ri-shield-user-line"></i> PROFIL PENGGUNA E-SUKET</div>
                    <h2 class="fw-bold mb-1">{{ $user->name ?? 'Pengguna' }}</h2>
                    <div class="opacity-75">{{ $roleName }} • {{ $namaInstansi }}</div>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if($isAdminKelurahan)
                    <button type="button" class="btn btn-light px-4 py-2 rounded-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEditAdminProfile">
                        <i class="ri-edit-2-line me-1"></i>Edit Profil
                    </button>
                @endif
                @if($canEditPhotoOnly)
                    <button type="button" class="btn btn-light px-4 py-2 rounded-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEditPhotoProfile">
                        <i class="ri-image-edit-line me-1"></i>Edit Foto
                    </button>
                @endif
                <button type="button" class="btn btn-modern-gold px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalPassword">
                    <i class="ri-lock-password-line me-1"></i>Ubah Password
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-light px-4 py-2 rounded-4"><i class="ri-arrow-left-line me-1"></i>Kembali</a>
            </div>
        </div>
    </div>

    <div class="card profile-card-modern">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-1">Informasi Akun</h4>
                    <div class="text-muted"></div>
                </div>
                <span class="badge rounded-pill text-bg-light px-3 py-2"><i class="ri-time-line me-1"></i>{{ now()->format('d M Y H:i') }}</span>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-xl-4"><div class="info-tile"><div class="d-flex gap-3"><div class="tile-icon"><i class="ri-user-line"></i></div><div><div class="tile-label">Nama Lengkap</div><div class="tile-value">{{ $user->name ?? '-' }}</div></div></div></div></div>
                <div class="col-md-6 col-xl-4"><div class="info-tile"><div class="d-flex gap-3"><div class="tile-icon"><i class="ri-mail-line"></i></div><div><div class="tile-label">Email</div><div class="tile-value">{{ $user->email ?? '-' }}</div></div></div></div></div>
                <div class="col-md-6 col-xl-4"><div class="info-tile"><div class="d-flex gap-3"><div class="tile-icon"><i class="ri-smartphone-line"></i></div><div><div class="tile-label">No. HP</div><div class="tile-value">{{ $user->phone ?? '-' }}</div></div></div></div></div>
                <div class="col-md-6 col-xl-4"><div class="info-tile"><div class="d-flex gap-3"><div class="tile-icon"><i class="ri-id-card-line"></i></div><div><div class="tile-label">NIK</div><div class="tile-value">{{ $user->nik ?? '-' }}</div></div></div></div></div>
                <div class="col-md-6 col-xl-4"><div class="info-tile"><div class="d-flex gap-3"><div class="tile-icon"><i class="ri-building-4-line"></i></div><div><div class="tile-label">Instansi</div><div class="tile-value">{{ $namaInstansi }}</div></div></div></div></div>
                <div class="col-md-6 col-xl-4"><div class="info-tile"><div class="d-flex gap-3"><div class="tile-icon"><i class="ri-user-star-line"></i></div><div><div class="tile-label">Role</div><div class="tile-value">{{ $roleName }}</div></div></div></div></div>
            </div>
        </div>
    </div>


    @if($isAdminKelurahan)
        <div class="modal fade" id="modalEditAdminProfile" tabindex="-1" aria-labelledby="modalEditAdminProfileLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow" style="border-radius:22px; overflow:hidden;">
                    <div class="modal-header text-white" style="background:linear-gradient(135deg,#1f3b57,#7896B2);">
                        <div>
                            <h5 class="modal-title fw-bold" id="modalEditAdminProfileLabel">Edit Profil Admin Kelurahan</h5>
                            <div class="small opacity-75">Data akun bisa diubah, wilayah tetap dikunci otomatis.</div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="alert alert-info border-0 rounded-4 small mb-4">
                                <i class="ri-lock-2-line me-1"></i>
                                Role dan wilayah tidak bisa diubah dari profil. Sistem tetap memakai wilayah login: <strong>{{ $namaKelurahan }}</strong>.
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control rounded-4" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                                    <input type="text" name="nik" class="form-control rounded-4" value="{{ old('nik', $user->nik) }}" maxlength="16" inputmode="numeric" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control rounded-4" value="{{ old('email', $user->email) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">No. HP <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control rounded-4" value="{{ old('phone', $user->phone) }}" inputmode="numeric" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Foto Profil</label>
                                    <input type="file" name="foto" class="form-control rounded-4" accept="image/png,image/jpeg,image/jpg,image/webp">
                                    <div class="form-text">Kosongkan jika tidak ingin mengganti foto.</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Role Akun</label>
                                    <input type="text" class="form-control rounded-4 bg-light" value="{{ $roleName }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Instansi</label>
                                    <input type="text" class="form-control rounded-4 bg-light" value="{{ $namaInstansi }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kecamatan</label>
                                    <input type="text" class="form-control rounded-4 bg-light" value="{{ $namaKecamatan }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kelurahan</label>
                                    <input type="text" class="form-control rounded-4 bg-light" value="{{ $namaKelurahan }}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-light rounded-4 px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-modern-primary px-4 py-2">
                                <i class="ri-save-3-line me-1"></i>Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($canEditPhotoOnly)
        <div class="modal fade" id="modalEditPhotoProfile" tabindex="-1" aria-labelledby="modalEditPhotoProfileLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content border-0 shadow" style="border-radius:22px; overflow:hidden;">
                    <div class="modal-header text-white" style="background:linear-gradient(135deg,#1f3b57,#7896B2);">
                        <div>
                            <h5 class="modal-title fw-bold" id="modalEditPhotoProfileLabel">Edit Foto Profil</h5>
                            <div class="small opacity-75">Khusus mengganti foto akun. Data lain tetap terkunci.</div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="avatar-modern" style="width:78px;height:78px;border-radius:24px;font-size:34px;background:#eef4ff;color:#1f3b57;border-color:#e5edf7;box-shadow:none;">
                                    @if($avatar)
                                        <img src="{{ $avatar }}" alt="Foto Profil">
                                    @else
                                        <i class="ri-user-3-line"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $user->name ?? 'Pengguna' }}</div>
                                    <div class="text-muted small">{{ $roleName }} • {{ $namaInstansi }}</div>
                                </div>
                            </div>

                           

                            <label class="form-label fw-semibold">Foto Profil Baru <span class="text-danger">*</span></label>
                            <input type="file" name="foto" class="form-control rounded-4" accept="image/png,image/jpeg,image/jpg,image/webp" required>
                            <div class="form-text">Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</div>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-light rounded-4 px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-modern-primary px-4 py-2">
                                <i class="ri-save-3-line me-1"></i>Simpan Foto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @include('profile.edit-password')
</div>
@endsection
