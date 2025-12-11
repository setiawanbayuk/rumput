{{-- resources/views/tambahWarga/addwarga.blade.php (SUKET Usaha) --}}
@extends('layouts.create')

@section('title', $title ?? 'Tambah Surat Keterangan Usaha')

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
                            <form id="formSkusaha" method="POST" enctype="multipart/form-data"
                                action="{{ route('skusaha.save') }}">
                                @csrf
                                <input type="hidden" id="nik" name="nik" value="{{ auth()->user()->nik }}">

                                {{-- ========== ROW: Dua Card Kiri-Kanan ========== --}}
                                <div class="row g-3" style="min-height: 500px">

                                    {{-- ===== KIRI: Jenis Surat + Data Usaha ===== --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">

                                                {{-- Jenis Surat Usaha --}}
                                                <div class="row mb-3 align-items-md-center">
                                                    <label class="col-md-3 col-form-label ms-4">Jenis Surat</label>
                                                    <div class="col-md-8">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="register_as" id="radioKelurahan" value="kelurahan"
                                                                onchange="handleChangeRegisterAs('kelurahan')"
                                                                @checked(old('register_as', 'kelurahan') === 'kelurahan')>
                                                            <label class="form-check-label"
                                                                for="radioKelurahan">Kelurahan</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="register_as" id="radioLuar" value="luar"
                                                                onchange="handleChangeRegisterAs('luar')"
                                                                @checked(old('register_as') === 'luar')>
                                                            <label class="form-check-label" for="radioLuar">Luar
                                                                Kelurahan</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Nama Usaha --}}
                                                <div class="row mb-3">
                                                    <label for="nama_usaha" class="col-md-3 col-form-label ms-4">Nama
                                                        Usaha</label>
                                                    <div class="col-md-8">
                                                        <input id="nama_usaha" type="text"
                                                            class="form-control @error('nama_usaha') is-invalid @enderror"
                                                            name="nama_usaha" value="{{ old('nama_usaha') }}">
                                                        @error('nama_usaha')
                                                            <span class="invalid-feedback"
                                                                role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Alamat Usaha --}}
                                                <div class="row mb-3">
                                                    <label for="alamat_usaha" class="col-md-3 col-form-label ms-4">Alamat
                                                        Usaha</label>
                                                    <div class="col-md-8">
                                                        <textarea class="form-control @error('alamat_usaha') is-invalid @enderror" id="alamat_usaha" name="alamat_usaha"
                                                            rows="3">{{ old('alamat_usaha') }}</textarea>
                                                        @error('alamat_usaha')
                                                            <span class="invalid-feedback"
                                                                role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    {{-- ===== KANAN: Kepada + Peruntukan + Pengantar ===== --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">
                                                {{-- Komponen Kepada --}}
                                                <x-kepada>
                                                    <x-slot:kepada></x-slot:kepada>
                                                </x-kepada>

                                                {{-- Peruntukan --}}
                                                <x-peruntukan>
                                                    <x-slot:peruntukan></x-slot:peruntukan>
                                                </x-peruntukan>

                                                {{-- Pengantar --}}
                                                <x-pengantar></x-pengantar>
                                            </div>
                                        </div>
                                    </div>

                                </div> {{-- end: row dua card --}}
                                {{-- Tombol Ajukan --}}
                                <div class="d-flex justify-content-center" style="margin-top: 100px;">
                                    <button type="button" class="btn-ajukan" data-bs-toggle="modal"
                                        data-bs-target="#modalSkusaha">
                                        Ajukan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- Modal Konfirmasi --}}
                    <x-confirm-ajukan modalId="modalSkusaha" formId="formSkusaha"
                        title="Apakah Anda Yakin Ingin Mengirim Ajuan Ini ?"
                        message="Dengan mengirim ajuan ini, Anda menyatakan bahwa seluruh data pribadi yang Anda masukkan, termasuk NIK, Nomor KK, dan dokumen pendukung lainnya, adalah benar, lengkap, dan sesuai dengan kondisi sebenarnya. Anda juga memahami bahwa kesalahan pengisian data atau ketidaksesuaian dokumen dapat mempengaruhi proses verifikasi dan dapat menyebabkan ajuan ditolak."
                        agreeLabel="Saya menyatakan bahwa seluruh data pribadi dan dokumen yang saya kirimkan adalah benar dan sesuai."
                        cancelText="Periksa Kembali" confirmText="Kirim Ajuan" />
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Placeholder untuk konsistensi; saat ini tidak ada field dinamis yang berubah berdasar pilihan.
        function handleChangeRegisterAs(mode) {
            // Jika nantinya ada field khusus 'luar' vs 'kelurahan', tampil/sembunyikan di sini.
            // Contoh:
            // const khususLuar = document.getElementById('field-khusus-luar');
            // mode === 'luar' ? khususLuar?.classList.remove('d-none') : khususLuar?.classList.add('d-none');
        }
    </script>
@endpush
