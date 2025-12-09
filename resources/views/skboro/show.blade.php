{{-- resources/views/skboro/show.blade.php --}}
@extends('layouts.create')

@section('title', $title)

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100"
    style="background: url('{{ asset('assets/form.png') }}') no-repeat center center; background-size: cover; margin-top: -75px;">

    <div class="container" style="margin-top: 125px; margin-bottom: 50px;">
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
                        {{-- ========================= 2 Kolom ========================= --}}
                        <div class="row g-3" style="min-height: 500px;">

                            {{-- ================= KIRI: DATA PENGIKUT ================= --}}
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-1 shadow-sm bg-white">
                                    <div class="card-body">

                                        <div class="card-header bg-transparent mb-3 text-center fw-bold" style="font-size: 1rem">
                                            DATA PENGIKUT
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>NIK</th>
                                                        <th>Nama</th>
                                                        <th>JK</th>
                                                        <th>Umur</th>
                                                        <th>Status</th>
                                                        <th>Hubungan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pengikut as $p)
                                                        <tr>
                                                            <td>{{ $p['nik'] }}</td>
                                                            <td>{{ $p['nama'] }}</td>
                                                            <td>{{ $p['gender_nm'] }}</td>
                                                            <td>{{ $p['umur'] }}</td>
                                                            <td>{{ $p['status_kwn'] }}</td>
                                                            <td>{{ $p['hubungan'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ================= KANAN: BORO/ALAMAT/PERUNTUKAN ================= --}}
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-1 shadow-sm bg-white">
                                    <div class="card-body">

                                        <div class="card-header bg-transparent mb-3 text-center fw-bold" style="font-size: 1rem">
                                            BEPERGIAN / BORO KE
                                        </div>

                                        <div class="ms-4">
                                            <x-boro
                                                :prov_boro_nm="$suratKeterangan->prov_boro_nm"
                                                :kabko_boro_nm="$suratKeterangan->kabko_boro_nm"
                                                :kec_boro_nm="$suratKeterangan->kec_boro_nm"
                                                :kel_boro_nm="$suratKeterangan->kel_boro_nm"
                                                :alamat_boro="$suratKeterangan->alamat_boro"
                                                :tgl_awal="$suratKeterangan->tgl_awal"
                                                :tgl_akhir="$suratKeterangan->tgl_akhir"
                                                readonly="true"
                                            />
                                            {{-- Peruntukan --}}
                                            <x-peruntukan :peruntukan="$suratKeterangan->peruntukan" :readonly="true" />
                                            {{-- Pengantar --}}
                                            <x-pengantar :pengantar="$suratKeterangan->pengantar" :readonly="true" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ===== TOMBOL EDIT ===== --}}
                        <div class="d-flex justify-content-center" style="margin-top:100px;">
                            @if ($suratKeterangan->status == 0)
                                <a href="{{ route('skboro.editwarga', $suratKeterangan->id) }}" class="btn-edit">
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div> {{-- END CARD BODY --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
