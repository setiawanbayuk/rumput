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
            {{-- <button class="btn btn-primary">
                <i class="ri-add-fill me-2"></i>
                <span>Tambah</span>
            </button> --}}
            <a class="btn btn-primary" href="{{ route('suket.add') }}">
                <i class="ri-add-fill me-2"></i>
                <span>Tambah</span></a>
            <button class="btn btn-secondary" onclick="reload()">Reload</button>
        </div>

        <br>

        <div class="card card-body">
            <div class="table-responsive">
                <table id="tableSurat" class="table table-hovered">
                    <thead>
                        <tr>
                            <th>No Urut</th>
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
                    ajax: "{{ route('suket.index') }}",
                    columns: [{
                            data: 'no_urut_surat',
                            name: 'no_urut_surat'
                        },
                        {
                            data: 'nik',
                            name: 'nik'
                        },
                        {
                            data: 'tgl_surat',
                            name: 'tgl_surat'
                        },
                        {
                            data: 'peruntukan',
                            name: 'peruntukan'
                        },
                        {
                            data: 'st',
                            name: 'st'
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
                $('#tableSurat').DataTable().ajax.reload();
            }
        </script>
    @endpush
@endsection
