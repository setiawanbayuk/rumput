<button class="naik btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-title="Naikkan" title="Naikkan"
    @if ($status != '1') disabled @endif onclick="handleNaik({{ $id }})">
    <i class="ri-arrow-up-double-fill"></i>
</button>
