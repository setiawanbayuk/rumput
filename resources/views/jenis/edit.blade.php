@extends('layouts.main')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
@endpush
@section('title', '{{ $title }}')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('jenis.update', ['id' => 1]) }}">
                            @csrf

                            <div class="row justify-content-center">
                                <div class="row mb-3">
                                    <label class="col-md-2" for="detail"
                                        class="col-md-3 col-form-label text-md-end">Deskripsi Surat</label>
                                    <div class="col-md-10">
                                        <div class="row mb-3" id="quill-editor" style="height: 300px;">
                                            {{ old('detail', $surat['detail']) }}
                                        </div>
                                        <textarea class="form-control d-none" id="quill-editor-area" name="detail">
                                            {{ old('detail', $surat['detail']) }}
                                        </textarea>
                                    </div>
                                </div>
                            </div>


                            <div class="row justify-content-center">
                                <div class="row mb-3">
                                    <label class="col-md-2" for="detail"
                                        class="col-md-3 col-form-label text-md-end">Persyaratan</label>
                                    <div class="col-md-10">
                                        <div class="row mb-3" id="quill-editor-persyaratan" style="height: 300px;">
                                            {{ old('persyaratan', $surat['persyaratan']) }}
                                        </div>
                                        <textarea class="form-control d-none" id="quill-editor-area-persyaratan" name="persyaratan">
                                            {{ old('persyaratan', $surat['persyaratan']) }}
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-3-fill"></i>
                                <span>Update</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

        <script>
            // const quill = new Quill('#editor', {
            //     theme: 'snow'
            // });
            document.addEventListener('DOMContentLoaded', function() {

                if (document.getElementById('quill-editor-area')) {

                    var editor = new Quill('#quill-editor', {

                        theme: 'snow'

                    });

                    var quillEditor = document.getElementById('quill-editor-area');

                    editor.on('text-change', function() {

                        quillEditor.value = editor.root.innerHTML;

                    });
                    quillEditor.addEventListener('input', function() {

                        editor.root.innerHTML = quillEditor.value;

                    });

                }

                if (document.getElementById('quill-editor-area-persyaratan')) {

                    var editorpersyaratan = new Quill('#quill-editor-persyaratan', {

                        theme: 'snow'

                    });

                    var quillEditorpersyaratan = document.getElementById('quill-editor-area-persyaratan');

                    editorpersyaratan.on('text-change', function() {

                        quillEditorpersyaratan.value = editorpersyaratan.root.innerHTML;

                    });
                    quillEditorpersyaratan.addEventListener('input', function() {

                        editorpersyaratan.root.innerHTML = quillEditorpersyaratan.value;

                    });

                }

            });
        </script>
    @endpush
@endsection
