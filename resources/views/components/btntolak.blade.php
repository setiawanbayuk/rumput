@props(['id', 'route'])

<button class="js-surat-action btn btn-danger btn-sm"
        data-url="{{ route(str_replace('.edit', '.tolak', $route), $id) }}"
        data-action="tolak"
        data-method="POST"
        data-confirm="Tolak pengajuan ini?"
        data-success="Pengajuan ditolak"
        data-bs-title="Tolak" title="Tolak">
    <i class="ri-delete-bin-6-line"></i>
</button>
