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

            .status-badge {
                display: inline-block;
                padding: 3px 9px;
                font-size: 0.8rem;
                font-weight: 600;
                border-radius: 5px;
            }

            /* warna-warna */
            .status-pengajuan  { background: #e0e0e0; color: #6C757D }
            .status-proses  { background: #e8f2ff; color: #1e63ff }
            .status-dinaikkan-ke-sekkel  { background: #ffe9d7; color: #F4A261 } 
            .status-dinaikkan-ke-lurah  { background: #ffe9d7; color:  #F4A261 }
            .status-dinaikkan-ke-camat  { background: #b4b1af; color:  #B2784A } 
            .status-disetujui   { background: #e6f6ee;color: #0e8a5f }
            .status-disetujui-lurah   { background: #e6f6ee;color: #0e8a5f }
            .status-disetujui-camat   { background: #ebe7f5;color: #A78BFA }
            .status-ditolak   { background: #fde4e6; color: #d2353c }
            .status-dihapus   { background: #fde4e6; color: #d2353c } 
            .status-dinilai   { background: #f6f1dd;color: #8b6f1d }
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
                        Daftar seluruh pengajuan surat keterangan warga <b>RT {{ $rt }}/RW {{ $rw }}, Kelurahan {{ $kelurahan }}.</b>
                    </h6>
                @elseif (auth()->user()->role_id == 1 ||  auth()->user()->role_id == 9)
                    <h6 class="text-muted mb-0">
                        Daftar seluruh pengajuan surat keterangan warga <b>Kota Kediri.</b>
                    </h6>
                @elseif  (auth()->user()->role_id == 5 ||  auth()->user()->role_id == 6)
                    <h6 class="text-muted mb-0">
                        Daftar seluruh pengajuan surat keterangan warga <b>Kecamatan {{ $kecamatan }}.</b>
                    </h6>
                @else
                    <h6 class="text-muted mb-0">
                        Daftar seluruh pengajuan surat keterangan warga <b>Kelurahan {{ $kelurahan }}.</b>
                    </h6>
                @endif
                <div class="d-flex gap-2">
                    @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 8 || auth()->user()->role_id == 9)
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle" style="border-radius: 8px" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-add-fill me-2"></i>
                                <span>Tambah</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('sktm.add', 'perorangan') }}">SKTM PERORANGAN</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('sktm.add', 'sekolah') }}">SKTM SEKOLAH</a>
                                </li>
                            </ul>
                        </div>
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
                                        <th>Peruntukan</th>
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
                    ajax: "{{ route('sktm.index') }}",
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
                            data: 'nik',
                            name: 'nik'
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
                            render: function(data) {
                                if (!data) return '-';

                                // Convert name → slug (huruf kecil, tanpa spasi)
                                let slug = data.name.toLowerCase().replace(/\s+/g, '-');

                                return `
                                    <span class="status-badge status-${slug}">
                                        ${data.name}
                                    </span>
                                `;
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
            //     window.open("{{ env('APP_URL', 'http://rumput.test') }}" + "/sktm/preview/" + e, 'preview',
            //         'width=600,height=1000');
            // }

            function handleCetak(e) {
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "{{ env('APP_URL', 'http://rumput.test') }}" + "/sktm/cetak/" + e,
                    success: function(response) {
                        window.open(response.file, 'preview',
                            'width=600,height=1000');
                    }
                });
            }

            // function handleProses(e) {
            //     console.log(e);
            //     let url = "{{ route('sktm.proses', ':id') }}"
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
            //     let url = "{{ route('sktm.tolak', ':id') }}"
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
            //     let url = "{{ route('sktm.naik', ':id') }}"
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

            $(function() {
                $('#esignModal').on('show.bs.modal', function(e) {
                    let btn = $(e.relatedTarget);
                    let id = btn.data('id');
                    let jenis = btn.data('jenis');
                    let role = btn.data('role');
                    let form = $(this).find('form#esignModal');
                    $(this).find('[name="_id"]').val(id);
                    $(this).find('[name="jenis"]').val(jenis);
                    $(this).find('[name="role"]').val(role);
                    $(this).find('.modal-title').text("Tanda Tangan No Surat : " + btn.data('no_surat'));
                });

                $('#esignModal').on('hidden.bs.modal', function() {
                    $(this).find('form#esignModal').trigger('reset');
                });


                $('#registerModal').on('show.bs.modal', function(e) {
                    let btn = $(e.relatedTarget);
                    let id = btn.data('id');
                    let form = $(this).find('form#registerModal');
                    $(this).find('[name="_id"]').val(id);
                    $(this).find('.modal-title').text("Register Surat : ");
                });

                $('#registerModal').on('hidden.bs.modal', function() {
                    $(this).find('form#registerModal').trigger('reset');
                });
            });
        </script>
    @endpush
@endsection
