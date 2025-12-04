@props(['alias', 'id', 'nosrt', 'nama'])

<button class="btn btn-chip text-white"
        style="background:#AEA07A"
        onclick="lihatPenilaian('{{ $alias }}', {{ $id }}, '{{ $nosrt }}', '{{ $nama }}')">
    Tampilkan Penilaian
</button>