@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('suket.store') }}">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <x-nosrt>
                                        <x-slot:kd_jenis_surat></x-slot:kd_jenis_surat>
                                        <x-slot:no_urut_surat>{{ $no_urut_surat }}</x-slot:no_urut_surat>
                                        <x-slot:instansi_kode>{{ $currentUser->skpd->instansi_kode }}</x-slot:instansi_kode>
                                        <x-slot:tgl_surat></x-slot:tgl_surat>
                                    </x-nosrt>
                                    <x-pribadi></x-pribadi>
                                </div>
                                <div class="col-md-6 border-start">
                                    <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Pengikut</div>
                                    <div id="data_pengikut" name="data_pengikut">
                                        <div class="row mb-3">
                                            <label for="pengikut_nik"
                                                class="col-md-3 col-form-label text-md-end">{{ __('NIK') }}</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control @error('pengikut_nik') is-invalid @enderror"
                                                        id="pengikut_nik" name="pengikut_nik"
                                                        placeholder="Masukkan 16 digit NIK" aria-label="NIK"
                                                        aria-describedby="basic-addon2">
                                                    {{-- <button type="button" class="input-group-text btn btn-subtle-primary"
                                                        onclick="checkNIK()">CARI</button> --}}

                                                    @error('pengikut_nik')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="pengikut"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama') }}</label>

                                            <div class="col-md-8">
                                                <input id="pengikut" type="text"
                                                    class="form-control @error('pengikut') is-invalid @enderror"
                                                    name="pengikut" value="{{ old('pengikut') }}" autocomplete="pengikut">

                                                @error('pengikut')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="pengikut_gender"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Jenis Kelamin') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('pengikut_gender') is-invalid @enderror"
                                                    id="pengikut_gender" name="pengikut_gender"
                                                    data-placeholder="Jenis Kelamin">
                                                </select>
                                                @error('pengikut_gender')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="pengikut_umur"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Umur') }}</label>

                                            <div class="col-md-8">
                                                <input id="pengikut_umur" type="number"
                                                    class="form-control @error('pengikut_umur') is-invalid @enderror"
                                                    name="pengikut_umur" value="{{ old('pengikut_umur') }}"
                                                    autocomplete="pengikut_umur">

                                                @error('pengikut')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="pengikut_status_kwn"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Status Perkawinan') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('pengikut_status_kwn') is-invalid @enderror"
                                                    id="pengikut_status_kwn" name="pengikut_status_kwn"
                                                    data-placeholder="Status Perkawinan">
                                                </select>
                                                @error('pengikut_status_kwn')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="pengikut_hubungan"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Hubungan Keluarga') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control select2-hubungan @error('pengikut_hubungan') is-invalid @enderror"
                                                    id="pengikut_hubungan" name="pengikut_hubungan"
                                                    data-placeholder="Hubungan">
                                                    <option value=""></option>
                                                    <option value="Putranya">Putranya</option>
                                                    <option value="Putrinya">Putrinya</option>
                                                    <option value="Cucunya">Cucunya</option>
                                                    <option value="Keluarga">Keluarga</option>
                                                </select>
                                                @error('pengikut_hubungan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-8 offset-md-3">
                                            <button type="button" class="btn btn-success" name="tambah_pengikut"
                                                id ="tambah_pengikut"><i class="ri-user-add-fill"></i>
                                                <span>Tambah</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-condensed">
                                                <thead>
                                                    <tr>
                                                        <th>NIK</th>
                                                        <th>Nama</th>
                                                        <th>Jenis Kelamin</th>
                                                        <th>Umur</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="tabelbody">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="card-header bg-transparent mb-3 text-center fw-bold">Bepergian / Boro Ke
                                    </div>
                                    <x-boro><x-slot:alamat_boro></x-slot:alamat_boro></x-boro>
                                    <x-peruntukan><x-slot:peruntukan></x-slot:peruntukan></x-peruntukan>
                                    <x-pengantar></x-pengantar>
                                    <div class="row mb-0">
                                        <div class="col-md-8 offset-md-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-save-3-fill"></i>
                                                <span>Simpan</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript" src="{{ asset('assets/js/personal.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/boro.js') }}"></script>
        <script>
            $(document).ready(function() {
                // $(".select2-hubungan").select2();
                $(".select2-hubungan").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                });
            });

            $("#tambah_pengikut").click(function() {
                var nik_p = $("#pengikut_nik").val();
                var nm_p = $("#pengikut").val();
                var jk = $("#pengikut_gender").val();
                var umr = $("#pengikut_umur").val();
                var stat = $("#pengikut_status_kwn").val();
                if (nik_p != "" || nm_p != "") {
                    var add =
                        "<tr><input type=\"hidden\" name=\"id_pengikut[]\"><td><input type=\"text\" name=\"add_nik[]\" value='" +
                        nik_p + "' readonly></td><td><input type=\"text\" name=\"add_nama[]\" value='" + nm_p +
                        "' readonly></td><td><input type=\"text\" name=\"add_jk[]\" value='" + jk +
                        "' readonly></td><td width=\"5%\"><input type=\"text\" name=\"add_umr[]\" value='" + umr +
                        "' readonly></td><td><input type=\"text\" name=\"add_stat[]\" value='" + stat +
                        "' readonly><td><button type=\"button\" class=\"btn btn-danger btn-sm\" onClick=\"return hapus_temp(this)\"><i class=\"fa fa-times-circle\"></i> </td></button></tr>";
                    $("#tabelbody").append(add);

                    $("#pengikut_nik").val('');
                    $("#pengikut").val('');
                    $("#pengikut_gender").val('');
                    $("#pengikut_umur").val('');
                    $("#pengikut_status_kwn").val('');
                } else {
                    alert("NIK atau Nama Harus Diisi");
                }
            });

            function hapus_temp(e) {
                $(e).parent().parent().remove();
            }
        </script>
    @endpush
@endsection
