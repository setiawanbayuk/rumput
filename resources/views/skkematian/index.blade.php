@extends('layouts.main')

@section('title', 'Surat Keterangan')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
        <style>
            .card input:focus,
            .card select:focus  {
                box-shadow: none !important;
                outline: none !important;
                border-color: #AEA07A;
            }
        </style>
    @endpush

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <h3 class="mb-0 fw-bold">{{ $title }}</h3>
            </div>
            <div class="col-md-12 d-flex align-items-center justify-content-between">
                @if (auth()->user()->role_id == 8)
                    <h6 class="text-muted mb-0">
                        Daftar pengajuan surat keterangan kematian warga <b>RT {{ $rt }}/RW {{ $rw }}, Kelurahan {{ $kelurahan }}.</b>
                    </h6>
                @elseif (auth()->user()->role_id == 1 ||  auth()->user()->role_id == 9)
                    <h6 class="text-muted mb-0">
                        Daftar seluruh pengajuan surat keterangan kematian warga <b>Kota Kediri.</b>
                    </h6>
                @else
                    <h6 class="text-muted mb-0">
                        Daftar pengajuan surat keterangan kematian warga <b>Kelurahan {{ $kelurahan }}.</b>
                    </h6>
                @endif
                <div class="d-flex gap-2">
                    @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 8 || auth()->user()->role_id == 9)
                        <a class="btn btn-primary btn-sm shadow-sm" style="border-radius: 6px" href="{{ route('skkematian.add') }}">
                            <i class="ri-add-fill me-2"></i>
                            <span>Tambah</span>
                        </a>
                    @endif
                    <button class="btn btn-secondary btn-sm shadow-sm" style="border-radius: 6px" onclick="reload()">
                        <i class="ri-loop-right-fill me-2"></i>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm rounded-4" style="border-color: #AEA07A">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table id="tableSurat" class="table table-hover align-middle mb-0" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Surat</th>
                                        <th>NIK</th>
                                        <th>Tanggal</th>
                                        <th>Nama Jenazah</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-esign></x-esign>
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
        <script src="{{ asset('assets/js/actions.js') }}"></script>
        <script type="text/javascript">
            $(function() {
                var table = $('#tableSurat').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    scrollX: false,
                    autoWidth: false,
                    ajax: "{{ route('skkematian.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'no_surat',
                            name: 'no_surat',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nik_pelapor',
                            name: 'nik_pelapor'
                        },
                        {
                            data: 'tgl_surat',
                            name: 'tgl_surat',
                            width: '10%',
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'st',
                            render: function(data, type) {
                                if (!data) return '-';
                                return `<span class="fw-semibold" style="color:${data.color}">${data.name}</span>`;
                            },
                            orderable: false,
                            searchable: false,
                            className: 'dt-status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    order: [
                        [3, "desc"],
                        [0, "desc"]
                    ],
                    pageLength: 10,
                    responsive: true,
                });
            });

            function reload() {
                $('#tableSurat').DataTable().ajax.reload();
            }
        </script>
        <script>
            // function handlePreview(e) {
            //     window.open("{{ env('APP_URL', 'http://rumput.test') }}" + "/skkematian/preview/" + e, 'preview',
            //         'width=600,height=1000');
            // }

            function handleCetak(e) {
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "{{ env('APP_URL', 'http://rumput.test') }}" + "/skkematian/cetak/" + e,
                    success: function(response) {
                        window.open(response.file, 'preview',
                            'width=600,height=1000');
                    }
                });
            }

            // function handleProses(e) {
            //     console.log(e);
            //     let url = "{{ route('skkematian.proses', ':id') }}"
            //     url = url.replace(':id', e);

            //     $.ajax({
            //         type: 'POST',
            //         url: url,
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         success: function(response) {
            //             Toastify({
            //                 text: response.message,
            //                 duration: 3000,
            //                 close: true,
            //                 gravity: "top", // `top` or `bottom`
            //                 position: "center", // `left`, `center` or `right`
            //                 stopOnFocus: true, // Prevents dismissing of toast on hover
            //                 style: {
            //                     background: "rgba(25, 135, 84, 1)",
            //                 },
            //             }).showToast();
            //             $('#tableSurat').DataTable().ajax.reload();
            //         },
            //         error: function(xhr) {
            //             const response = JSON.parse(xhr.responseText);
            //             // console.log('hey error', response.message);
            //             Swal.fire({
            //                 title: 'Ooopppsss...',
            //                 text: response.message,
            //                 icon: 'error',
            //                 confirmButtonText: 'OK'
            //             });
            //         }
            //     });
            // }

            // function handleTolak(e) {
            //     console.log(e);
            //     let url = "{{ route('skkematian.tolak', ':id') }}"
            //     url = url.replace(':id', e);

            //     Swal.fire({
            //         title: 'Apakah Anda Yakin?',
            //         text: "Aapakah yakin akan menolak pengajuan dokumen ini?!",
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //         confirmButtonText: 'Ya',
            //         cancelButtonText: 'Batal'
            //     }).then((result) => {
            //         if (result.value) {
            //             $.ajax({
            //                 type: 'POST',
            //                 url: url,
            //                 headers: {
            //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //                 },
            //                 success: function(response) {
            //                     Swal.fire({
            //                         title: 'Pengajuan berhasil ditolak!',
            //                         text: response.message,
            //                         icon: 'error',
            //                         confirmButtonText: 'OK'
            //                     });
            //                     $('#tableSurat').DataTable().ajax.reload();
            //                 },
            //                 error: function(xhr) {
            //                     const response = JSON.parse(xhr.responseText);
            //                     Swal.fire({
            //                         title: 'Ooopppsss...',
            //                         text: response.message,
            //                         icon: 'error',
            //                         confirmButtonText: 'OK'
            //                     });
            //                 }
            //             });
            //         }
            //     });
            // }

            // function handleNaik(e) {
            //     console.log(e);
            //     let url = "{{ route('skkematian.naik', ':id') }}"
            //     url = url.replace(':id', e);

            //     $.ajax({
            //         type: 'POST',
            //         url: url,
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         success: function(response) {
            //             Toastify({
            //                 text: response.message,
            //                 duration: 3000,
            //                 close: true,
            //                 gravity: "top", // `top` or `bottom`
            //                 position: "center", // `left`, `center` or `right`
            //                 stopOnFocus: true, // Prevents dismissing of toast on hover
            //                 style: {
            //                     background: "rgba(25, 135, 84, 1)",
            //                 },
            //             }).showToast();
            //             $('#tableSurat').DataTable().ajax.reload();
            //         },
            //         error: function(xhr) {
            //             const response = JSON.parse(xhr.responseText);
            //             // console.log('hey error', response.message);
            //             Swal.fire({
            //                 title: 'Ooopppsss...',
            //                 text: response.message,
            //                 icon: 'error',
            //                 confirmButtonText: 'OK'
            //             });
            //         }
            //     });
            // }

            // script esign tetap
            $(function () {
                // Saat modal dibuka → isi hidden field dari tombol pemicu
                $('#esignModal').on('show.bs.modal', function (e) {

                    let btn = $(e.relatedTarget);
                    let id = btn.data('id');
                    let jenis = btn.data('jenis');
                    let no_surat = btn.data('no_surat');

                    $(this).find('[name="_id"]').val(id);
                    $(this).find('[name="jenis"]').val(jenis);
                    $(this).find('.modal-title').text("Tanda Tangan No Surat : " + no_surat);

                    $("#btn-ttd").prop("disabled", true);
                    $("#status").html('');
                });

                // Saat modal ditutup → reset form
                $('#esignModal').on('hidden.bs.modal', function () {

                    $('#clientsForm')[0].reset();
                    $("#status").empty();
                    $("#btn-ttd").prop("disabled", true);
                });

            });
        </script>
    @endpush
@endsection
