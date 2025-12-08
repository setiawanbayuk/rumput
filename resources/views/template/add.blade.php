@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $errors->first() }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <form method="POST" enctype="multipart/form-data" action="{{ route('template.store') }}">
                            @csrf

                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="row mb-3">
                                        <label for="name"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Nama Template') }}</label>

                                        <div class="col-md-8">
                                            <input id="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror" name="name"
                                                value="{{ old('name') }}" autocomplete="name">

                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="file"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Template Surat') }}</label>
                                        <div class="col-md-8">
                                            <input class="form-control" type="file" id="file" name="file"
                                                accept=".docx">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="jenis"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Jenis Surat') }}</label>

                                        <div class="col-md-8">
                                            <select class="form-control @error('jenis') is-invalid @enderror" id="jenis"
                                                name="jenis" data-placeholder="Jenis Surat">
                                                <option value=""></option>
                                                <option value="skbn">Surak Keterangan Belum Menikah</option>
                                                <option value="skboro">Surak Keterangan Boro</option>
                                                <option value="skdom">Surak Keterangan Domisili</option>
                                                <option value="skhsl">Surak Keterangan Penghasilan</option>
                                                <option value="sktm_perorangan">Surak Keterangan Miskin Perorangan</option>
                                                <option value="sktm_sekolah">Surak Keterangan Miskin Sekolah</option>
                                                <option value="skusaha">Surak Keterangan Usaha</option>
                                                <option value="suket">Surak Keterangan</option>
                                            </select>
                                            @error('jenis')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-0">
                                        <div class="col-md-8 offset-md-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-save-3-fill"></i>
                                                <span>Simpan</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function() {

                $("#jenis").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                });
            });
        </script>
    @endpush
@endsection
