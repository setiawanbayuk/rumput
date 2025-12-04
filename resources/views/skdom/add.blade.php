@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container mt-2">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header border-0 pt-3 pb-2" style="background: #AEA07A; border-radius: 1rem 1rem 0 0">
                        <h5 class="my-3 fw-bold text-white text-center" style="letter-spacing: .5px">{{ $title }}</h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('skdom.store') }}">
                            @csrf

                            <div class="row" style="min-height: 500px;">
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-1 shadow-sm rounded-4"
                                        style="background: #fff; border-color: #AEA07A">
                                        <div class="card-body">
                                            <div>
                                                <x-nosrt>
                                                    <x-slot:kd_jenis_surat></x-slot:kd_jenis_surat>
                                                    <x-slot:no_urut_surat>{{ $no_urut_surat }}</x-slot:no_urut_surat>
                                                    <x-slot:instansi_kode>{{ $currentUser->skpd->instansi_kode }}</x-slot:instansi_kode>
                                                    <x-slot:tgl_surat></x-slot:tgl_surat>
                                                </x-nosrt>
                                                <x-pribadi></x-pribadi>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-1 shadow-sm rounded-4"
                                        style="background: #fff; border-color: #AEA07A">
                                        <div class="card-body">
                                            <div class="row mb-3 align-items-md-center">
                                                <label for="nip" class="col-md-3 col-form-label text-md-start ms-2">Jenis
                                                    Surat
                                                    Domisili</label>
                                                <div class="col-md-8">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="register_as"
                                                            id="flexRadioDefault1" value="perorangan"
                                                            onchange="handleChangeRegisterAs('perorangan')"
                                                            @checked(old('register_as', 'perorangan') == 'perorangan')>
                                                        <label class="form-check-label" for="flexRadioDefault1">Perorangan</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="register_as"
                                                            id="flexRadioDefault2" value="perusahaan"
                                                            onchange="handleChangeRegisterAs('perusahaan')"
                                                            @checked(old('register_as') == 'perusahaan')>
                                                        <label class="form-check-label" for="flexRadioDefault2">Perusahaan</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="input-perusahaan" @class([
                                                'd-none' => old('register_as', 'perorangan') == 'perorangan',
                                            ])>

                                                <div class="row mb-3">
                                                    <label for="nama_perusahaan"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama Perusahaan') }}</label>

                                                    <div class="col-md-8">
                                                        <input id="nama_perusahaan" type="text"
                                                            class="form-control @error('nama_perusahaan') is-invalid @enderror"
                                                            name="nama_perusahaan" value="{{ old('nama_perusahaan') }}"
                                                            autocomplete="nama_perusahaan">

                                                        @error('nama_perusahaan')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="status_bangunan"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Status Bangunan') }}</label>

                                                    <div class="col-md-8">
                                                        <input id="status_bangunan" type="text"
                                                            class="form-control @error('status_bangunan') is-invalid @enderror"
                                                            name="status_bangunan" value="{{ old('status_bangunan') }}"
                                                            autocomplete="status_bangunan">

                                                        @error('status_bangunan')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="jumlah_karyawan"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Jumlah Karyawan') }}</label>

                                                    <div class="col-md-8">
                                                        <input id="jumlah_karyawan" type="number"
                                                            class="form-control @error('jumlah_karyawan') is-invalid @enderror"
                                                            name="jumlah_karyawan" value="{{ old('jumlah_karyawan') }}"
                                                            autocomplete="jumlah_karyawan">

                                                        @error('jumlah_karyawan')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="tgl_berlaku"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Tgl. Berlaku') }}</label>
                                                    <div class="col-md-8">
                                                        <input id="tgl_berlaku" type="date"
                                                            class="form-control @error('tgl_berlaku') is-invalid @enderror"
                                                            name="tgl_berlaku" value="{{ old('tgl_berlaku') }}"
                                                            autocomplete="tgl_berlaku" autofocus>

                                                        @error('tgl_berlaku')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label for="alamat_domisili"
                                                    class="col-md-3 col-form-label text-md-start ms-2">{{ __('Alamat Domisili') }}</label>

                                                <div class="col-md-8">
                                                    <textarea class="form-control @error('alamat_domisili') is-invalid @enderror" id="alamat_domisili"
                                                        name="alamat_domisili" autocomplete="alamat_domisili" autofocus>{{ old('alamat_domisili') }}</textarea>
                                                    @error('alamat_domisili')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="kepada" id='labelKepada'
                                                    class="col-md-3 col-form-label text-md-start ms-2">{{ __('Diberikan Kepada') }}</label>

                                                <div class="col-md-8">
                                                    <input id="kepada" type="text"
                                                        class="form-control @error('kepada') is-invalid @enderror" name="kepada"
                                                        value="{{ old('kepada') }}" autocomplete="kepada">

                                                    @error('kepada')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            @isset($var)
                                                @foreach ($var as $item)
                                                    <div class="row mb-3">
                                                        <label for="{{ $item }}"
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ $item }}</label>

                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control @error('{{ $item }}') is-invalid @enderror"
                                                                name="<?= $item ?>" id="<?= $item ?>" placeholder="" />

                                                            @error('{{ $item }}')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endisset
                                            <x-peruntukan><x-slot:peruntukan></x-slot:peruntukan></x-peruntukan>
                                            <x-pengantar></x-pengantar>
                                            <div class="row mb-0">
                                                <div class="col-md-8 offset-md-4">
                                                    <a href="{{ url()->previous() ?? route('skdom.index')}}" class="btn btn-danger py-2 px-4 me-2" style="border-radius: 8px">
                                                        <i class="ri-close-line me-1"></i>
                                                        <span>Batal</span>
                                                    </a>
                                                    <button type="submit" class="btn text-white py-2 px-4"
                                                        style="background: #7896B2; border-radius: 8px;">
                                                        <i class="ri-save-3-fill me-1"></i>
                                                        <span>Simpan</span>
                                                    </button>
                                                </div>
                                            </div>
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
            const handleChangeRegisterAs = (value) => {
                if (value == 'perusahaan') {
                    $('#input-perusahaan').removeClass('d-none');
                    document.getElementById('labelKepada').innerHTML = 'Pimpinan / Pemilik';
                } else {
                    $('#input-perusahaan').addClass('d-none');
                    document.getElementById('labelKepada').innerHTML = 'Diberikan Kepada';
                }
            }
        </script>
    @endpush
@endsection
