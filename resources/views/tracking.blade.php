{{-- resources/views/tracking.blade.php --}}
@extends('layouts.create')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .btn-chip {
            border-radius: 999px;
            padding: .35rem 2rem;
            font-size: .8rem;
            font-weight: 500;
        }

        .rating-star i {
            font-size: 28px;
            color: #c2c2c2;
            cursor: pointer;
        }
        .rating-star i.active {
            color: #e2b84c;
        }

        .divider {
            border-top: 1px solid #E5E7EB;
            margin: 0 18px
        }

        .tracking-card {
            max-width: 820px;
            margin: 80px auto 60px;
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .06);
            overflow: hidden
        }

        .tracking-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 0;
            background: #fff
        }

        .tracking-bar {
            display: inline-block;
            font-weight: 700;
            font-size: .78rem;
            letter-spacing: .4px;
            padding: .35rem .65rem;
            border-radius: .45rem
        }

        .tracking-bar--DIAJUKAN { background: #e0e0e0; color: #6C757D; }
        .tracking-bar--DIPROSES { background: #e8f2ff;ncolor: #1e63ff; }
        .tracking-bar--DITOLAK { background: #fde4e6; color: #d2353c; }
        .tracking-bar--SELESAI { background: #e6f6ee; color: #0e8a5f; }
        .tracking-bar--DINILAI { background: #f6f1dd; color: #8b6f1d;}

        .tracking-wrapper {
            display: flex;
            overflow-x: auto;
            padding-bottom: 12px;
        }

        .tracking-step {
            flex: 0 0 120px;        /* ukuran minimum mobile */
            text-align: center;
        }

        .step-icon-wrapper {
            height: 70px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .tracking-icon {
            width: 55px;
            height: 55px;
            margin-bottom: 8px;
        }

        .tracking-title {
            margin-top: .7rem;
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1.1;
            margin-bottom: 2px;
            color: #9ca3af;
        }

        .tracking-title.active {
            color: #7896B2; /* biru / bisa ganti #7896B2 */
        }

        .tracking-time {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .tracking-connect {
            width: 55px;
            height: 4px;
            margin-top: 25px; /* set ke tengah ikon */
            background: #e5e7eb;
            border-radius: 5px;
        }

        .tracking-connect.active {
            background: #7AB4ED;
        }

        .tracking-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            padding: 0 18px 12px
        }

        .tracking-body {
            padding: 16px 18px 22px
        }

        .timeline {
            margin: 10px 0 0 18px;
            border-left: 2px solid #E5E7EB;
            padding-left: 16px;
        }

        .timeline .item {
            position: relative;
            margin-bottom: 16px;
        }

        .timeline .item::before {
            content: "";
            position: absolute;
            left: -22px;
            top: 4px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #BFC5CD;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #E5E7EB;
        }

        .timeline .item.done::before {
            background: #16A34A;
        }

        .timeline .item.reject::before {
            background: #D2353C;
        }

        .timeline .item .meta {
            display: grid;
            grid-template-columns: max-content 1fr;
            grid-auto-rows: auto;
            column-gap: 12px;
            row-gap: 4px;
            align-items: start;
        }

        .timeline .item .meta .time {
            grid-column: 1;
            grid-row: 1 / span 2;
            color: #6B7280;
            font-size: .85rem;
            white-space: nowrap;
        }

        .timeline .item .meta .status {
            grid-column: 2;
            grid-row: 1;
            font-weight: 700;
        }

        .timeline .item .meta .status.ok {
            color: #148a52;
        }

        .timeline .item .meta .status.no {
            color: #d2353c;
        }

        .timeline .item .meta .desc {
            grid-column: 2;
            grid-row: 2;
            color: #6B7280;
            font-size: .9rem;
        }

        @media (max-width: 576px) {
            .tracking-card {
                margin: 80px 0px 30px;
            }
            
            .tracking-step {
                flex: 0 0 85px;
            }

            .step-icon-wrapper {
                height: 50px;
            }

            .tracking-icon {
                width: 38px;
                height: 38px;
            }

            .tracking-title {
                font-size: 0.75rem;
                line-height: 1.1;
            }

            .tracking-time {
                font-size: 0.6rem;
            }

            .tracking-connect {
                width: 25px;
                height: 3px;
                margin-top: 20px;
            }

            .btn-chip {
                padding: .35rem 1.4rem;
                font-size: .75rem;
            }

            .tracking-actions {
                flex-direction: column;
                gap: 8px;
            }

            .timeline {
                margin-left: 12px;
                padding-left: 14px;
            }

            .timeline .item .meta {
                grid-template-columns: minmax(70px, max-content) 1fr;
            }

            .timeline .item .meta .time {
                font-size: 0.75rem;
            }

            .timeline .item .meta .status {
                font-size: 0.85rem;
            }

            .timeline .item .meta .desc {
                font-size: 0.8rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/form.png') }}') no-repeat center center; background-size: cover; margin-top: -75px;">
        <div class="container" style="margin-top: 50px">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="tracking-card">
                        @php
                            $first      = $logs->first();
                            $last       = $logs->last();
                            $namasrt    = $last?->nama_surat;
                        @endphp
                        <div class="tracking-head">
                            <span
                                class="tracking-bar tracking-bar--{{ $bar ?? 'DIAJUKAN' }}">{{ $bar ?? 'DIAJUKAN' }}</span>
                            <div class="small text-muted">No. Surat: {{ $nomorSurat }}</div>
                        </div>

                        <div class="divider"></div>

                        <div class="row justify-content-center text-center tracking-wrapper mt-4">
                            <div class="col-auto tracking-step">
                                <div class="step-icon-wrapper">
                                    <img src="{{ asset('assets/images/diajukan.png') }}" class="tracking-icon">
                                </div>

                                <div class="tracking-title {{ $step >= 1 ? 'active' : '' }}">
                                    Surat Diajukan
                                </div>

                                <div class="tracking-time">
                                    {{ optional($times['diajukan'])->translatedFormat('d F Y H:i') ?? '—' }}
                                </div>
                            </div>

                            <div class="col-auto tracking-connect {{ $step >= 2 ? 'active' : '' }}"></div>

                            <div class="col-auto tracking-step">
                                <div class="step-icon-wrapper">
                                    <img src="{{ asset('assets/images/' . ($step >= 2 ? 'diproses-active.png' : 'diproses.png')) }}" class="tracking-icon">
                                </div>

                                <div class="tracking-title {{ $step >= 2 ? 'active' : '' }}">
                                    Surat Diproses
                                </div>

                                <div class="tracking-time">
                                    {{ optional($times['proses'])->translatedFormat('d F Y H:i') ?? '—' }}
                                </div>
                            </div>

                            <div class="col-auto tracking-connect {{ $step >= 3 ? 'active' : '' }}"></div>

                            <div class="col-auto tracking-step">
                                <div class="step-icon-wrapper">
                                    <img src="{{ asset('assets/images/' . ($step >= 3 ? 'disetujui-active.png' : 'disetujui.png')) }}" class="tracking-icon">
                                </div>

                                <div class="tracking-title {{ $step >= 3 ? 'active' : '' }}">
                                    Surat Disetujui
                                </div>

                                <div class="tracking-time">
                                    {{ optional($times['selesai'])->translatedFormat('d F Y H:i') ?? '—' }}
                                </div>
                            </div>

                            <div class="col-auto tracking-connect {{$step >= 4 ? 'active' : '' }}"></div>

                            <div class="col-auto tracking-step">
                                <div class="step-icon-wrapper">
                                    <img src="{{ asset('assets/images/' . ($step >= 4 ? 'dinilai-active.png' : 'dinilai.png')) }}" class="tracking-icon">
                                </div>

                                <div class="tracking-title {{$step >= 4 ? 'active' : '' }}">
                                    Surat Dinilai
                                </div>

                                <div class="tracking-time">
                                    {{ optional($times['nilai'])->translatedFormat('d F Y H:i') ?? '—' }}
                                </div>
                            </div>
                        </div>

                        <div class="tracking-actions mt-4">
                            @if ($step == 1 || $step == 2)
                                <a href="{{ route($alias.'.show', ['id' => $id]) }}"
                                    class="btn btn-chip text-white" style="background: #7896B2">
                                    Lihat
                                </a>
                            @endif

                            @if ($step == 3 || $step == 4)
                                <a href="#" class="btn btn-success btn-chip">
                                    Cetak Surat
                                </a>
                            @endif

                            @if ($step == 3)
                                @if ($times['nilai'] == null)
                                    <x-btnnilai :alias="$jenisSurat" :id="$id" :nosrt="$nomorSurat" :nama="$namasrt" />
                                @else
                                    <x-btnlihatnilai :alias="$jenisSurat" :id="$id" :nosrt="$nomorSurat" :nama="$namasrt"
                                    />
                                @endif
                            @endif

                            @if ($step == 4)
                                <x-btnlihatnilai :alias="$jenisSurat" :id="$id" :nosrt="$nomorSurat" :nama="$namasrt" />
                            @endif

                            @if (($step == 1 || $step == 2) && $alias && Route::has($alias.'.hapus'))
                                <button type="button"
                                    @if ($step == 2) disabled @endif
                                    class="btn btn-danger btn-chip btn-open-hapus"
                                    data-id="{{ $id }}"
                                    data-action="{{ route($alias.'.hapus', ['id' => '__ID__']) }}">
                                    Hapus
                                </button>
                            @endif
                        </div>

                        <div class="divider mt-2"></div>

                        <div class="tracking-body">
                            <div class="fw-bold mb-2">Detail Pengajuan</div>
                            <div class="timeline">
                                @forelse($logs as $tracking)
                                    @php
                                        $status  = $tracking->status_surat;
                                        $selesai = $status == 5;
                                        $ditolak = $status == 6;
                                    @endphp
                                    <div class="item {{ $selesai ? 'done' : '' }} {{ $ditolak ? 'reject' : '' }}">
                                        <div class="meta">
                                            <div class="time">
                                                {{ optional($tracking->created_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}
                                            </div>

                                            <div class="status {{ $selesai ? 'ok' : '' }} {{ $ditolak ? 'no' : '' }}">
                                                @if ($selesai && $jenisSurat !== 'sktm')
                                                    Selesai
                                                @else
                                                    {{ $tracking->st['name'] }}
                                                @endif
                                            </div>

                                            <div class="desc">
                                                {{ $tracking->st['keterangan'] ?? '—' }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted small">Belum ada jejak proses.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="formHapusGlobal" method="POST" data-action-template="">@csrf</form>
        <div class="modal fade" id="modalHapusGlobal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px; overflow:hidden;">
                    <div class="modal-header" style="background:#7896B2; color:#fff;">
                        <h5 class="modal-title fw-semibold mx-auto text-center">
                            Anda yakin ingin membatalkan dan menghapus pengajuan surat?</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('assets/images/hapus-surat.png') }}" style="max-width:200px" class="mb-3">
                        <p class="mb-2 mx-2 text-start">
                            Tindakan ini akan menghapus data pengajuan Anda. Surat ini tidak akan diproses dan akan hilang dari riwayat pengajuan Anda.</p>
                    </div>

                    <div class="modal-footer justify-content-center gap-3">
                        <button id="btnHapusGlobal" class="btn text-white px-4" 
                            style="border-radius:999px; background:#dc3545;">Hapus</button>
                        <button class="btn text-white px-4" data-bs-dismiss="modal" 
                            style="border-radius:999px; background:#AEA07A;">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalEl = document.getElementById('modalHapusGlobal');
            const modal   = new bootstrap.Modal(modalEl);
            const form    = document.getElementById('formHapusGlobal');
            const submit  = document.getElementById('btnHapusGlobal');

            // buka modal + simpan action template & id
            document.querySelectorAll('.btn-open-hapus').forEach(btn => {
                btn.addEventListener('click', () => {
                const tpl = btn.dataset.action;      // mis: /skbn/__ID__/hapus
                const id  = btn.dataset.id;
                form.dataset.actionTemplate = tpl;
                form.dataset.deleteId = id;
                modal.show();
                });
            });

            // kirim AJAX ke route per-jenis
            submit.addEventListener('click', async (e) => {
                e.preventDefault();
                const tpl = form.dataset.actionTemplate || '';
                const id  = form.dataset.deleteId || '';
                if (!tpl || !id) return;

                const url = tpl.replace('__ID__', id);

                try {
                const res  = await fetch(url, {
                    method: 'POST',
                    headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                    }
                });
                const json = await res.json();
                if (res.ok) {
                    alert(json.message || 'Berhasil dihapus.');
                    bootstrap.Modal.getInstance(modalEl)?.hide();
                    window.location.href = "{{ route('ajukan') }}";
                } else {
                    alert(json.message || 'Terjadi kesalahan.');
                }
                } catch (err) {
                alert('Koneksi gagal. Silakan coba lagi.');
                }
            });
        });
    </script>
    <script>
        var selectedRating = 0;

        // highlight bintang
        $(document).on('click', '.rating-star i', function () {
            selectedRating = $(this).data('value');
            $(".rating-star i").removeClass('active');
            for (let i = 1; i <= selectedRating; i++) {
                $(".rating-star i[data-value='" + i + "']").addClass('active');
            }
        });

        // buka modal nilai
        function openModalNilai(alias, id, noSurat, judul) {
            $("#modalNilai").data('alias', alias);
            $("#modalNilai").data('id', id);
            $("#judulSurat").text(judul);
            $("#noSurat").text(noSurat);

            selectedRating = 0;
            $(".rating-star i").removeClass('active');
            $("#komentar").val('');

            $("#modalNilai").modal('show');
        }

        // kirim penilaian
        $("#btnKirimNilai").on("click", function () {
            let modal  = $("#modalNilai");
            let alias  = modal.data('alias');
            let id     = modal.data('id');
            let komentar = $("#komentar").val();

            if (selectedRating === 0) {
                return Swal.fire("Oops!", "Silakan beri rating terlebih dahulu", "warning");
            }

            $.ajax({
                url: "/warga/" + alias + "/" + id + "/nilai",
                type: "POST",
                data: {
                    rating: selectedRating,
                    komentar: komentar,
                    _token: "{{ csrf_token() }}"
                },
                success: function () {
                    $("#modalNilai").modal("hide");
                    Swal.fire("Berhasil!", "Terima kasih atas penilaian Anda!", "success");
                    location.reload();
                },
                error: function (res) {
                    console.log(res.responseText);
                    Swal.fire("Gagal", "Terjadi kesalahan", "error");
                }
            });
        });
    </script>
    <script>
        function lihatPenilaian(alias, id, noSurat, judul) {
            $.ajax({
                url: "/warga/" + alias + "/" + id + "/nilai",
                method: "GET",
                success: function (res) {
                    let stars = "";
                    for (let i = 1; i <= 5; i++) {
                        stars += `<i class="ri-star-${i <= res.rating ? 'fill' : 'line'}"></i>`;
                    }
                    $("#lihatStars").html(stars);

                    $("#lihatKomentar").text(res.komentar || "-");
                    $("#lihatTanggalNilai").text(res.tanggal || "-");

                    $("#modalLihatNilai").modal('show');
                },
                error: function () {
                    Swal.fire("Gagal", "Tidak dapat mengambil data penilaian.", "error");
                }
            });
        }
    </script>
    @endpush
    <x-nilai />
    <x-lihatnilai />
@endsection