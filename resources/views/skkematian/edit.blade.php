@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('skkematian.update', ['id' => $suratKeterangan->id]) }}">
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


                                    <div id="data-jenazah" name="data-jenazah">
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Jenazah</div>


                                        <div class="row mb-3">
                                            <label for="nik"
                                                class="col-md-3 col-form-label text-md-end">{{ __('NIK Jenazah') }}</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control @error('nik') is-invalid @enderror"
                                                        id="nik" name="nik"
                                                        placeholder="Masukkan 16 digit NIK" aria-label="NIK"
                                                        value="{{ old('nik', $suratKeterangan->nik) }}"
                                                        aria-describedby="basic-addon2">

                                                    @error('nik')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="name"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Nama') }}</label>

                                            <div class="col-md-8">
                                                <input id="name" type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    name="name" value="{{ old('name', $suratKeterangan->nik) }}"
                                                    autocomplete="name" autofocus>

                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="tgl_kematian"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Tgl. Kematian') }}</label>

                                            <div class="col-md-2">
                                                <input id="tgl_kematian" type="date"
                                                    class="form-control @error('tgl_kematian') is-invalid @enderror"
                                                    name="tgl_kematian" value="{{ old('tgl_kematian', $suratKeterangan->tgl_kematian) }}"
                                                    autocomplete="tgl_kematian" autofocus>

                                                @error('tgl_kematian')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <input id="jam_kematian" type="time"
                                                    class="form-control @error('jam_kematian') is-invalid @enderror"
                                                    name="jam_kematian" value="{{ old('jam_kematian', $suratKeterangan->jam_kematian) }}"
                                                    autocomplete="jam_kematian" autofocus>

                                                @error('jam_kematian')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="sebab_kematian"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Sebab Kematian') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('sebab_kematian') is-invalid @enderror"
                                                    id="sebab_kematian" name="sebab_kematian"
                                                    data-placeholder="Sebab Kematian">
                                                    <option value=""></option>
                                                    <option value="Sakit Biasa/Tua">Sakit Biasa/Tua</option>
                                                    <option value="Wabah Penyakit">Wabah Penyakit</option>
                                                    <option value="Kecelakaan">Kecelakaan</option>
                                                    <option value="Kriminalitas">Kriminalitas</option>
                                                    <option value="Bunuh Diri">Bunuh Diri</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                                @error('sebab_kematian')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="tempat_kematian"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Tempat Kematian') }}</label>

                                            <div class="col-md-8">
                                                <input id="tempat_kematian" type="text"
                                                    class="form-control @error('tempat_kematian') is-invalid @enderror"
                                                    name="tempat_kematian"
                                                    value="{{ old('tempat_kematian', $suratKeterangan->tempat_kematian) }}"
                                                    autocomplete="tempat_kematian" autofocus>

                                                @error('tempat_kematian')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="yang_menerangkan"
                                                class="col-md-3 col-form-label text-md-end">{{ __('Yang Menerangkan') }}</label>

                                            <div class="col-md-8">
                                                <select
                                                    class="form-control @error('yang_menerangkan') is-invalid @enderror"
                                                    id="yang_menerangkan" name="yang_menerangkan"
                                                    data-placeholder="Yang Menerangkan">
                                                    <option value=""></option>
                                                    <option value="Dokter">Dokter</option>
                                                    <option value="Tenaga Kesehatan">Tenaga Kesehatan</option>
                                                    <option value="Kepolisian">Kepolisian</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                                @error('yang_menerangkan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
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


                $("#sebab_kematian").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                });

                $("#yang_menerangkan").select2({
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
                $("#sebab_kematian").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->sebab_kematian }}',
                    },
                });
                $("#yang_menerangkan").select2("trigger", "select", {
                    data: {
                        id: '{{ $suratKeterangan->yang_menerangkan }}',
                    },
                });
            });
        </script>
    @endpush
@endsection
