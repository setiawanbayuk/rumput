@extends('layouts.main')

@section('title', 'Manajemen Kontrol')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
        <style>
            .mw-page { --mw-ink:#1f2937; --mw-muted:#6b7280; --mw-line:#e5e7eb; --mw-gold:#b7791f; }
            .mw-title { font-weight:900; color:var(--mw-ink); letter-spacing:-.025em; }
            .mw-subtitle { color:var(--mw-muted); }
            .mw-heading { border:1px solid var(--mw-line); border-radius:18px; background:linear-gradient(180deg,#ffffff 0%,#fbfcff 100%); padding:18px 20px; box-shadow:0 10px 26px rgba(15,23,42,.045); }
            .mw-heading-icon { width:46px; height:46px; display:inline-flex; align-items:center; justify-content:center; border-radius:14px; color:#8a5a10; background:linear-gradient(135deg,#fff7e6,#f8fafc); border:1px solid #f1dfb8; font-size:1.35rem; }
            .mw-card { border:1px solid var(--mw-line); border-radius:18px; box-shadow:0 12px 30px rgba(15,23,42,.05); overflow:hidden; }
            .mw-card::before { content:''; display:block; height:3px; background:linear-gradient(90deg,#b7791f,#e9d8a6,#2563eb); }
            .mw-note { background:#fffdf7; border:1px solid #f1dfb8; border-left:4px solid var(--mw-gold); border-radius:14px; color:#475569; }
            .mw-btn-gold { border-color:#d6b56d; color:#8a5a10; background:#fffdf7; }
            .mw-btn-gold:hover { border-color:#b7791f; background:#b7791f; color:#fff; }
            #tableKontrol { margin-bottom:0!important; }
            #tableKontrol thead th { font-size:.78rem; text-transform:uppercase; letter-spacing:.045em; color:#475569; background:#f8fafc; border-bottom:1px solid var(--mw-line); white-space:nowrap; }
            #tableKontrol tbody td { color:#334155; vertical-align:middle; }
            #tableKontrol tbody tr:hover { background:#fbfdff; }
            .dataTables_wrapper .form-control, .dataTables_wrapper .form-select, .btn { border-radius:10px; }
        </style>
    @endpush

    <div class="container-fluid mw-page">
        <div class="mw-heading d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="mw-heading-icon"><i class="ri-user-settings-line"></i></div>
                <div>
                    <h3 class="mw-title mb-1">{{ $title ?? 'Manajemen Kontrol' }}</h3>
			@php
				$namaKelurahan = optional($adminSkpd->kelurahan)->nama ?? $adminSkpd->nama ?? 'Wilayah Admin';
			@endphp

			<div class="mw-subtitle small">
				Kelola Akun Warga, Sekkel, Dan Lurah Kelurahan {{ \Illuminate\Support\Str::title(strtolower($namaKelurahan)) }}.
			</div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.manajemen-warga.create', ['role_id' => 2]) }}" class="btn btn-dark fw-semibold">
                    <i class="ri-user-add-line me-1"></i> Tambah Warga
                </a>
                @if (!($hasSekkel ?? false))
                    <a href="{{ route('admin.manajemen-warga.create', ['role_id' => 4]) }}" class="btn btn-outline-dark fw-semibold">
                        <i class="ri-user-star-line me-1"></i> Tambah Sekkel
                    </a>
                @else
                    <button type="button" class="btn btn-outline-secondary fw-semibold" disabled title="Sekkel sudah ada. Edit atau Delete akun Sekkel lama jika ingin mengganti.">
                        <i class="ri-lock-2-line me-1"></i> Sekkel Sudah Ada
                    </button>
                @endif
                @if (!($hasLurah ?? false))
                    <a href="{{ route('admin.manajemen-warga.create', ['role_id' => 3]) }}" class="btn btn-outline-dark fw-semibold">
                        <i class="ri-shield-user-line me-1"></i> Tambah Lurah
                    </a>
                @else
                    <button type="button" class="btn btn-outline-secondary fw-semibold" disabled title="Lurah sudah ada. Edit atau Delete akun Lurah lama jika ingin mengganti.">
                        <i class="ri-lock-2-line me-1"></i> Lurah Sudah Ada
                    </button>
                @endif
                <button class="btn mw-btn-gold" onclick="reloadTable()">
                    <i class="ri-refresh-line me-1"></i> Reload
                </button>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div>

        </div>

        <div class="card mw-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableKontrol" class="table table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Role</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>RT/RW</th>
                                <th>Alamat</th>
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
            let tableKontrol;

            $(function() {
                tableKontrol = $('#tableKontrol').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    scrollX: true,
                    ajax: "{{ route('admin.manajemen-warga.index') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'role_label', name: 'users.role_id', orderable: true, searchable: false },
                        { data: 'nik', name: 'users.nik' },
                        { data: 'name', name: 'users.name' },
                        { data: 'email', name: 'users.email', defaultContent: '-' },
                        { data: 'phone', name: 'users.phone', defaultContent: '-' },
                        { data: 'rt_rw', name: 'rt_rw', orderable: false, searchable: false },
                        { data: 'alamat', name: 'alamat', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ],
                    order: [[3, 'asc']],
                    pageLength: 10,
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                        processing: 'Memuat data...',
                        zeroRecords: 'Data akun tidak ditemukan',
                        paginate: { previous: 'Sebelumnya', next: 'Berikutnya' }
                    }
                });
            });

            function reloadTable() {
                if (tableKontrol) tableKontrol.ajax.reload(null, false);
            }
        </script>
    @endpush
@endsection
