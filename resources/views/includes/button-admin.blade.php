<div class="d-flex gap-1">
    @if ($status == 0)
        <x-btn-tolak><x-slot:id>{{ $id }}</x-slot:id></x-btn-tolak>
    @endif
    @if ($status != 3)
        <x-btn-edit>
            <x-slot:route>{{ $route }}</x-slot:route>
            <x-slot:id>{{ $id }}</x-slot:id>
        </x-btn-edit>
        <x-btn-preview><x-slot:id>{{ $id }}</x-slot:id></x-btn-preview>
    @else
        <x-btn-cetak><x-slot:id>{{ $id }}</x-slot:id></x-btn-cetak>
    @endif
</div>
