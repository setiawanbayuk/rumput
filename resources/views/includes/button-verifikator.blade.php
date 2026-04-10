<div class="d-flex gap-1">
    @if ((int) $role === 4 && (int) $status === 2)
        <x-btnnaik :id="$id" :status="$status" :route="$route" />

        <button class="js-surat-action btn btn-danger btn-sm"
                data-url="{{ route(str_replace('.edit', '.turunkan', $route), $id) }}"
                data-action="turunkan"
                data-method="POST"
                data-confirm="Turunkan surat ini kembali ke akun pembuat?"
                data-success="Surat berhasil diturunkan ke akun pembuat."
                data-bs-title="Turunkan" title="Turunkan">
            <i class="ri-arrow-down-double-line"></i>
        </button>
    @endif

    @if (in_array((int) $status, [4, 9]))
        <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
    @else
        <x-btnpreview :id="$id" :route="$route" />
    @endif
</div>
