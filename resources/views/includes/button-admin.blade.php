@php
    $submitterType = $submitter_type ?? (((int) $status === 0) ? 'warga' : 'admin');
    $isWarga = $submitterType === 'warga';
    $isManual = !empty($manual_signature);
    $hasProof = !empty($bukti_ttd_basah);
    $isManualFinal = $isManual && $hasProof;
@endphp

<div class="d-flex gap-1 flex-wrap justify-content-center">
    @if ($isManualFinal)
        <button class="js-surat-action btn btn-secondary btn-sm" data-url="{{ route(str_replace('.edit', '.previewBasah', $route), $id) }}" data-action="preview" data-method="GET" title="Preview TTD Basah"><i class="ri-file-paper-2-line"></i></button>
        <a href="{{ asset($bukti_ttd_basah) }}" target="_blank" class="btn btn-success btn-sm" title="Preview Bukti Upload"><i class="ri-attachment-2"></i></a>
    @elseif (in_array((int) $status, [0, 1], true))
        <x-btnnaik :id="$id" :route="$route" :status="$status" />
        @if ($isWarga)
            <button class="js-surat-action btn btn-danger btn-sm" data-url="{{ route(str_replace('.edit', '.tolak', $route), $id) }}" data-action="tolak" data-method="POST" data-confirm="Tolak pengajuan surat ini?" data-prompt="Alasan penolakan" data-prompt-required="1" data-success="Pengajuan berhasil ditolak." title="Tolak"><i class="ri-close-circle-line"></i></button>
        @endif
        <button class="js-surat-action btn btn-danger btn-sm" data-url="{{ route(str_replace('.edit', '.hapus', $route), $id) }}" data-action="hapus" data-method="DELETE" data-confirm="Hapus surat ini?" data-success="Surat berhasil dihapus." title="Hapus"><i class="ri-delete-bin-6-line"></i></button>
        <x-btnedit><x-slot:route>{{ $route }}</x-slot:route><x-slot:id>{{ $id }}</x-slot:id></x-btnedit>
        <x-btnpreview :id="$id" :route="$route" />
        <button class="js-surat-action btn btn-secondary btn-sm" data-url="{{ route(str_replace('.edit', '.previewBasah', $route), $id) }}" data-action="preview_basah" data-method="GET" data-confirm="Jika memilih TTD Basah, surat akan ditandai sebagai TTD Basah dan Anda wajib upload bukti TTD Basah di menu Edit. Lanjutkan?" title="Preview TTD Basah"><i class="ri-file-paper-2-line"></i></button>
    @elseif (in_array((int) $status, [4, 9], true))
        <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
    @else
        <x-btnpreview :id="$id" :route="$route" />
    @endif
</div>
