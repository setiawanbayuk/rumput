@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('skboro.store') }}">
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
                                                    <option value="KEPALA KELUARGA">KEPALA KELUARGA</option>
                                                    <option value="SUAMI">SUAMI</option>
                                                    <option value="ISTERI">ISTERI</option>
                                                    <option value="ANAK">ANAK</option>
                                                    <option value="MENANTU">MENANTU</option>
                                                    <option value="CUCU">CUCU</option>
                                                    <option value="ORANG TUA">ORANG TUA</option>
                                                    <option value="MERTUA">MERTUA</option>
                                                    <option value="FAMILI LAIN">FAMILI LAIN</option>
                                                    <option value="PEMBANTU">PEMBANTU</option>
                                                    <option value="LAINNYA">LAINNYA</option>
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
                                                        <th>Hubungan</th>
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
                                    <x-boro>
                                        <x-slot:alamat_boro></x-slot:alamat_boro>
                                        <x-slot:tgl_awal></x-slot:tgl_awal>
                                        <x-slot:tgl_akhir></x-slot:tgl_akhir>
                                    </x-boro>
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

            $("#pengikut_gender").select2({
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

            $("#pengikut_status_kwn").select2({
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




            $("#pengikut_nik").keyup(function() {
                if ($(this).val().length == 16) {
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#pengikut").val(response.name);
                            $("#pengikut_gender").select2("trigger", "select", {
                                data: {
                                    id: response.gender,
                                    text: response.gender_nm,
                                },
                            });
                            $("#pengikut_status_kwn").select2("trigger", "select", {
                                data: {
                                    id: response.status_kwn,
                                    text: response.status_kwn_nm,
                                },
                            });

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                    });

                }
            });

            $("#tambah_pengikut").click(function() {
                var nik_p = $("#pengikut_nik").val();
                var nm_p = $("#pengikut").val();
                var jk = $("#pengikut_gender").val();
                var umr = $("#pengikut_umur").val();
                var stat = $("#pengikut_status_kwn").val();
                var hub = $("#pengikut_hubungan").val();
                if (nik_p != "" || nm_p != "") {
                    var add =
                        "<tr><td><input type=\"text\" name=\"add_nik[]\" value='" +
                        nik_p + "' readonly></td><td><input type=\"text\" name=\"add_nama[]\" value='" + nm_p +
                        "' readonly></td><td><input type=\"text\" name=\"add_jk[]\" value='" + jk +
                        "' readonly></td><td><input type=\"text\" name=\"add_umr[]\" value='" + umr +
                        "' readonly></td><td><input type=\"text\" name=\"add_stat[]\" value='" + stat +
                        "' readonly></td><td><input type=\"text\" name=\"add_hub[]\" value='" + hub +
                        "' readonly><td><button type=\"button\" class=\"btn btn-danger btn-sm\" onClick=\"return hapus_temp(this)\"><i class=\"ri-delete-bin-6-line\"></i> </td></button></tr>";
                    $("#tabelbody").append(add);

                    $("#pengikut_nik").val('');
                    $("#pengikut").val('');
                    // $("#pengikut_gender").val('');
                    $("#pengikut_umur").val('');
                    // $("#pengikut_status_kwn").val('');
                    // $("#pengikut_hubungan").val('');
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
