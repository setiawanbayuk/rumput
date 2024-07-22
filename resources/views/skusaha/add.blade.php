@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('skusaha.store') }}">
                            @csrf

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <x-nosrt>
                                        <x-slot:kd_jenis_surat></x-slot:kd_jenis_surat>
                                        <x-slot:no_urut_surat>{{ $no_urut_surat }}</x-slot:no_urut_surat>
                                        <x-slot:instansi_kode>{{ $currentUser->skpd->instansi_kode }}</x-slot:instansi_kode>
                                        <x-slot:tgl_surat></x-slot:tgl_surat>
                                    </x-nosrt>
                                    <x-pribadi></x-pribadi>
                                </div>
                                <div class="col-md-6 border-start">
                                    <div class="row mb-3 align-items-md-center">
                                        <label for="nip" class="col-md-3 col-form-label text-md-end">Jenis Surat
                                            Usaha</label>
                                        <div class="col-md-8">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="register_as"
                                                    id="flexRadioDefault1" value="kelurahan"
                                                    onchange="handleChangeRegisterAs('kelurahan')"
                                                    @checked(old('register_as', 'kelurahan') == 'kelurahan')>
                                                <label class="form-check-label" for="flexRadioDefault1">Kelurahan</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="register_as"
                                                    id="flexRadioDefault2" value="luar"
                                                    onchange="handleChangeRegisterAs('luar')"
                                                    @checked(old('register_as') == 'luar')>
                                                <label class="form-check-label" for="flexRadioDefault2">Luar Kelurahan</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="nama_usaha"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Nama Usaha') }}</label>

                                        <div class="col-md-8">
                                            <input id="nama_usaha" type="text"
                                                class="form-control @error('nama_usaha') is-invalid @enderror"
                                                name="nama_usaha" value="{{ old('nama_usaha') }}" autocomplete="nama_usaha">

                                            @error('nama_usaha')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="alamat_usaha"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Alamat Usaha') }}</label>

                                        <div class="col-md-8">
                                            <textarea class="form-control @error('alamat_usaha') is-invalid @enderror" id="alamat_usaha" name="alamat_usaha"
                                                autocomplete="alamat_usaha" autofocus>{{ old('alamat_usaha') }}</textarea>
                                            @error('alamat_usaha')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <x-kepada><x-slot:kepada></x-slot:kepada></x-kepada>
                                    <x-peruntukan><x-slot:peruntukan></x-slot:peruntukan></x-peruntukan>
                                    <x-pengantar></x-pengantar>
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
        <script type="text/javascript" src="{{ asset('assets/js/personal.js') }}"></script>
    @endpush
@endsection
