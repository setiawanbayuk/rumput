@php
    $submitterType = strtolower(trim((string) ($submitter_type ?? (((int) $status === 0) ? 'warga' : 'admin'))));
    $isWarga = $submitterType === 'warga';
    $isManual = !empty($manual_signature);
    $hasProof = !empty($bukti_ttd_basah);
    $isManualFinal = $isManual && $hasProof;

    // Tombol respon TTD Basah hanya untuk Admin Kelurahan role_id 1.
    // Tidak boleh muncul di Sekkel, Lurah, Sekcam, Camat, RT/RW, atau role lain.
    $currentRole = (int) optional(auth()->user())->role_id;
    $isAdminKelurahan = $currentRole === 1;

    // SKTM status 4/11/8 masih tahap monitoring/naik Sekcam/Camat, jadi admin hanya boleh View.
    // Cetak hanya saat final benar-benar selesai: status 9. Non-SKTM status 4 tetap final seperti alur lama.
    $statusValue = (int) ($status ?? 0);
    $jenisValue = strtolower(trim((string) ($jenis ?? $jenis_surat ?? '')));
    // Kolom Action Admin Kelurahan menerima flag revisi SKTM dari controller,
    // sedangkan variabel `jenis` tidak selalu ikut dikirim ke partial ini.
    // Flag tersebut khusus SKTM yang sudah TTE Lurah dan diturunkan sampai Admin Kelurahan.
    $isSktm = str_contains($jenisValue, 'sktm') || !empty($isRevisiBuktiSktm);
    $isFinalCetak = ($statusValue === 9) || ($statusValue === 4 && !$isSktm);
@endphp

<div class="d-flex gap-1 flex-wrap align-items-center justify-content-center">
    {!! $catatanHtml ?? '' !!}
    @if ($isManualFinal)
        <button class="js-surat-action btn btn-secondary btn-sm" data-url="{{ route(str_replace('.edit', '.previewBasah', $route), $id) }}" data-bukti-url="{{ route('bukti-pendukung.show', [($route ?? '') === 'admin.surat.edit' ? 'pengajuan' : explode('.', (string) $route)[0], $id]) }}" data-action="preview" data-method="GET" title="Preview TTD Basah"><i class="ri-file-paper-2-line"></i></button>

        @if ($isAdminKelurahan && $isWarga && (int) $status !== 6)
            <button
                class="js-surat-action btn btn-danger btn-sm"
                data-url="{{ route(str_replace('.edit', '.tolak', $route), $id) }}"
                data-action="respon_ttd_basah_warga"
                data-method="POST"
                data-confirm="Kirim respon TTD Basah ke warga? Status online warga akan menjadi Ditolak dan alasan ini akan tampil seperti penolakan."
                data-prompt="Keterangan untuk warga"
                data-prompt-required="1"
                data-success="Respon TTD Basah berhasil dikirim ke warga."
                title="Kirim Respon ke Warga">
                <i class="ri-chat-check-line"></i>
            </button>
        @endif
    @elseif ($isManual && !$hasProof)
        <button class="js-surat-action btn btn-secondary btn-sm" data-url="{{ route(str_replace('.edit', '.previewBasah', $route), $id) }}" data-bukti-url="{{ route('bukti-pendukung.show', [($route ?? '') === 'admin.surat.edit' ? 'pengajuan' : explode('.', (string) $route)[0], $id]) }}" data-action="preview" data-method="GET" title="Preview TTD Basah"><i class="ri-file-paper-2-line"></i></button>
        <x-btnedit><x-slot:route>{{ $route }}</x-slot:route><x-slot:id>{{ $id }}</x-slot:id></x-btnedit>
        @if ($isAdminKelurahan && \Illuminate\Support\Facades\Route::has('admin.surat.uploadBuktiBasah'))
            <button class="js-surat-action btn btn-success btn-sm" data-url="{{ route('admin.surat.uploadBuktiBasah', $id) }}" data-action="upload_bukti_basah" data-method="POST" data-success="Bukti TTD Basah berhasil diupload." title="Upload Bukti TTD Basah"><i class="ri-upload-cloud-2-line"></i></button>
        @endif
    @elseif (in_array((int) $status, [0, 1], true))
        @if ($isSktm && !empty($isRevisiBuktiSktm) && $statusValue === 1)
            <button type="button" class="btn btn-outline-success btn-sm js-tambah-bukti-sktm"
                data-url="{{ route('admin.surat.revisiBuktiSktm', $id) }}"
                title="Lengkapi Bukti Pendukung SKTM" aria-label="Lengkapi Bukti Pendukung SKTM"><i class="ri-upload-cloud-2-line"></i></button>
        @endif
        <x-btnnaik :id="$id" :route="$route" :status="$status" />
        @if ($isWarga && empty($isRevisiBuktiSktm))
            <button class="js-surat-action btn btn-danger btn-sm" data-url="{{ route(str_replace('.edit', '.tolak', $route), $id) }}" data-action="tolak" data-method="POST" data-confirm="Tolak pengajuan surat ini?" data-prompt="Alasan penolakan" data-prompt-required="1" data-success="Pengajuan berhasil ditolak." title="Tolak"><i class="ri-close-circle-line"></i></button>
        @endif
        @if (empty($isRevisiBuktiSktm))
            <button class="js-surat-action btn btn-danger btn-sm" data-url="{{ route(str_replace('.edit', '.hapus', $route), $id) }}" data-action="hapus" data-method="DELETE" data-confirm="Hapus surat ini?" data-success="Surat berhasil dihapus." title="Hapus"><i class="ri-delete-bin-6-line"></i></button>
            <x-btnedit><x-slot:route>{{ $route }}</x-slot:route><x-slot:id>{{ $id }}</x-slot:id></x-btnedit>
        @endif
        <x-btnpreview :id="$id" :route="$route" />
        @if (empty($isRevisiBuktiSktm))
            <button class="js-surat-action btn btn-secondary btn-sm" data-url="{{ route(str_replace('.edit', '.previewBasah', $route), $id) }}" data-action="preview_basah" data-method="GET" data-confirm="Jika memilih TTD Basah, surat akan ditandai sebagai TTD Basah dan Anda wajib upload bukti TTD Basah melalui tombol Upload Bukti di sebelah Edit. Lanjutkan?" title="Preview TTD Basah"><i class="ri-file-paper-2-line"></i></button>
        @endif
    @elseif ($isFinalCetak)
        <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
    @else
        <x-btnpreview :id="$id" :route="$route" />
    @endif
</div>
