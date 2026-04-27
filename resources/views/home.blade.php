{{-- resources/views/home.blade.php --}}
@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css">
        <style>
            .card input:focus,
            .card select:focus  {
                box-shadow: none !important;
                outline: none !important;
                border-color: #AEA07A;
            }

            #chartSuratDrill {
                width: 100% !important;
                display: block !important;
                transition: opacity .35s ease, transform .35s ease;
            }

            #chartSuratDrill text {
                font-family: 'Poppins', sans-serif !important;
            }

            #chartSuratDrill.animating {
                opacity: 0.4;
                transform: scale(.98);
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
            .status-disetujui-warga   { background: #e0f7f4;color: #07847b }
            .status-disetujui-camat   { background: #ebe7f5;color: #7c5cc4 }
            .status-sudah-upload-bukti { background: #eef4ff;color: #2f5fb3 }
            .status-ttd-basah---bukti-uploaded { background: #eef4ff;color: #2f5fb3 }
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

            @if (auth()->user()->role_id == 1 ||  auth()->user()->role_id == 9)
            <h6 class="text-muted mt-1 mb-3">
                Statistik seluruh pengajuan surat keterangan warga <b>Kota Kediri.</b>
            </h6>
            {{-- CARD CHART --}}
            <div class="col-md-12 mb-3">
                <div class="card shadow-sm rounded-4" style="border-color:#AEA07A">
                    <div class="card-body">
                        <div id="chartSuratDrill" style="height: 350px;"></div>
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-12 d-flex align-items-center justify-content-between">
                {{-- @if (auth()->user()->role_id == 8)
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
                @endif --}}

                <div class="d-flex gap-2">
                    @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 8 || auth()->user()->role_id == 9)
                        <div class="dropdown">
                            <button class="btn btn-sm btn-primary dropdown-toggle"  style="border-radius: 8px" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-add-fill me-2"></i>
                                <span>Tambah</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('skbn.add') }}">SK BELUM MENIKAH</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('skboro.add') }}">SK BORO</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('skdom.add') }}">SK DOMISILI</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('skkelahiran.add') }}">SK KELAHIRAN</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('skkematian.add') }}">SK KEMATIAN</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('sktm.add', 'perorangan') }}">SKTM PERORANGAN</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('sktm.add', 'sekolah') }}">SKTM SEKOLAH</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('skhsl.add') }}">SK PENGHASILAN</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('skusaha.add') }}">SK USAHA</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('suket.add') }}">SURAT KETERANGAN</a>
                                </li>
                            </ul>
                        </div>
                    @endif
                    <button class="btn btn-secondary btn-sm shadow-sm" style="border-radius: 8px" onclick="reload()">
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
                                        <th>Jenis Surat</th>
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

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/drilldown.js"></script>
        <script src="{{ asset('assets/js/actions.js') }}"></script>
        <script type="text/javascript">
            $(function() {
                var table = $('#tableSurat').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    scrollX: false,
                    autoWidth: false,
                    ajax: "{{ route('home') }}",
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
                            data: 'jenis',
                            name: 'jenis'
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
                        [4, "desc"],
                        [0, "desc"]
                    ],
                    pageLength: 10,
                    responsive: true,
                });
            });

            function reload() {
                $('#tableSurat').DataTable().ajax.reload();
            }

            // script esign tetap
            $(function () {
                // Saat modal dibuka → isi hidden field dari tombol pemicu
					$('#esignModal').on('show.bs.modal', function (e) {
						let btn = $(e.relatedTarget);
						let id = btn.data('id');
						let jenis = btn.data('jenis');
						let role = btn.data('role');
						let no_surat = btn.data('no_surat');
					
						$(this).find('[name="_id"]').val(id);
						$(this).find('[name="jenis"]').val(jenis);
						$(this).find('[name="role"]').val(role);
						$(this).find('.modal-title').text("Tanda Tangan No Surat : " + no_surat);
					
						$("#btn-ttd").prop("disabled", true);
						$("#status").html('');
					});

                // Saat modal ditutup → reset form
                $('#esignModal').on('hidden.bs.modal', function () {

                    $('#clientsForm')[0].reset();
                    $("#status").empty();
                    $("#btn-ttd").prop("disabled", true);
                });

            });
        </script>
        <script>
            window.chartSurat = null;
            fetch("/chart/surat").then(r => r.json()).then(json => {

                const mainSeries = Object.entries(json.level1).map(([jenis, total]) => ({
                    name: jenis.toUpperCase(),
                    y: total,
                    drilldown: jenis.toLowerCase()
                }));

                window.chartSurat = Highcharts.chart("chartSuratDrill", {
                    chart: { type: "column" },
                    colors: [
                        '#7896B2', '#AEA07A', '#B2784A', '#FF9B9B', '#D8CFC4',
                        '#A8B6BF', '#E2B84C', '#92A8A1', '#7A9E7E'
                    ],
                    title: { text: "Statistik Semua Surat E-SUKET" },
                    subtitle: { text: "Klik bar untuk melihat detail status" },
                    xAxis: { type: "category" },
                    yAxis: { title: { text: "Jumlah Surat" } },
                    legend: { enabled: false },
                    series: [{
                        name: "Jenis Surat",
                        colorByPoint: true,
                        data: mainSeries
                    }],
                    drilldown: { series: json.level2 }
                });
            });
        </script>
    @endpush
    <x-esign></x-esign>
@endsection
