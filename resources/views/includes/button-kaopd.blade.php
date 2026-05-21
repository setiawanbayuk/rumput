@php
    $isManual = !empty($manual_signature);
    $hasProof = !empty($bukti_ttd_basah);
    $roleValue = (int) ($role ?? 0);
    $statusValue = (int) ($status ?? 0);
    $jenisValue = strtolower(trim((string) ($jenis ?? $jenis_surat ?? '')));
    $isSktm = str_contains($jenisValue, 'sktm');

    // SKTM status 4 = sudah TTE Lurah, tetapi belum final karena wajib naik ke Sekcam lalu TTE Camat.
    $isFinalCetak = ($statusValue === 9) || ($statusValue === 4 && !$isSktm);
    $routeNaik = route(str_replace('.edit', '.naik', $route), $id);
@endphp
<div class="d-flex gap-1 flex-wrap">
    @if ($isManual && $hasProof)
        <button class="js-surat-action btn btn-secondary btn-sm" data-url="{{ route(str_replace('.edit', '.previewBasah', $route), $id) }}" data-action="preview" data-method="GET" title="Preview TTD Basah"><i class="ri-file-paper-2-line"></i></button>
        <a href="{{ asset($bukti_ttd_basah) }}" target="_blank" class="btn btn-success btn-sm" title="Preview Bukti Upload"><i class="ri-attachment-2"></i></a>
    @else
        @if ($roleValue === 3)
            {{-- Lurah: sebelum TTE tetap proses lama, boleh TTE dan boleh turunkan ke Sekkel. --}}
            @if ($statusValue === 3)
                <button class="js-surat-action btn btn-danger btn-sm" data-url="{{ route(str_replace('.edit', '.turunkan', $route), $id) }}" data-action="turunkan" data-method="POST" data-confirm="Turunkan surat ini kembali ke Sekkel?" data-success="Surat berhasil diturunkan ke Sekkel." title="Turunkan"><i class="ri-arrow-down-double-line"></i></button>
                <x-btnsign><x-slot:id>{{ $id }}</x-slot:id><x-slot:nomorSurat>{{ $nomorSurat }}</x-slot:nomorSurat><x-slot:status>{{ $status }}</x-slot:status><x-slot:jenis>{{ $jenis }}</x-slot:jenis><x-slot:role>{{ $role }}</x-slot:role></x-btnsign>
            @endif

            {{-- Khusus SKTM: setelah TTE Lurah berhasil, jangan tampilkan Turunkan/Cetak. Wajib tampil Naikkan ke Sekcam. --}}
            @if ($isSktm && $statusValue === 4)
                <button type="button"
                    class="btn btn-primary btn-sm btn-naik-surat"
                    data-url="{{ $routeNaik }}"
                    data-title="Naikkan SKTM ini ke Sekretaris Camat?"
                    title="Naikkan ke Sekretaris Camat">
                    <i class="ri-arrow-up-double-line"></i>
                </button>
            @endif
        @endif

        @if ($roleValue === 5)
            @if ($isSktm && $statusValue === 8)
                <button class="js-surat-action btn btn-danger btn-sm" data-url="{{ route(str_replace('.edit', '.turunkan', $route), $id) }}" data-action="turunkan" data-method="POST" data-confirm="Turunkan SKTM ini kembali ke Sekretaris Camat?" data-success="SKTM berhasil diturunkan ke Sekretaris Camat." title="Turunkan"><i class="ri-arrow-down-double-line"></i></button>
                <x-btnsign><x-slot:id>{{ $id }}</x-slot:id><x-slot:nomorSurat>{{ $nomorSurat }}</x-slot:nomorSurat><x-slot:status>{{ $status }}</x-slot:status><x-slot:jenis>{{ $jenis }}</x-slot:jenis><x-slot:role>{{ $role }}</x-slot:role></x-btnsign>
            @endif
        @endif

        @if ($isFinalCetak)
            <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
        @else
            <x-btnpreview :id="$id" :route="$route" />
        @endif
    @endif
</div>
