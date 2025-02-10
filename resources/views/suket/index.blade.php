@extends('layouts.main')

@section('title', 'Surat Keterangan')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
    @endpush

    <div class="container">
        <h3>{{ $title }}</h3>

        <br>

        <div class="d-flex gap-2">
            @if (auth()->user()->role_id == 1)
                <a class="btn btn-primary" href="{{ route('suket.add') }}">
                    <i class="ri-add-fill me-2"></i>
                    <span>Tambah</span></a>
            @endif
            <button class="btn btn-secondary" onclick="reload()">Reload</button>
        </div>

        <br>

        <div class="card card-body">
            <div class="table-responsive">
                <table id="tableSurat" class="table table-hovered" style="width: 100%">
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

    <x-esign></x-esign>
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
                    ajax: "{{ route('suket.index') }}",
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
                window.open("{{ env('APP_URL', 'http://rumput.test') }}" + "/suket/preview/" + e, 'preview',
                    'width=600,height=1000');
            }

            function handleCetak(e) {
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "{{ env('APP_URL', 'http://rumput.test') }}" + "/suket/cetak/" + e,
                    success: function(response) {
                        window.open(response.file, 'preview',
                            'width=600,height=1000');
                    }
                });
            }


            function handleTolak(e) {
                console.log(e);
                let url = "{{ route('suket.tolak', ':id') }}"
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
                let url = "{{ route('suket.naik', ':id') }}"
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
                    let form = $(this).find('form#esignModal');
                    $(this).find('[name="_id"]').val(id);
                    $(this).find('[name="jenis"]').val(jenis);
                    $(this).find('.modal-title').text("Tanda Tangan No Surat : " + btn.data('no_surat'));
                });

                $('#esignModal').on('hidden.bs.modal', function() {
                    $(this).find('form#esignModal').trigger('reset');
                })
            });
        </script>
    @endpush
@endsection
