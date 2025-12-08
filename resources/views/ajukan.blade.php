@extends('layouts.page')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* kartu status seperti di mockup */
        .suket-card {
            border-radius: .75rem;
            background: #fff;
            border: 1px solid #E5E7EB;
        }

        .suket-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            background: #fff;
            border-radius: .75rem .75rem 0 0;
        }

        .suket-card .card-body {
            padding: 1rem 1.25rem
        }

        .suket-meta {
            font-size: .8rem;
            color: #6B7280
        }

        .suket-card .card-footer {
            background: #fff;
            border-top: 0;
            border-radius: 0 0 .75rem .75rem;
        }

        .divider-card-line {
            border-top: 1px solid #E0E0E0;
            margin: 0 1.25rem;
        }

        /* efek hover body */
        .card-click {
            cursor: pointer;
            transition: background .15s ease, box-shadow .15s ease;
        }

        .card-click:hover {
            background: #F8F9FB;
            box-shadow: inset 0 2px 14px rgba(0, 0, 0, .04);
        }

        .bar {
            display: inline-block;
            font-weight: 700;
            font-size: .78rem;
            letter-spacing: .4px;
            padding: .35rem .65rem;
            border-radius: .45rem
        }

        .bar--diajukan {
            background: #e0e0e0;
            color: #6C757D
        }

        .bar--diproses {
            background: #e8f2ff;
            color: #1e63ff
        }

        .bar--ditolak {
            background: #fde4e6;
            color: #d2353c
        }

        .bar--selesai {
            background: #e6f6ee;
            color: #0e8a5f
        }

        .bar--dinilai {
            background: #f6f1dd;
            color: #8b6f1d
        }

        /* tombol kecil bundar */
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
    </style>
@endpush

@section('content')
    <div class="container py-3">
        {{-- Konten Utama --}}
        <section class="col-lg-12">
            {{-- Search --}}
            <form class="mb-3" method="get">
                <div class="fi fi--s">
                    <input id="search-box" type="text" class="fi-input" name="q" value="{{ request('q') }}"
                        placeholder=" ">
                    <label for="search-box" class="fi-label">Cari Surat yang Telah Anda Ajukan</label>
                    <button class="fi-btn" type="submit"><i class="ri-search-line"></i></button>
                </div>
            </form>

            {{-- Tabs --}}
            <div class="seg seg-2 mb-3">
                <!-- radio disembunyikan -->
                <input type="radio" name="seg" id="seg-a" checked>
                <input type="radio" name="seg" id="seg-b">

                <div class="seg-wrap">
                    <label for="seg-a" class="seg-btn">Sedang Proses</label>
                    <label for="seg-b" class="seg-btn">Riwayat</label>
                    <span class="seg-indicator"></span>
                </div>
            </div>

            <div class="tab-content">
                {{-- TAB: SEDANG PROSES --}}
                <div class="tab-pane fade show active" id="tab-proses" role="tabpanel">
                    <div class="container rounded-3 px-4 py-2" style="background:rgba(174,160,122,.25)">
                        @forelse($sedangProses as $pengajuan)
                            @php
                                $raw   = $pengajuan->raw;
                                $ui    = $raw->ui_status;
                                $step  = $ui['step'] ?? 0;
                                $label = $ui['label'] ?? '—';
                                $color = $ui['color_class'] ?? 'default';
                                $id    = $raw->id;
                                $nama  = $pengajuan->jenis_label;
                                $tgl   = optional($raw->created_at)->translatedFormat('d F Y');
                                $nosrt = $pengajuan->nomor_surat ?? '—';
                                $alias = $pengajuan->jenis
                            @endphp

                            <div class="card suket-card mb-3 border-0 shadow-sm mt-3">
                                {{-- HEAD --}}
                                <div class="suket-head">
                                    <span class="bar {{ $color }}">{{ $label }}</span>
                                </div>

                                <div class="divider-card-line"></div>

                                {{-- BODY --}}
                                <div class="card-body position-relative p-4 card-click">
                                    <h5 class="fw-bold mb-1 text-uppercase">{{ $nama }}</h5>
                                    <div class="text-muted small mb-2">
                                        No. Surat : {{ $nosrt }}
                                    </div>
                                    {{-- Link tracking (alias + id) --}}
                                    <a class="stretched-link"
                                        href="{{ route('tracking', ['jenisSurat' => $alias, 'id' => $id]) }}"
                                        aria-label="Lihat tracking {{ $nama }}">
                                    </a>
                                </div>

                                <div class="divider-card-line"></div>

                                {{-- FOOTER --}}
                                <div class="card-footer d-flex justify-content-between align-items-center px-4 py-3">
                                    <div class="text-muted small">
                                        <i class="ri-calendar-2-line me-1"></i>{{ $tgl }}
                                    </div>
                                    <div class="d-flex gap-2">
                                        {{-- STEP 1 & 2: Tombol PREVIEW --}}
                                        @if ($step == 1 || $step == 2)
                                            <a href="{{ route($alias.'.show', ['id' => $id]) }}" target="_blank"
                                            class="btn btn-chip text-white" style="background: #7896B2">
                                                Lihat
                                            </a>
                                        @endif
                                        {{-- Tombol Cetak Surat - Tampil jika STEP 3 (SELESAI) atau STEP 4 (DINILAI) --}}
                                        @if ($step == 3)
                                            <a href="{{-- route('pengajuan.cetak', [$jenis, $id]) --}}"
                                            class="btn btn-success btn-chip">
                                                Cetak Surat
                                            </a>

                                            <x-btnnilai :alias="$alias" :id="$id" :nosrt="$nosrt" :nama="$nama" />
                                        @endif

                                        {{-- STEP 1 → tombol hapus --}}
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
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">Belum ada pengajuan.</div>
                        @endforelse
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $sedangProses->withQueryString()->fragment('tab-proses')->onEachSide(1)->links('vendor.pagination.e-suket') }}
                        </div>
                    </div>
                </div>

                {{-- TAB: RIWAYAT --}}
                <div class="tab-pane fade" id="tab-riwayat" role="tabpanel">
                    <div class="container rounded-3 px-4 py-2" style="background:rgba(174,160,122,.25)">
                        @forelse($riwayat as $pengajuan)
                                @php
                                    $raw   = $pengajuan->raw;
                                    $ui    = $raw->ui_status;
                                    $step  = $ui['step'];
                                    $label = $ui['label'];
                                    $color = $ui['color_class'];
                                    $id    = $raw->id;
                                    $nama  = $pengajuan->jenis_label;
                                    $tgl   = optional($raw->created_at)->translatedFormat('d F Y');
                                    $nosrt = $pengajuan->nomor_surat ?? '—';
                                    $alias = $pengajuan->jenis
                                @endphp

                            <div class="card suket-card mb-3 border-0 shadow-sm mt-3">
                                {{-- HEAD --}}
                                <div class="suket-head">
                                    <span class="bar {{ $color }}">{{ $label }}</span>
                                </div>

                                <div class="divider-card-line"></div>

                                {{-- BODY --}}
                                <div class="card-body position-relative p-4 card-click">
                                    <h5 class="fw-bold mb-1 text-uppercase">{{ $nama }}</h5>
                                    <div class="text-muted small mb-2">
                                        No. Surat : {{ $nosrt }}
                                    </div>
                                    {{-- Link tracking (alias + id) --}}
                                    <a class="stretched-link"
                                        href="{{ route('tracking', ['jenisSurat' => $alias, 'id' => $id]) }}"
                                        aria-label="Lihat tracking {{ $nama }}">
                                    </a>
                                </div>

                                <div class="divider-card-line"></div>

                                {{-- FOOTER --}}
                                <div class="card-footer d-flex justify-content-between align-items-center px-4 py-3">
                                    <div class="text-muted small">
                                        <i class="ri-calendar-2-line me-1"></i>{{ $tgl }}
                                    </div>
                                    <div class="d-flex gap-2">
                                        {{-- Tombol Cetak Surat - Tampil jika STEP 3 (SELESAI) atau STEP 4 (DINILAI) --}}
                                        <a href="{{-- route('pengajuan.cetak', [$jenis, $id]) --}}"
                                        class="btn btn-success btn-chip">
                                            Cetak Surat
                                        </a>

                                        <x-btnlihatnilai 
                                            :alias="$alias" 
                                            :id="$id"
                                            :nosrt="$nosrt"
                                            :nama="$nama"
                                        />
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">Riwayat masih kosong.</div>
                        @endforelse
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $riwayat->withQueryString()->fragment('tab-riwayat')->onEachSide(1)->links('vendor.pagination.e-suket') }}
                        </div>
                    </div>
                </div>
            </div>

            <form id="formHapusGlobal" method="POST" data-action-template="">
                @csrf
            </form>

            {{-- Modal global --}}
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
        </section>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const panes = document.querySelectorAll('.tab-pane');
            const segA = document.getElementById('seg-a');
            const segB = document.getElementById('seg-b');
            const STORAGE_KEY = 'lastOpenedTab';

            function showPane(id) {
                panes.forEach(p => p.classList.remove('show', 'active'));
                document.getElementById(id).classList.add('show', 'active');
            }

            function saveLastTab(tabId) {
                sessionStorage.setItem(STORAGE_KEY, tabId); // ← pakai sessionStorage
            }

            segA.addEventListener('change', e => {
                if (e.target.checked) {
                    showPane('tab-proses');
                    saveLastTab('tab-proses');
                }
            });
            segB.addEventListener('change', e => {
                if (e.target.checked) {
                    showPane('tab-riwayat');
                    saveLastTab('tab-riwayat');
                }
            });

            const lastTab = sessionStorage.getItem(STORAGE_KEY); // ← juga di sini

            if (lastTab === 'tab-riwayat') {
                segB.checked = true;
                showPane('tab-riwayat');
            } else {
                segA.checked = true;
                showPane('tab-proses');
            }
        });
    </script>
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
                    location.reload();
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
        let selectedRating = 0;

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
            // isi judul & no surat
            $("#lihatJudulSurat").text(judul);
            $("#lihatNoSurat").text(noSurat);

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
