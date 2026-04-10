<div class="d-flex gap-1">
    @if ((int) $role === 3)
        @if ((int) $status === 3 && $jenis === 'sktm')
            <x-btnnaik :id="$id" :status="$status" :route="$route" />
        @endif

        @if ((int) $status === 3)
            <button class="js-surat-action btn btn-danger btn-sm"
                    data-url="{{ route(str_replace('.edit', '.turunkan', $route), $id) }}"
                    data-action="turunkan"
                    data-method="POST"
                    data-confirm="Turunkan surat ini kembali ke Sekkel?"
                    data-success="Surat berhasil diturunkan ke Sekkel."
                    data-bs-title="Turunkan" title="Turunkan">
                <i class="ri-arrow-down-double-line"></i>
            </button>

            <x-btnsign>
                <x-slot:id>{{ $id }}</x-slot:id>
                <x-slot:nomorSurat>{{ $nomorSurat }}</x-slot:nomorSurat>
                <x-slot:status>{{ $status }}</x-slot:status>
                <x-slot:jenis>{{ $jenis }}</x-slot:jenis>
                <x-slot:role>{{ $role }}</x-slot:role>
            </x-btnsign>
        @endif
    @endif

    @if ((int) $role === 5)
        @if ((int) $status === 8)
            <button class="js-surat-action btn btn-danger btn-sm"
                    data-url="{{ route(str_replace('.edit', '.turunkan', $route), $id) }}"
                    data-action="turunkan"
                    data-method="POST"
                    data-confirm="Turunkan surat ini kembali ke Lurah?"
                    data-success="Surat berhasil diturunkan ke Lurah."
                    data-bs-title="Turunkan" title="Turunkan">
                <i class="ri-arrow-down-double-line"></i>
            </button>

            <x-btnsign>
                <x-slot:id>{{ $id }}</x-slot:id>
                <x-slot:nomorSurat>{{ $nomorSurat }}</x-slot:nomorSurat>
                <x-slot:status>{{ $status }}</x-slot:status>
                <x-slot:jenis>{{ $jenis }}</x-slot:jenis>
                <x-slot:role>{{ $role }}</x-slot:role>
            </x-btnsign>
        @endif
    @endif

    @if (in_array((int) $status, [4, 9]))
        <x-btncetak>
            <x-slot:id>{{ $id }}</x-slot:id>
        </x-btncetak>
    @else
        <x-btnpreview :id="$id" :route="$route" />
    @endif
</div>
