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

    @include('modals.skbn-add-modal')
    {{-- <x-esign></x-esign> --}}
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
                    ajax: "{{ route('skbn.warga') }}",
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
                    url: "{{ env('APP_URL', 'http://rumput.test') }}" + "/skbn/cetak/" + e,
                    success: function(response) {
                        window.open(response.file, 'preview',
                            'width=600,height=1000');
                    }
                });
            }
        </script>
    @endpush
@endsection
