<div class="d-flex gap-1">
    <x-btnsign>
        <x-slot:id>{{ $id }}</x-slot:id>
        <x-slot:nomorSurat>{{ $nomorSurat }}</x-slot:nomorSurat>
        <x-slot:status>{{ $status }}</x-slot:status>
        <x-slot:jenis>{{ $jenis }}</x-slot:jenis>
    </x-btnsign>
    @if ($status != 3)
        <x-btnpreview><x-slot:id>{{ $id }}</x-slot:id></x-btnpreview>
    @else
        <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
    @endif
</div>
