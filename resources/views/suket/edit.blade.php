@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('suket.update', ['id' => $suratKeterangan->id]) }}">
                            @csrf

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <x-nosrt>
                                        <x-slot:kd_jenis_surat>{{ $suratKeterangan->kd_jenis_surat }}</x-slot:kd_jenis_surat>
                                        <x-slot:no_urut_surat>{{ $suratKeterangan->no_urut_surat }}</x-slot:no_urut_surat>
                                        <x-slot:instansi_kode>{{ $currentUser->skpd->instansi_kode }}</x-slot:instansi_kode>
                                        <x-slot:tgl_surat>{{ $suratKeterangan->tgl_surat }}</x-slot:tgl_surat>
                                    </x-nosrt>
                                    <x-pribadi></x-pribadi>
                                </div>
                                <div class="col-md-6 border-start">
                                    <div class="row mb-3">
                                        <label for="keterangan"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Keterangan') }}</label>

                                        <div class="col-md-8">
                                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                                autocomplete="keterangan" autofocus>{{ old('keterangan', $suratKeterangan->keterangan) }}</textarea>
                                            @error('keterangan')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="peruntukan"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Peruntukan') }}</label>

                                        <div class="col-md-8">
                                            <textarea class="form-control @error('peruntukan') is-invalid @enderror" id="peruntukan" name="peruntukan"
                                                autocomplete="peruntukan" autofocus>{{ old('peruntukan', $suratKeterangan->peruntukan) }}</textarea>
                                            @error('peruntukan')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="kepada"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Diberikan Kepada') }}</label>

                                        <div class="col-md-8">
                                            <input id="kepada" type="kepada"
                                                class="form-control @error('kepada') is-invalid @enderror" name="kepada"
                                                value="{{ old('kepada', $suratKeterangan->kepada) }}"
                                                autocomplete="kepada">

                                            @error('kepada')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="kepada"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Surat Pengantar') }}</label>
                                        <div class="col-md-8">
                                            <input class="form-control" type="file" id="pengantar" name="pengantar"
                                                accept="image/jpeg, image/jpg, image/png">
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-md-3 col-form-label text-md-end"></label>
                                        <div class="col-md-8">
                                            <img src="{{ asset($suratKeterangan->pengantar) }}" alt="" height="100%" style="max-height: 400px">
                                        </div>
                                    </div>
                                    <div class="row mb-0">
                                        <div class="col-md-8 offset-md-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-save-3-fill"></i>
                                                <span>Update</span>
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
        <script type="text/javascript" src="{{ asset('assets/js/personal.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#nik').val({{ $suratKeterangan->nik }});
                checkNIK();
            });
        </script>
    @endpush
@endsection
