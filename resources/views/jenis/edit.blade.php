@extends('layouts.main')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
@endpush

@section('title', $title)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h3 class="fw-bold mb-1">{{ $title }}</h3>
                <div class="text-muted">{{ strtoupper($surat->nama ?? $surat->name ?? $surat->jenis ?? 'Jenis Surat') }}</div>
            </div>
            <a href="{{ route('jenis.index') }}" class="btn btn-outline-dark rounded-4"><i class="ri-arrow-left-line me-1"></i>Kembali</a>
        </div>

        @if (! $hasDetail || ! $hasPersyaratan)
            <div class="alert alert-warning rounded-4">
                Tabel <b>jenis_surats</b> pada database kamu belum lengkap untuk edit detail/persyaratan. Halaman ini tetap aman dibuka agar tidak error.
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('jenis.update', ['id' => $surat->id]) }}">
                    @csrf

                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi Surat</label>
                            @if ($hasDetail)
                                <div id="quill-editor" style="height: 260px;">{!! old('detail', $surat->detail ?? '') !!}</div>
                                <textarea class="form-control d-none" id="quill-editor-area" name="detail">{{ old('detail', $surat->detail ?? '') }}</textarea>
                            @else
                                <div class="form-control bg-light rounded-4" style="min-height:120px">Kolom detail belum ada di database.</div>
                            @endif
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Persyaratan</label>
                            @if ($hasPersyaratan)
                                <div id="quill-editor-persyaratan" style="height: 260px;">{!! old('persyaratan', $surat->persyaratan ?? '') !!}</div>
                                <textarea class="form-control d-none" id="quill-editor-area-persyaratan" name="persyaratan">{{ old('persyaratan', $surat->persyaratan ?? '') }}</textarea>
                            @else
                                <div class="form-control bg-light rounded-4" style="min-height:120px">Kolom persyaratan belum ada di database.</div>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark rounded-4 fw-semibold mt-4" @disabled(! $hasDetail && ! $hasPersyaratan)>
                        <i class="ri-save-3-fill me-1"></i> Update
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (document.getElementById('quill-editor-area')) {
                    const editor = new Quill('#quill-editor', { theme: 'snow' });
                    const area = document.getElementById('quill-editor-area');
                    editor.on('text-change', function() { area.value = editor.root.innerHTML; });
                }
                if (document.getElementById('quill-editor-area-persyaratan')) {
                    const editorPersyaratan = new Quill('#quill-editor-persyaratan', { theme: 'snow' });
                    const areaPersyaratan = document.getElementById('quill-editor-area-persyaratan');
                    editorPersyaratan.on('text-change', function() { areaPersyaratan.value = editorPersyaratan.root.innerHTML; });
                }
            });
        </script>
    @endpush
@endsection
