@props(['id', 'status', 'route'])

@php
    $user = auth()->user();
    $role = $user->role_id;

    $routeNaik = null;

    if ($role == 4 && $status == 1) {
        $routeNaik = Str::replaceLast('.edit', '.naikLurah', $route);
    }
@endphp

@if ($routeNaik)
    <button type="button" class="js-surat-action btn btn-warning btn-sm" data-url="{{ route($routeNaik, $id) }}"
        data-action="naik" data-method="POST" data-confirm="Apakah Anda yakin ingin menaikkan pengajuan ini ke Lurah?"
        data-success="Pengajuan berhasil dinaikkan." data-bs-toggle="tooltip" title="Naikkan ke Lurah">
        <i class="ri-arrow-up-double-fill"></i>
    </button>
@endif
