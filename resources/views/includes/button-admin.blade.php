@php
    $role = auth()->user()->role_id;
@endphp

<div class="d-flex gap-1">
    @if ((int) $status === 1)
        <x-btnnaik :id="$id" :route="$route" :status="$status" />

        <button class="js-surat-action btn btn-danger btn-sm"
                data-url="{{ route(str_replace('.edit', '.hapus', $route), $id) }}"
                data-action="hapus"
                data-method="DELETE"
                data-confirm="Hapus draft surat ini?"
                data-success="Draft surat berhasil dihapus."
                data-bs-title="Hapus" title="Hapus">
            <i class="ri-delete-bin-6-line"></i>
        </button>

        <x-btnedit>
            <x-slot:route>{{ $route }}</x-slot:route>
            <x-slot:id>{{ $id }}</x-slot:id>
        </x-btnedit>

        <x-btnpreview :id="$id" :route="$route" />
    @elseif (in_array((int) $status, [4, 9]))
        <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
    @else
        <x-btnpreview :id="$id" :route="$route" />
    @endif
</div>
