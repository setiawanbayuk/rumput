{{-- Fragmen UI saja; tidak disisipkan ke file PDF maupun proses TTE. --}}
@php
    $lama = trim((string) ($surat->pengantar ?? ''));
    $total = ($lama !== '' ? 1 : 0) + $lampiran->count();
@endphp
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div class="fw-semibold"><i class="ri-attachment-2 me-1"></i>Bukti Pendukung</div>
    <span class="badge bg-light text-secondary border">{{ $total }} berkas</span>
</div>
@if($total === 0)
    <div class="text-muted small border rounded-3 p-3">Belum ada Bukti Pendukung pada surat ini.</div>
@else
    <div class="row g-2">
        @if($lama !== '')
            @php
                $urlLama = route('bukti-pendukung.legacy-file', [$sumber, $surat->id]);
                $pdfLama = str_ends_with(strtolower(parse_url($lama, PHP_URL_PATH) ?: $lama), '.pdf');
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <button type="button" class="btn p-0 text-start w-100 h-100 js-esuket-bukti-file"
                        data-file-url="{{ $urlLama }}" data-file-kind="{{ $pdfLama ? 'pdf' : 'image' }}"
                        data-file-title="Bukti Pendukung 1" title="Lihat Bukti Pendukung 1">
                    <span class="border rounded-3 d-block overflow-hidden h-100 bg-light">
                        @if($pdfLama)
                            <span class="d-flex justify-content-center align-items-center" style="height:125px"><i class="ri-file-pdf-2-line fs-1 text-danger"></i></span>
                        @else
                            <img src="{{ $urlLama }}" alt="Bukti Pendukung 1" loading="lazy" class="w-100"
                                 style="height:125px;object-fit:contain;background:#f8fafc">
                        @endif
                        <span class="d-block p-2 small text-dark text-truncate">Bukti Pendukung 1</span>
                    </span>
                </button>
            </div>
        @endif
        @foreach($lampiran as $bukti)
            @php
                $urlBukti = route('bukti-pendukung.file', [$sumber, $surat->id, $bukti->id]);
                $isPdf = $bukti->mime_type === 'application/pdf';
                $index = $loop->iteration + ($lama !== '' ? 1 : 0);
                $namaBukti = $bukti->nama_asli ?: 'Bukti Pendukung '.$index;
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <button type="button" class="btn p-0 text-start w-100 h-100 js-esuket-bukti-file"
                        data-file-url="{{ $urlBukti }}" data-file-kind="{{ $isPdf ? 'pdf' : 'image' }}"
                        data-file-title="{{ $namaBukti }}" title="Lihat {{ $namaBukti }}">
                    <span class="border rounded-3 d-block overflow-hidden h-100 bg-light">
                        @if($isPdf)
                            <span class="d-flex justify-content-center align-items-center" style="height:125px"><i class="ri-file-pdf-2-line fs-1 text-danger"></i></span>
                        @else
                            <img src="{{ $urlBukti }}" alt="Bukti Pendukung {{ $index }}" loading="lazy" class="w-100"
                                 style="height:125px;object-fit:contain;background:#f8fafc">
                        @endif
                        <span class="d-block p-2 small text-dark text-truncate" title="{{ $namaBukti }}">{{ $namaBukti }}</span>
                    </span>
                </button>
            </div>
        @endforeach
    </div>
    <p class="small text-muted mt-2 mb-0">Pilih gambar atau PDF untuk memperbesar, tanpa berpindah halaman.</p>
@endif
