@extends('layouts.main')

@section('title', 'Template Surat')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
    @endpush

    <div class="container">
        <h3>{{ $title }}</h3>
        <div class="d-flex gap-2 mt-3">
            @if (auth()->user()->role_id == 1)
                <a class="btn btn-primary" href="{{ route('template.add') }}">
                    <i class="ri-add-fill me-2"></i>
                    <span>Tambah</span></a>
            @endif
            <button class="btn btn-secondary" onclick="reload()">Reload</button>
        </div>
        <div class="card card-body mt-3">
            <div class="table-responsive">
                <table id="tableTemplate" class="table table-hovered" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>State</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
        <script type="text/javascript">
            $(function() {
                var table = $('#tableTemplate').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    scrollX: true,
                    ajax: "{{ route('template.index') }}",
                    columns: [{
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'jenis',
                            name: 'jenis',
                        },
                        {
                            data: 'state',
                            render: function(data, type) {
                                if (data == 'default') {
                                    return `<span style="color:Blue">${data}</span>`;
                                } else {
                                    return `<span style="color:Green">${data}</span>`;
                                }
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
                        [0, "desc"]
                    ],
                    pageLength: 10,
                });
            });

            function reload() {
                $('#tableTemplate').DataTable().ajax.reload();
            }
        </script>
        <script>
            function handleHapus(e) {
                console.log(e);
                let url = "{{ route('template.hapus', ':id') }}"
                url = url.replace(':id', e);

                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Aapakah yakin akan menghapus dokumen ini?!",
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
                                    title: 'Dokumen berhasil dihapus!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                $('#tableTemplate').DataTable().ajax.reload();
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
        </script>
    @endpush
@endsection
