@extends('layouts.main')

@section('title', 'Bukti Pendukung')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom px-4 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h4 class="fw-bold mb-1">Bukti Pendukung</h4>
                <div class="small text-muted">{{ strtoupper($surat->jenis_surat ?? $sumber) }} • Pengajuan #{{ $surat->id }}</div>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.close()">Tutup</button>
        </div>
        <div class="card-body p-4">
            @php
                $lama = trim((string) ($surat->pengantar ?? ''));
                $total = ($lama !== '' ? 1 : 0) + $lampiran->count();
            @endphp
            <p class="text-muted mb-3">{{ $total }} file bukti pendukung</p>
            @if ($total === 0)
                <div class="alert alert-light border mb-0">Belum ada Bukti Pendukung pada pengajuan lama ini.</div>
            @else
                <div class="row g-3">
                    @if ($lama !== '')
                        @php $pdfLama = str_ends_with(strtolower(parse_url($lama, PHP_URL_PATH) ?: $lama), '.pdf'); @endphp
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('bukti-pendukung.legacy-file', [$sumber, $surat->id]) }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                <div class="card h-100 border rounded-3 overflow-hidden">
                                    @if ($pdfLama)
                                        <div class="d-flex align-items-center justify-content-center bg-light" style="height:180px"><i class="ri-file-pdf-line fs-1 text-danger"></i></div>
                                    @else
                                        <img src="{{ route('bukti-pendukung.legacy-file', [$sumber, $surat->id]) }}" alt="Bukti Pendukung 1" class="w-100" loading="lazy" style="height:180px; object-fit:contain; background:#f8f9fa">
                                    @endif
                                    <div class="card-body p-2 small text-dark"><i class="ri-attachment-2 me-1"></i>Bukti Pendukung 1</div>
                                </div>
                            </a>
                        </div>
                    @endif
                    @foreach ($lampiran as $bukti)
                        @php
                            $urlFile = route('bukti-pendukung.file', [$sumber, $surat->id, $bukti->id]);
                            $isPdf = $bukti->mime_type === 'application/pdf';
                            $index = $loop->iteration + ($lama !== '' ? 1 : 0);
                        @endphp
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ $urlFile }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                <div class="card h-100 border rounded-3 overflow-hidden">
                                    @if ($isPdf)
                                        <div class="d-flex align-items-center justify-content-center bg-light" style="height:180px"><i class="ri-file-pdf-line fs-1 text-danger"></i></div>
                                    @else
                                        <img src="{{ $urlFile }}" alt="Bukti Pendukung {{ $index }}" class="w-100" loading="lazy" style="height:180px; object-fit:contain; background:#f8f9fa">
                                    @endif
                                    <div class="card-body p-2 small text-dark text-truncate" title="{{ $bukti->nama_asli }}"><i class="ri-attachment-2 me-1"></i>{{ $bukti->nama_asli ?: 'Bukti Pendukung '.$index }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
