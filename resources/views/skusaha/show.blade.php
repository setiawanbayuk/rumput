{{-- resources/views/skusaha/show.blade.php --}}
@extends('layouts.create')

@section('title', $title)

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100"
    style="background: url('{{ asset('assets/form.png') }}') no-repeat center center; background-size: cover; margin-top: -75px;">
    <div class="container" style="margin-top:125px; margin-bottom:50px;">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm rounded-3"
                    style="background-color: rgba(255,255,255,.28); backdrop-filter: blur(10px);">

                    <div class="card-header bg-transparent text-center pt-3 pb-2">
                        <h5 class="mt-3 fw-bold text-white" style="letter-spacing:.5px">
                            {{ $title }}
                        </h5>
                        <h6 class="mb-3 fw-semibold text-white">
                            No. Surat : {{ $suratKeterangan->getNoSrt($suratKeterangan) }}
                        </h6>
                    </div>

                    <div class="card-body">

                        {{-- ================== ROW 2 KOLOM ================== --}}
                        <div class="row g-3" style="min-height:500px;">

                            {{-- ========== KIRI ========== --}}
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-1 shadow-sm bg-white">
                                    <div class="card-body">

                                        {{-- Jenis Surat --}}
                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label ms-2">Jenis Surat</label>
                                            <div class="col-md-8">
                                                <div class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                                    @if ($suratKeterangan->jenis == 'luar')
                                                        LUAR KELURAHAN
                                                    @else
                                                        KELURAHAN
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Nama Usaha --}}
                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label ms-2">Nama Usaha</label>
                                            <div class="col-md-8">
                                                <div class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                                    {{ $suratKeterangan->nama_usaha }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Alamat Usaha --}}
                                        <div class="row mb-3">
                                            <label class="col-md-3 col-form-label ms-2">Alamat Usaha</label>
                                            <div class="col-md-8">
                                                <div class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                                    style="white-space: pre-line;">
                                                    {{ $suratKeterangan->alamat_usaha }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ========== KANAN ========== --}}
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-1 shadow-sm bg-white">
                                    <div class="card-body">

                                        {{-- Kepada --}}
                                        <x-kepada :kepada="$suratKeterangan->kepada" :readonly="true" />

                                        {{-- Peruntukan --}}
                                        <x-peruntukan :peruntukan="$suratKeterangan->peruntukan" :readonly="true" />

                                        {{-- Pengantar --}}
                                        <x-pengantar :pengantar="$suratKeterangan->pengantar" :readonly="true" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- END ROW --}}

                        {{-- ===== TOMBOL EDIT ===== --}}
                        <div class="d-flex justify-content-center" style="margin-top:100px;">
                            @if ($suratKeterangan->status == 0)
                                <a href="{{ route('skusaha.editwarga', $suratKeterangan->id) }}" class="btn-edit">
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div> {{-- END card-body --}}
                </div> {{-- END card --}}
            </div>
        </div>
    </div>
</div>
@endsection
