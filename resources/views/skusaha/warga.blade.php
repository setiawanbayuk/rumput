@extends('layouts.page')

@section('title', 'Surat Keterangan')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
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
                border-radius: 2rem;
                padding: .35rem 2rem;
            }
        </style>
    @endpush

    <div class="container py-3">
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
        <div class="seg seg-3">
            <!-- radio disembunyikan -->
            <input type="radio" name="seg" id="seg-detail">
            <input type="radio" name="seg" id="seg-proses" checked>
            <input type="radio" name="seg" id="seg-riwayat">

            <div class="seg-wrap">
                <label for="seg-detail" class="seg-btn">Detail</label>
                <label for="seg-proses" class="seg-btn">Sedang Proses</label>
                <label for="seg-riwayat" class="seg-btn">Riwayat</label>
                <span class="seg-indicator"></span>
            </div>
        </div>

        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="tab-detail" role="tabpanel">
                <x-detail-surat>
                    <x-slot:title>{{ $title }}</x-slot:title>
                    <x-slot:detail>{!! $detail_surat[0]->detail !!}</x-slot:detail>
                    <x-slot:persyaratan>{!! $detail_surat[0]->persyaratan !!}</x-slot:persyaratan>
                </x-detail-surat>
                <div class="text-center">
                    <a href="{{ route('skusaha.create') }}" class="btn-ajukan">
                        Ajukan
                    </a>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-proses" role="tabpanel">
                <div class="container rounded-3 px-4 py-2" style="background:rgba(174,160,122,.25)">
                    @forelse($sedangProses as $pengajuan)
                        @php
                            $ui = $pengajuan->ui_status;
                            $step = $ui['step'];
                            $bar = $ui['color_class'];
                            $label = $ui['label'];
                            $nosrt  = $pengajuan->nomor_surat ?? '—';
                            $tgl = optional($pengajuan->created_at)->translatedFormat('d F Y');
                            $alias  = $pengajuan->jenisSurat ?? 'skusaha';
                            $id     = $pengajuan->id_surat ?? $pengajuan->id;
                        @endphp

                        {{-- strip header + body persis seperti mockup "Sedang Proses" --}}
                        <div class="card suket-card mb-3 border-0 shadow-sm mt-3">
                            {{-- HEAD: pill status + no surat --}}
                            <div class="suket-head">
                                <span class="bar {{ $ui['color_class'] }}">{{ $ui['label'] }}</span>
                            </div>

                            <div class="divider-card-line"></div>

                            {{-- BODY: bisa diklik (stretched-link di dalamnya) --}}
                            <div class="card-body position-relative p-4 card-click">
                                <h5 class="fw-bold mb-1 text-uppercase">{{ $nama }}</h5>
                                <div class="text-muted small mb-2">No. Surat : {{ $nosrt }}</div>

                                {{-- link tak terlihat yang membentang di area body --}}
                                <a class="stretched-link"
                                    href="{{ route('tracking', ['jenisSurat' => $alias, 'id' => $id]) }}"
                                    aria-label="Lihat tracking">
                                </a>
                            </div>

                            <div class="divider-card-line"></div>

                            {{-- FOOTER: tanggal kiri, tombol kanan, tidak ikut klik --}}
                            <div class="card-footer d-flex justify-content-between align-items-center px-4 py-3">
                                <div class="text-muted small">
                                    <i class="ri-calendar-2-line me-1"></i>{{ $tgl }}
                                </div>

                                <div class="d-flex gap-2">
                                    {{-- STEP 1 & 2: Tombol PREVIEW --}}
                                    @if ($step == 1 || $step == 2)
                                        <a href="{{ route($alias.'.show', ['id' => $id]) }}"
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
                    {{ $sedangProses
                        ->withQueryString()       // pertahankan ?q=...
                        ->fragment('tab-proses')  // balik ke tab-proses saat paging
                        ->onEachSide(1)
                        ->links('vendor.pagination.e-suket') }}
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-riwayat" role="tabpanel">
                <div class="container rounded-3 px-4 py-2" style="background:rgba(174,160,122,.25)">
                    @forelse($riwayat as $pengajuan)
                        @php
                            $ui = $pengajuan->ui_status;
                            $step = $ui['step'];
                            $bar = $ui['color_class'];
                            $label = $ui['label'];
                            $nosrt  = $pengajuan->nomor_surat ?? '—';
                            $tgl = optional($pengajuan->created_at)->translatedFormat('d F Y');
                            $alias  = $pengajuan->jenisSurat ?? 'skusaha';
                            $id     = $pengajuan->id_surat ?? $pengajuan->id;
                        @endphp

                        <div class="card suket-card mb-3 border-0 shadow-sm mt-3">
                            {{-- HEAD: pill status + no surat --}}
                            <div class="suket-head">
                                <span class="bar {{ $ui['color_class'] }}">{{ $ui['label'] }}</span>
                            </div>

                            <div class="divider-card-line"></div>

                            {{-- BODY: bisa diklik --}}
                            <div class="card-body position-relative p-4 card-click">
                                <h5 class="fw-bold mb-1 text-uppercase">{{ $nama }}</h5>
                                <div class="text-muted small mb-2">No. Surat : {{ $nosrt }}</div>

                                {{-- link tak terlihat yang membentang di area body --}}
                                <a class="stretched-link"
                                    href="{{ route('tracking', ['jenisSurat' => $alias, 'id' => $id]) }}"
                                    aria-label="Lihat tracking">
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
                    {{ $riwayat
                        ->withQueryString()
                        ->fragment('tab-riwayat')
                        ->onEachSide(1)
                        ->links('vendor.pagination.e-suket') }}
                    </div>
                </div>
            </div>
            {{-- Form global untuk hapus Skusaha (akan diisi otomatis oleh komponen) --}}
            <form id="formHapusSkusaha"
                method="POST"
                data-action-template="{{ route('skusaha.hapus', ['id' => '__ID__']) }}">
                @csrf
                {{-- hidden "id" akan dibuat/diisi otomatis oleh komponen saat modal dibuka --}}
            </form>
            <x-btn-hapus
                modalId="modalHapusSkusaha"
                formId="formHapusSkusaha"
                title="Anda yakin ingin membatalkan dan menghapus pengajuan surat?"
                message="Tindakan ini akan menghapus data pengajuan Anda. Surat ini tidak akan diproses dan akan hilang dari riwayat pengajuan Anda."
                cancelText="Batal"
                confirmText="Hapus"
            />
        </div>
    </div>

    {{-- <x-esign></x-esign> --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const panes = document.querySelectorAll('.tab-pane');
                const STORAGE_KEY = 'lastOpenedTabSkusaha';

                const map = {
                    'seg-detail': 'tab-detail',
                    'seg-proses': 'tab-proses',
                    'seg-riwayat': 'tab-riwayat'
                };

                function showPane(id) {
                    panes.forEach(p => p.classList.remove('show', 'active'));
                    document.getElementById(id)?.classList.add('show', 'active');
                    sessionStorage.setItem(STORAGE_KEY, id);
                }

                // pasang listener singkat
                Object.keys(map).forEach(segId => {
                    document.getElementById(segId)?.addEventListener('change', e => {
                        if (e.target.checked) showPane(map[segId]);
                    });
                });

                // restore: tentukan segmen dari lastTab
                const lastTab = sessionStorage.getItem(STORAGE_KEY);
                const segIdFromLast = Object.keys(map).find(k => map[k] === lastTab);

                // fallback ke 'proses' jika null/invalid
                const segToCheck = segIdFromLast || 'seg-proses';
                const tabToShow = map[segToCheck];

                // PENTING: tandai radio dulu, baru tampilkan pane
                const segEl = document.getElementById(segToCheck);
                if (segEl) segEl.checked = true;
                showPane(tabToShow);
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modalEl = document.getElementById('modalHapusSkusaha');
                const modal   = new bootstrap.Modal(modalEl);
                const form    = document.getElementById('formHapusSkusaha');
                const submit  = document.getElementById('btnHapusSkusaha');

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
    @endpush
    <x-nilai />
    <x-lihatnilai />
@endsection