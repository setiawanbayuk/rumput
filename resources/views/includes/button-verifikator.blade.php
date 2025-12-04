<div class="d-flex gap-1">
    @if ($role == '6')
        <x-btnregister>
            <x-slot:id>{{ $id }}</x-slot:id>
            <x-slot:status>{{ $status }}</x-slot:status>
            <x-slot:role>{{ $role }}</x-slot:role>
        </x-btnregister>
    @else
        <x-btnnaik   :id="$id" :status="$status" :route="$route" />
    @endif
    @if (in_array($status, [4, 5]))
        <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
    @else
        <x-btnpreview  :id="$id" :route="$route" />
    @endif
</div>
