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
                        <form method="POST" enctype="multipart/form-data" action="{{ route('skhsl.store') }}">
                            @csrf

                            <div class="row" style="min-height: 500px;">
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-1 shadow-sm rounded-4" style="background: #fff; border-color: #AEA07A">
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
                                    <div class="card h-80 border-1 shadow-sm rounded-4" style="background: #fff; border-color: #AEA07A">
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <label for="penghasilan"
                                                    class="col-md-3 col-form-label text-md-start ms-2">{{ __('Penghasilan (Rp.)') }}</label>

                                                <div class="col-md-8">
                                                    <input id="penghasilan" type="number"
                                                        class="form-control @error('penghasilan') is-invalid @enderror"
                                                        name="penghasilan" value="{{ old('penghasilan') }}"
                                                        autocomplete="penghasilan">

                                                    {{-- <input type="hidden" class="form-control" id="serapan_h" name="serapan_h"
                                                        required> --}}
                                                    @error('penghasilan')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="terbilang"
                                                    class="col-md-3 col-form-label text-md-start ms-2">{{ __('Terbilang') }}</label>

                                                <div class="col-md-8">
                                                    <input id="terbilang" type="text"
                                                        class="form-control @error('terbilang') is-invalid @enderror"
                                                        name="terbilang" value="{{ old('terbilang') }}" autocomplete="terbilang">

                                                    @error('terbilang')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="kepada"
                                                    class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama Anak') }}</label>

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
                                            <div class="row mb-3">
                                                <label for="kepada_ttl"
                                                    class="col-md-3 col-form-label text-md-start ms-2">{{ __('Tempat/Tgl. Lahir') }}</label>

                                                <div class="col-md-5">
                                                    <input id="kepada_tempat_lhr" type="text"
                                                        class="form-control @error('kepada_tempat_lhr') is-invalid @enderror"
                                                        name="kepada_tempat_lhr" value="{{ old('kepada_tempat_lhr') }}"
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
                                                        name="kepada_tgl_lhr" value="{{ old('kepada_tgl_lhr') }}"
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
                                                        id="kepada_gender" name="kepada_gender" data-placeholder="Jenis Kelamin">
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
                                                    <input id="kepada_sekolah" type="text"
                                                        class="form-control @error('kepada_sekolah') is-invalid @enderror"
                                                        name="kepada_sekolah" value="{{ old('kepada_sekolah') }}"
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
                                                    <input id="kepada_kelas" type="text"
                                                        class="form-control @error('kepada_kelas') is-invalid @enderror"
                                                        name="kepada_kelas" value="{{ old('kepada_kelas') }}"
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
                                                        name="kepada_alamat_sekolah" autocomplete="kepada_alamat_sekolah" autofocus>{{ old('kepada_alamat_sekolah') }}</textarea>
                                                    @error('kepada_alamat_sekolah')
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
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ ucwords(str_replace('_', ' ', $item)) }}</label>

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
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center mt-4">
                                        <a href="{{ url()->previous() ?? route('skhsl.index') }}" class="btn btn-danger py-2 px-4 me-2" style="border-radius: 8px">
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

                $("#kepada_hubungan").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                });
            });
            $('#penghasilan').keyup(function() {
                var txtsrc = $(this);
                var txtout = $("#terbilang");
                if (txtsrc.val() != "") {
                    readNumbers(txtsrc, txtout);
                } else {
                    txtout.val("");
                }
            });
        </script>
    @endpush
@endsection
