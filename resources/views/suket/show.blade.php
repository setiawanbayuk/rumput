{{-- resources/views/suket/show.blade.php --}}
@extends('layouts.create')

@section('title', $title ?? 'Detail Surat Keterangan')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100"
    style="background: url('{{ asset('assets/form.png') }}') no-repeat center center; background-size: cover; margin-top:-75px;">

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
                        {{-- ================== ROW DUA KARTU ================== --}}
                        <div class="row g-3" style="min-height:500px;">

                            {{-- ========== KIRI: KETERANGAN ========== --}}
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-1 shadow-sm bg-white">
                                    <div class="card-body">
                                        <x-keterangan :keterangan="$suratKeterangan->keterangan" readonly="true" />
                                    </div>
                                </div>
                            </div>

                            {{-- ========== KANAN: KEPADA + PERUNTUKAN + PENGANTAR ========== --}}
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-1 shadow-sm bg-white">
                                    <div class="card-body">
                                        <x-kepada :kepada="$suratKeterangan->kepada" readonly="true" />
                                        <x-peruntukan :peruntukan="$suratKeterangan->peruntukan" readonly="true" />
                                        <x-pengantar :pengantar="$suratKeterangan->pengantar" readonly="true" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- END ROW --}}

                        {{-- ===== TOMBOL EDIT ===== --}}
                        <div class="d-flex justify-content-center gap-4" style="margin-top:100px;">
                            <a href="{{ route('suket.warga') }}" class="btn-ajukan">
                                Kembali
                            </a>
                            @if ($suratKeterangan->status == 0)
                                <a href="{{ route('suket.edit', $suratKeterangan->id) }}" class="btn-edit">
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div> {{-- card-body --}}
                </div> {{-- card --}}
            </div>
        </div>
    </div>
</div>
@endsection
