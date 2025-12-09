{{-- resources/views/skkelahiran/show.blade.php --}}
@extends('layouts.create')

@section('title', $title)

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100"
    style="background:url('{{ asset('assets/form.png') }}') no-repeat center center; background-size:cover; margin-top:-75px;">
    <div class="container" style="margin-top:125px; margin-bottom:50px;">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm rounded-3"
                    style="background-color:rgba(255,255,255,.28); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);">

                    <div class="card-header bg-transparent text-center pt-3 pb-2">
                        <h5 class="mt-3 fw-bold text-white" style="letter-spacing:.5px">
                            {{ $title }}
                        </h5>
                        <h6 class="mb-3 fw-semibold text-white">
                            No. Surat : {{ $suratKeterangan->getNoSrt($suratKeterangan) }}
                        </h6>
                    </div>

                    <div class="card-body">
                        <div class="row g-3" style="min-height:420px">

                            {{-- ================= KIRI ================= --}}
                            <div class="col-md-6">
                                <div class="card h-100 border-1 shadow-sm">
                                    <div class="card-body">

                                        {{-- === DATA SAKSI 1 === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Data Saksi 1
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">NIK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nik_saksi1 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">No. KK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kk_saksi1 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Nama</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama_saksi1 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Kewarganegaraan</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kewarganegaraan_saksi1_nm }}">
                                            </div>
                                        </div>

                                        {{-- === DATA SAKSI 2 === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Data Saksi 2
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">NIK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nik_saksi2 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">No. KK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kk_saksi2 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Nama</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama_saksi2 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Kewarganegaraan</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kewarganegaraan_saksi2_nm }}">
                                            </div>
                                        </div>

                                        {{-- Lampiran Pengantar --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Lampiran
                                        </div>

                                        <x-pengantar :pengantar="$suratKeterangan->pengantar" :readonly="true" />
                                    </div>
                                </div>
                            </div>

                            {{-- ================= KANAN ================= --}}
                            <div class="col-md-6">
                                <div class="card h-100 border-1 shadow-sm">
                                    <div class="card-body">

                                        {{-- === DATA ANAK === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Data Anak
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2"> Nama Anak</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama_anak }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Jenis Kelamin</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->gender_anak_nm }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Tempat Dilahirkan</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->tempat_dilahirkan_anak }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Tempat Kelahiran</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->tempat_kelahiran_anak }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="hari_tgl_lhr_anak"
                                                class="col-md-3 col-form-label text-md-start ms-2">Hari & Tgl. Lahir</label>

                                            <div class="col-md-2">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly
                                                    value="{{ $suratKeterangan->hari_lhr_anak }}">
                                            </div>

                                            <div class="col-md-3">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly
                                                    value="{{ $suratKeterangan->tgl_lhr_anak }}">
                                            </div>

                                            <div class="col-md-3">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly
                                                    value="{{ $suratKeterangan->jam_lhr_anak }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Jenis Kelahiran</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->jenis_klhr_anak }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Kelahiran ke</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->klhr_ke_anak }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Penolong Kelahiran</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->penolong_klhr_anak }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">{{__('BB / TB')}}</label>
                                            <div class="col-md-3">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly
                                                    value="{{ $suratKeterangan->bb_anak }}">
                                            </div>
                                            <div class="col-md-1 d-flex align-items-center">{{__('kg')}}</div>
                                            <div class="col-md-3">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly
                                                    value="{{ $suratKeterangan->tb_anak }}">
                                            </div>
                                            <div class="col-md-1 d-flex align-items-center">{{__('cm')}}</div>
                                        </div>

                                        {{-- === DATA AYAH === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Data Ayah
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">NIK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nik_ayah }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">No. KK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kk_ayah }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Nama</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama_ayah }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="ttl_ayah"
                                                class="col-md-3 col-form-label text-md-start ms-2">'Tempat/Tgl. Lahir</label>

                                            <div class="col-md-5">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly
                                                    value="{{ $suratKeterangan->tempat_lhr_ayah }}">
                                            </div>

                                            <div class="col-md-3">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly
                                                    value="{{ $suratKeterangan->tgl_lhr_ayah }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Kewarganegaraan</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kewarganegaraan_ayah_nm }}">
                                            </div>
                                        </div>

                                        {{-- === DATA IBU === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Data Ibu
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">NIK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nik_ibu }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">No. KK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kk_ibu }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Nama</label>  
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama_ibu }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="ttl_ibu"
                                                class="col-md-3 col-form-label text-md-start ms-2">'Tempat/Tgl. Lahir</label>

                                            <div class="col-md-5">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly
                                                    value="{{ $suratKeterangan->tempat_lhr_ibu }}">
                                            </div>

                                            <div class="col-md-3">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly
                                                    value="{{ $suratKeterangan->tgl_lhr_ibu }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Kewarganegaraan</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kewarganegaraan_ibu_nm }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ===== TOMBOL EDIT ===== --}}
                        <div class="d-flex justify-content-center" style="margin-top:100px;">
                            @if ($suratKeterangan->status == 0)
                                <a href="{{ route('skkelahiran.editwarga', $suratKeterangan->id) }}" class="btn-edit">
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
