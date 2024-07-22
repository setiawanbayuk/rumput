@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('suket.store') }}">
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
                                    <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Pengikut</div>
                                    <div id="data_pengikut" name="data_pengikut">
                                        <div class="row mb-3">
                                            <label for="pengikut_nik"
                                                class="col-md-3 col-form-label text-md-end">{{ __('NIK') }}</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control @error('pengikut_nik') is-invalid @enderror"
                                                        id="pengikut_nik" name="pengikut_nik[]"
                                                        placeholder="Masukkan 16 digit NIK" aria-label="NIK"
                                                        aria-describedby="basic-addon2">
                                                    {{-- <button type="button" class="input-group-text btn btn-subtle-primary"
                                                        onclick="checkNIK()">CARI</button> --}}

                                                    @error('pengikut_nik')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="pengikut"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama') }}</label>

                                            <div class="col-md-8">
                                                <input id="pengikut" type="text"
                                                    class="form-control @error('pengikut') is-invalid @enderror"
                                                    name="pengikut[]" value="{{ old('pengikut') }}" autocomplete="pengikut">

                                                @error('pengikut')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="pengikut_gender"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Jenis Kelamin') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('pengikut_gender') is-invalid @enderror"
                                                    id="pengikut_gender" name="pengikut_gender[]"
                                                    data-placeholder="Jenis Kelamin">
                                                </select>
                                                @error('pengikut_gender')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="row mb-3">
                                            <label for="pengikut_status_kwn"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Status Perkawinan') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('pengikut_status_kwn') is-invalid @enderror"
                                                    id="pengikut_status_kwn" name="pengikut_status_kwn[]"
                                                    data-placeholder="Status Perkawinan">
                                                </select>
                                                @error('pengikut_status_kwn')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="pengikut_hubungan"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Hubungan Keluarga') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control select2-hubungan @error('pengikut_hubungan') is-invalid @enderror"
                                                    id="pengikut_hubungan" name="pengikut_hubungan[]"
                                                    data-placeholder="Hubungan">
                                                    <option value=""></option>
                                                    <option value="Putranya">Putranya</option>
                                                    <option value="Putrinya">Putrinya</option>
                                                    <option value="Cucunya">Cucunya</option>
                                                    <option value="Keluarga">Keluarga</option>
                                                </select>
                                                @error('pengikut_hubungan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="pengikut_nik"
                                                class="col-md-3 col-form-label text-md-end">{{ __('NIK') }}</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control @error('pengikut_nik') is-invalid @enderror"
                                                        id="pengikut_nik" name="pengikut_nik[]"
                                                        placeholder="Masukkan 16 digit NIK" aria-label="NIK"
                                                        aria-describedby="basic-addon2">
                                                    {{-- <button type="button" class="input-group-text btn btn-subtle-primary"
                                                        onclick="checkNIK()">CARI</button> --}}

                                                    @error('pengikut_nik')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="pengikut"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama') }}</label>

                                            <div class="col-md-8">
                                                <input id="pengikut" type="text"
                                                    class="form-control @error('pengikut') is-invalid @enderror"
                                                    name="pengikut[]" value="{{ old('pengikut') }}"
                                                    autocomplete="pengikut">

                                                @error('pengikut')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="pengikut_gender"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Jenis Kelamin') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('pengikut_gender') is-invalid @enderror"
                                                    id="pengikut_gender" name="pengikut_gender[]"
                                                    data-placeholder="Jenis Kelamin">
                                                </select>
                                                @error('pengikut_gender')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="row mb-3">
                                            <label for="pengikut_status_kwn"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Status Perkawinan') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('pengikut_status_kwn') is-invalid @enderror"
                                                    id="pengikut_status_kwn" name="pengikut_status_kwn[]"
                                                    data-placeholder="Status Perkawinan">
                                                </select>
                                                @error('pengikut_status_kwn')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="pengikut_hubungan"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Hubungan Keluarga') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control select2-hubungan @error('pengikut_hubungan') is-invalid @enderror"
                                                    id="pengikut_hubungan" name="pengikut_hubungan[]"
                                                    data-placeholder="Hubungan">
                                                    <option value=""></option>
                                                    <option value="Putranya">Putranya</option>
                                                    <option value="Putrinya">Putrinya</option>
                                                    <option value="Cucunya">Cucunya</option>
                                                    <option value="Keluarga">Keluarga</option>
                                                </select>
                                                @error('pengikut_hubungan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row mb-0">
                                        <div class="col-md-8 offset-md-3">
                                            <button type="button" class="btn btn-success"><i
                                                    class="ri-user-add-fill"></i>
                                                <span>Tambah</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card-header bg-transparent mb-3 text-center fw-bold">Bepergian / Boro Ke
                                    </div>
                                    <x-boro><x-slot:alamat_boro></x-slot:alamat_boro></x-boro>
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
        <script type="text/javascript" src="{{ asset('assets/js/boro.js') }}"></script>
        <script>
            $(document).ready(function() {
                // $(".select2-hubungan").select2();
                $(".select2-hubungan").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                });
            });
        </script>
    @endpush
@endsection
