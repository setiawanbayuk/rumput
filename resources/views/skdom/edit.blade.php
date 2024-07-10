@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data"
                            action="{{ route('skdom.update', ['id' => $suratKeterangan->id]) }}">
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
                                    <div class="row mb-3 align-items-md-center">
                                        <label for="nip" class="col-md-3 col-form-label text-md-end">Jenis Surat
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
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama Perusahaan') }}</label>

                                            <div class="col-md-8">
                                                <input id="nama_perusahaan" type="nama_perusahaan"
                                                    class="form-control @error('nama_perusahaan') is-invalid @enderror"
                                                    name="nama_perusahaan"
                                                    value="{{ old('nama_perusahaan', $suratKeterangan->nama_perusahaan) }}"
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
                                                class="col-md-3 col-form-label text-md-end">{{ __('Status Bangunan') }}</label>

                                            <div class="col-md-8">
                                                <input id="status_bangunan" type="status_bangunan"
                                                    class="form-control @error('status_bangunan') is-invalid @enderror"
                                                    name="status_bangunan"
                                                    value="{{ old('status_bangunan', $suratKeterangan->status_bangunan) }}"
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
                                                class="col-md-3 col-form-label text-md-end">{{ __('Jumlah Karyawan') }}</label>

                                            <div class="col-md-8">
                                                <input id="jumlah_karyawan" type="jumlah_karyawan"
                                                    class="form-control @error('jumlah_karyawan') is-invalid @enderror"
                                                    name="jumlah_karyawan"
                                                    value="{{ old('jumlah_karyawan', $suratKeterangan->jumlah_karyawan) }}"
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
                                                class="col-md-3 col-form-label text-md-end">{{ __('Tgl. Berlaku') }}</label>
                                            <div class="col-md-8">
                                                <input id="tgl_berlaku" type="date"
                                                    class="form-control @error('tgl_berlaku') is-invalid @enderror"
                                                    name="tgl_berlaku"
                                                    value="{{ old('tgl_berlaku', $suratKeterangan->tgl_berlaku) }}"
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
                                            class="col-md-3 col-form-label text-md-end">{{ __('Alamat Domisili') }}</label>

                                        <div class="col-md-8">
                                            <textarea class="form-control @error('alamat_domisili') is-invalid @enderror" id="alamat_domisili"
                                                name="alamat_domisili" autocomplete="alamat_domisili" autofocus>{{ old('alamat_domisili', $suratKeterangan->alamat_domisili) }}</textarea>
                                            @error('alamat_domisili')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="kepada" id='labelKepada'
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
                                    <x-peruntukan><x-slot:peruntukan>{{ $suratKeterangan->peruntukan }}</x-slot:peruntukan></x-peruntukan>
                                    <x-pengantar></x-pengantar>
                                    <div class="row mb-3">
                                        <label class="col-md-3 col-form-label text-md-end"></label>
                                        <div class="col-md-8">
                                            <img src="{{ asset($suratKeterangan->pengantar) }}" alt=""
                                                height="100%" style="max-height: 400px">
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

                if ('{{ $suratKeterangan->jenis }}' == 'perusahaan') {
                    $("#flexRadioDefault2").attr('checked', true).trigger('click');
                    handleChangeRegisterAs('perusahaan');
                }
            });
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
