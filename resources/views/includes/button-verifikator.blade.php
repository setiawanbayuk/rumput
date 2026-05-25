@php
    $isManual = !empty($manual_signature);
    $hasProof = !empty($bukti_ttd_basah);
    $roleValue = (int) ($role ?? 0);
    $statusValue = (int) ($status ?? 0);
    $jenisValue = strtolower(trim((string) ($jenis ?? $jenis_surat ?? '')));
    $isSktm = str_contains($jenisValue, 'sktm');

    // SKTM status 4/11/8 belum final. Final SKTM hanya status 9 setelah TTE Camat.
    $isFinalCetak = ($statusValue === 9) || ($statusValue === 4 && !$isSktm);
    $routeNaik = route(str_replace('.edit', '.naik', $route), $id);
@endphp
<div class="d-flex gap-1 flex-wrap">
    @if ($isManual && $hasProof)
        <button class="js-surat-action btn btn-secondary btn-sm" data-url="{{ route(str_replace('.edit', '.previewBasah', $route), $id) }}" data-action="preview" data-method="GET" title="Preview TTD Basah"><i class="ri-file-paper-2-line"></i></button>
        <a href="{{ asset($bukti_ttd_basah) }}" target="_blank" class="btn btn-success btn-sm" title="Preview Bukti Upload"><i class="ri-attachment-2"></i></a>
    @else
        {{-- Sekkel: proses lama tetap. --}}
        @if ($roleValue === 4 && $statusValue === 2)
            <x-btnnaik :id="$id" :status="$status" :route="$route" />
            <button class="js-surat-action btn btn-danger btn-sm" data-url="{{ route(str_replace('.edit', '.turunkan', $route), $id) }}" data-action="turunkan" data-method="POST" data-confirm="Turunkan surat ini kembali ke akun pembuat?" data-success="Surat berhasil diturunkan ke akun pembuat." title="Turunkan"><i class="ri-arrow-down-double-line"></i></button>
        @endif

        {{-- Sekretaris Camat: SKTM dari Lurah status 11 wajib tampil tombol Naikkan ke Camat. --}}
        @if ($roleValue === 6 && $isSktm && $statusValue === 11)
            <button class="js-surat-action btn btn-danger btn-sm" data-url="{{ route(str_replace('.edit', '.turunkan', $route), $id) }}" data-action="turunkan" data-method="POST" data-confirm="Turunkan SKTM ini kembali ke Lurah?" data-success="SKTM berhasil diturunkan ke Lurah." title="Turunkan ke Lurah"><i class="ri-arrow-down-double-line"></i></button>
            <button type="button"
                class="btn btn-primary btn-sm btn-naik-surat"
                data-url="{{ $routeNaik }}"
                data-title="Naikkan SKTM ini ke Camat untuk TTE?"
                data-register-kecamatan="1"
                title="Naikkan ke Camat">
                <i class="ri-arrow-up-double-line"></i>
            </button>
        @endif

        @if ($isFinalCetak)
            <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
        @else
            <x-btnpreview :id="$id" :route="$route" />
        @endif
    @endif
</div>
