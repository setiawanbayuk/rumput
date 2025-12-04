{{-- <button class="print btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-title="Preview" title="Preview"
    id="{{ $id }}" onclick="handlePreview({{ $id }})">
    <i class="ri-eye-line"></i>
</button> --}}

@props(['id', 'route'])

<button class="js-surat-action btn btn-success btn-sm"
    data-bs-toggle="tooltip"
    data-bs-title="Preview"
    title="Preview"
    data-url="{{ route(str_replace('.edit', '.preview', $route), $id) }}"
    data-action="preview"
    data-method="GET">
    <i class="ri-eye-line"></i>
</button>
