@props(['id', 'status', 'route'])

@php
    $role = auth()->user()->role_id;

    if ($role == 9 && $status == 1) {
        $routeNaik = str_replace('.edit', '.naik', $route);
    } elseif ($role == 4 && $status == 2) {
        $routeNaik = str_replace('.edit', '.naikLurah', $route);
    } else {
        $routeNaik = null;
    }
@endphp

@if ($routeNaik)
<button class="js-surat-action btn btn-warning btn-sm"
        data-url="{{ route($routeNaik, $id) }}"
        data-action="naik"
        data-method="POST"
        data-confirm="Naikkan pengajuan ini?"
        data-success="Berhasil dinaikkan."
        data-bs-title="Naikkan" title="Naikkan">
    <i class="ri-arrow-up-double-fill"></i>
</button>
@endif
