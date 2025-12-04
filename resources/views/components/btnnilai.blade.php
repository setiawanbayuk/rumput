@props(['alias', 'id', 'nosrt', 'nama'])

<button class="btn btn-chip text-white"
        style="background:#AEA07A"
        onclick="openModalNilai('{{ $alias }}', {{ $id }}, '{{ $nosrt }}', '{{ $nama }}')">
    Nilai
</button>
