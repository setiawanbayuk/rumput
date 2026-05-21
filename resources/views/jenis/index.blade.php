@extends('layouts.main')

@section('title', 'Jenis Surat')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
    @endpush

    <style>
        .jenis-card{border:1px solid #e2e8f0;border-radius:24px;box-shadow:0 16px 40px rgba(15,23,42,.07)}
        .jenis-note{border:1px dashed #cbd5e1;background:#f8fafc;border-radius:18px;padding:14px;color:#475569}
    </style>

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h3 class="fw-bold mb-1">{{ $title }}</h3>
                <div class="text-muted">Master jenis surat dipakai untuk daftar tipe surat, deskripsi layanan, dan persyaratan yang tampil ke pengguna.</div>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success rounded-4">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-warning rounded-4">{{ session('error') }}</div>
        @endif

        <div class="jenis-note mb-3">
            <b>Fungsi menu Jenis Surat:</b> untuk mengatur informasi layanan setiap jenis surat seperti SUKET, SKBN, SKTM, SKDOM, dan lainnya. Menu ini bukan alur verifikasi/TTE, tetapi master informasi yang membantu tampilan form, keterangan layanan, dan persyaratan.
            @if (! $hasDetail || ! $hasPersyaratan)
                <div class="mt-2 text-danger fw-semibold">Catatan: database kamu saat ini belum memiliki kolom {{ ! $hasDetail ? 'detail' : '' }} {{ (! $hasDetail && ! $hasPersyaratan) ? 'dan' : '' }} {{ ! $hasPersyaratan ? 'persyaratan' : '' }} pada tabel jenis_surats, jadi menu dibuat aman agar tidak error.</div>
            @endif
        </div>

        <div class="card jenis-card">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="tableJenis" class="table table-hover align-middle" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Persyaratan</th>
                                <th style="width:110px">Action</th>
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
        <script type="text/javascript">
            $(function() {
                $('#tableJenis').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    scrollX: true,
                    ajax: "{{ route('jenis.index') }}",
                    columns: [
                        { data: 'nama_label', name: 'nama_label' },
                        { data: 'detail_label', render: function(data) { return $("<div></div>").html(data || '-').text(); } },
                        { data: 'persyaratan_label', render: function(data) { return $("<div></div>").html(data || '-').text(); } },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ],
                    order: [[0, "asc"]],
                    pageLength: 10,
                });
            });
        </script>
    @endpush
@endsection
