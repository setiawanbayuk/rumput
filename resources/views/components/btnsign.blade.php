@php
    $idValue = trim((string) $id);
    $nomorSuratValue = trim((string) $nomorSurat);
    $jenisValue = trim((string) $jenis);
    $statusValue = (int) trim((string) $status);
    $roleValue = (int) trim((string) $role);

    $disabled = (($statusValue !== 3) && ($roleValue === 3))
        || (($statusValue !== 8) && ($roleValue === 5));
@endphp

<button class="btn btn-primary btn-sm"
    data-id="{{ $idValue }}"
    data-no_surat="{{ $nomorSuratValue }}"
    data-jenis="{{ $jenisValue }}"
    data-role="{{ $roleValue }}"
    data-bs-toggle="modal"
    data-bs-target="#esignModal"
    @if ($disabled) disabled @endif>
    <i class="ri-edit-line" data-bs-toggle="tooltip" data-bs-title="Esign" title="Esign"></i>
</button>