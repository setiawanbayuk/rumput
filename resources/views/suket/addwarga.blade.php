{{-- resources/views/tambahWarga/addwarga.blade.php (SUKET Umum) --}}
@extends('layouts.create')

@section('title', $title ?? 'Tambah Surat Keterangan')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/form.png') }}') no-repeat center center; background-size: cover; margin-top: -75px;">
        <div class="container" style="margin-top: 125px; margin-bottom: 50px;">
            <div class="row justify-content-center">
                <div class="col-md-12">

                    <div class="card border-0 shadow-sm rounded-3"
                        style="background-color: rgba(255, 255, 255, .28); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                        <div class="card-header bg-transparent py-3">
                            <h5 class="my-3 fw-bold text-white text-center" style="letter-spacing:.5px">
                                {{ $title }}
                            </h5>
                        </div>

                        <div class="card-body">
                            <form id="formSuket" method="POST" enctype="multipart/form-data"
                                action="{{ route('suket.save') }}">
                                @csrf
                                <input type="hidden" id="nik" name="nik" value="{{ auth()->user()->nik }}">

                                {{-- ========== ROW: Dua Card Kiri-Kanan ========== --}}
                                <div class="row g-3" style="min-height: 500px">

                                    {{-- ===== KIRI: Keterangan ===== --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">
                                                <x-keterangan>
                                                    <x-slot:keterangan></x-slot:keterangan>
                                                </x-keterangan>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ===== KANAN: Kepada + Peruntukan + Pengantar ===== --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">
                                                <x-kepada>
                                                    <x-slot:kepada></x-slot:kepada>
                                                </x-kepada>

                                                <x-peruntukan>
                                                    <x-slot:peruntukan></x-slot:peruntukan>
                                                </x-peruntukan>

                                                <x-pengantar></x-pengantar>
                                            </div>
                                        </div>
                                    </div>
                                </div> {{-- end: row dua card --}}
                                {{-- Tombol Ajukan --}}
                                <div class="d-flex justify-content-center" style="margin-top: 100px;">
                                    <button type="button" class="btn-ajukan" data-bs-toggle="modal"
                                        data-bs-target="#modalSuket">
                                        Ajukan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- Modal Konfirmasi --}}
                    <x-confirm-ajukan modalId="modalSuket" formId="formSuket"
                        title="Apakah Anda Yakin Ingin Mengirim Ajuan Ini ?"
                        message="Dengan mengirim ajuan ini, Anda menyatakan bahwa seluruh data pribadi yang Anda masukkan, termasuk NIK, Nomor KK, dan dokumen pendukung lainnya, adalah benar, lengkap, dan sesuai dengan kondisi sebenarnya. Anda juga memahami bahwa kesalahan pengisian data atau ketidaksesuaian dokumen dapat mempengaruhi proses verifikasi dan dapat menyebabkan ajuan ditolak."
                        agreeLabel="Saya menyatakan bahwa seluruh data pribadi dan dokumen yang saya kirimkan adalah benar dan sesuai."
                        cancelText="Periksa Kembali" confirmText="Kirim Ajuan" />
            </div>
        </div>
    </div>
@endsection
