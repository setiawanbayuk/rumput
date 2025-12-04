@extends('layouts.create')

@section('title', 'Profile')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
        <style>
            /* kotak foto dengan pola checker transparan */
            .avatar-frame {
                width: clamp(180px, 28vw, 280px);
                aspect-ratio: 1 / 1;
                background:
                    conic-gradient(#0000 90deg, rgba(0, 0, 0, .06) 0) 0 0/20px 20px,
                    conic-gradient(#0000 90deg, rgba(0, 0, 0, .06) 0) 10px 10px/20px 20px,
                    #f8f9fa;
                border: 1px solid #AEA07A;
                /* seirama gold-beige */
            }

            .btn-ganti {
                background: white;
                color: black;
                border-radius: 8px;
                box-shadow: 0 0px 8px rgba(0, 0, 0, .2);
            }

            .btn-ganti:hover {
                background: #AEA07A;
                color: white;
            }

            .select2-container--bootstrap-5 .select2-selection--single {
                border: 1px solid #AEA07A;
                border-radius: 8px;
            }
        </style>
    @endpush

    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/profile.png') }}') no-repeat center center; background-size: cover; margin-top: -75px;">
        <div class="container" style="margin-top: 125px; margin-bottom: 50px;">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <div class="card profile-card border-0 shadow-sm px-2"
                style="background-color: rgba(255, 255, 255, .50); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                <div class="card-body">
                    <!-- Judul Profil -->
                    <h4 class="text-center profile-title fw-bold py-3" style="letter-spacing: 1px;">
                        PROFIL PENGGUNA</h4>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card shadow-sm p-4 mb-4 rounded-3">
                                <div class="row g-4 align-items-start py-4">

                                    {{-- KIRI: FOTO + GANTI FOTO --}}
                                    <div class="col-lg-4">
                                        <div class="text-center">
                                            <div
                                                class="avatar-frame mx-auto rounded-3 d-flex align-items-center justify-content-center overflow-hidden">
                                                <img id="avatarPreview"
                                                    src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('assets/default-avatar.png') }}"
                                                    alt="Foto profil" style="width:100%; height:100%; object-fit:cover;">
                                            </div>

                                            {{-- input file DIPINDAHKAN ke dalam form (lihat di bawah) --}}
                                            {{-- Tombol trigger tetap label atau button --}}
                                            <label for="avatar" class="btn-ganti mt-3 px-4 py-2">Ganti Foto</label>
                                        </div>
                                    </div>

                                    {{-- KANAN: FORM DATA PRIBADI --}}
                                    <div class="col-lg-7">
                                        <form id="profileForm" method="POST" action="{{ route('profile.update') }}"
                                            enctype="multipart/form-data">
                                            @csrf

                                            {{-- TARUH DI SINI (bisa di bagian atas form) --}}
                                            <input type="file" id="avatar" name="avatar" accept="image/*"
                                                class="d-none">
                                            @error('avatar')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror

                                            {{-- NIK --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="nik"
                                                    class="col-md-4 col-form-label text-start">{{ __('NIK') }}</label>
                                                <div class="col-md-8">
                                                    <input type="number"
                                                        class="form-control  @error('nik') is-invalid @enderror "
                                                        style="border: 1px solid #AEA07A" id="nik" name="nik"
                                                        placeholder="Masukkan 16 digit NIK"
                                                        value="{{ old('nik', Auth::user()->nik) }}" aria-label="NIK"
                                                        autofocus readonly>
                                                    @error('nik')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- KK --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="kk"
                                                    class="col-md-4 col-form-label text-start">{{ __('No. KK') }}</label>
                                                <div class="col-md-8">
                                                    <input id="kk" type="number"
                                                        class="form-control @error('kk') is-invalid @enderror"
                                                        style="border: 1px solid #AEA07A" name="kk"
                                                        value="{{ old('kk', $penduduk['kk'] ?? '') }}" autocomplete="kk">
                                                    @error('kk')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Nama --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="name"
                                                    class="col-md-4 col-form-label text-start">{{ __('Nama') }}</label>
                                                <div class="col-md-8">
                                                    <input id="name" type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        style="border: 1px solid #AEA07A" name="name"
                                                        value="{{ old('name', $penduduk['name'] ?? '') }}"
                                                        autocomplete="name">
                                                    @error('name')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Jenis Kelamin --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="gender"
                                                    class="col-md-4 col-form-label text-start">{{ __('Jenis Kelamin') }}</label>
                                                <div class="col-md-8">
                                                    <select class="form-control @error('gender') is-invalid @enderror"
                                                        id="gender" name="gender"
                                                        data-placeholder="Jenis Kelamin"></select>
                                                    @error('gender')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Status Perkawinan --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="status_kwn"
                                                    class="col-md-4 col-form-label text-start">{{ __('Status Perkawinan') }}</label>
                                                <div class="col-md-8">
                                                    <select class="form-control @error('status_kwn') is-invalid @enderror"
                                                        id="status_kwn" name="status_kwn"
                                                        data-placeholder="Status Perkawinan"></select>
                                                    @error('status_kwn')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Kewarganegaraan --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="kewarganegaraan"
                                                    class="col-md-4 col-form-label text-start">{{ __('Kewarganegaraan') }}</label>
                                                <div class="col-md-8">
                                                    <select
                                                        class="form-control @error('kewarganegaraan') is-invalid @enderror"
                                                        id="kewarganegaraan" name="kewarganegaraan"
                                                        data-placeholder="Kewarganegaraan"></select>
                                                    @error('kewarganegaraan')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Tempat/Tgl. Lahir --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="ttl"
                                                    class="col-md-4 col-form-label text-start">{{ __('Tempat/Tgl. Lahir') }}</label>
                                                <div class="col-md-4">
                                                    <input id="tempat_lhr" type="text"
                                                        class="form-control @error('tempat_lhr') is-invalid @enderror"
                                                        style="border: 1px solid #AEA07A" name="tempat_lhr"
                                                        value="{{ old('tempat_lhr', $penduduk['tempat_lhr'] ?? '') }}"
                                                        autocomplete="tempat_lhr">
                                                    @error('tempat_lhr')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mt-3 mt-md-0">
                                                    <input id="tgl_lhr" type="date"
                                                        class="form-control @error('tgl_lhr') is-invalid @enderror"
                                                        style="border: 1px solid #AEA07A" name="tgl_lhr"
                                                        value="{{ old('tgl_lhr', $penduduk['tgl_lhr'] ?? '') }}"
                                                        autocomplete="tgl_lhr">
                                                    @error('tgl_lhr')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Agama --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="agama"
                                                    class="col-md-4 col-form-label text-start">{{ __('Agama') }}</label>
                                                <div class="col-md-8">
                                                    <select class="form-control @error('agama') is-invalid @enderror"
                                                        id="agama" name="agama" data-placeholder="Agama"></select>
                                                    @error('agama')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Pendidikan --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="pendidikan"
                                                    class="col-md-4 col-form-label text-start">{{ __('Pendidikan Terakhir') }}</label>
                                                <div class="col-md-8">
                                                    <select class="form-control @error('pendidikan') is-invalid @enderror"
                                                        id="pendidikan" name="pendidikan"
                                                        data-placeholder="Pendidikan Terakhir"></select>
                                                    @error('pendidikan')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Pekerjaan --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="pekerjaan"
                                                    class="col-md-4 col-form-label text-start">{{ __('Pekerjaan') }}</label>
                                                <div class="col-md-8">
                                                    <select class="form-control @error('pekerjaan') is-invalid @enderror"
                                                        id="pekerjaan" name="pekerjaan"
                                                        data-placeholder="Pekerjaan"></select>
                                                    @error('pekerjaan')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Provinsi --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="provinsi"
                                                    class="col-md-4 col-form-label text-start">{{ __('Provinsi') }}</label>
                                                <div class="col-md-8">
                                                    <select class="form-control @error('provinsi') is-invalid @enderror"
                                                        id="provinsi" name="provinsi"
                                                        data-placeholder="Provinsi"></select>
                                                    @error('provinsi')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Kabupaten/Kota --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="kabko"
                                                    class="col-md-4 col-form-label text-start">{{ __('Kabupaten/Kota') }}</label>
                                                <div class="col-md-8">
                                                    <select class="form-control @error('kabko') is-invalid @enderror"
                                                        id="kabko" name="kabko"
                                                        data-placeholder="Kabupaten/Kota"></select>
                                                    @error('kabko')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Kecamatan --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="kecamatan"
                                                    class="col-md-4 col-form-label text-start">{{ __('Kecamatan') }}</label>
                                                <div class="col-md-8">
                                                    <select class="form-control @error('kecamatan') is-invalid @enderror"
                                                        id="kecamatan" name="kecamatan"
                                                        data-placeholder="Kecamatan"></select>
                                                    @error('kecamatan')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Kelurahan --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="kelurahan"
                                                    class="col-md-4 col-form-label text-start">{{ __('Kelurahan') }}</label>
                                                <div class="col-md-8">
                                                    <select class="form-control @error('kelurahan') is-invalid @enderror"
                                                        id="kelurahan" name="kelurahan"
                                                        data-placeholder="Kelurahan"></select>
                                                    @error('kelurahan')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- RW --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="rw"
                                                    class="col-md-4 col-form-label text-start">{{ __('RW') }}</label>
                                                <div class="col-md-8">
                                                    <select class="form-control @error('rw') is-invalid @enderror"
                                                        id="rw" name="rw"
                                                        data-placeholder="Pilih RW"></select>
                                                    @error('rw')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- RT --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="rt"
                                                    class="col-md-4 col-form-label text-start">{{ __('RT') }}</label>
                                                <div class="col-md-8">
                                                    <select class="form-control @error('rt') is-invalid @enderror"
                                                        id="rt" name="rt"
                                                        data-placeholder="Pilih RT"></select>
                                                    @error('rt')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Alamat --}}
                                            <div class="row mb-3 justify-content-end">
                                                <label for="alamat"
                                                    class="col-md-4 col-form-label text-start">{{ __('Alamat') }}</label>
                                                <div class="col-md-8 mb-4">
                                                    <textarea class="form-control @error('alamat') is-invalid @enderror" style="border: 1px solid #AEA07A"
                                                        id="alamat" name="alamat" autocomplete="alamat">{{ old('alamat', $penduduk['alamat'] ?? '') }}</textarea>
                                                    @error('alamat')
                                                        <span class="invalid-feedback"
                                                            role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- tombol submit disembunyikan agar Enter tetap bekerja walau tombol utama di luar --}}
                                            <button type="submit" class="d-none"></button>
                                        </form>

                                        {{-- AKSI BAWAH KANAN: EDIT & SIMPAN --}}
                                        <div class="d-flex justify-content-end gap-4">
                                            <button type="button" class="btn text-white py-2 px-4"
                                                style="background: #AEA07A; border-radius: 8px;" data-bs-toggle="modal"
                                                data-bs-target="#modalPassword">
                                                <i class="ri-edit-line me-1"></i>Ubah Password
                                            </button>
                                            <button type="submit" form="profileForm" class="btn text-white py-2 px-4"
                                                style="background: #7896B2; border-radius: 8px;">
                                                <i class="ri-save-3-line me-1"></i> Simpan
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
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
                function resetSelect(id) {
                    $(id).empty().trigger("change");
                }

                // PROVINSI → reset kabko, kecamatan, kelurahan, rw, rt
                $("#provinsi").on("change", function () {
                    resetSelect("#kabko");
                    resetSelect("#kecamatan");
                    resetSelect("#kelurahan");
                    resetSelect("#rw");
                    resetSelect("#rt");
                });

                // KAB/KOTA → reset kecamatan, kelurahan, rw, rt
                $("#kabko").on("change", function () {
                    resetSelect("#kecamatan");
                    resetSelect("#kelurahan");
                    resetSelect("#rw");
                    resetSelect("#rt");
                });

                // KECAMATAN → reset kelurahan, rw, rt
                $("#kecamatan").on("change", function () {
                    resetSelect("#kelurahan");
                    resetSelect("#rw");
                    resetSelect("#rt");
                });

                // KELURAHAN → reset rw, rt
                $("#kelurahan").on("change", function () {
                    resetSelect("#rw");
                    resetSelect("#rt");
                });

                // RW → reset rt
                $("#rw").on("change", function () {
                    resetSelect("#rt");
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
