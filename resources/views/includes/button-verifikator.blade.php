<div class="d-flex gap-1">
    @if ($role == '6')
        <x-btnregister>
            <x-slot:id>{{ $id }}</x-slot:id>
            <x-slot:status>{{ $status }}</x-slot:status>
            <x-slot:role>{{ $role }}</x-slot:role>
        </x-btnregister>
    @else
        <x-btnnaik>
            <x-slot:id>{{ $id }}</x-slot:id>
            <x-slot:status>{{ $status }}</x-slot:status>
        </x-btnnaik>
    @endif
    @if ($status == 3 || $status == 5 || $status == 6)
        <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
    @else
        <x-btnpreview><x-slot:id>{{ $id }}</x-slot:id></x-btnpreview>
    @endif
</div>
