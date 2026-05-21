@extends('layouts.create')

@section('title', 'Profil Pengguna')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
        <style>
            .profile-bg{min-height:100vh;background:linear-gradient(135deg,rgba(31,59,87,.92),rgba(120,150,178,.72)),url('{{ asset('assets/profile.png') }}') no-repeat center/cover;margin-top:-75px;padding-top:125px;padding-bottom:55px}.profile-shell{border-radius:30px;background:rgba(255,255,255,.88);backdrop-filter:blur(18px);box-shadow:0 25px 65px rgba(15,23,42,.22);overflow:hidden}.profile-hero{background:linear-gradient(135deg,#1f3b57,#7896B2 55%,#B6A16B);color:#fff;padding:28px;position:relative;overflow:hidden}.profile-hero:before{content:"";position:absolute;right:-80px;top:-90px;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.13)}.profile-hero>*{position:relative;z-index:2}.avatar-box{width:190px;height:190px;border-radius:34px;background:rgba(255,255,255,.18);border:4px solid rgba(255,255,255,.42);display:flex;align-items:center;justify-content:center;overflow:hidden;box-shadow:0 18px 40px rgba(0,0,0,.2)}.avatar-box img{width:100%;height:100%;object-fit:cover}.btn-avatar{background:#fff;color:#1f3b57;border-radius:14px;font-weight:700;box-shadow:0 12px 24px rgba(0,0,0,.12);cursor:pointer}.form-section{background:#fff;border:1px solid #edf1f7;border-radius:22px;padding:22px;height:100%}.section-title{font-weight:800;color:#1f3b57;margin-bottom:18px;display:flex;align-items:center;gap:9px}.form-control,.select2-container--bootstrap-5 .select2-selection{border:1px solid #d7c899!important;border-radius:13px!important;min-height:43px}.form-label{font-weight:700;color:#334155;font-size:13px}.btn-save-profile{background:linear-gradient(135deg,#1f3b57,#7896B2);border:0;border-radius:14px;color:#fff}.btn-pass-profile{background:linear-gradient(135deg,#B6A16B,#D8C58A);border:0;border-radius:14px;color:#fff}.mini-chip{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:999px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.25);font-size:12px;font-weight:700}
        </style>
    @endpush

    <div class="profile-bg">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success border-0 shadow-sm rounded-4">{{ session('status') }}</div>
            @endif
            @php
                $avatar = Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('assets/default-avatar.png');
            @endphp
            <div class="profile-shell">
                <div class="profile-hero d-flex flex-column flex-lg-row align-items-center justify-content-between gap-4">
                    <div class="d-flex flex-column flex-md-row align-items-center gap-4 text-center text-md-start">
                        <a href="{{ $avatar }}" target="_blank" class="text-decoration-none"><div class="avatar-box"><img id="avatarPreview" src="{{ $avatar }}" alt="Foto profil"></div></a>
                        <div>
                            <div class="mini-chip mb-3"><i class="ri-user-heart-line"></i> PROFIL PENGGUNA</div>
                            <h2 class="fw-bold mb-1">{{ old('name', $penduduk['name'] ?? Auth::user()->name ?? 'Pengguna') }}</h2>
                            <div class="opacity-75">Lengkapi data pribadi agar proses surat lebih cepat dan akurat.</div>
                        </div>
                    </div>
                    <label for="avatar" class="btn btn-avatar px-4 py-2"><i class="ri-image-edit-line me-1"></i>Ganti Foto</label>
                </div>

                <div class="p-4">
                    <form id="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="avatar" name="avatar" accept="image/*" class="d-none">
                        @error('avatar')<div class="text-danger small mb-3">{{ $message }}</div>@enderror

                        <div class="row g-4">
                            <div class="col-xl-6">
                                <div class="form-section">
                                    <h5 class="section-title"><i class="ri-id-card-line"></i>Identitas Pribadi</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6"><label for="nik" class="form-label">NIK</label><input type="number" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" placeholder="Masukkan 16 digit NIK" value="{{ old('nik', Auth::user()->nik) }}" readonly>@error('nik')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="kk" class="form-label">No. KK</label><input id="kk" type="number" class="form-control @error('kk') is-invalid @enderror" name="kk" value="{{ old('kk', $penduduk['kk'] ?? '') }}">@error('kk')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-12"><label for="name" class="form-label">Nama</label><input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $penduduk['name'] ?? '') }}">@error('name')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="gender" class="form-label">Jenis Kelamin</label><select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" data-placeholder="Jenis Kelamin"></select>@error('gender')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="status_kwn" class="form-label">Status Perkawinan</label><select class="form-control @error('status_kwn') is-invalid @enderror" id="status_kwn" name="status_kwn" data-placeholder="Status Perkawinan"></select>@error('status_kwn')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="kewarganegaraan" class="form-label">Kewarganegaraan</label><select class="form-control @error('kewarganegaraan') is-invalid @enderror" id="kewarganegaraan" name="kewarganegaraan" data-placeholder="Kewarganegaraan"></select>@error('kewarganegaraan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="agama" class="form-label">Agama</label><select class="form-control @error('agama') is-invalid @enderror" id="agama" name="agama" data-placeholder="Agama"></select>@error('agama')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="tempat_lhr" class="form-label">Tempat Lahir</label><input id="tempat_lhr" type="text" class="form-control @error('tempat_lhr') is-invalid @enderror" name="tempat_lhr" value="{{ old('tempat_lhr', $penduduk['tempat_lhr'] ?? '') }}">@error('tempat_lhr')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="tgl_lhr" class="form-label">Tanggal Lahir</label><input id="tgl_lhr" type="date" class="form-control @error('tgl_lhr') is-invalid @enderror" name="tgl_lhr" value="{{ old('tgl_lhr', $penduduk['tgl_lhr'] ?? '') }}">@error('tgl_lhr')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="pendidikan" class="form-label">Pendidikan</label><select class="form-control @error('pendidikan') is-invalid @enderror" id="pendidikan" name="pendidikan" data-placeholder="Pendidikan"></select>@error('pendidikan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="pekerjaan" class="form-label">Pekerjaan</label><select class="form-control @error('pekerjaan') is-invalid @enderror" id="pekerjaan" name="pekerjaan" data-placeholder="Pekerjaan"></select>@error('pekerjaan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="form-section">
                                    <h5 class="section-title"><i class="ri-map-pin-line"></i>Alamat Domisili</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6"><label for="provinsi" class="form-label">Provinsi</label><select class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" data-placeholder="Pilih Provinsi"></select>@error('provinsi')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="kabko" class="form-label">Kabupaten/Kota</label><select class="form-control @error('kabko') is-invalid @enderror" id="kabko" name="kabko" data-placeholder="Pilih Kabupaten/Kota"></select>@error('kabko')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="kecamatan" class="form-label">Kecamatan</label><select class="form-control @error('kecamatan') is-invalid @enderror" id="kecamatan" name="kecamatan" data-placeholder="Pilih Kecamatan"></select>@error('kecamatan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="kelurahan" class="form-label">Kelurahan</label><select class="form-control @error('kelurahan') is-invalid @enderror" id="kelurahan" name="kelurahan" data-placeholder="Pilih Kelurahan"></select>@error('kelurahan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="rw" class="form-label">RW</label><select class="form-control @error('rw') is-invalid @enderror" id="rw" name="rw" data-placeholder="Pilih RW"></select>@error('rw')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-md-6"><label for="rt" class="form-label">RT</label><select class="form-control @error('rt') is-invalid @enderror" id="rt" name="rt" data-placeholder="Pilih RT"></select>@error('rt')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                        <div class="col-12"><label for="alamat" class="form-label">Alamat</label><textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="4">{{ old('alamat', $penduduk['alamat'] ?? '') }}</textarea>@error('alamat')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="d-none"></button>
                    </form>
                    <div class="d-flex justify-content-end gap-3 mt-4 flex-wrap">
                        <button type="button" class="btn btn-pass-profile px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalPassword"><i class="ri-lock-password-line me-1"></i>Ubah Password</button>
                        <button type="submit" form="profileForm" class="btn btn-save-profile px-4 py-2"><i class="ri-save-3-line me-1"></i>Simpan Profil</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                let provinsi_id = "";
                let kabko_id = "";
                let kecamatan_id = "";
                let kelurahan_id = "";
                let rw_id ="";
                $("#gender").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width")
                        ? $(this).data("width")
                        : $(this).hasClass("w-100")
                            ? "100%"
                            : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLength: 2,
                    ajax: {
                        url: route("gender.index"),
                        dataType: "json",
                        processResults: function (response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });
                $("#pekerjaan").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width")
                        ? $(this).data("width")
                        : $(this).hasClass("w-100")
                            ? "100%"
                            : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLength: 2,
                    ajax: {
                        url: route("pekerjaan.index"),
                        dataType: "json",
                        data: (params) => {
                            let query = {
                                q: params.term,
                                page: params.page || 1,
                            };
                            return query;
                        },
                        processResults: function (response) {
                            return {
                                results: response,
                                pagination: {
                                    more: response.current_page < response.last_page,
                                },
                            };
                        },
                    },
                });
                $("#pendidikan").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width")
                        ? $(this).data("width")
                        : $(this).hasClass("w-100")
                            ? "100%"
                            : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLength: 2,
                    ajax: {
                        url: route("pendidikan.index"),
                        dataType: "json",
                        data: (params) => {
                            let query = {
                                q: params.term,
                                page: params.page || 1,
                            };
                            return query;
                        },
                        processResults: function (response) {
                            console.log("bawah");
                            return {
                                results: response,
                                pagination: {
                                    more: response.current_page < response.last_page,
                                },
                            };
                        },
                    },
                });
                $("#agama").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width")
                        ? $(this).data("width")
                        : $(this).hasClass("w-100")
                            ? "100%"
                            : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLength: 2,
                    ajax: {
                        url: route("agama.index"),
                        dataType: "json",
                        processResults: function (response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#kewarganegaraan").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width")
                        ? $(this).data("width")
                        : $(this).hasClass("w-100")
                            ? "100%"
                            : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLength: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function (response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#status_kwn").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width")
                        ? $(this).data("width")
                        : $(this).hasClass("w-100")
                            ? "100%"
                            : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLength: 2,
                    ajax: {
                        url: route("status_kwn.index"),
                        dataType: "json",
                        processResults: function (response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });
                $("#provinsi").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width")
                        ? $(this).data("width")
                        : $(this).hasClass("w-100")
                            ? "100%"
                            : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLength: 2,
                    ajax: {
                        url: route("provinsi.index"),
                        dataType: "json",
                        processResults: function (response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });
                $("#provinsi").on("change", function () {
                    provinsi_id = $(this).val();
                    $("#kabko").select2({
                        theme: "bootstrap-5",
                        width: $(this).data("width")
                            ? $(this).data("width")
                            : $(this).hasClass("w-100")
                                ? "100%"
                                : "style",
                        placeholder: $(this).data("placeholder"),
                        minimumInputLength: 2,
                        ajax: {
                            url:
                                window.location.origin + "/api/kabko?kode_provinsi=" + provinsi_id,
                            dataType: "json",
                            processResults: function (response) {
                                return {
                                    results: response,
                                };
                            },
                        },
                    });
                });
                $("#kabko").on("change", function () {
                    kabko_id = $(this).val();
                    $("#kecamatan").select2({
                        theme: "bootstrap-5",
                        width: $(this).data("width")
                            ? $(this).data("width")
                            : $(this).hasClass("w-100")
                                ? "100%"
                                : "style",
                        placeholder: $(this).data("placeholder"),
                        minimumInputLength: 2,
                        ajax: {
                            url:
                                window.location.origin + "/api/kecamatan?kode_kabkota=" + kabko_id, //route('regional.kecamatan'),
                            dataType: "json",
                            processResults: function (response) {
                                return {
                                    results: response,
                                };
                            },
                        },
                    });
                });

                $("#kecamatan").on("change", function () {
                    kecamatan_id = $(this).val();
                    $("#kelurahan").select2({
                        theme: "bootstrap-5",
                        width: $(this).data("width")
                            ? $(this).data("width")
                            : $(this).hasClass("w-100")
                                ? "100%"
                                : "style",
                        placeholder: $(this).data("placeholder"),
                        minimumInputLength: 2,
                        ajax: {
                            url:
                                window.location.origin + "/api/kelurahan?kode_kecamatan=" +
                                kecamatan_id, //route('regional.kelurahan'),
                            dataType: "json",
                            processResults: function (response) {
                                return {
                                    results: response,
                                };
                            },
                        },
                    });
                });
                // KELURAHAN -> RW
                $("#kelurahan").on("change", function () {
                    kelurahan_id = $(this).val();
                    $("#rw").select2({
                        theme: "bootstrap-5",
                        width: $(this).data("width")
                            ? $(this).data("width")
                            : $(this).hasClass("w-100")
                                ? "100%"
                                : "style",
                        placeholder: $(this).data("placeholder"),
                        ajax: {
                            url:
                                window.location.origin +
                                "/api/rw?kode_kelurahan=" +
                                kelurahan_id,
                            dataType: "json",
                            processResults: function (response) {
                                return {
                                    results: response,
                                };
                            },
                        },
                    });
                });

                // RW -> RT
                $("#rw").on("change", function () {
                    kelurahan_id = $("#kelurahan").val();
                    rw_id = $(this).val();
                    $("#rt").select2({
                        theme: "bootstrap-5",
                        width: $(this).data("width")
                            ? $(this).data("width")
                            : $(this).hasClass("w-100")
                                ? "100%"
                                : "style",
                        placeholder: $(this).data("placeholder"),
                        ajax: {
                            url:
                                window.location.origin +
                                "/api/rt?kode_kelurahan=" +
                                kelurahan_id +
                                "&rw=" +
                                rw_id,
                            dataType: "json",
                            processResults: function (response) {
                                return {
                                    results: response,
                                };
                            },
                        },
                    });
                });

                // ---------- RESET CHAIN ----------
                function reset(id) {
                    $(id).empty().trigger("change");
                }

                // PROVINSI → reset kabko, kecamatan, kelurahan, rw, rt
                $("#provinsi").on("change", function () {
                    reset("#kabko");
                    reset("#kecamatan");
                    reset("#kelurahan");
                    reset("#rw");
                    reset("#rt");
                });

                // KAB/KOTA → reset kecamatan, kelurahan, rw, rt
                $("#kabko").on("change", function () {
                    reset("#kecamatan");
                    reset("#kelurahan");
                    reset("#rw");
                    reset("#rt");
                });

                // KECAMATAN → reset kelurahan, rw, rt
                $("#kecamatan").on("change", function () {
                    reset("#kelurahan");
                    reset("#rw");
                    reset("#rt");
                });

                // KELURAHAN → reset rw, rt
                $("#kelurahan").on("change", function () {
                    reset("#rw");
                    reset("#rt");
                });

                // RW → reset rt
                $("#rw").on("change", function () {
                    reset("#rt");
                });

                $("#gender").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['gender']) ? $penduduk['gender'] : '' !!}',
                        text: '{!! isset($penduduk['gender_nm']) ? $penduduk['gender_nm'] : '' !!}',
                    },
                });
                $("#status_kwn").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['status_kwn']) ? $penduduk['status_kwn'] : '' !!}',
                        text: '{!! isset($penduduk['status_kwn_nm']) ? $penduduk['status_kwn_nm'] : '' !!}',
                    },
                });
                $("#kewarganegaraan").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['kewarganegaraan']) ? $penduduk['kewarganegaraan'] : '' !!}',
                        text: '{!! isset($penduduk['kewarganegaraan_nm']) ? $penduduk['kewarganegaraan_nm'] : '' !!}',
                    },
                });
                $("#agama").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['agama']) ? $penduduk['agama'] : '' !!}',
                        text: '{!! isset($penduduk['agama_nm']) ? $penduduk['agama_nm'] : '' !!}',
                    },
                });
                $("#pendidikan").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['pendidikan']) ? $penduduk['pendidikan'] : '' !!}',
                        text: '{!! isset($penduduk['pendidikan_nm']) ? $penduduk['pendidikan_nm'] : '' !!}',
                    },
                });
                $("#pekerjaan").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['pekerjaan']) ? $penduduk['pekerjaan'] : '' !!}',
                        text: '{!! isset($penduduk['pekerjaan_nm']) ? $penduduk['pekerjaan_nm'] : '' !!}',
                    },
                });
                $("#provinsi").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['provinsi']) ? $penduduk['provinsi'] : '' !!}',
                        text: '{!! isset($penduduk['provinsi_nm']) ? $penduduk['provinsi_nm'] : '' !!}',
                    },
                });
                $("#kabko").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['kabko']) ? $penduduk['kabko'] : '' !!}',
                        text: '{!! isset($penduduk['kabko_nm']) ? $penduduk['kabko_nm'] : '' !!}',
                    },
                });
                $("#kecamatan").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['kecamatan']) ? $penduduk['kecamatan'] : '' !!}',
                        text: '{!! isset($penduduk['kecamatan_nm']) ? $penduduk['kecamatan_nm'] : '' !!}',
                    },
                });
                $("#kelurahan").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['kelurahan']) ? $penduduk['kelurahan'] : '' !!}',
                        text: '{!! isset($penduduk['kelurahan_nm']) ? $penduduk['kelurahan_nm'] : '' !!}',
                    },
                });
                $("#rw").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['rw']) ? $penduduk['rw'] : '' !!}',
                        text: '{!! isset($penduduk['rw_nm']) ? $penduduk['rw_nm'] : '' !!}',
                    },
                });
                $("#rt").select2("trigger", "select", {
                    data: {
                        id: '{!! isset($penduduk['rt']) ? $penduduk['rt'] : '' !!}',
                        text: '{!! isset($penduduk['rt_nm']) ? $penduduk['rt_nm'] : '' !!}',
                    },
                });

            });
            const toggleSecureInput = (fieldName, e) => {
                const type = $(`[name="${fieldName}"]`).attr('type');
                $(`[name="${fieldName}"]`).attr('type', type == 'password' ? 'text' : 'password');
                if (type == 'password') {
                    $(e).find('i').removeClass('ri-eye-fill');
                    $(e).find('i').addClass('ri-eye-off-fill');
                } else {
                    $(e).find('i').removeClass('ri-eye-off-fill');
                    $(e).find('i').addClass('ri-eye-fill');
                }
            }
        </script>
        <script>
            // Preview avatar saat file dipilih
            document.addEventListener('DOMContentLoaded', function() {
                const input = document.getElementById('avatar');
                const preview = document.getElementById('avatarPreview');
                if (!input || !preview) return;

                input.addEventListener('change', function() {
                    const file = this.files && this.files[0];
                    if (!file) return;

                    // Validasi cepat di sisi client (opsional)
                    if (!file.type.match(/^image\//)) {
                        alert('File harus berupa gambar.');
                        this.value = '';
                        return;
                    }
                    if (file.size > 2 * 1024 * 1024) { // 2MB
                        alert('Ukuran gambar maksimal 2MB.');
                        this.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            });
        </script>
    @endpush
    @include('profile.edit-password')
@endsection
