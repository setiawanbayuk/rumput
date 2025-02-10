@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('skkelahiran.update', ['id' => $suratKeterangan->id]) }}">
                            @csrf

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Surat</div>
                                    <x-nosrt>
                                        <x-slot:kd_jenis_surat>{{ $suratKeterangan->kd_jenis_surat }}</x-slot:kd_jenis_surat>
                                        <x-slot:no_urut_surat>{{ $suratKeterangan->no_urut_surat }}</x-slot:no_urut_surat>
                                        <x-slot:instansi_kode>{{ $currentUser->skpd->instansi_kode }}</x-slot:instansi_kode>
                                        <x-slot:tgl_surat>{{ $suratKeterangan->tgl_surat }}</x-slot:tgl_surat>
                                    </x-nosrt>

                                    <x-pengantar></x-pengantar>
                                    <div class="row mb-3">
                                        <label class="col-md-3 col-form-label text-md-end"></label>
                                        <div class="col-md-8">
                                            <img src="{{ asset($suratKeterangan->pengantar) }}" alt=""
                                                height="100%" style="max-width: 400px;">
                                        </div>
                                    </div>
                                    <div id="data-pelapor" name="data-pelapor">
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Pelapor</div>
                                        <div class="row mb-3">
                                            <label for="nik_pelapor"
                                                class="col-md-3 col-form-label text-md-end">{{ __('NIK Pelapor') }}</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control @error('nik_pelapor') is-invalid @enderror"
                                                        id="nik_pelapor" name="nik_pelapor"
                                                        placeholder="Masukkan 16 digit NIK" aria-label="NIK"
                                                        value="{{ old('nik_pelapor', $suratKeterangan->nik_pelapor) }}"
                                                        aria-describedby="basic-addon2">
                                                    {{-- <button type="button" class="input-group-text btn btn-subtle-primary"
                                                        onclick="checkNIK()">CARI</button> --}}

                                                    @error('nik_pelapor')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="no_dokumen_perjalanan"
                                                class="col-md-3 col-form-label text-md-end">{{ __('No. Dokumen Perjalanan') }}</label>

                                            <div class="col-md-8">
                                                <input id="no_dokumen_perjalanan" type="number"
                                                    class="form-control @error('no_dokumen_perjalanan') is-invalid @enderror"
                                                    name="no_dokumen_perjalanan" value="{{ old('no_dokumen_perjalanan', $suratKeterangan->no_dokumen_perjalanan) }}"
                                                    autocomplete="no_dokumen_perjalanan" autofocus>

                                                @error('no_dokumen_perjalanan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="kk_pelapor"
                                                class="col-md-3 col-form-label text-md-end">{{ __('No. KK Pelapor') }}</label>

                                            <div class="col-md-8">
                                                <input id="kk_pelapor" type="number"
                                                    class="form-control @error('kk_pelapor') is-invalid @enderror"
                                                    name="kk_pelapor"
                                                    value="{{ old('kk_pelapor', $suratKeterangan->kk_pelapor) }}"
                                                    autocomplete="kk_pelapor" autofocus>

                                                @error('kk_pelapor')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="name_pelapor"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama Pelapor') }}</label>

                                            <div class="col-md-8">
                                                <input id="name_pelapor" type="text"
                                                    class="form-control @error('name_pelapor') is-invalid @enderror"
                                                    name="name_pelapor"
                                                    value="{{ old('name_pelapor', $suratKeterangan->nama_pelapor) }}"
                                                    autocomplete="name_pelapor" autofocus>

                                                @error('name_pelapor')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="kewarganegaraan_pelapor"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Kewarganegaraan Pelapor') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('kewarganegaraan_pelapor') is-invalid @enderror"
                                                    id="kewarganegaraan_pelapor" name="kewarganegaraan_pelapor"
                                                    data-placeholder="Kewarganegaraan">
                                                </select>
                                                @error('kewarganegaraan_pelapor')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div id="data-saksi" name="data-saksi">
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Saksi 1</div>
                                        <div class="row mb-3">
                                            <label for="nik_saksi1"
                                                class="col-md-3 col-form-label text-md-end">{{ __('NIK Saksi 1') }}</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control @error('nik_saksi1') is-invalid @enderror"
                                                        id="nik_saksi1" name="nik_saksi1"
                                                        placeholder="Masukkan 16 digit NIK" aria-label="NIK"
                                                        value="{{ old('nik_saksi1', $suratKeterangan->nik_saksi1) }}"
                                                        aria-describedby="basic-addon2">

                                                    @error('nik_saksi1')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="kk_saksi1"
                                                class="col-md-3 col-form-label text-md-end">{{ __('No. KK Saksi 1') }}</label>
                                            <div class="col-md-8">
                                                <input id="kk_saksi1" type="number"
                                                    class="form-control @error('kk_saksi1') is-invalid @enderror"
                                                    name="kk_saksi1"
                                                    value="{{ old('kk_saksi1', $suratKeterangan->kk_saksi1) }}"
                                                    autocomplete="kk_saksi1" autofocus>

                                                @error('kk_saksi1')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="name_saksi1"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama Saksi 1') }}</label>
                                            <div class="col-md-8">
                                                <input id="name_saksi1" type="text"
                                                    class="form-control @error('name_saksi1') is-invalid @enderror"
                                                    name="name_saksi1"
                                                    value="{{ old('name_saksi1', $suratKeterangan->nama_saksi1) }}"
                                                    autocomplete="name_saksi1" autofocus>

                                                @error('name_saksi1')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="kewarganegaraan_saksi1"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Kewarganegaraan Saksi 1') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('kewarganegaraan_saksi1') is-invalid @enderror"
                                                    id="kewarganegaraan_saksi1" name="kewarganegaraan_saksi1"
                                                    data-placeholder="Kewarganegaraan">
                                                </select>
                                                @error('kewarganegaraan_saksi1')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- <div class="garis"></div> --}}

                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Saksi 2</div>

                                        <div class="row mb-3">
                                            <label for="nik_saksi2"
                                                class="col-md-3 col-form-label text-md-end">{{ __('NIK Saksi 2') }}</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control @error('nik_saksi2') is-invalid @enderror"
                                                        id="nik_saksi2" name="nik_saksi2"
                                                        placeholder="Masukkan 16 digit NIK" aria-label="NIK"
                                                        value="{{ old('nik_saksi2', $suratKeterangan->nik_saksi2) }}"
                                                        aria-describedby="basic-addon2">

                                                    @error('nik_saksi2')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="kk_saksi2"
                                                class="col-md-3 col-form-label text-md-end">{{ __('No. KK Saksi 2') }}</label>

                                            <div class="col-md-8">
                                                <input id="kk_saksi2" type="number"
                                                    class="form-control @error('kk_saksi2') is-invalid @enderror"
                                                    name="kk_saksi2"
                                                    value="{{ old('kk_saksi2', $suratKeterangan->kk_saksi2) }}"
                                                    autocomplete="kk_saksi2" autofocus>

                                                @error('kk_saksi2')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="name_saksi2"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama Saksi 2') }}</label>

                                            <div class="col-md-8">
                                                <input id="name_saksi2" type="text"
                                                    class="form-control @error('name_saksi2') is-invalid @enderror"
                                                    name="name_saksi2"
                                                    value="{{ old('name_saksi2', $suratKeterangan->nama_saksi2) }}"
                                                    autocomplete="name_saksi2" autofocus>

                                                @error('name_saksi2')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="kewarganegaraan_saksi2"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Kewarganegaraan Saksi 2') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('kewarganegaraan_saksi2') is-invalid @enderror"
                                                    id="kewarganegaraan_saksi2" name="kewarganegaraan_saksi2"
                                                    data-placeholder="Kewarganegaraan">
                                                </select>
                                                @error('kewarganegaraan_saksi2')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6 border-start">

                                    <div id="data-anak" name="data-anak">
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Anak</div>

                                        <div class="row mb-3">
                                            <label for="name_anak"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama') }}</label>

                                            <div class="col-md-8">
                                                <input id="name_anak" type="text"
                                                    class="form-control @error('name_anak') is-invalid @enderror"
                                                    name="name_anak"
                                                    value="{{ old('name_anak', $suratKeterangan->nama_anak) }}"
                                                    autocomplete="name_anak" autofocus>

                                                @error('name_anak')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="row mb-3">
                                            <label for="gender_anak"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Jenis Kelamin') }}</label>

                                            <div class="col-md-8">
                                                <select class="form-control @error('gender_anak') is-invalid @enderror"
                                                    id="gender_anak" name="gender_anak" data-placeholder="Jenis Kelamin">
                                                </select>
                                                @error('gender_anak')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="tempat_dilahirkan_anak"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Tempat Dilahirkan') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('tempat_dilahirkan_anak') is-invalid @enderror"
                                                    id="tempat_dilahirkan_anak" name="tempat_dilahirkan_anak"
                                                    data-placeholder="Tempat Dilahirkan">
                                                    <option value=""></option>
                                                    <option value="RS/RB">RS/RB</option>
                                                    <option value="Puskesmas">Puskesmas</option>
                                                    <option value="Polindes">Polindes</option>
                                                    <option value="Rumah">Rumah</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                                @error('tempat_dilahirkan_anak')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="tempat_kelahiran_anak"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Tempat Kelahiran') }}</label>

                                            <div class="col-md-8">
                                                <input id="tempat_kelahiran_anak" type="text"
                                                    class="form-control @error('tempat_kelahiran_anak') is-invalid @enderror"
                                                    name="tempat_kelahiran_anak"
                                                    value="{{ old('tempat_kelahiran_anak', $suratKeterangan->tempat_kelahiran_anak) }}"
                                                    autocomplete="tempat_kelahiran_anak" autofocus>

                                                @error('tempat_kelahiran_anak')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="hari_tgl_lhr_anak"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Hari dan Tgl. Lahir Anak') }}</label>

                                            <div class="col-md-4">
                                                <input id="hari_lhr_anak" type="text"
                                                    class="form-control @error('hari_lhr_anak') is-invalid @enderror"
                                                    name="hari_lhr_anak"
                                                    value="{{ old('hari_lhr_anak', $suratKeterangan->hari_lhr_anak) }}"
                                                    autocomplete="hari_lhr_anak" autofocus>

                                                @error('hari_lhr_anak')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <input id="tgl_lhr_anak" type="date"
                                                    class="form-control @error('tgl_lhr_anak') is-invalid @enderror"
                                                    name="tgl_lhr_anak"
                                                    value="{{ old('tgl_lhr_anak', $suratKeterangan->tgl_lhr_anak) }}"
                                                    autocomplete="tgl_lhr_anak" autofocus>

                                                @error('tgl_lhr_anak')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <input id="jam_lhr_anak" type="time"
                                                    class="form-control @error('jam_lhr_anak') is-invalid @enderror"
                                                    name="jam_lhr_anak"
                                                    value="{{ old('jam_lhr_anak', $suratKeterangan->jam_lhr_anak) }}"
                                                    autocomplete="jam_lhr_anak" autofocus>

                                                @error('jam_lhr_anak')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="jenis_klhr_anak"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Jenis Kelahiran') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('jenis_klhr_anak') is-invalid @enderror"
                                                    id="jenis_klhr_anak" name="jenis_klhr_anak"
                                                    data-placeholder="Jenis Kelahiran">
                                                    <option value=""></option>
                                                    <option value="Tunggal">Tunggal</option>
                                                    <option value="Kembar 2">Kembar 2</option>
                                                    <option value="Kembar 3">Kembar 3</option>
                                                    <option value="Kembar 4">Kembar 4</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                                @error('jenis_klhr_anak')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="klhr_ke_anak"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Kelahiran ke') }}</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control @error('klhr_ke_anak') is-invalid @enderror"
                                                        id="klhr_ke_anak" name="klhr_ke_anak"
                                                        value="{{ old('klhr_ke_anak', $suratKeterangan->klhr_ke_anak) }}">

                                                    @error('klhr_ke_anak')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="penolong_klhr_anak"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Penolong Kelahiran') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('penolong_klhr_anak') is-invalid @enderror"
                                                    id="penolong_klhr_anak" name="penolong_klhr_anak"
                                                    data-placeholder="Penolong Kelahiran">
                                                    <option value=""></option>
                                                    <option value="Dokter">Dokter</option>
                                                    <option value="Bidan">Bidan</option>
                                                    <option value="Dukun">Dukun</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                                @error('penolong_klhr_anak')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="bb_anak"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Berat') }}</label>
                                            <div class="col-md-2">
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control @error('bb_anak') is-invalid @enderror"
                                                        id="bb_anak" name="bb_anak"
                                                        value="{{ old('bb_anak', $suratKeterangan->bb_anak) }}">

                                                    @error('bb_anak')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <label for="tb_anak"
                                                class="col-md-1 col-form-label text-md-start">{{ __('kg') }}</label>
                                            <label for="tb_anak"
                                                class="col-md-2 col-form-label text-md-end">{{ __('Panjang') }}</label>
                                            <div class="col-md-2">
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control @error('tb_anak') is-invalid @enderror"
                                                        id="tb_anak" name="tb_anak"
                                                        value="{{ old('tb_anak', $suratKeterangan->tb_anak) }}">

                                                    @error('tb_anak')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <label for="tb_anak"
                                                class="col-md-1 col-form-label text-md-start">{{ __('cm') }}</label>
                                        </div>

                                    </div>
                                    <div id="data-ortu" name="data-ortu">
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Ayah
                                        </div>
                                        <div class="row mb-3">
                                            <label for="nik_ayah"
                                                class="col-md-3 col-form-label text-md-end">{{ __('NIK Ayah') }}</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control @error('nik_ayah') is-invalid @enderror"
                                                        id="nik_ayah" name="nik_ayah"
                                                        placeholder="Masukkan 16 digit NIK" aria-label="NIK"
                                                        value="{{ old('nik_ayah', $suratKeterangan->nik_ayah) }}"
                                                        aria-describedby="basic-addon2">

                                                    @error('nik_ayah')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="kk_ayah"
                                                class="col-md-3 col-form-label text-md-end">{{ __('No. KK Ayah') }}</label>

                                            <div class="col-md-8">
                                                <input id="kk_ayah" type="number"
                                                    class="form-control @error('kk_ayah') is-invalid @enderror"
                                                    name="kk_ayah"
                                                    value="{{ old('kk_ayah', $suratKeterangan->kk_ayah) }}"
                                                    autocomplete="kk_ayah" autofocus>

                                                @error('kk_ayah')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="name_ayah"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama Ayah') }}</label>

                                            <div class="col-md-8">
                                                <input id="name_ayah" type="text"
                                                    class="form-control @error('name_ayah') is-invalid @enderror"
                                                    name="name_ayah"
                                                    value="{{ old('name_ayah', $suratKeterangan->nama_ayah) }}"
                                                    autocomplete="name_ayah" autofocus>

                                                @error('name_ayah')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="ttl_ayah"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Tempat/Tgl. Lahir Ayah') }}</label>

                                            <div class="col-md-5">
                                                <input id="tempat_lhr_ayah" type="text"
                                                    class="form-control @error('tempat_lhr_ayah') is-invalid @enderror"
                                                    name="tempat_lhr_ayah"
                                                    value="{{ old('tempat_lhr_ayah', $suratKeterangan->tempat_lhr_ayah) }}"
                                                    autocomplete="tempat_lhr_ayah" autofocus>

                                                @error('tempat_lhr_ayah')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <input id="tgl_lhr_ayah" type="date"
                                                    class="form-control @error('tgl_lhr_ayah') is-invalid @enderror"
                                                    name="tgl_lhr_ayah"
                                                    value="{{ old('tgl_lhr_ayah', $suratKeterangan->tgl_lhr_ayah) }}"
                                                    autocomplete="tgl_lhr_ayah" autofocus>

                                                @error('tgl_lhr_ayah')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="kewarganegaraan_ayah"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Kewarganegaraan Ayah') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('kewarganegaraan_ayah') is-invalid @enderror"
                                                    id="kewarganegaraan_ayah" name="kewarganegaraan_ayah"
                                                    data-placeholder="Kewarganegaraan">
                                                </select>
                                                @error('kewarganegaraan_ayah')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Ibu</div>
                                        <div class="row mb-3">
                                            <label for="nik_ibu"
                                                class="col-md-3 col-form-label text-md-end">{{ __('NIK Ibu') }}</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control @error('nik_ibu') is-invalid @enderror"
                                                        id="nik_ibu" name="nik_ibu" placeholder="Masukkan 16 digit NIK"
                                                        value="{{ old('nik_ibu', $suratKeterangan->nik_ibu) }}"
                                                        aria-label="NIK" aria-describedby="basic-addon2">

                                                    @error('nik_ibu')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="kk_ibu"
                                                class="col-md-3 col-form-label text-md-end">{{ __('No. KK Ibu') }}</label>

                                            <div class="col-md-8">
                                                <input id="kk_ibu" type="number"
                                                    class="form-control @error('kk_ibu') is-invalid @enderror"
                                                    name="kk_ibu" value="{{ old('kk_ibu', $suratKeterangan->kk_ibu) }}"
                                                    autocomplete="kk_ibu" autofocus>

                                                @error('kk_ibu')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="name_ibu"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama Ibu') }}</label>

                                            <div class="col-md-8">
                                                <input id="name_ibu" type="text"
                                                    class="form-control @error('name_ibu') is-invalid @enderror"
                                                    name="name_ibu"
                                                    value="{{ old('name_ibu', $suratKeterangan->nama_ibu) }}"
                                                    autocomplete="name_ibu" autofocus>

                                                @error('name_ibu')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="ttl_ibu"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Tempat/Tgl. Lahir Ibu') }}</label>

                                            <div class="col-md-5">
                                                <input id="tempat_lhr_ibu" type="text"
                                                    class="form-control @error('tempat_lhr_ibu') is-invalid @enderror"
                                                    name="tempat_lhr_ibu"
                                                    value="{{ old('tempat_lhr_ibu', $suratKeterangan->tempat_lhr_ibu) }}"
                                                    autocomplete="tempat_lhr_ibu" autofocus>

                                                @error('tempat_lhr_ibu')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <input id="tgl_lhr_ibu" type="date"
                                                    class="form-control @error('tgl_lhr_ibu') is-invalid @enderror"
                                                    name="tgl_lhr_ibu"
                                                    value="{{ old('tgl_lhr_ibu', $suratKeterangan->tgl_lhr_ibu) }}"
                                                    autocomplete="tgl_lhr_ibu" autofocus>

                                                @error('tgl_lhr_ibu')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="kewarganegaraan_ibu"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Kewarganegaraan Ibu') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('kewarganegaraan_ibu') is-invalid @enderror"
                                                    id="kewarganegaraan_ibu" name="kewarganegaraan_ibu"
                                                    data-placeholder="Kewarganegaraan">
                                                </select>
                                                @error('kewarganegaraan_ibu')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row mb-0">
                                        <div class="d-flex col-md-8 offset-md-3 justify-content-md-end">
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
        {{-- <script type="text/javascript" src="{{ asset('assets/js/personal.js') }}"></script> --}}
        <script>
            $("#nik_pelapor").keyup(function() {
                if ($(this).val().length == 16) {
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#kk_pelapor").val(response.kk);
                            $("#name_pelapor").val(response.name);
                            $("#kewarganegaraan_pelapor").select2("trigger", "select", {
                                data: {
                                    id: response.kewarganegaraan,
                                    text: response.kewarganegaraan_nm,
                                },
                            });

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            // const response = JSON.parse(xhr.responseText);
                            // // console.log('hey error', response.message);
                            // Swal.fire({
                            //     title: "Ooopppsss...",
                            //     text: response.message,
                            //     icon: "error",
                            //     confirmButtonText: "OK",
                            // });
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                    });

                }
            });

            $("#nik_saksi1").keyup(function() {
                if ($(this).val().length == 16) {
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#kk_saksi1").val(response.kk);
                            $("#name_saksi1").val(response.name);
                            $("#kewarganegaraan_saksi1").select2("trigger", "select", {
                                data: {
                                    id: response.kewarganegaraan,
                                    text: response.kewarganegaraan_nm,
                                },
                            });

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                    });

                }
            });

            $("#nik_saksi2").keyup(function() {
                if ($(this).val().length == 16) {
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#kk_saksi2").val(response.kk);
                            $("#name_saksi2").val(response.name);
                            $("#kewarganegaraan_saksi2").select2("trigger", "select", {
                                data: {
                                    id: response.kewarganegaraan,
                                    text: response.kewarganegaraan_nm,
                                },
                            });

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                    });

                }
            });

            $("#nik_ayah").keyup(function() {
                if ($(this).val().length == 16) {
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#kk_ayah").val(response.kk);
                            $("#name_ayah").val(response.name);
                            $("#tempat_lhr_ayah").val(response.tempat_lhr);
                            $("#tgl_lhr_ayah").val(response.tgl_lhr);
                            $("#kewarganegaraan_ayah").select2("trigger", "select", {
                                data: {
                                    id: response.kewarganegaraan,
                                    text: response.kewarganegaraan_nm,
                                },
                            });

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                    });

                }
            });

            $("#nik_ibu").keyup(function() {
                if ($(this).val().length == 16) {
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#kk_ibu").val(response.kk);
                            $("#name_ibu").val(response.name);
                            $("#tempat_lhr_ibu").val(response.tempat_lhr);
                            $("#tgl_lhr_ibu").val(response.tgl_lhr);
                            $("#kewarganegaraan_ibu").select2("trigger", "select", {
                                data: {
                                    id: response.kewarganegaraan,
                                    text: response.kewarganegaraan_nm,
                                },
                            });

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                    });

                }
            });

            $(document).ready(function() {
                $("#kewarganegaraan_pelapor").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#kewarganegaraan_saksi1").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#kewarganegaraan_saksi2").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#kewarganegaraan_ayah").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#kewarganegaraan_ibu").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });


                $("#gender_anak").select2({
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


                $("#tempat_dilahirkan_anak").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                });

                $("#jenis_klhr_anak").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                });

                $("#penolong_klhr_anak").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                });

                $("#kewarganegaraan_pelapor").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->kewarganegaraan_pelapor }}',
                        text: '{{ $suratKeterangan->kewarganegaraan_pelapor_nm }}',
                    },
                });
                $("#kewarganegaraan_saksi1").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->kewarganegaraan_saksi1 }}',
                        text: '{{ $suratKeterangan->kewarganegaraan_saksi1_nm }}',
                    },
                });
                $("#kewarganegaraan_saksi2").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->kewarganegaraan_saksi2 }}',
                        text: '{{ $suratKeterangan->kewarganegaraan_saksi2_nm }}',
                    },
                });
                $("#kewarganegaraan_ayah").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->kewarganegaraan_ayah }}',
                        text: '{{ $suratKeterangan->kewarganegaraan_ayah_nm }}',
                    },
                });
                $("#kewarganegaraan_ibu").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->kewarganegaraan_ibu }}',
                        text: '{{ $suratKeterangan->kewarganegaraan_ibu_nm }}',
                    },
                });
                $("#gender_anak").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->gender_anak }}',
                        text: '{{ $suratKeterangan->gender_anak_nm }}',
                    },
                });
                $("#tempat_dilahirkan_anak").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->tempat_dilahirkan_anak }}',
                        text: '{{ $suratKeterangan->tempat_dilahirkan_anak_nm }}',
                    },
                });
                $("#jenis_klhr_anak").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->jenis_klhr_anak }}',
                        text: '{{ $suratKeterangan->jenis_klhr_anak_nm }}',
                    },
                });
                $("#penolong_klhr_anak").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->penolong_klhr_anak }}',
                        text: '{{ $suratKeterangan->penolong_klhr_anak_nm }}',
                    },
                });
            });
        </script>
    @endpush
@endsection
