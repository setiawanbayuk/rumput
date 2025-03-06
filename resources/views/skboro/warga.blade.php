@extends('layouts.warga')

@section('title', 'Surat Keterangan')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
    @endpush

    <div class="container">
        <h3>{{ $title }}</h3>

        <br>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="ri-add-fill me-2"></i><span>Tambah</span>
            </button>
            <button class="btn btn-secondary" onclick="reload()">Reload</button>
        </div>

        <br>

        <div class="card card-body">
            <div class="table-responsive">
                <table id="tableSurat" class="table table-hovered" style="width: 100%" style="width: 100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Surat</th>
                            <th>Tanggal</th>
                            <th>Peruntukan</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('modals.skboro-add-modal')
    {{-- <x-esign></x-esign> --}}
    @push('scripts')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script type="text/javascript" src="{{ asset('assets/js/boro.js') }}"></script>
        <script type="text/javascript">
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
            $(function() {
                var table = $('#tableSurat').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    scrollX: true,
                    ajax: "{{ route('skboro.warga') }}",
                    columns: [{
                            data: 'no_urut_surat',
                            name: 'no_urut_surat'
                        },
                        {
                            data: 'no_surat',
                            name: 'no_surat',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tgl_surat',
                            name: 'tgl_surat',
                            width: '10%',
                        },
                        {
                            data: 'peruntukan',
                            name: 'peruntukan'
                        },
                        {
                            data: 'st',
                            // name: 'st',
                            render: function(data, type) {
                                return `<span style="color:${data.color}">${data.name}</span>`;
                            },
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    order: [
                        [2, "desc"],
                        [0, "desc"]
                    ],
                    pageLength: 10,
                });
            });

            function reload() {
                $('#tableSurat').DataTable().ajax.reload();
            }
        </script>
        <script>
            function handleCetak(e) {
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "{{ env('APP_URL', 'http://rumput.test') }}" + "/skboro/cetak/" + e,
                    success: function(response) {
                        window.open(response.file, 'preview',
                            'width=600,height=1000');
                    }
                });
            }
        </script>
    @endpush
@endsection
