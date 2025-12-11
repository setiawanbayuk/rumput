{{-- resources/views/suket/edit.blade.php --}}
@extends('layouts.create')

@section('title', $title)

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/profile.png') }}') no-repeat center center; background-size: cover; margin-top:-75px;">
        <div class="container" style="margin-top:125px; margin-bottom:50px;">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm rounded-3"
                        style="background-color:rgba(255,255,255,.28); backdrop-filter:blur(10px);">
                        
                        <div class="card-header bg-transparent text-center pt-3 pb-2">
                            <h5 class="mt-3 fw-bold text-white" style="letter-spacing:.5px">
                                {{ $title }}
                            </h5>
                            <h6 class="mb-3 fw-semibold text-white">
                                No. Surat : {{ $suratKeterangan->getNoSrt($suratKeterangan) }}
                            </h6>
                        </div>

                        <div class="card-body">
                            <form id="formEditSuket" method="POST" enctype="multipart/form-data" 
                                action="{{ route('suket.updatewarga', $suratKeterangan->id) }}" >
                                @csrf
                                <input type="hidden" id="nik" name="nik" value="{{ $suratKeterangan->nik }}">
                                {{-- ================== ROW KEDUA KARTU ================== --}}
                                <div class="row g-3" style="min-height:500px;">

                                    {{-- ========== KIRI: KETERANGAN ========== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">
                                                <x-keterangan :keterangan="$suratKeterangan->keterangan" :readonly="false" />
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ========== KANAN: KEPADA + PERUNTUKAN + PENGANTAR ========== --}}
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 border-1 shadow-sm bg-white">
                                            <div class="card-body">
                                                <x-kepada :kepada="$suratKeterangan->kepada" :readonly="false" />
                                                <x-peruntukan :peruntukan="$suratKeterangan->peruntukan" :readonly="false" />
                                                <x-pengantar :pengantar="$suratKeterangan->pengantar" :readonly="false" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- END ROW --}}

                                {{-- ========== BUTTON UPDATE ========== --}}
                                <div class="d-flex justify-content-center gap-3 mb-3">
                                    <a href="{{ route('suket.warga') }}" class="btn btn-danger py-2 px-4 me-2" style="border-radius: 8px">
                                        Batal
                                    </a>
                                    <button type="button" class="btn text-white py-2 px-4"
                                            style="background: #7896B2; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalEditSuket">
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
                <x-confirm-ajukan modalId="modalEditSuket" formId="formEditSuket"
                    title="Yakin Ingin Mengubah Surat Ini?"
                    message="Pastikan perubahan sudah benar sebelum mengirim pembaruan."
                    agreeLabel="Saya memastikan bahwa data yang saya ubah sudah benar."
                    cancelText="Cek Lagi"
                    confirmText="Simpan Perubahan" />
            </div>
        </div>
    </div>
@endsection
