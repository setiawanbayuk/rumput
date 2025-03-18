@extends('layouts.main')

@section('title', 'Surat Keterangan')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
    @endpush

    <div class="container">
        <h3>{{ $title }}</h3>
        <div class="d-flex gap-2 mt-3">
            @if (auth()->user()->role_id == 1)
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                    Tambah
                </button>
            @endif
            <button class="btn btn-secondary" onclick="reload()">Reload</button>
        </div>
        <div class="card card-body mt-3">
            <div class="table-responsive">
                <table id="tableSurat" class="table table-hovered" style="width: 100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Surat</th>
                            <th>NIK</th>
                            <th>Tanggal</th>
                            <th>Peruntukan</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="tambahModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="d-flex justify-content-between">
                        <a class="btn btn-primary btn-block" href="{{ route('sktm.add', 'perorangan') }}">
                            {{-- <i class="ri-add-fill me-2"></i> --}}
                            <span>SKTM PERORANGAN</span></a>
                        <a class="btn btn-primary btn-block" href="{{ route('sktm.add', 'sekolah') }}">
                            {{-- <i class="ri-add-fill me-2"></i> --}}
                            <span>SKTM SEKOLAH</span></a>
                    </div>
                </div>
                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div> --}}
            </div>
        </div>
    </div>

    <x-esign></x-esign>
    <x-register></x-register>
    @push('scripts')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
        <script type="text/javascript">
            $(function() {
                var table = $('#tableSurat').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    scrollX: true,
                    ajax: "{{ route('sktm.index') }}",
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
                            data: 'jenis',
                            // name: 'jenis'
                            render: function(data, type) {
                                var str = data;
                                str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                                    return letter.toUpperCase();
                                });
                                return `<span>${str}</span> `;
                            },
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
                        [3, "desc"],
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
            function handlePreview(e) {
                window.open("{{ env('APP_URL', 'http://rumput.test') }}" + "/sktm/preview/" + e, 'preview',
                    'width=600,height=1000');
            }

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


            function handleTolak(e) {
                console.log(e);
                let url = "{{ route('sktm.tolak', ':id') }}"
                url = url.replace(':id', e);

                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Aapakah yakin akan menolak pengajuan dokumen ini?!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Pengajuan berhasil ditolak!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                $('#tableSurat').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                const response = JSON.parse(xhr.responseText);
                                Swal.fire({
                                    title: 'Ooopppsss...',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            }

            function handleNaik(e) {
                console.log(e);
                let url = "{{ route('sktm.naik', ':id') }}"
                url = url.replace(':id', e);

                $.ajax({
                    type: 'POST',
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Toastify({
                            text: response.message,
                            duration: 3000,
                            close: true,
                            gravity: "top", // `top` or `bottom`
                            position: "center", // `left`, `center` or `right`
                            stopOnFocus: true, // Prevents dismissing of toast on hover
                            style: {
                                background: "rgba(25, 135, 84, 1)",
                            },
                        }).showToast();
                        $('#tableSurat').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        const response = JSON.parse(xhr.responseText);
                        // console.log('hey error', response.message);
                        Swal.fire({
                            title: 'Ooopppsss...',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }

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
                })


                $('#registerModal').on('show.bs.modal', function(e) {
                    let btn = $(e.relatedTarget);
                    let id = btn.data('id');
                    let form = $(this).find('form#registerModal');
                    $(this).find('[name="_id"]').val(id);
                    $(this).find('.modal-title').text("Register Surat : ");
                });

                $('#registerModal').on('hidden.bs.modal', function() {
                    $(this).find('form#registerModal').trigger('reset');
                })
            });
        </script>
    @endpush
@endsection
