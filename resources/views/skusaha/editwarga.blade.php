{{-- resources/views/suket/edit.blade.php --}}
@extends('layouts.create')

@section('title', $title)

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/profile.png') }}') no-repeat center center; background-size: cover; margin-top:-75px;">
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
                            <form id="formEditSkusaha" method="POST" enctype="multipart/form-data" 
                                action="{{ route('skusaha.updatewarga', $suratKeterangan->id) }}" >
                                @csrf
                                <input type="hidden" id="nik" name="nik" value="{{ $suratKeterangan->nik }}">
                                {{-- ================== ROW KEDUA KARTU ================== --}}
                                <div class="row g-3" style="min-height:500px;">

                                    {{-- ========== KIRI: KETERANGAN ========== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">

                                                <div class="row mb-3 align-items-md-center">
                                                    <label for="nip" class="col-md-3 col-form-label text-md-start ms-2">Jenis Surat
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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama Usaha') }}</label>

                                                    <div class="col-md-8">
                                                        <input id="nama_usaha" type="text"
                                                            class="form-control @error('nama_usaha') is-invalid @enderror"
                                                            name="nama_usaha" value="{{ old('nama_usaha', $suratKeterangan->nama_usaha) }}" autocomplete="nama_usaha">

                                                        @error('nama_usaha')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="alamat_usaha"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Alamat Usaha') }}</label>

                                                    <div class="col-md-8">
                                                        <textarea class="form-control @error('alamat_usaha') is-invalid @enderror" id="alamat_usaha" name="alamat_usaha"
                                                            autocomplete="alamat_usaha" autofocus>{{ old('alamat_usaha', $suratKeterangan->alamat_usaha) }}</textarea>
                                                        @error('alamat_usaha')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ========== KANAN: KEPADA + PERUNTUKAN + PENGANTAR ========== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">
                                                <x-kepada :kepada="$suratKeterangan->kepada" :readonly="false" />
                                                <x-peruntukan :peruntukan="$suratKeterangan->peruntukan" :readonly="false" />
                                                <x-pengantar :pengantar="$suratKeterangan->pengantar" :readonly="false" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- END ROW --}}

                                {{-- ========== BUTTON UPDATE ========== --}}
                                <div class="d-flex justify-content-center gap-3" style="margin-top:75px;">
                                    <a href="{{ route('skusaha.warga') }}" class="btn btn-secondary px-4 py-2 rounded-3">
                                        Batal
                                    </a>
                                    <button type="button" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEditSkusaha">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div>
                        {{-- card-body --}}
                    </div>
                    {{-- card --}}
                </div>
                {{-- Modal Konfirmasi --}}
                <x-confirm-ajukan modalId="modalEditSkusaha" formId="formEditSkusaha"
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
                if ('{{ $suratKeterangan->jenis }}' == 'luar') {
                    $("#flexRadioDefault2").attr('checked', true).trigger('click');
                }
            });
        </script>
    @endpush
@endsection
