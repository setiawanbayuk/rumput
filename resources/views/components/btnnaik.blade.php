@props(['id', 'status', 'route'])

@php
    use Illuminate\Support\Str;

    $user = auth()->user();
    $role = (int) $user->role_id;

    $routeNaik = null;
    $confirmText = null;
    $successText = 'Pengajuan berhasil diajukan.';
    $titleText = 'Ajukan ke Atasan';

    // Admin / RT / Super admin -> ajukan ke Sekkel
    if (in_array($role, [1, 8, 9]) && in_array((int) $status, [0, 1], true)) {
        $routeNaik = Str::replaceLast('.edit', '.naik', $route);
        $confirmText = 'Apakah Anda yakin ingin mengajukan pengajuan ini ke atasan yang lebih tinggi?';
        $successText = 'Pengajuan berhasil diajukan ke atasan.';
        $titleText = 'Ajukan ke Atasan';
    }

    // Verifikator kelurahan -> ajukan ke Lurah
    elseif ($role === 4 && in_array((int) $status, [1, 2])) {
        $routeNaik = Str::replaceLast('.edit', '.naikLurah', $route);
        $confirmText = 'Apakah Anda yakin ingin mengajukan pengajuan ini ke Lurah?';
        $successText = 'Pengajuan berhasil diajukan ke Lurah.';
        $titleText = 'Ajukan ke Lurah';
    }

    // KaOPD/Kelurahan lebih tinggi -> ajukan ke level lebih tinggi
    elseif (in_array($role, [3, 5]) && in_array((int) $status, [2, 3, 8])) {
        $routeNaik = Str::replaceLast('.edit', '.naik', $route);
        $confirmText = 'Apakah Anda yakin ingin mengajukan pengajuan ini ke atasan yang lebih tinggi?';
        $successText = 'Pengajuan berhasil diajukan ke atasan yang lebih tinggi.';
        $titleText = 'Ajukan ke Atasan';
    }
@endphp

@if ($routeNaik)
    <button
        type="button"
        class="js-surat-action btn btn-warning btn-sm"
        data-url="{{ route($routeNaik, $id) }}"
        data-action="naik"
        data-method="POST"
        data-confirm="{{ $confirmText }}"
        data-success="{{ $successText }}"
        data-bs-toggle="tooltip"
        title="{{ $titleText }}"
    >
        <i class="ri-arrow-up-double-fill"></i>
    </button>
@endif