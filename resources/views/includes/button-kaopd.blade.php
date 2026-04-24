@php
    $isManual = !empty($manual_signature);
    $hasProof = !empty($bukti_ttd_basah);
@endphp
<div class="d-flex gap-1 flex-wrap">
    @if ($isManual && $hasProof)
        <button class="js-surat-action btn btn-secondary btn-sm" data-url="{{ route(str_replace('.edit', '.previewBasah', $route), $id) }}" data-action="preview" data-method="GET" title="Preview TTD Basah"><i class="ri-file-paper-2-line"></i></button>
        <a href="{{ asset($bukti_ttd_basah) }}" target="_blank" class="btn btn-success btn-sm" title="Preview Bukti Upload"><i class="ri-attachment-2"></i></a>
    @else
        @if ((int) $role === 3)
            @if ((int) $status === 3 && $jenis === 'sktm')
                <x-btnnaik :id="$id" :status="$status" :route="$route" />
            @endif
            @if ((int) $status === 3)
                <button class="js-surat-action btn btn-danger btn-sm" data-url="{{ route(str_replace('.edit', '.turunkan', $route), $id) }}" data-action="turunkan" data-method="POST" data-confirm="Turunkan surat ini kembali ke Sekkel?" data-success="Surat berhasil diturunkan ke Sekkel." title="Turunkan"><i class="ri-arrow-down-double-line"></i></button>
                <x-btnsign><x-slot:id>{{ $id }}</x-slot:id><x-slot:nomorSurat>{{ $nomorSurat }}</x-slot:nomorSurat><x-slot:status>{{ $status }}</x-slot:status><x-slot:jenis>{{ $jenis }}</x-slot:jenis><x-slot:role>{{ $role }}</x-slot:role></x-btnsign>
            @endif
        @endif
        @if ((int) $role === 5)
            @if ((int) $status === 8)
                <button class="js-surat-action btn btn-danger btn-sm" data-url="{{ route(str_replace('.edit', '.turunkan', $route), $id) }}" data-action="turunkan" data-method="POST" data-confirm="Turunkan surat ini kembali ke Lurah?" data-success="Surat berhasil diturunkan ke Lurah." title="Turunkan"><i class="ri-arrow-down-double-line"></i></button>
                <x-btnsign><x-slot:id>{{ $id }}</x-slot:id><x-slot:nomorSurat>{{ $nomorSurat }}</x-slot:nomorSurat><x-slot:status>{{ $status }}</x-slot:status><x-slot:jenis>{{ $jenis }}</x-slot:jenis><x-slot:role>{{ $role }}</x-slot:role></x-btnsign>
            @endif
        @endif
        @if (in_array((int) $status, [4, 9]))
            <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
        @else
            <x-btnpreview :id="$id" :route="$route" />
        @endif
    @endif
</div>
