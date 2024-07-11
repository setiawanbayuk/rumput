@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('sktm.store') }}">
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
                                        <label for="nip" class="col-md-3 col-form-label text-md-end">Jenis SKTM</label>
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
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama Anak') }}</label>

                                            <div class="col-md-8">
                                                <input id="kepada" type="text"
                                                    class="form-control @error('kepada') is-invalid @enderror"
                                                    name="kepada" value="{{ old('kepada') }}"
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
                                                class="col-md-3 col-form-label text-md-end">{{ __('Tempat/Tgl. Lahir') }}</label>

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
                                                    name="kepada_tgl_lhr" value="{{ old('kepada_tgl_lhr') }}" autocomplete="kepada_tgl_lhr"
                                                    autofocus>

                                                @error('kepada_tgl_lhr')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="kepada_gender"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Jenis Kelamin Anak') }}</label>

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
                                                class="col-md-3 col-form-label text-md-end">{{ __('Hubungan Dengan Wali') }}</label>

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
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama Sekolah') }}</label>

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
                                                class="col-md-3 col-form-label text-md-end">{{ __('Kelas/Semester') }}</label>

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
                                                class="col-md-3 col-form-label text-md-end">{{ __('Alamat Sekolah') }}</label>

                                            <div class="col-md-8">
                                                <textarea class="form-control @error('kepada_alamat_sekolah') is-invalid @enderror" id="kepada_alamat_sekolah" name="kepada_alamat_sekolah"
                                                    autocomplete="kepada_alamat_sekolah" autofocus>{{ old('kepada_alamat_sekolah') }}</textarea>
                                                @error('kepada_alamat_sekolah')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                    <x-peruntukan><x-slot:peruntukan></x-slot:peruntukan></x-peruntukan>

                                    <div class="row mb-3">
                                        <label for="kategori"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Kategori') }}</label>

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
        <script>
            $(document).ready(function() {

                $("#kategori").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                });

                $("#kepada_gender").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
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
                    minimumInputLenght: 2,
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
