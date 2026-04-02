@php
    $role = auth()->user()->role_id;
@endphp

<div class="d-flex gap-1">

    @if ($status == 0)
        <x-btntolak :id="$id" :route="$route" />
        <x-btnproses :id="$id" :route="$route" />
    @endif

    @if ($status == 1)
        <x-btnnaik :id="$id" :route="$route" :status="$status" />
    @endif

    @if (in_array($status, [4, 5]))
        <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
    @else
        <x-btnedit>
            <x-slot:route>{{ $route }}</x-slot:route>
            <x-slot:id>{{ $id }}</x-slot:id>
        </x-btnedit>
        <x-btnpreview :id="$id" :route="$route" />
    @endif
</div>
