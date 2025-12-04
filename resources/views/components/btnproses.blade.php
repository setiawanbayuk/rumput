@props(['id', 'route'])

<button class="js-surat-action btn btn-primary btn-sm"
        data-url="{{ route(str_replace('.edit', '.proses', $route), $id) }}"
        data-action="proses"
        data-method="POST"
        data-confirm="Proses pengajuan ini?"
        data-success="Berhasil diproses">
    <i class="ri-arrow-up-double-fill"></i>
</button>
