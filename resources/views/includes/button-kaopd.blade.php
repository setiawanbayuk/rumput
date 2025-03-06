<div class="d-flex gap-1">
    <x-btnsign>
        <x-slot:id>{{ $id }}</x-slot:id>
        <x-slot:nomorSurat>{{ $nomorSurat }}</x-slot:nomorSurat>
        <x-slot:status>{{ $status }}</x-slot:status>
        <x-slot:jenis>{{ $jenis }}</x-slot:jenis>
        <x-slot:role>{{ $role }}</x-slot:role>
    </x-btnsign>
    @if ($status == 3 || $status == 5 || $status == 6)
        <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
    @else
        <x-btnpreview><x-slot:id>{{ $id }}</x-slot:id></x-btnpreview>
    @endif
</div>
