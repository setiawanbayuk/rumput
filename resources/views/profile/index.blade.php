@extends('layouts.warga')

@section('title', 'Profile')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
    @endpush

    <div class="container mt-4">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <div class="card profile-card" style="background-color: rgba(174,160,122,.25)">
            <div class="card-body">
                <!-- Judul Profil -->
                <h2 class="text-center profile-title" style="color: rgb(174,160,122)">PROFIL PENGGUNA</h2>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: rgb(174,160,122)">Data Pribadi</h5>
                                <form method="POST" action="{{ route('profile.update') }}">
                                    @csrf
                                    <div id="pribadi" name="pribadi">
                                        <div class="row mb-3">
                                            <label for="nik"
                                                class="col-md-3 col-form-label text-md-end">{{ __('NIK') }}</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control @error('nik') is-invalid @enderror"
                                                        id="nik" name="nik" placeholder="Masukkan 16 digit NIK"
                                                        value="{{ Auth::user()->nik, old('nik') }}" aria-label="NIK"
                                                        autofocus>

                                                    @error('nik')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="kk"
                                                class="col-md-3 col-form-label text-md-end">{{ __('No. KK') }}</label>

                                            <div class="col-md-8">
                                                <input id="kk" type="number"
                                                    class="form-control @error('kk') is-invalid @enderror" name="kk"
                                                    value="{{ isset($penduduk['kk']) ? $penduduk['kk'] : '', old('kk') }}"
                                                    autocomplete="kk" autofocus>

                                                @error('kk')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="name"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama') }}</label>

                                            <div class="col-md-8">
                                                <input id="name" type="text"
                                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                                    value="{{ isset($penduduk['name']) ? $penduduk['name'] : '', old('name') }}"
                                                    autocomplete="name" autofocus>

                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="gender"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Jenis Kelamin') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('gender') is-invalid @enderror"
                                                    id="gender" name="gender" data-placeholder="Jenis Kelamin">
                                                </select>
                                                @error('gender')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="status_kwn"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Status Perkawinan') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('status_kwn') is-invalid @enderror"
                                                    id="status_kwn" name="status_kwn" data-placeholder="Status Perkawinan">
                                                </select>
                                                @error('status_kwn')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="kewarganegaraan"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Kewarganegaraan') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('kewarganegaraan') is-invalid @enderror"
                                                    id="kewarganegaraan" name="kewarganegaraan"
                                                    data-placeholder="Kewarganegaraan">
                                                </select>
                                                @error('kewarganegaraan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="ttl"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Tempat/Tgl. Lahir') }}</label>

                                            <div class="col-md-4">
                                                <input id="tempat_lhr" type="text"
                                                    class="form-control @error('tempat_lhr') is-invalid @enderror"
                                                    name="tempat_lhr"
                                                    value="{{ isset($penduduk['tempat_lhr']) ? $penduduk['tempat_lhr'] : '', old('tempat_lhr') }}"
                                                    autocomplete="tempat_lhr" autofocus>

                                                @error('tempat_lhr')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <input id="tgl_lhr" type="date"
                                                    class="form-control @error('tgl_lhr') is-invalid @enderror"
                                                    name="tgl_lhr"
                                                    value="{{ isset($penduduk['tgl_lhr']) ? $penduduk['tgl_lhr'] : '', old('tgl_lhr') }}"
                                                    autocomplete="tgl_lhr" autofocus>

                                                @error('tgl_lhr')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="agama"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Agama') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('agama') is-invalid @enderror"
                                                    id="agama" name="agama" data-placeholder="Agama">
                                                </select>
                                                @error('agama')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="pendidikan"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Pendidikan Terakhir') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('pendidikan') is-invalid @enderror"
                                                    id="pendidikan" name="pendidikan"
                                                    data-placeholder="Pendidikan Terakhir">
                                                </select>
                                                @error('pendidikan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="pekerjaan"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Pekerjaan') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('pekerjaan') is-invalid @enderror"
                                                    id="pekerjaan" name="pekerjaan" data-placeholder="Pekerjaan">
                                                </select>
                                                @error('pekerjaan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="provinsi"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Provinsi') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('provinsi') is-invalid @enderror"
                                                    id="provinsi" name="provinsi" data-placeholder="Provinsi">
                                                </select>
                                                @error('provinsi')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="kabko"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Kabupaten/Kota') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('kabko') is-invalid @enderror"
                                                    id="kabko" name="kabko" data-placeholder="Kabupaten/Kota">
                                                </select>
                                                @error('kabko')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="kecamatan"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Kecamatan') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('kecamatan') is-invalid @enderror"
                                                    id="kecamatan" name="kecamatan" data-placeholder="Kecamatan">
                                                </select>
                                                @error('kecamatan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="kelurahan"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Kelurahan') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('kelurahan') is-invalid @enderror"
                                                    id="kelurahan" name="kelurahan" data-placeholder="Kelurahan">
                                                </select>
                                                @error('kelurahan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="alamat"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Alamat') }}</label>

                                            <div class="col-md-8">
                                                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat"
                                                    autocomplete="alamat" autofocus>{{ isset($penduduk['alamat']) ? $penduduk['alamat'] : '', old('alamat') }}</textarea>
                                                @error('alamat')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-start">
                                        <button type="submit" class="btn text-white mb-3 py-2 w-50"
                                            style="background: linear-gradient(to right, #c19a6b, #b08d57); font-size: 14px; border-radius: 6px;">
                                            Update Data Pribadi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Kolom Formulir Profil -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title" style="color: rgb(174,160,122)">Akun</h5>
                                <form method="POST" action="{{ route('profile.akun', Auth::user()->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <div class="input-group has-validation">
                                            <div class="form-floating @error('password') is-invalid @enderror">
                                                <input type="password" name="password"
                                                    class="form-control border-end-0 @error('password') is-invalid @enderror"
                                                    placeholder="">
                                                <label>New Password <span class="text-danger">*</span></label>

                                            </div>
                                            <span class="input-group-text border-start-0 cursor-pointer bg-transparent"
                                                onclick="toggleSecureInput('password', this)">
                                                <i class="ri-eye-fill text-secondary"></i>
                                            </span>
                                            @error('password')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <small class="password-rules">
                                            • Panjang minimal 8 karakter <br>
                                            • Harus mengandung huruf besar & kecil (A-a) <br>
                                            • Harus menyertakan angka (1,2,3, dst.) <br>
                                            • Harus menyertakan simbol (!@#$%^&*, dst.)
                                        </small>
                                    </div>

                                    <div class="mb-4">
                                        <div class="input-group has-validation">
                                            <div class="form-floating">
                                                <input type="password" name="password_confirmation"
                                                    class="form-control border-end-0" placeholder="">
                                                <label>Confirm new Password <span class="text-danger">*</span></label>
                                            </div>
                                            <span class="input-group-text border-start-0 cursor-pointer bg-transparent"
                                                onclick="toggleSecureInput('password_confirmation', this)">
                                                <i class="ri-eye-fill text-secondary"></i>
                                            </span>
                                        </div>

                                    </div>

                                    <div class="text-start">
                                        <button type="submit" class="btn text-white mb-3 py-2 w-50"
                                            style="background: linear-gradient(to right, #c19a6b, #b08d57); font-size: 14px; border-radius: 6px;">
                                            Update Password
                                        </button>
                                        {{-- <style>
                                            .btn-icon {
                                                background: none;
                                                border: none;
                                                cursor: pointer;
                                                padding: 5px;
                                                transition: transform 0.2s ease-in-out;
                                            }

                                            .btn-icon:hover {
                                                transform: scale(1.1);
                                            }

                                            .icon-size {
                                                width: 150px;
                                                /* Sesuaikan ukuran ikon */
                                                height: auto;
                                            }
                                        </style> --}}

                                        <!-- Form Logout dengan Ikon dan Teks -->
                                        {{-- <form method="POST" action="{{ route('logout') }}"
                                            class="d-flex justify-content-end mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-light d-flex align-items-center gap-2">
                                                <img src="{{ asset('images/logout.png') }}" alt="Logout"
                                                    style="width: 40px; height: 40px;">
                                                <span class="text-danger fw-bold">Log out</span>
                                            </button>
                                        </form> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                let provinsi_id = "";
                let kabko_id = "";
                let kecamatan_id = "";
                $("#gender").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("gender.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });
                $("#pekerjaan").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
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
                        processResults: function(response) {
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
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
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
                        processResults: function(response) {
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
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("agama.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#kewarganegaraan").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#status_kwn").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("status_kwn.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });
                $("#provinsi").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("provinsi.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });
                $("#provinsi").change(function() {
                    provinsi_id = $(this).val();
                    $("#kabko").select2({
                        theme: "bootstrap-5",
                        width: $(this).data("width") ?
                            $(this).data("width") : $(this).hasClass("w-100") ?
                            "100%" : "style",
                        placeholder: $(this).data("placeholder"),
                        minimumInputLenght: 2,
                        ajax: {
                            url: window.location.origin + "/api/kabko?kode_provinsi=" + provinsi_id,
                            dataType: "json",
                            processResults: function(response) {
                                return {
                                    results: response,
                                };
                            },
                        },
                    });
                });
                $("#kabko").change(function() {
                    kabko_id = $(this).val();
                    $("#kecamatan").select2({
                        theme: "bootstrap-5",
                        width: $(this).data("width") ?
                            $(this).data("width") : $(this).hasClass("w-100") ?
                            "100%" : "style",
                        placeholder: $(this).data("placeholder"),
                        minimumInputLenght: 2,
                        ajax: {
                            url: window.location.origin + "/api/kecamatan?kode_kabkota=" +
                                kabko_id, //route('regional.kecamatan'),
                            dataType: "json",
                            processResults: function(response) {
                                return {
                                    results: response,
                                };
                            },
                        },
                    });
                });

                $("#kecamatan").change(function() {
                    kecamatan_id = $(this).val();
                    $("#kelurahan").select2({
                        theme: "bootstrap-5",
                        width: $(this).data("width") ?
                            $(this).data("width") : $(this).hasClass("w-100") ?
                            "100%" : "style",
                        placeholder: $(this).data("placeholder"),
                        minimumInputLenght: 2,
                        ajax: {
                            url: window.location.origin + "/api/kelurahan?kode_kecamatan=" +
                                kecamatan_id, //route('regional.kelurahan'),
                            dataType: "json",
                            processResults: function(response) {
                                return {
                                    results: response,
                                };
                            },
                        },
                    });
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
    @endpush
@endsection
