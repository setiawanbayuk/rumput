<div class="d-flex gap-1">
    <x-btnnaik>
        <x-slot:id>{{ $id }}</x-slot:id>
        <x-slot:status>{{ $status }}</x-slot:status></x-btnnaik>
    @if ($status != 3)
        <x-btnpreview><x-slot:id>{{ $id }}</x-slot:id></x-btnpreview>
    @else
        <x-btncetak><x-slot:id>{{ $id }}</x-slot:id></x-btncetak>
    @endif
</div>
