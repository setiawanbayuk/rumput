@extends('layouts.create')

@section('title', $title)

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100"
    style="background: url('{{ asset('assets/form.png') }}') no-repeat center center; background-size: cover; margin-top: -75px;">

    <div class="container" style="margin-top: 125px; margin-bottom: 50px;">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card border-0 shadow-sm rounded-3"
                    style="background-color: rgba(255, 255, 255, .28); backdrop-filter: blur(10px);">

                    <div class="card-header bg-transparent pt-3 pb-2">
                        <h5 class="my-3 fw-bold text-white text-center">{{ $title }}</h5>
                    </div>

                    <div class="card-body">

                        {{-- DETAIL TAMPILAN --}}
                        <div class="row" style="min-height: 500px;">

                            {{-- Kolom Kiri --}}
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-1 shadow-sm bg-white">
                                    <div class="card-body">
                                        <x-kepada :kepada="$suratKeterangan->kepada" readonly="true" />
                                    </div>
                                </div>
                            </div>

                            {{-- Kolom Kanan --}}
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-1 shadow-sm bg-white">
                                    <div class="card-body">
                                        <x-peruntukan :peruntukan="$suratKeterangan->peruntukan" readonly="true" />
                                        <x-pengantar :pengantar="$suratKeterangan->pengantar" readonly="true" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL KEMBALI --}}
                        <div class="d-flex justify-content-center" style="bottom: 0px; margin-top: 75px;">
                            <a href="{{ route('skbn.warga') }}" class="btn-edit">
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
