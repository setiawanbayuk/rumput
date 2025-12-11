{{-- resources/views/tambahWarga/addwarga.blade.php (SKBORO) --}}
@extends('layouts.create')

@section('title', $title ?? 'Tambah Surat Keterangan Boro')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/form.png') }}') no-repeat center center; background-size: cover; margin-top: -75px;">
        <div class="container" style="margin-top: 125px; margin-bottom: 50px;">
            <div class="row justify-content-center">
                <div class="col-md-12">

                    <div class="card border-0 shadow-sm rounded-3"
                        style="background-color: rgba(255, 255, 255, .28); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                        <div class="card-header bg-transparent pt-3 pb-2">
                            <h5 class="my-3 fw-bold text-white text-center" style="letter-spacing:.5px">
                                {{ $title }}
                            </h5>
                        </div>

                        <div class="card-body">
                            <form id="formSkboro" method="POST" enctype="multipart/form-data"
                                action="{{ route('skboro.save') }}">
                                @csrf
                                <input type="hidden" id="nik" name="nik" value="{{ auth()->user()->nik }}">

                                {{-- ========== ROW: Dua Card Kiri-Kanan ========== --}}
                                <div class="row g-3" style="min-height:500px;">

                                    {{-- ===== KIRI: Data Pengikut ===== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm" style="background:#fff;">
                                            <div class="card-body">
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold" style="font-size: 1rem">DATA
                                                    PENGIKUT</div>
                                                <div id="data_pengikut" name="data_pengikut">
                                                    {{-- NIK --}}
                                                    <div class="row mb-3">
                                                        <label for="pengikut_nik"
                                                            class="col-md-4 col-form-label ms-4">NIK</label>
                                                        <div class="col-md-7">
                                                            <div class="input-group">
                                                                {{-- NOTE: lebih aman text daripada number agar tidak hilang leading zero --}}
                                                                <input type="text"
                                                                    class="form-control @error('pengikut_nik') is-invalid @enderror"
                                                                    id="pengikut_nik" name="pengikut_nik"
                                                                    placeholder="Masukkan 16 digit NIK" maxlength="16"
                                                                    aria-label="NIK">
                                                                @error('pengikut_nik')
                                                                    <span class="invalid-feedback"
                                                                        role="alert"><strong>{{ $message }}</strong></span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Nama --}}
                                                    <div class="row mb-3">
                                                        <label for="pengikut"
                                                            class="col-md-4 col-form-label ms-4">Nama</label>
                                                        <div class="col-md-7">
                                                            <input id="pengikut" type="text"
                                                                class="form-control @error('pengikut') is-invalid @enderror"
                                                                name="pengikut" value="{{ old('pengikut') }}">
                                                            @error('pengikut')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Jenis Kelamin --}}
                                                    <div class="row mb-3">
                                                        <label for="pengikut_gender"
                                                            class="col-md-4 col-form-label ms-4">Jenis Kelamin</label>
                                                        <div class="col-md-7">
                                                            <select
                                                                class="form-control @error('pengikut_gender') is-invalid @enderror"
                                                                id="pengikut_gender" name="pengikut_gender"
                                                                data-placeholder="Jenis Kelamin">
                                                            </select>
                                                            @error('pengikut_gender')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Umur --}}
                                                    <div class="row mb-3">
                                                        <label for="pengikut_umur"
                                                            class="col-md-4 col-form-label ms-4">Umur</label>
                                                        <div class="col-md-7">
                                                            <input id="pengikut_umur" type="number" min="0"
                                                                class="form-control @error('pengikut_umur') is-invalid @enderror"
                                                                name="pengikut_umur" value="{{ old('pengikut_umur') }}">
                                                            @error('pengikut_umur')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Status Perkawinan --}}
                                                    <div class="row mb-3">
                                                        <label for="pengikut_status_kwn"
                                                            class="col-md-4 col-form-label ms-4">Status Perkawinan</label>
                                                        <div class="col-md-7">
                                                            <select
                                                                class="form-control @error('pengikut_status_kwn') is-invalid @enderror"
                                                                id="pengikut_status_kwn" name="pengikut_status_kwn"
                                                                data-placeholder="Status Perkawinan">
                                                            </select>
                                                            @error('pengikut_status_kwn')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Hubungan Keluarga --}}
                                                    <div class="row mb-3">
                                                        <label for="pengikut_hubungan"
                                                            class="col-md-4 col-form-label ms-4">Hubungan Keluarga</label>
                                                        <div class="col-md-7">
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
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Tombol Tambah ke Tabel --}}
                                                    <div class="row mb-3">
                                                        <div class="col-md-7 offset-md-4">
                                                            <button type="button" class="btn btn-success w-100"
                                                                id="tambah_pengikut">
                                                                <i class="ri-user-add-fill me-1"></i> Tambah
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div> {{-- end: data_pengikut --}}
                                            </div> {{-- end: card-body kiri --}}
                                        </div> {{-- end: card kiri --}}
                                    </div> {{-- end: col kiri --}}

                                    {{-- ===== KANAN: Boro / Peruntukan / Pengantar ===== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm" style="background:#fff;">
                                            <div class="card-body d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="card-header bg-transparent mb-3 text-center fw-bold" style="font-size: 1rem">
                                                        BEPERGIAN / BORO KE</div>
                                                    <div class="ms-4">
                                                        <x-boro>
                                                            <x-slot:alamat_boro></x-slot:alamat_boro>
                                                            <x-slot:tgl_awal></x-slot:tgl_awal>
                                                            <x-slot:tgl_akhir></x-slot:tgl_akhir>
                                                        </x-boro>

                                                        <x-peruntukan>
                                                            <x-slot:peruntukan></x-slot:peruntukan>
                                                        </x-peruntukan>

                                                        <x-pengantar></x-pengantar>
                                                    </div>
                                                </div>
                                            </div> {{-- end: card-body kanan --}}
                                        </div> {{-- end: card kanan --}}
                                    </div> {{-- end: col kanan --}}
                                </div> {{-- end: ROW Dua Card --}}

                                {{-- ========== ROW: Tabel di Bawah Dua Card ========== --}}
                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <div class="card h-100 border-1 shadow-sm" style="background:#fff;">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped table-condensed mb-0">
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
                                                        <tbody id="tabelbody"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> {{-- end: ROW Tabel --}}

                                {{-- Tombol Ajukan --}}
                                <div class="d-flex justify-content-center" style="margin-top: 75px;">
                                    <button type="button" class="btn-ajukan" data-bs-toggle="modal"
                                        data-bs-target="#modalSkboro">
                                        Ajukan
                                    </button>
                                </div>
                            </form>
                        </div> {{-- end: card-body outer --}}
                    </div> {{-- end: card outer --}}
                    {{-- Modal Konfirmasi --}}
                    <x-confirm-ajukan modalId="modalSkboro" formId="formSkboro"
                        title="Apakah Anda Yakin Ingin Mengirim Ajuan Ini ?"
                        message="Dengan mengirim ajuan ini, Anda menyatakan bahwa seluruh data pribadi yang Anda masukkan, termasuk NIK, Nomor KK, dan dokumen pendukung lainnya, adalah benar, lengkap, dan sesuai dengan kondisi sebenarnya. Anda juga memahami bahwa kesalahan pengisian data atau ketidaksesuaian dokumen dapat mempengaruhi proses verifikasi dan dapat menyebabkan ajuan ditolak."
                        agreeLabel="Saya menyatakan bahwa seluruh data pribadi dan dokumen yang saya kirimkan adalah benar dan sesuai."
                        cancelText="Periksa Kembali" confirmText="Kirim Ajuan" />
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
                });
            });

            $("#pengikut_gender").select2({
                theme: "bootstrap-5",
                width: $(this).data("width") ?
                    $(this).data("width") : $(this).hasClass("w-100") ?
                    "100%" : "style",
                placeholder: $(this).data("placeholder"),
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

            let isProcessing = false;
            $("#pengikut_nik").keyup(function() {
                if (isProcessing) return;
                if ($(this).val().length == 16) {
                    isProcessing = true;
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
                        complete: function () {
                            isProcessing = false; // izinkan input berikutnya
                        }
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
