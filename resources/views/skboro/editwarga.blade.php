{{-- resources/views/skboro/show.blade.php --}}
@extends('layouts.create')

@section('title', $title)

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/form.png') }}') no-repeat center center; background-size: cover; margin-top: -75px;">
        <div class="container" style="margin-top: 125px; margin-bottom: 50px;">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-3"
                        style="background-color: rgba(255,255,255,.28); backdrop-filter: blur(10px);">
                        
                        <div class="card-header bg-transparent text-center pt-3 pb-2">
                            <h5 class="mt-3 fw-bold text-white" style="letter-spacing:.5px">
                                {{ $title }}
                            </h5>
                            <h6 class="mb-3 fw-semibold text-white">
                                No. Surat : {{ $suratKeterangan->getNoSrt($suratKeterangan) }}
                            </h6>
                        </div>

                        <div class="card-body">
                            <form id="formEditSkboro" method="POST" enctype="multipart/form-data" 
                                action="{{ route('skboro.updatewarga', $suratKeterangan->id) }}" >
                                @csrf
                                <input type="hidden" id="nik" name="nik" value="{{ $suratKeterangan->nik }}">
                                {{-- ========================= 2 Kolom ========================= --}}
                                <div class="row g-3" style="min-height: 500px;">

                                    {{-- ================= KIRI: DATA PENGIKUT ================= --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">

                                                <div class="card-header bg-transparent mb-3 text-center fw-bold" style="font-size: 1rem">
                                                    DATA PENGIKUT
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>NIK</th>
                                                                <th>Nama</th>
                                                                <th>JK</th>
                                                                <th>Umur</th>
                                                                <th>Status</th>
                                                                <th>Hubungan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($pengikut as $p)
                                                                <tr>
                                                                    <td>{{ $p['nik'] }}</td>
                                                                    <td>{{ $p['nama'] }}</td>
                                                                    <td>{{ $p['gender_nm'] }}</td>
                                                                    <td>{{ $p['umur'] }}</td>
                                                                    <td>{{ $p['status_kwn'] }}</td>
                                                                    <td>{{ $p['hubungan'] }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ================= KANAN: BORO/ALAMAT/PERUNTUKAN ================= --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">

                                                <div class="card-header bg-transparent mb-3 text-center fw-bold" style="font-size: 1rem">
                                                    BEPERGIAN / BORO KE
                                                </div>

                                                <div class="ms-4">
                                                    <x-boro
                                                        :prov_boro_nm="$suratKeterangan->prov_boro_nm"
                                                        :kabko_boro_nm="$suratKeterangan->kabko_boro_nm"
                                                        :kec_boro_nm="$suratKeterangan->kec_boro_nm"
                                                        :kel_boro_nm="$suratKeterangan->kel_boro_nm"
                                                        :alamat_boro="$suratKeterangan->alamat_boro"
                                                        :tgl_awal="$suratKeterangan->tgl_awal"
                                                        :tgl_akhir="$suratKeterangan->tgl_akhir"

                                                        :prov_boro="$suratKeterangan->prov_boro"
                                                        :kabko_boro="$suratKeterangan->kabko_boro"
                                                        :kec_boro="$suratKeterangan->kec_boro"
                                                        :kel_boro="$suratKeterangan->kel_boro"
                                                        :readonly="false"
                                                    />
                                                    {{-- Peruntukan --}}
                                                    <x-peruntukan :peruntukan="$suratKeterangan->peruntukan" :readonly="false" />
                                                    {{-- Pengantar --}}
                                                    <x-pengantar :pengantar="$suratKeterangan->pengantar" :readonly="false" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- ========== BUTTON UPDATE ========== --}}
                                <div class="d-flex justify-content-center gap-3" style="margin-top:75px;">
                                    <a href="{{ route('skboro.warga') }}" class="btn btn-secondary px-4 py-2 rounded-3">
                                        Batal
                                    </a>
                                    <button type="button" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEditSkboro">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div> {{-- END CARD BODY --}}
                    </div>
                </div>
                {{-- Modal Konfirmasi --}}
                <x-confirm-ajukan modalId="modalEditSkboro" formId="formEditSkboro"
                    title="Yakin Ingin Mengubah Surat Ini?"
                    message="Pastikan perubahan sudah benar sebelum mengirim pembaruan."
                    agreeLabel="Saya memastikan bahwa data yang saya ubah sudah benar."
                    cancelText="Cek Lagi"
                    confirmText="Simpan Perubahan" />
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
