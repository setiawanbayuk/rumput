{{-- resources/views/skdom/show.blade.php --}}
@extends('layouts.create')

@section('title', $title)

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/form.png') }}') no-repeat center center; background-size: cover; margin-top:-75px;">
        <div class="container" style="margin-top:125px; margin-bottom:50px;">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-3"
                        style="background-color:rgba(255,255,255,.28); backdrop-filter:blur(10px);">
                        
                        <div class="card-header bg-transparent text-center pt-3 pb-2">
                            <h5 class="mt-3 fw-bold text-white" style="letter-spacing:.5px">
                                {{ $title }}
                            </h5>
                            <h6 class="mb-3 fw-semibold text-white">
                                No. Surat : {{ $suratKeterangan->getNoSrt($suratKeterangan) }}
                            </h6>
                        </div>

                        <div class="card-body">
                            <form id="formEditSkdom" method="POST" enctype="multipart/form-data" 
                                action="{{ route('skdom.updatewarga', $suratKeterangan->id) }}" >
                                @csrf
                                <input type="hidden" id="nik" name="nik" value="{{ $suratKeterangan->nik }}">
                                {{-- ================== ROW DUA KOLOM ================== --}}
                                <div class="row g-3" style="min-height:500px;">

                                    {{-- ========== KIRI ========== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">
                                                {{-- Jenis Domisili --}}
                                                <div class="row mb-3 align-items-md-center">
                                                    <label for="nip" class="col-md-3 col-form-label text-md-start ms-2">Jenis
                                                        Surat Domisili</label>
                                                    <div class="col-md-8">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="register_as"
                                                                id="flexRadioDefault1" value="perorangan"
                                                                onchange="handleChangeRegisterAs('perorangan')"
                                                                @checked(old('register_as', 'perorangan') == 'perorangan')>
                                                            <label class="form-check-label"
                                                                for="flexRadioDefault1">Perorangan</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="register_as"
                                                                id="flexRadioDefault2" value="perusahaan"
                                                                onchange="handleChangeRegisterAs('perusahaan')"
                                                                @checked(old('register_as') == 'perusahaan')>
                                                            <label class="form-check-label"
                                                                for="flexRadioDefault2">Perusahaan</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="input-perusahaan" @class([
                                                    'd-none' => old('register_as', 'perorangan') == 'perorangan',
                                                ])>
                                                {{-- ===== Jika Perusahaan, tampilkan field perusahaan ===== --}}
                                                    {{-- Nama Perusahaan --}}
                                                    <div class="row mb-3">
                                                        <label for="nama_perusahaan"
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama Perusahaan') }}</label>

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
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ __('Status Bangunan') }}</label>

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
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ __('Jumlah Karyawan') }}</label>

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
                                                </div>
                                                {{-- Kepada / Atas Nama --}}
                                                <div class="row mb-3">
                                                    <label for="kepada" id='labelKepada'
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Diberikan Kepada') }}</label>

                                                    <div class="col-md-8">
                                                        <input id="kepada" type="kepada"
                                                            class="form-control @error('kepada') is-invalid @enderror"
                                                            name="kepada"
                                                            value="{{ old('kepada', $suratKeterangan->kepada) }}"
                                                            autocomplete="kepada">

                                                        @error('kepada')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ========== KANAN ========== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">
                                                {{-- Alamat Domisili --}}
                                                <div class="row mb-3">
                                                    <label for="alamat_domisili"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Alamat Domisili') }}</label>

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

                                                {{-- Peruntukan --}}
                                                <x-peruntukan :peruntukan="$suratKeterangan->peruntukan" :readonly="false" />
                                                {{-- Pengantar --}}
                                                <x-pengantar :pengantar="$suratKeterangan->pengantar" :readonly="false" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- ========== BUTTON UPDATE ========== --}}
                                <div class="d-flex justify-content-center gap-3 mb-3">
                                    <a href="{{ route('skdom.warga') }}" class="btn btn-danger py-2 px-4 me-2" style="border-radius: 8px">
                                        Batal
                                    </a>
                                    <button type="button" class="btn text-white py-2 px-4"
                                            style="background: #7896B2; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalEditSkdom">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div> {{-- card-body --}}
                    </div> {{-- card --}}
                </div>
                {{-- Modal Konfirmasi --}}
                <x-confirm-ajukan modalId="modalEditSkdom" formId="formEditSkdom"
                    title="Yakin Ingin Mengubah Surat Ini?"
                    message="Pastikan perubahan sudah benar sebelum mengirim pembaruan."
                    agreeLabel="Saya memastikan bahwa data yang saya ubah sudah benar."
                    cancelText="Cek Lagi"
                    confirmText="Simpan Perubahan" />
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
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
