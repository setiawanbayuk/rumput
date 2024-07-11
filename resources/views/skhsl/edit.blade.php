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
                            action="{{ route('skhsl.update', ['id' => $suratKeterangan->id]) }}">
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
                                        <label for="penghasilan"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Penghasilan (Rp.)') }}</label>

                                        <div class="col-md-8">
                                            <input id="penghasilan" type="number"
                                                class="form-control @error('penghasilan') is-invalid @enderror"
                                                name="penghasilan"
                                                value="{{ old('penghasilan', $suratKeterangan->penghasilan) }}"
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
                                            class="col-md-3 col-form-label text-md-end">{{ __('Terbilang') }}</label>

                                        <div class="col-md-8">
                                            <input id="terbilang" type="text"
                                                class="form-control @error('terbilang') is-invalid @enderror"
                                                name="terbilang"
                                                value="{{ old('terbilang', $suratKeterangan->terbilang) }}"
                                                autocomplete="terbilang">

                                            @error('terbilang')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="kepada"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Nama Anak') }}</label>

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
                                        <label for="kepada_ttl"
                                            class="col-md-3 col-form-label text-md-end">{{ __('Tempat/Tgl. Lahir') }}</label>

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
                                            class="col-md-3 col-form-label text-md-end">{{ __('Kelas/Semester') }}</label>

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
                                            class="col-md-3 col-form-label text-md-end">{{ __('Alamat Sekolah') }}</label>

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
