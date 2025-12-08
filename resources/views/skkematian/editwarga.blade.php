{{-- resources/views/suket/edit.blade.php --}}
@extends('layouts.create')

@section('title', $title ?? 'Edit Surat Keterangan')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/profile.png') }}') no-repeat center center; background-size: cover; margin-top:-75px;">
        <div class="container" style="margin-top:125px; margin-bottom:50px;">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-3"
                        style="background-color:rgba(255,255,255,.28); backdrop-filter:blur(10px);">
                        <div class="card-header bg-transparent py-3">
                            <h5 class="my-3 fw-bold text-white text-center" style="letter-spacing:.5px">
                                {{ $title }}
                            </h5>
                        </div>

                        <div class="card-body">
                            <form id="formEditSkkematian" method="POST" enctype="multipart/form-data" 
                                action="{{ route('skkematian.updatewarga', $suratKeterangan->id) }}" >
                                @csrf
                                <input type="hidden" id="nik_pelapor" name="nik_pelapor" value="{{ $suratKeterangan->nik_pelapor }}">
                                {{-- ================== ROW KEDUA KARTU ================== --}}
                                <div class="row g-3" style="min-height:500px;">

                                    {{-- ========== KIRI: KETERANGAN ========== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">

                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Saksi 1</div>

                                                <div class="row mb-3">
                                                    <label for="nik_saksi1"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('NIK') }}</label>

                                                    <div class="col-md-8">
                                                        <div class="input-group">
                                                            <input type="number"
                                                                class="form-control @error('nik_saksi1') is-invalid @enderror"
                                                                id="nik_saksi1" name="nik_saksi1"
                                                                placeholder="Masukkan 16 digit NIK"
                                                                aria-label="NIK"
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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('No. KK') }}</label>

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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama') }}</label>

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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Kewarganegaraan') }}</label>

                                                    <div class="col-md-8">
                                                        <select
                                                            class="form-control @error('kewarganegaraan_saksi1') is-invalid @enderror"
                                                            id="kewarganegaraan_saksi1"
                                                            name="kewarganegaraan_saksi1"
                                                            data-placeholder="Kewarganegaraan">
                                                        </select>
                                                        @error('kewarganegaraan_saksi1')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Saksi 2</div>

                                                <div class="row mb-3">
                                                    <label for="nik_saksi2"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('NIK') }}</label>

                                                    <div class="col-md-8">
                                                        <div class="input-group">
                                                            <input type="number"
                                                                class="form-control @error('nik_saksi2') is-invalid @enderror"
                                                                id="nik_saksi2" name="nik_saksi2"
                                                                placeholder="Masukkan 16 digit NIK"
                                                                aria-label="NIK"
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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('No. KK') }}</label>

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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama') }}</label>

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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Kewarganegaraan') }}</label>

                                                    <div class="col-md-8">
                                                        <select
                                                            class="form-control @error('kewarganegaraan_saksi2') is-invalid @enderror"
                                                            id="kewarganegaraan_saksi2"
                                                            name="kewarganegaraan_saksi2"
                                                            data-placeholder="Kewarganegaraan">
                                                        </select>
                                                        @error('kewarganegaraan_saksi2')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- === PERUNTUKAN & PENGANTAR === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Lampiran
                                                </div>

                                                {{-- Pengantar (WAJIB sesuai controller) --}}
                                                <x-pengantar :pengantar="$suratKeterangan->pengantar" :readonly="false" />
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ========== KANAN: KEPADA + PERUNTUKAN + PENGANTAR ========== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">
                                            <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Jenazah</div>
                                                <div class="row mb-3">
                                                    <label for="nik"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('NIK') }}</label>
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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama') }}</label>

                                                    <div class="col-md-8">
                                                        <input id="name" type="text"
                                                            class="form-control @error('name') is-invalid @enderror"
                                                            name="name" value="{{ old('name', $suratKeterangan->nama) }}"
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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Tgl. & Jam Kematian') }}</label>

                                                    <div class="col-md-4">
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
                                                    <div class="col-md-4">
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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Sebab Kematian') }}</label>

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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Tempat Kematian') }}</label>

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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Yang Menerangkan') }}</label>

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

                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Ayah</div>
                                                <div class="row mb-3">
                                                    <label for="nik_ayah"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('NIK') }}</label>

                                                    <div class="col-md-8">
                                                        <div class="input-group">
                                                            <input type="number"
                                                                class="form-control @error('nik_ayah') is-invalid @enderror"
                                                                id="nik_ayah" name="nik_ayah"
                                                                placeholder="Masukkan 16 digit NIK"
                                                                aria-label="NIK"
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

                                                {{-- <div class="row mb-3">
                                                    <label for="kk_ayah"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('No. KK Ayah') }}</label>

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
                                                </div> --}}

                                                <div class="row mb-3">
                                                    <label for="name_ayah"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama') }}</label>

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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Tempat/Tgl. Lahir') }}</label>

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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Kewarganegaraan') }}</label>

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

                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Ibu</div>
                                                <div class="row mb-3">
                                                    <label for="nik_ibu"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('NIK') }}</label>

                                                    <div class="col-md-8">
                                                        <div class="input-group">
                                                            <input type="number"
                                                                class="form-control @error('nik_ibu') is-invalid @enderror"
                                                                id="nik_ibu" name="nik_ibu"
                                                                placeholder="Masukkan 16 digit NIK"
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

                                                {{-- <div class="row mb-3">
                                                    <label for="kk_ibu"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('No. KK Ibu') }}</label>

                                                    <div class="col-md-8">
                                                        <input id="kk_ibu" type="number"
                                                            class="form-control @error('kk_ibu') is-invalid @enderror"
                                                            name="kk_ibu"
                                                            value="{{ old('kk_ibu', $suratKeterangan->kk_ibu) }}"
                                                            autocomplete="kk_ibu" autofocus>

                                                        @error('kk_ibu')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div> --}}

                                                <div class="row mb-3">
                                                    <label for="name_ibu"
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama') }}</label>

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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Tempat/Tgl. Lahir') }}</label>

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
                                                        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Kewarganegaraan') }}</label>

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
                                        </div>
                                    </div>
                                </div>
                                {{-- END ROW --}}

                                {{-- ========== BUTTON UPDATE ========== --}}
                                <div class="d-flex justify-content-center gap-3" style="margin-top:75px;">
                                    <a href="{{ route('skkematian.warga') }}" class="btn btn-secondary px-4 py-2 rounded-3">
                                        Batal
                                    </a>
                                    <button type="button" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEditSkkematian">
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
                <x-confirm-ajukan modalId="modalEditSkkematian" formId="formEditSkkematian"
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
                });

                $("#yang_menerangkan").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
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
