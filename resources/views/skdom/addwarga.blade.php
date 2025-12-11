{{-- resources/views/tambahWarga/addwarga.blade.php (SKDOM) --}}
@extends('layouts.create')

@section('title', $title ?? 'Tambah Surat Keterangan Domisili')

@section('content')
    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('{{ asset('assets/form.png') }}') no-repeat center center; background-size: cover; margin-top: -75px;">
        <div class="container" style="margin-top: 125px; margin-bottom: 50px;">
            <div class="row justify-content-center">
                <div class="col-md-12">

                    <div class="card border-0 shadow-sm rounded-3"
                        style="background-color: rgba(255, 255, 255, .28); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
                        <div class="card-header bg-transparent pt-3 pb-2">
                            <h5 class="my-3 fw-bold text-white text-center" style="letter-spacing:.5px">
                                {{ $title }}
                            </h5>
                        </div>

                        <div class="card-body">
                            <form id="formSkdom" method="POST" enctype="multipart/form-data"
                                action="{{ route('skdom.save') }}">
                                @csrf
                                <input type="hidden" id="nik" name="nik" value="{{ auth()->user()->nik }}">

                                {{-- ========== ROW: Dua Card Kiri-Kanan ========== --}}
                                <div class="row g-3" style="min-height: 500px">

                                    {{-- ===== KIRI: Jenis Domisili + Perusahaan (jika dipilih) + Penerima ===== --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">
                                                {{-- Jenis Surat Domisili --}}
                                                <div class="row mb-3 align-items-md-center">
                                                    <label class="col-md-3 col-form-label ms-4">Jenis
                                                        Domisili</label>
                                                    <div class="col-md-8">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="register_as" id="radioPerorangan" value="perorangan"
                                                                onchange="handleChangeRegisterAs('perorangan')"
                                                                @checked(old('register_as', 'perorangan') === 'perorangan')>
                                                            <label class="form-check-label"
                                                                for="radioPerorangan">Perorangan</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="register_as" id="radioPerusahaan" value="perusahaan"
                                                                onchange="handleChangeRegisterAs('perusahaan')"
                                                                @checked(old('register_as') === 'perusahaan')>
                                                            <label class="form-check-label"
                                                                for="radioPerusahaan">Perusahaan</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Field PERUSAHAAN (muncul hanya jika register_as=perusahaan) --}}
                                                <div id="input-perusahaan" @class([
                                                    'd-none' => old('register_as', 'perorangan') === 'perorangan',
                                                ])>
                                                    <div class="row mb-3">
                                                        <label for="nama_perusahaan"
                                                            class="col-md-3 col-form-label ms-4">Nama
                                                            Perusahaan</label>
                                                        <div class="col-md-8">
                                                            <input id="nama_perusahaan" type="text"
                                                                class="form-control @error('nama_perusahaan') is-invalid @enderror"
                                                                name="nama_perusahaan" value="{{ old('nama_perusahaan') }}">
                                                            @error('nama_perusahaan')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="status_bangunan"
                                                            class="col-md-3 col-form-label ms-4">Status
                                                            Bangunan</label>
                                                        <div class="col-md-8">
                                                            <input id="status_bangunan" type="text"
                                                                class="form-control @error('status_bangunan') is-invalid @enderror"
                                                                name="status_bangunan" value="{{ old('status_bangunan') }}">
                                                            @error('status_bangunan')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="jumlah_karyawan"
                                                            class="col-md-3 col-form-label ms-4">Jumlah
                                                            Karyawan</label>
                                                        <div class="col-md-8">
                                                            <input id="jumlah_karyawan" type="number" min="0"
                                                                class="form-control @error('jumlah_karyawan') is-invalid @enderror"
                                                                name="jumlah_karyawan"
                                                                value="{{ old('jumlah_karyawan') }}">
                                                            @error('jumlah_karyawan')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Diberikan Kepada / Atas Nama --}}
                                                <div class="row mb-3">
                                                    <label for="kepada" id="labelKepada"
                                                        class="col-md-3 col-form-label ms-4">
                                                        {{ old('register_as', 'perorangan') === 'perorangan' ? 'Diberikan Kepada' : 'Atas Nama' }}
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input id="kepada" type="text"
                                                            class="form-control @error('kepada') is-invalid @enderror"
                                                            name="kepada" value="{{ old('kepada') }}">
                                                        @error('kepada')
                                                            <span class="invalid-feedback"
                                                                role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ===== KANAN: Alamat Domisili + Peruntukan + Pengantar ===== --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">
                                                {{-- Alamat Domisili --}}
                                                <div class="row mb-3">
                                                    <label for="alamat_domisili"
                                                        class="col-md-3 col-form-label ms-2">Alamat
                                                        Domisili</label>
                                                    <div class="col-md-8">
                                                        <textarea class="form-control @error('alamat_domisili') is-invalid @enderror" id="alamat_domisili"
                                                            name="alamat_domisili" rows="3">{{ old('alamat_domisili') }}</textarea>
                                                        @error('alamat_domisili')
                                                            <span class="invalid-feedback"
                                                                role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- Peruntukan (pakai komponen yang sudah ada) --}}
                                                <x-peruntukan>
                                                    <x-slot:peruntukan></x-slot:peruntukan>
                                                </x-peruntukan>

                                                {{-- Pengantar (pakai komponen yang sudah ada) --}}
                                                <x-pengantar></x-pengantar>
                                            </div>
                                        </div>
                                    </div>

                                </div> {{-- end: row dua card --}}

                                {{-- Tombol Ajukan --}}
                                <div class="d-flex justify-content-center" style="margin-top: 100px;">
                                    <button type="button" class="btn-ajukan" data-bs-toggle="modal"
                                        data-bs-target="#modalSkdom">
                                        Ajukan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- Modal Konfirmasi --}}
                    <x-confirm-ajukan modalId="modalSkdom" formId="formSkdom"
                        title="Apakah Anda Yakin Ingin Mengirim Ajuan Ini ?"
                        message="Dengan mengirim ajuan ini, Anda menyatakan bahwa seluruh data pribadi yang Anda masukkan, termasuk NIK, Nomor KK, dan dokumen pendukung lainnya, adalah benar, lengkap, dan sesuai dengan kondisi sebenarnya. Anda juga memahami bahwa kesalahan pengisian data atau ketidaksesuaian dokumen dapat mempengaruhi proses verifikasi dan dapat menyebabkan ajuan ditolak."
                        agreeLabel="Saya menyatakan bahwa seluruh data pribadi dan dokumen yang saya kirimkan adalah benar dan sesuai."
                        cancelText="Periksa Kembali" confirmText="Kirim Ajuan" />
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle section perusahaan + ganti label "kepada" sesuai pilihan
        function handleChangeRegisterAs(mode) {
            const perSection = document.getElementById('input-perusahaan');
            const lbl = document.getElementById('labelKepada');
            if (mode === 'perusahaan') {
                perSection?.classList.remove('d-none');
                if (lbl) lbl.textContent = 'Atas Nama';
            } else {
                perSection?.classList.add('d-none');
                if (lbl) lbl.textContent = 'Diberikan Kepada';
            }
        }

        // Inisialisasi awal sesuai old() saat halaman load
        document.addEventListener('DOMContentLoaded', () => {
            const current = `{{ old('register_as', 'perorangan') }}`;
            handleChangeRegisterAs(current);
        });
    </script>
@endpush
