@extends('layouts.main')

@section('title', 'Tambah Surat Keterangan')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('suket.store') }}">
                            @csrf

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <x-nosrt>
                                        <x-slot:no_urut_surat>{{ $no_urut_surat }}</x-slot:no_urut_surat>
                                        <x-slot:instansi_kode>{{ $currentUser->skpd->instansi_kode }}</x-slot:instansi_kode>
                                    </x-nosrt>
                                    <x-pribadi></x-pribadi>
                                </div>
                                <div class="col-md-6 border-start">
                                    <div class="row mb-3">
                                        <label for="keterangan"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Keterangan') }}</label>

                                        <div class="col-md-8">
                                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                                autocomplete="keterangan" autofocus>{{ old('keterangan') }}</textarea>
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
                                                autocomplete="peruntukan" autofocus>{{ old('peruntukan') }}</textarea>
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
                                                value="{{ old('kepada') }}" autocomplete="kepada">

                                            @error('kepada')
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
@endsection
