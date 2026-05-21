@extends('layouts.main')

@section('title', 'Rekap Surat')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
    @endpush

    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h3 class="fw-bold mb-1">{{ $title }}</h3>
                <div class="text-muted small">Daftar rekap seluruh pengajuan sesuai wilayah dan role akun login.</div>
            </div>
            <button class="btn btn-secondary" onclick="reloadRekap()">
                <i class="ri-refresh-line me-1"></i> Reload
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableRekap" class="table table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Surat</th>
                                <th>Jenis Surat</th>
                                <th>NIK</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
        <script>
            let tableRekap;

            $(function() {
                tableRekap = $('#tableRekap').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    scrollX: true,
                    ajax: "{{ route('rekap') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'no_surat', name: 'no_surat', orderable: false },
                        { data: 'jenis', name: 'jenis' },
                        { data: 'nik', name: 'nik' },
                        { data: 'tgl_surat', name: 'tgl_surat' },
                        { data: 'status', name: 'status', orderable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ],
                    order: [[4, 'desc']],
                    pageLength: 10,
                });
            });

            function reloadRekap() {
                if (tableRekap) tableRekap.ajax.reload(null, false);
            }
        </script>
    @endpush
@endsection
