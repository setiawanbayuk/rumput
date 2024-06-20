<div class="d-flex gap-1">
    <x-btn-sign>
        <x-slot:id>{{ $id }}</x-slot:id>
        <x-slot:nomorSurat>{{ $nomorSurat }}</x-slot:nomorSurat>
        <x-slot:status>{{ $status }}</x-slot:status>
    </x-btn-sign>
    @if ($status != 3)
        <x-btn-preview><x-slot:id>{{ $id }}</x-slot:id></x-btn-preview>
    @else
        <x-btn-cetak><x-slot:id>{{ $id }}</x-slot:id></x-btn-cetak>
    @endif
</div>
