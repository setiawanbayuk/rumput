<button class="btn btn-primary btn-sm"
    data-id="{{ $id }}"
    data-no_surat="{{ $nomorSurat }}"
    data-jenis="{{ $jenis }}"
    data-role="{{ $role }}"
    data-bs-toggle="modal"
    data-bs-target="#esignModal"
    @if (($status != '3' && $role == '3') || ($status != '8' && $role == '5')) disabled @endif>
    <i class="ri-edit-line" data-bs-toggle="tooltip" data-bs-title="Esign" title="Esign"></i>
</button>