<div class="d-flex gap-1">
    <a href="{{ route('template.download', $data->id) }}" class="print btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-title="Download" title="Download"><i class="ri-file-download-line"></i></a>
    @if ($data->state != 'default')
        <button class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-title="Hapus" title="Hapus"
            id="hapus{{ $data->id }}" onclick="handleHapus({{ $data->id }})">
            <i class="ri-delete-bin-6-line"></i>
        </button>
    @endif


</div>
