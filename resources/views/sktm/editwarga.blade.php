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
                            <form id="formEditSktm" method="POST" enctype="multipart/form-data" 
                                action="{{ route('sktm.updatewarga', $suratKeterangan->id) }}" >
                                @csrf
                                <input type="hidden" id="nik" name="nik" value="{{ $suratKeterangan->nik }}">
                                {{-- ================== ROW KEDUA KARTU ================== --}}
                                <div class="row g-3" style="min-height:500px;">

                                    {{-- ========== KIRI: KETERANGAN ========== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">

                                                <div class="row mb-3 align-items-md-center">
                                                    <label for="nip" class="col-md-3 col-form-label text-md-start ms-2">Jenis SKTM</label>
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
                                                                id="flexRadioDefault2" value="sekolah"
                                                                onchange="handleChangeRegisterAs('sekolah')"
                                                                @checked(old('register_as') == 'sekolah')>
                                                            <label class="form-check-label" for="flexRadioDefault2">Sekolah</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="input-sekolah" @class([
                                                    'd-none' => old('register_as', 'perorangan') == 'perorangan',
                                                ])>

                                                    <div class="row mb-3">
                                                        <label for="kepada"
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama Anak') }}</label>

                                                        <div class="col-md-8">
                                                            <input id="kepada" type="kepada"
                                                                class="form-control @error('kepada') is-invalid @enderror"
                                                                name="kepada" value="{{ old('kepada', $suratKeterangan->kepada) }}"
                                                                autocomplete="kepada">

                                                            @error('kepada')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <label for="kepada_ttl"
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ __('Tempat/Tgl. Lahir') }}</label>

                                                        <div class="col-md-5">
                                                            <input id="kepada_tempat_lhr" type="text"
                                                                class="form-control @error('kepada_tempat_lhr') is-invalid @enderror"
                                                                name="kepada_tempat_lhr"
                                                                value="{{ old('kepada_tempat_lhr', $suratKeterangan->kepada_tempat_lhr) }}"
                                                                autocomplete="kepada_tempat_lhr" autofocus>

                                                            @error('kepada_tempat_lhr')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input id="kepada_tgl_lhr" type="date"
                                                                class="form-control @error('kepada_tgl_lhr') is-invalid @enderror"
                                                                name="kepada_tgl_lhr"
                                                                value="{{ old('kepada_tgl_lhr', $suratKeterangan->kepada_tgl_lhr) }}"
                                                                autocomplete="kepada_tgl_lhr" autofocus>

                                                            @error('kepada_tgl_lhr')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <label for="kepada_gender"
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ __('Jenis Kelamin Anak') }}</label>

                                                        <div class="col-md-8">
                                                            <select class="form-control @error('kepada_gender') is-invalid @enderror"
                                                                id="kepada_gender" name="kepada_gender"
                                                                data-placeholder="Jenis Kelamin">
                                                            </select>
                                                            @error('kepada_gender')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="kepada_hubungan"
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ __('Hubungan Dengan Wali') }}</label>

                                                        <div class="col-md-8">
                                                            <select class="form-control @error('kepada_hubungan') is-invalid @enderror"
                                                                id="kepada_hubungan" name="kepada_hubungan" data-placeholder="Hubungan">
                                                                <option value=""></option>
                                                                <option value="Putranya">Putranya</option>
                                                                <option value="Putrinya">Putrinya</option>
                                                                <option value="Cucunya">Cucunya</option>
                                                                <option value="Keluarga">Keluarga</option>
                                                            </select>
                                                            @error('kepada_hubungan')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="kepada_sekolah"
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama Sekolah') }}</label>

                                                        <div class="col-md-8">
                                                            <input id="kepada_sekolah" type="kepada_sekolah"
                                                                class="form-control @error('kepada_sekolah') is-invalid @enderror"
                                                                name="kepada_sekolah"
                                                                value="{{ old('kepada_sekolah', $suratKeterangan->kepada_sekolah) }}"
                                                                autocomplete="kepada_sekolah">

                                                            @error('kepada_sekolah')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="kepada_kelas"
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ __('Kelas/Semester') }}</label>

                                                        <div class="col-md-8">
                                                            <input id="kepada_kelas" type="kepada_kelas"
                                                                class="form-control @error('kepada_kelas') is-invalid @enderror"
                                                                name="kepada_kelas"
                                                                value="{{ old('kepada_kelas', $suratKeterangan->kepada_kelas) }}"
                                                                autocomplete="kepada_kelas">

                                                            @error('kepada_kelas')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="kepada_alamat_sekolah"
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ __('Alamat Sekolah') }}</label>

                                                        <div class="col-md-8">
                                                            <textarea class="form-control @error('kepada_alamat_sekolah') is-invalid @enderror" id="kepada_alamat_sekolah"
                                                                name="kepada_alamat_sekolah" autocomplete="kepada_alamat_sekolah" autofocus>{{ old('kepada_alamat_sekolah', $suratKeterangan->kepada_alamat_sekolah) }}</textarea>
                                                            @error('kepada_alamat_sekolah')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ========== KANAN: KEPADA + PERUNTUKAN + PENGANTAR ========== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">
                                                <x-peruntukan :peruntukan="$suratKeterangan->peruntukan" :readonly="false" />
                                                    
                                                <div class="row mb-3">
                                                    <label for="kategori"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Kategori') }}</label>

                                                    <div class="col-md-8">
                                                        <select class="form-control @error('kategori') is-invalid @enderror"
                                                            id="kategori" name="kategori" data-placeholder="Kategori">
                                                            <option value=""></option>
                                                            <option value="DTKS">DTKS</option>
                                                            <option value="Non DTKS">Non DTKS</option>
                                                        </select>
                                                        @error('kategori')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <x-pengantar :pengantar="$suratKeterangan->pengantar" :readonly="false" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- END ROW --}}

                                {{-- ========== BUTTON UPDATE ========== --}}
                                <div class="d-flex justify-content-center gap-3 mb-3">
                                    <a href="{{ route('sktm.warga') }}" class="btn btn-danger py-2 px-4 me-2" style="border-radius: 8px">
                                        Batal
                                    </a>
                                    <button type="button" class="btn text-white py-2 px-4"
                                            style="background: #7896B2; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalEditSktm">
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
                <x-confirm-ajukan modalId="modalEditSktm" formId="formEditSktm"
                    title="Yakin Ingin Mengubah Surat Ini?"
                    message="Pastikan perubahan sudah benar sebelum mengirim pembaruan."
                    agreeLabel="Saya memastikan bahwa data yang saya ubah sudah benar."
                    cancelText="Cek Lagi"
                    confirmText="Simpan Perubahan" />
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript" src="{{ asset('assets/js/personal.js') }}"></script>
        <script>
            $(document).ready(function() {
                $("#kepada_gender").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    ajax: {
                        url: route("gender.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#kategori").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                });
                $("#kepada_hubungan").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                });
                if ('{{ $suratKeterangan->jenis }}' == 'sekolah') {
                    $("#flexRadioDefault2").attr('checked', true).trigger('click');
                    handleChangeRegisterAs('sekolah');


                    $("#kepada_gender").select2("trigger", "select", {
                        data: {
                            id: '{{ $suratKeterangan->kepada_gender }}',
                            text: '{{ $suratKeterangan->kepada_gender_nm }}',
                        },
                    });

                    $("#kepada_hubungan").select2("trigger", "select", {
                        data: {
                            id: '{{ $suratKeterangan->kepada_hubungan }}',
                            text: '{{ $suratKeterangan->kepada_hubungan }}',
                        },
                    });
                }

                $("#kategori").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->kategori }}',
                        text: '{{ $suratKeterangan->kategori }}',
                    },
                });
            });

            const handleChangeRegisterAs = (value) => {
                if (value == 'sekolah') {
                    $('#input-sekolah').removeClass('d-none');
                } else {
                    $('#input-sekolah').addClass('d-none');
                }
            }
        </script>
    @endpush
@endsection
