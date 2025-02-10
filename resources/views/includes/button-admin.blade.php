<div class="d-flex gap-1">
    @if ($status == 0)
        <x-btntolak><x-slot:id>{{ $id }}</x-slot:id></x-btntolak>
    @endif
    @if ($status != 3)
        <x-btnedit>
            <x-slot:route>{{ $route }}</x-slot:route>
            <x-slot:id>{{ $id }}</x-slot:id>
        </x-btnedit>
        <x-btnpreview><x-slot:id>{{ $id }}</x-slot:id></x-btnpreview>
    @else
        <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
    @endif
</div>
