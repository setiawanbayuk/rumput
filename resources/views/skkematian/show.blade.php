@extends('layouts.create')

@section('title', $title ?? 'Detail Surat Keterangan Kematian')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100"
    style="background:url('{{ asset('assets/form.png') }}') no-repeat center center; background-size:cover; margin-top:-75px;">
    <div class="container" style="margin-top:125px; margin-bottom:50px;">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card border-0 shadow-sm rounded-3"
                    style="background-color:rgba(255,255,255,.28); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px);">

                    <div class="card-header bg-transparent pt-3 pb-2">
                        <h5 class="my-3 fw-bold text-white text-center" style="letter-spacing:.5px">
                            DETAIL SURAT KETERANGAN KEMATIAN
                        </h5>
                    </div>

                    <div class="card-body">

                        <div class="row g-3" style="min-height:420px">

                            {{-- ===================== K I R I ===================== --}}
                            <div class="col-md-6">
                                <div class="card h-100 border-1 shadow-sm">
                                    <div class="card-body">

                                        {{-- === DATA SAKSI 1 === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Data Saksi 1
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">NIK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nik_saksi1 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">No. KK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kk_saksi1 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">Nama</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama_saksi1 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <label class="col-md-4 col-form-label">Kewarganegaraan</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kewarganegaraan_saksi1_nm }}">
                                            </div>
                                        </div>

                                        {{-- === DATA SAKSI 2 === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Data Saksi 2
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">NIK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nik_saksi2 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">No. KK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kk_saksi2 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">Nama</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama_saksi2 }}">
                                            </div>
                                        </div>

                                        <div class="row mb-0">
                                            <label class="col-md-4 col-form-label">Kewarganegaraan</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kewarganegaraan_saksi2_nm }}">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- ===================== K A N A N ===================== --}}
                            <div class="col-md-6">
                                <div class="card h-100 border-1 shadow-sm">
                                    <div class="card-body">

                                        {{-- === DATA JENAZAH === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Data Jenazah
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">NIK Jenazah</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nik }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">Nama</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">Tgl & Jam Meninggal</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly 
                                                    value="{{ $suratKeterangan->tgl_kematian }} — {{ $suratKeterangan->jam_kematian }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">Sebab Kematian</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->sebab_kematian }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">Tempat Kematian</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->tempat_kematian }}">
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <label class="col-md-4 col-form-label">Yang Menerangkan</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->yang_menerangkan }}">
                                            </div>
                                        </div>

                                        {{-- === DATA AYAH === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Data Ayah
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">NIK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nik_ayah }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">No. KK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kk_ayah }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">Nama</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama_ayah }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">Tempat / Tgl Lahir</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly
                                                    value="{{ $suratKeterangan->tempat_lhr_ayah }} — {{ $suratKeterangan->tgl_lhr_ayah }}">
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <label class="col-md-4 col-form-label">Kewarganegaraan</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kewarganegaraan_ayah_nm }}">
                                            </div>
                                        </div>

                                        {{-- === DATA IBU === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Data Ibu
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">NIK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nik_ibu }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">No. KK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kk_ibu }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">Nama</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama_ibu }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-4 col-form-label">Tempat / Tgl Lahir</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly
                                                    value="{{ $suratKeterangan->tempat_lhr_ibu }} — {{ $suratKeterangan->tgl_lhr_ibu }}">
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <label class="col-md-4 col-form-label">Kewarganegaraan</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->kewarganegaraan_ibu_nm }}">
                                            </div>
                                        </div>

                                        {{-- === LAMPIRAN PENGANTAR === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Lampiran
                                        </div>

                                        <x-pengantar :pengantar="$suratKeterangan->pengantar" readonly="true" />

                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- ===== TOMBOL EDIT ===== --}}
                        <div class="d-flex justify-content-center" style="margin-top:100px;">
                            <a href="{{ route('skkematian.warga') }}" class="btn-ajukan">
                                Edit
                            </a>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
