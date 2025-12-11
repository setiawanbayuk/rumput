{{-- resources/views/tambahWarga/addwarga_kelahiran.blade.php --}}
@extends('layouts.create')

@section('title', $title ?? 'Tambah Surat Keterangan Kelahiran')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/form.png') }}') no-repeat center center; background-size: cover; margin-top: -75px;">
        <div class="container" style="margin-top: 125px; margin-bottom: 50px;">
            <div class="row justify-content-center">
                <div class="col-md-12">

                    <div class="card border-0 shadow-sm rounded-3"
                        style="background-color: rgba(255, 255, 255, .28); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                        <div class="card-header bg-transparent pt-3 pb-2">
                            <h5 class="my-3 fw-bold text-white text-center" style="letter-spacing:.5px">
                                USULAN PENGAJUAN SURAT KETERANGAN KELAHIRAN
                            </h5>
                        </div>

                        <div class="card-body">
                            <form id="formSkkelahiran" method="POST" enctype="multipart/form-data"
                                action="{{ route('skkelahiran.save') }}">
                                @csrf
                                {{-- nik pemohon (akun warga) --}}
                                <input type="hidden" id="nik_pelapor" name="nik_pelapor" value="{{ auth()->user()->nik }}">

                                {{-- ===== Grid dua kolom ===== --}}
                                <div class="row g-3" style="min-height: 420px">

                                    {{-- KIRI --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">

                                                {{-- === DATA SAKSI 1 === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Saksi 1
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="nik_saksi1" class="col-md-3 col-form-label text-md-start ms-2">NIK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="nik_saksi1" name="nik_saksi1"
                                                            class="form-control @error('nik_saksi1') is-invalid @enderror"
                                                            placeholder="16 digit NIK" value="{{ old('nik_saksi1') }}">
                                                        @error('nik_saksi1')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="kk_saksi1" class="col-md-3 col-form-label text-md-start ms-2">No. KK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="kk_saksi1" name="kk_saksi1"
                                                            class="form-control @error('kk_saksi1') is-invalid @enderror"
                                                            value="{{ old('kk_saksi1') }}">
                                                        @error('kk_saksi1')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="name_saksi1" class="col-md-3 col-form-label text-md-start ms-2">Nama</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="name_saksi1" name="name_saksi1"
                                                            class="form-control @error('name_saksi1') is-invalid @enderror"
                                                            value="{{ old('name_saksi1') }}">
                                                        @error('name_saksi1')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <label for="kewarganegaraan_saksi1"
                                                        class="col-md-3 col-form-label text-md-start ms-2">Kewarganegaraan</label>
                                                    <div class="col-md-8">
                                                        <select id="kewarganegaraan_saksi1" name="kewarganegaraan_saksi1"
                                                            class="form-control @error('kewarganegaraan_saksi1') is-invalid @enderror"
                                                            data-placeholder="Pilih kewarganegaraan"></select>
                                                        @error('kewarganegaraan_saksi1')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- === DATA SAKSI 2 === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Saksi 2
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="nik_saksi2" class="col-md-3 col-form-label text-md-start ms-2">NIK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="nik_saksi2" name="nik_saksi2"
                                                            class="form-control @error('nik_saksi2') is-invalid @enderror"
                                                            placeholder="16 digit NIK" value="{{ old('nik_saksi2') }}">
                                                        @error('nik_saksi2')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="kk_saksi2" class="col-md-3 col-form-label text-md-start ms-2">No. KK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="kk_saksi2" name="kk_saksi2"
                                                            class="form-control @error('kk_saksi2') is-invalid @enderror"
                                                            value="{{ old('kk_saksi2') }}">
                                                        @error('kk_saksi2')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="name_saksi2" class="col-md-3 col-form-label text-md-start ms-2">Nama</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="name_saksi2" name="name_saksi2"
                                                            class="form-control @error('name_saksi2') is-invalid @enderror"
                                                            value="{{ old('name_saksi2') }}">
                                                        @error('name_saksi2')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="kewarganegaraan_saksi2"
                                                        class="col-md-3 col-form-label text-md-start ms-2">Kewarganegaraan</label>
                                                    <div class="col-md-8">
                                                        <select id="kewarganegaraan_saksi2" name="kewarganegaraan_saksi2"
                                                            class="form-control @error('kewarganegaraan_saksi2') is-invalid @enderror"
                                                            data-placeholder="Pilih kewarganegaraan"></select>
                                                        @error('kewarganegaraan_saksi2')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- === PERUNTUKAN & PENGANTAR === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Lampiran
                                                </div>

                                                {{-- Pengantar (WAJIB sesuai controller) --}}
                                                <x-pengantar></x-pengantar>

                                            </div>
                                        </div>
                                    </div>

                                    {{-- KANAN --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">

                                                {{-- === DATA ANAK === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Anak
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="name_anak" class="col-md-3 col-form-label text-md-start ms-2">Nama
                                                        Anak</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="name_anak" name="name_anak"
                                                            class="form-control @error('name_anak') is-invalid @enderror"
                                                            value="{{ old('name_anak') }}">
                                                        @error('name_anak')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="gender_anak" class="col-md-3 col-form-label text-md-start ms-2">Jenis
                                                        Kelamin</label>
                                                    <div class="col-md-8">
                                                        <select id="gender_anak" name="gender_anak"
                                                            class="form-control @error('gender_anak') is-invalid @enderror"
                                                            data-placeholder="Pilih jenis kelamin"></select>
                                                        @error('gender_anak')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="tempat_dilahirkan_anak"
                                                        class="col-md-3 col-form-label text-md-start ms-2">Tempat Dilahirkan</label>
                                                    <div class="col-md-8">
                                                        <select id="tempat_dilahirkan_anak" name="tempat_dilahirkan_anak"
                                                            class="form-control @error('tempat_dilahirkan_anak') is-invalid @enderror"
                                                            data-placeholder="Pilih tempat">
                                                            <option value=""></option>
                                                            <option value="RS/RB">RS/RB</option>
                                                            <option value="Puskesmas">Puskesmas</option>
                                                            <option value="Polindes">Polindes</option>
                                                            <option value="Rumah">Rumah</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                        @error('tempat_dilahirkan_anak')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="tempat_kelahiran_anak"
                                                        class="col-md-3 col-form-label text-md-start ms-2">Tempat Kelahiran</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="tempat_kelahiran_anak"
                                                            name="tempat_kelahiran_anak"
                                                            class="form-control @error('tempat_kelahiran_anak') is-invalid @enderror"
                                                            value="{{ old('tempat_kelahiran_anak') }}">
                                                        @error('tempat_kelahiran_anak')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="hari_tgl_lhr_anak"
                                                        class="col-md-3 col-form-label text-md-start ms-2">Hari & Tgl. Lahir</label>

                                                    <div class="col-md-2">
                                                        <input id="hari_lhr_anak" type="text"
                                                            class="form-control @error('hari_lhr_anak') is-invalid @enderror"
                                                            name="hari_lhr_anak" value="{{ old('hari_lhr_anak') }}"
                                                            autocomplete="hari_lhr_anak" autofocus>

                                                        @error('hari_lhr_anak')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input id="tgl_lhr_anak" type="date"
                                                            class="form-control @error('tgl_lhr_anak') is-invalid @enderror"
                                                            name="tgl_lhr_anak" value="{{ old('tgl_lhr_anak') }}"
                                                            autocomplete="tgl_lhr_anak" autofocus>

                                                        @error('tgl_lhr_anak')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input id="jam_lhr_anak" type="time"
                                                            class="form-control @error('jam_lhr_anak') is-invalid @enderror"
                                                            name="jam_lhr_anak" value="{{ old('jam_lhr_anak') }}"
                                                            autocomplete="jam_lhr_anak" autofocus>

                                                        @error('jam_lhr_anak')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="jenis_klhr_anak" class="col-md-3 col-form-label text-md-start ms-2">Jenis
                                                        Kelahiran</label>
                                                    <div class="col-md-8">
                                                        <select id="jenis_klhr_anak" name="jenis_klhr_anak"
                                                            class="form-control @error('jenis_klhr_anak') is-invalid @enderror"
                                                            data-placeholder="Pilih jenis kelahiran">
                                                            <option value=""></option>
                                                            <option value="Tunggal">Tunggal</option>
                                                            <option value="Kembar 2">Kembar 2</option>
                                                            <option value="Kembar 3">Kembar 3</option>
                                                            <option value="Kembar 4">Kembar 4</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                        @error('jenis_klhr_anak')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="klhr_ke_anak" class="col-md-3 col-form-label text-md-start ms-2">Kelahiran
                                                        ke</label>
                                                    <div class="col-md-8">
                                                        <input type="number" id="klhr_ke_anak" name="klhr_ke_anak"
                                                            class="form-control @error('klhr_ke_anak') is-invalid @enderror"
                                                            value="{{ old('klhr_ke_anak') }}">
                                                        @error('klhr_ke_anak')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="penolong_klhr_anak"
                                                        class="col-md-3 col-form-label text-md-start ms-2">Penolong Kelahiran</label>
                                                    <div class="col-md-8">
                                                        <select id="penolong_klhr_anak" name="penolong_klhr_anak"
                                                            class="form-control @error('penolong_klhr_anak') is-invalid @enderror"
                                                            data-placeholder="Pilih penolong">
                                                            <option value=""></option>
                                                            <option value="Dokter">Dokter</option>
                                                            <option value="Bidan">Bidan</option>
                                                            <option value="Dukun">Dukun</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                        @error('penolong_klhr_anak')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <label class="col-md-3 col-form-label text-md-start ms-2">BB / TB</label>
                                                    <div class="col-md-3">
                                                        <input type="text" id="bb_anak" name="bb_anak"
                                                            class="form-control @error('bb_anak') is-invalid @enderror"
                                                            placeholder="kg" value="{{ old('bb_anak') }}">
                                                        @error('bb_anak')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-1 d-flex align-items-center">kg</div>
                                                    <div class="col-md-3">
                                                        <input type="text" id="tb_anak" name="tb_anak"
                                                            class="form-control @error('tb_anak') is-invalid @enderror"
                                                            placeholder="cm" value="{{ old('tb_anak') }}">
                                                        @error('tb_anak')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-1 d-flex align-items-center">cm</div>
                                                </div>

                                                {{-- === DATA AYAH === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Ayah
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="nik_ayah" class="col-md-3 col-form-label text-md-start ms-2">NIK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="nik_ayah" name="nik_ayah"
                                                            class="form-control @error('nik_ayah') is-invalid @enderror"
                                                            placeholder="16 digit NIK" value="{{ old('nik_ayah') }}">
                                                        @error('nik_ayah')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="kk_ayah" class="col-md-3 col-form-label text-md-start ms-2">No. KK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="kk_ayah" name="kk_ayah"
                                                            class="form-control @error('kk_ayah') is-invalid @enderror"
                                                            value="{{ old('kk_ayah') }}">
                                                        @error('kk_ayah')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="name_ayah" class="col-md-3 col-form-label text-md-start ms-2">Nama</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="name_ayah" name="name_ayah"
                                                            class="form-control @error('name_ayah') is-invalid @enderror"
                                                            value="{{ old('name_ayah') }}">
                                                        @error('name_ayah')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label class="col-md-3 col-form-label text-md-start ms-2">Tempat / Tgl. Lahir</label>
                                                    <div class="col-md-5">
                                                        <input type="text" id="tempat_lhr_ayah" name="tempat_lhr_ayah"
                                                            class="form-control @error('tempat_lhr_ayah') is-invalid @enderror"
                                                            value="{{ old('tempat_lhr_ayah') }}">
                                                        @error('tempat_lhr_ayah')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input id="tgl_lhr_ayah" type="date"
                                                            class="form-control @error('tgl_lhr_ayah') is-invalid @enderror"
                                                            name="tgl_lhr_ayah" value="{{ old('tgl_lhr_ayah') }}"
                                                            autocomplete="tgl_lhr_ayah" autofocus>

                                                        @error('tgl_lhr_ayah')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <label for="kewarganegaraan_ayah"
                                                        class="col-md-3 col-form-label text-md-start ms-2">Kewarganegaraan</label>
                                                    <div class="col-md-8">
                                                        <select id="kewarganegaraan_ayah" name="kewarganegaraan_ayah"
                                                            class="form-control @error('kewarganegaraan_ayah') is-invalid @enderror"
                                                            data-placeholder="Pilih kewarganegaraan"></select>
                                                        @error('kewarganegaraan_ayah')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- === DATA IBU === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Ibu
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="nik_ibu" class="col-md-3 col-form-label text-md-start ms-2">NIK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="nik_ibu" name="nik_ibu"
                                                            class="form-control @error('nik_ibu') is-invalid @enderror"
                                                            placeholder="16 digit NIK" value="{{ old('nik_ibu') }}">
                                                        @error('nik_ibu')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="kk_ibu" class="col-md-3 col-form-label text-md-start ms-2">No. KK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="kk_ibu" name="kk_ibu"
                                                            class="form-control @error('kk_ibu') is-invalid @enderror"
                                                            value="{{ old('kk_ibu') }}">
                                                        @error('kk_ibu')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="name_ibu" class="col-md-3 col-form-label text-md-start ms-2">Nama</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="name_ibu" name="name_ibu"
                                                            class="form-control @error('name_ibu') is-invalid @enderror"
                                                            value="{{ old('name_ibu') }}">
                                                        @error('name_ibu')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label class="col-md-3 col-form-label text-md-start ms-2">Tempat / Tgl. Lahir</label>
                                                    <div class="col-md-5">
                                                        <input type="text" id="tempat_lhr_ibu" name="tempat_lhr_ibu"
                                                            class="form-control @error('tempat_lhr_ibu') is-invalid @enderror"
                                                            value="{{ old('tempat_lhr_ibu') }}">
                                                        @error('tempat_lhr_ibu')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input id="tgl_lhr_ibu" type="date"
                                                            class="form-control @error('tgl_lhr_ibu') is-invalid @enderror"
                                                            name="tgl_lhr_ibu" value="{{ old('tgl_lhr_ibu') }}"
                                                            autocomplete="tgl_lhr_ibu" autofocus>

                                                        @error('tgl_lhr_ibu')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <label for="kewarganegaraan_ibu"
                                                        class="col-md-3 col-form-label text-md-start ms-2">Kewarganegaraan</label>
                                                    <div class="col-md-8">
                                                        <select id="kewarganegaraan_ibu" name="kewarganegaraan_ibu"
                                                            class="form-control @error('kewarganegaraan_ibu') is-invalid @enderror"
                                                            data-placeholder="Pilih kewarganegaraan"></select>
                                                        @error('kewarganegaraan_ibu')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tombol Ajukan --}}
                                <div class="d-flex justify-content-center" style="margin-top: 100px;">
                                    <button type="button" class="btn-ajukan" data-bs-toggle="modal"
                                        data-bs-target="#modalSkkelahiran">
                                        Ajukan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Modal Konfirmasi --}}
                    <x-confirm-ajukan modalId="modalSkkelahiran" formId="formSkkelahiran"
                        title="Apakah Anda Yakin Ingin Mengirim Ajuan Ini ?"
                        message="Dengan mengirim ajuan ini, Anda menyatakan bahwa seluruh data pribadi yang Anda masukkan, termasuk NIK, Nomor KK, dan dokumen pendukung lainnya, adalah benar, lengkap, dan sesuai dengan kondisi sebenarnya. Anda juga memahami bahwa kesalahan pengisian data atau ketidaksesuaian dokumen dapat mempengaruhi proses verifikasi dan dapat menyebabkan ajuan ditolak."
                        agreeLabel="Saya menyatakan bahwa seluruh data pribadi dan dokumen yang saya kirimkan adalah benar dan sesuai."
                        cancelText="Periksa Kembali" confirmText="Kirim Ajuan" />
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- <script type="text/javascript" src="{{ asset('assets/js/personal.js') }}"></script> --}}
        <script>
            let isProcessingPelapor = false;
            $("#nik_pelapor").keyup(function() {
                if ($(this).val().length == 16) {
                    if (isProcessingPelapor) return; // ⛔ cegah dobel
                    isProcessingPelapor = true;
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
                        complete: function () {
                            isProcessingPelapor = false; // ✅ Allow next request
                        }
                    });
                }
            });

            let isProcessingSaksi1 = false;
            $("#nik_saksi1").keyup(function() {
                if ($(this).val().length == 16) {
                    if (isProcessingSaksi1) return; // ⛔ cegah dobel
                    isProcessingSaksi1 = true;
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
                        complete: function () {
                            isProcessingSaksi1 = false; // ✅ Allow next request
                        }
                    });
                }
            });

            let isProcessingSaksi2 = false;
            $("#nik_saksi2").keyup(function() {
                if ($(this).val().length == 16) {
                    if (isProcessingSaksi2) return; // ⛔ cegah dobel
                    isProcessingSaksi2 = true;
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
                        complete: function () {
                            isProcessingSaksi2 = false; // ✅ Allow next request
                        }
                    });
                }
            });

            let isProcessingAyah = false;
            $("#nik_ayah").keyup(function() {
                if ($(this).val().length == 16) {
                    if (isProcessingAyah) return; // ⛔ cegah dobel
                    isProcessingAyah = true;
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
                        complete: function () {
                            isProcessingAyah = false; // ✅ Allow next request
                        }
                    });
                }
            });

            let isProcessingIbu = false;
            $("#nik_ibu").keyup(function() {
                if ($(this).val().length == 16) {
                    if (isProcessingIbu) return; // ⛔ cegah dobel
                    isProcessingIbu = true;
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
                        complete: function () {
                            isProcessingIbu = false; // ✅ Allow next request
                        }
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


                $("#gender_anak").select2({
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


                $("#tempat_dilahirkan_anak").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                });

                $("#jenis_klhr_anak").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                });

                $("#penolong_klhr_anak").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                });
            });
        </script>
    @endpush
@endsection
