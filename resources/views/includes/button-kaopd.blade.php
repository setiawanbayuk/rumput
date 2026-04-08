<div class="d-flex gap-1">
    @if (in_array((int) $status, [2, 3, 8]))
        <x-btnnaik :id="$id" :status="$status" :route="$route" />
    @endif

    <x-btnsign>
        <x-slot:id>{{ $id }}</x-slot:id>
        <x-slot:nomorSurat>{{ $nomorSurat }}</x-slot:nomorSurat>
        <x-slot:status>{{ $status }}</x-slot:status>
        <x-slot:jenis>{{ $jenis }}</x-slot:jenis>
        <x-slot:role>{{ $role }}</x-slot:role>
    </x-btnsign>

    @if (in_array((int) $status, [4, 5, 8, 9]))
        <x-btncetak>
            <x-slot:id>{{ $id }}</x-slot:id>
        </x-btncetak>
    @else
        <x-btnpreview :id="$id" :route="$route" />
    @endif
</div>