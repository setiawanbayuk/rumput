{{-- resources/views/tambahWarga/addwarga.blade.php --}}
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
                        <div class="card-header bg-transparent pt-3 pb-2">
                            <h5 class="my-3 fw-bold text-white text-center" style="letter-spacing: .5px">{{ $title }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="formSkbn" method="POST" enctype="multipart/form-data"
                                action="{{ route('skbn.save') }}">
                                @csrf
                                <input type="hidden" id="nik" name="nik" value="{{ auth()->user()->nik }}">
                                {{-- HAPUS _method kalau ini pure CREATE --}}

                                <div class="row" style="min-height: 500px;">
                                    {{-- Kolom Kiri --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm" style="background: #fff;">
                                            <div class="card-body d-flex flex-column justify-content-between">
                                                <div>
                                                    <x-kepada><x-slot:kepada></x-slot:kepada></x-kepada>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Kolom Kanan --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm" style="background: #fff;">
                                            <div class="card-body d-flex flex-column justify-content-between">
                                                <div>
                                                    <x-peruntukan><x-slot:peruntukan></x-slot:peruntukan></x-peruntukan>
                                                    <x-pengantar></x-pengantar>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center" style="bottom: 0px; margin-top: 75px;">
                                    <button type="button" class="btn-ajukan" data-bs-toggle="modal"
                                        data-bs-target="#modalSkbn">
                                        Ajukan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- panggil komponen modal --}}
                    <x-confirm-ajukan modalId="modalSkbn" formId="formSkbn"
                        title="Apakah Anda Yakin Ingin Mengirim Ajuan Ini ?"
                        message="Pastikan isian ajuan telah sesuai. Kesalahan pengisian data atau lampiran dokumen dapat mengakibatkan ajuan ditolak saat proses verifikasi."
                        agreeLabel="Saya menyatakan bahwa isian formulir dan dokumen yang terlampir pada ajuan ini telah sesuai"
                        cancelText="Periksa Kembali" confirmText="Kirim Ajuan" />
                </div>
            </div>
        </div>
    </div>
@endsection
