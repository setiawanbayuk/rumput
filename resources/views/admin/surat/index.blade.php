@extends('layouts.main')

@section('title', $title)

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
        <style>
            .card input:focus,
            .card select:focus {
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
                text-align: center;
                min-width: 80px;
            }

            /* Warna Status Berdasarkan Slug */
            .status-pengajuan { background: #e0e0e0; color: #6C757D }
            .status-proses { background: #e8f2ff; color: #1e63ff }
            .status-dinaikkan-ke-sekkel, .status-dinaikkan-ke-lurah { background: #ffe9d7; color: #F4A261 }
            .status-dinaikkan-ke-camat { background: #b4b1af; color: #B2784A }
            .status-disetujui, .status-disetujui-lurah { background: #e6f6ee; color: #0e8a5f }
            .status-disetujui-camat { background: #ebe7f5; color: #A78BFA }
            .status-ditolak, .status-dihapus { background: #fde4e6; color: #d2353c }
            .status-dinilai { background: #f6f1dd; color: #8b6f1d }
        </style>
    @endpush

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <h3 class="mb-0 fw-bold">{{ $title }}</h3>
            </div>
            <div class="col-md-12 d-flex align-items-center justify-content-between">
                <h6 class="text-muted mb-0">
                    Daftar seluruh pengajuan surat warga <b>Kota Kediri</b> dalam satu sistem terpadu.
                </h6>
                <div class="d-flex gap-2">
                    @if (in_array(auth()->user()->role_id, [1, 8, 9]))
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-add-fill me-1"></i> Tambah Surat
                            </button>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'skbn') }}">SK Belum Menikah</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'sktm') }}">SK Miskin (SKTM)</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'skdom') }}">SK Domisili</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'skusaha') }}">SK Usaha</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.surat.create', 'skhsl') }}">SK Penghasilan</a></li>
                            </ul>
                        </div>
                    @endif
                    <button class="btn btn-secondary btn-sm shadow-sm" onclick="reload()">
                        <i class="ri-loop-right-fill me-1"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm rounded-4" style="border-color: #AEA07A">
                    <div class="card-header bg-transparent border-0 p-4 pb-0">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted">Filter Jenis Surat</label>
                                <select id="filterJenis" class="form-select form-select-sm shadow-sm">
                                    <option value="">-- Semua Jenis --</option>
                                    <option value="suket">Surat Keterangan</option>
                                    <option value="skbn">SK Belum Menikah</option>
                                    <option value="skboro">SK Boro</option>
                                    <option value="skdom">SK Domisili</option>
                                    <option value="skhsl">SK Penghasilan</option>
                                    <option value="sktm">SK Miskin (SKTM)</option>
                                    <option value="skusaha">SK Usaha</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table id="tableSurat" class="table table-hover align-middle mb-0" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tipe</th>
                                        <th>No Surat</th>
                                        <th>NIK Pengaju</th>
                                        <th>Tanggal</th>
                                        <th>Peruntukan</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
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
                    ajax: {
                        url: "{{ route('admin.surat.index') }}",
                        data: function (d) {
                            d.jenis = $('#filterJenis').val(); // Kirim filter ke backend
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'tipe', name: 'jenis_surat', orderable: true },
                        { data: 'no_surat', name: 'no_surat', orderable: false },
                        { data: 'nik', name: 'nik' },
                        { data: 'tgl_surat', name: 'tgl_surat', width: '12%' },
                        { data: 'peruntukan', name: 'peruntukan' },
                        {
                            data: 'st',
                            name: 'status',
                            render: function(data) {
                                if (!data) return '-';
                                let slug = data.name.toLowerCase().replace(/\s+/g, '-');
                                return `<span class="status-badge status-${slug}">${data.name}</span>`;
                            },
                            className: 'text-center'
                        },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                    ],
                    order: [[4, "desc"]], // Default urutkan berdasarkan Tanggal
                    language: {
                        searchPlaceholder: "Cari NIK atau Peruntukan...",
                        processing: '<div class="spinner-border text-primary" role="status"></div>'
                    }
                });

                // Reload tabel saat filter dropdown berubah
                $('#filterJenis').on('change', function() {
                    table.ajax.reload();
                });
            });

            function reload() {
                $('#tableSurat').DataTable().ajax.reload();
            }

            // Fungsi Global Handle Cetak (BSRE Signed File)
            function handleCetak(id, jenis) {
                $.ajax({
                    type: "GET",
                    url: `/admin/surat/cetak/${id}`,
                    data: { jenis: jenis },
                    dataType: "json",
                    success: function(response) {
                        if(response.file) {
                            window.open(response.file, '_blank', 'width=800,height=1000');
                        } else {
                            alert("File tidak ditemukan.");
                        }
                    }
                });
            }

            // Integrasi Modal Esign
            $('#esignModal').on('show.bs.modal', function(e) {
                let btn = $(e.relatedTarget);
                $(this).find('[name="_id"]').val(btn.data('id'));
                $(this).find('[name="jenis"]').val(btn.data('jenis'));
                $(this).find('.modal-title').text("Tanda Tangan Surat : " + btn.data('no_surat'));
            });
        </script>
    @endpush
@endsection
