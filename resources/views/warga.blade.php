@extends('layouts.warga')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard E-Suket') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{ __('Selamat Datang') }}
                        {{ auth()->user()->name }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <br>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <h5 class="fw-bold">Jumlah Permohonan per Jenis Surat Keterangan</h5>
                <hr>
                <div class="row">
                    <div class="col-6 col-md-4">
                        <div class=" bd-callout bd-callout-info">
                            <a href="{{ route('skbn.warga') }}">
                                <p class="text-muted">Surat Keterangan Belum Menikah</p>
                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                    <p class="my-0 fw-bold text-black">{{ $jumlah_skbn }}</p>
                                    <i class="ri-arrow-right-double-line"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class=" bd-callout bd-callout-warning">
                            <a href="{{ route('skboro.warga') }}">
                                <p class="text-muted">Surat Keterangan Boro</p>
                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                    <p class="my-0 fw-bold text-black">{{ $jumlah_boro }}</p>
                                    <i class="ri-arrow-right-double-line"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class=" bd-callout bd-callout-success">
                            <a href="{{ route('skdom.warga') }}">
                                <p class="text-muted">Surat Keterangan Domisili</p>
                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                    <p class="my-0 fw-bold text-black">{{ $jumlah_domisili }}</p>
                                    <i class="ri-arrow-right-double-line"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class=" bd-callout bd-callout-info">
                            <a href="{{ route('sktm.warga') }}">
                                <p class="text-muted">Surat Keterangan Miskin</p>
                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                    <p class="my-0 fw-bold text-black">{{ $jumlah_sktm }}</p>
                                    <i class="ri-arrow-right-double-line"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class=" bd-callout bd-callout-warning">
                            <a href="{{ route('skhsl.warga') }}">
                                <p class="text-muted">Surat Keterangan Penghasilan</p>
                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                    <p class="my-0 fw-bold text-black">{{ $jumlah_penghasilan }}</p>
                                    <i class="ri-arrow-right-double-line"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class=" bd-callout bd-callout-success">
                            <a href="{{ route('skusaha.warga') }}">
                                <p class="text-muted">Surat Keterangan Usaha</p>
                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                    <p class="my-0 fw-bold text-black">{{ $jumlah_usaha }}</p>
                                    <i class="ri-arrow-right-double-line"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class=" bd-callout bd-callout-info">
                            <a href="{{ route('suket.warga') }}">
                                <p class="text-muted">Surat Keterangan</p>
                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                    <p class="my-0 fw-bold text-black">{{ $jumlah_suket }}</p>
                                    <i class="ri-arrow-right-double-line"></i>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
