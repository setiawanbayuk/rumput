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

                        <form action="{{ route('suket.updatewarga', $suratKeterangan->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- ================== ROW KEDUA KARTU ================== --}}
                            <div class="row g-3" style="min-height:500px;">

                                {{-- ========== KIRI: KETERANGAN ========== --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-1 shadow-sm bg-white">
                                        <div class="card-body">

                                            <x-keterangan><x-slot:keterangan>{{ $suratKeterangan->keterangan }}</x-slot:keterangan></x-keterangan>

                                        </div>
                                    </div>
                                </div>

                                {{-- ========== KANAN: KEPADA + PERUNTUKAN + PENGANTAR ========== --}}
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-1 shadow-sm bg-white">
                                        <div class="card-body">
                                            <x-kepada><x-slot:kepada>{{ $suratKeterangan->kepada }}</x-slot:kepada></x-kepada>
                                            <x-peruntukan><x-slot:peruntukan>{{ $suratKeterangan->peruntukan }}</x-slot:peruntukan></x-peruntukan>
                                            <x-pengantar></x-pengantar>
                                            <div class="row mb-3">
                                                <label class="col-md-3 col-form-label text-md-start ms-2"></label>
                                                <div class="col-md-8">
                                                    <x-viewer src="{{ $suratKeterangan->pengantar }}" height="150px" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {{-- END ROW --}}

                            {{-- ========== BUTTON UPDATE ========== --}}
                            <div class="d-flex justify-content-center gap-3" style="margin-top:75px;">
                                <a href="{{ route('suket.warga') }}" class="btn btn-secondary px-4 py-2 rounded-3">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">
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
            <x-confirm-ajukan modalId="modalSuket" formId="formSuket"
                title="Apakah Anda Yakin Ingin Mengirim Ajuan Ini ?"
                message="Pastikan isian ajuan telah sesuai. Kesalahan pengisian data atau lampiran dokumen dapat mengakibatkan ajuan ditolak saat proses verifikasi."
                agreeLabel="Saya menyatakan bahwa isian formulir dan dokumen yang terlampir pada ajuan ini telah sesuai"
                cancelText="Periksa Kembali" confirmText="Kirim Ajuan" />
        </div>
    </div>
</div>
@endsection
