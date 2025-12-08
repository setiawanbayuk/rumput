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

                            {{-- ===================== K I R I ===================== --}}
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
                                        {{-- === LAMPIRAN PENGANTAR === --}}
                                        <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                            Lampiran
                                        </div>

                                        <x-pengantar :pengantar="$suratKeterangan->pengantar" readonly="true" />
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
                                            <label class="col-md-3 col-form-label text-md-start ms-2">NIK</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nik }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Nama</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="tgl_kematian"
                                                class="col-md-3 col-form-label text-md-start ms-2">Tgl. & Jam Kematian</label>

                                            <div class="col-md-4">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly 
                                                    value="{{ $suratKeterangan->tgl_kematian }}">
                                            </div>

                                            <div class="col-md-4">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly 
                                                    value="{{ $suratKeterangan->jam_kematian }}">
                                            </div>      
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Sebab Kematian</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->sebab_kematian }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Tempat Kematian</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->tempat_kematian }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Yang Menerangkan</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->yang_menerangkan }}">
                                            </div>
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
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Nama</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama_ayah }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="ttl_ayah"
                                                class="col-md-3 col-form-label text-md-start ms-2">Tempat/Tgl. Lahir</label>

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
                                            <label class="col-md-3 col-form-label text-md-start ms-2">Nama</label>
                                            <div class="col-md-8">
                                                <input class="form-control-plaintext border rounded px-3 py-2 bg-light" readonly value="{{ $suratKeterangan->nama_ibu }}">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="ttl_ibu"
                                                class="col-md-3 col-form-label text-md-start ms-2">Tempat/Tgl. Lahir</label>

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
                                <a href="{{ route('skkematian.editwarga', $suratKeterangan->id) }}" class="btn-edit">
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
