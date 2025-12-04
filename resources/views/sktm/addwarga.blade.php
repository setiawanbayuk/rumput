{{-- resources/views/tambahWarga/addwarga.blade.php (SKTM) --}}
@extends('layouts.create')

@section('title', $title ?? 'Tambah Surat Keterangan Miskin')

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
                            <form id="formSktm" method="POST" enctype="multipart/form-data"
                                action="{{ route('sktm.save') }}">
                                @csrf
                                <input type="hidden" id="nik" name="nik" value="{{ auth()->user()->nik }}">

                                {{-- ========== ROW: Dua Card Kiri-Kanan ========== --}}
                                <div class="row g-3" style="min-height: 500px">

                                    {{-- ===== KIRI: Jenis SKTM + Data Sekolah (jika dipilih) ===== --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">

                                                {{-- Jenis SKTM --}}
                                                <div class="row mb-3 align-items-md-center">
                                                    <label class="col-md-3 col-form-label ms-4">Jenis SKTM</label>
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
                                                                name="register_as" id="radioSekolah" value="sekolah"
                                                                onchange="handleChangeRegisterAs('sekolah')"
                                                                @checked(old('register_as') === 'sekolah')>
                                                            <label class="form-check-label"
                                                                for="radioSekolah">Sekolah</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Seksi SEKOLAH (muncul hanya jika register_as=sekolah) --}}
                                                <div id="input-sekolah" @class([
                                                    'd-none' => old('register_as', 'perorangan') === 'perorangan',
                                                ])>

                                                    {{-- Nama Anak --}}
                                                    <div class="row mb-3">
                                                        <label for="kepada"
                                                            class="col-md-3 col-form-label text-md-end">Nama Anak</label>
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

                                                    {{-- Tempat/Tgl Lahir --}}
                                                    <div class="row mb-3">
                                                        <label class="col-md-3 col-form-label text-md-end">Tempat/Tgl.
                                                            Lahir</label>
                                                        <div class="col-md-5">
                                                            <input id="kepada_tempat_lhr" type="text"
                                                                class="form-control @error('kepada_tempat_lhr') is-invalid @enderror"
                                                                name="kepada_tempat_lhr"
                                                                value="{{ old('kepada_tempat_lhr') }}">
                                                            @error('kepada_tempat_lhr')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input id="kepada_tgl_lhr" type="date"
                                                                class="form-control @error('kepada_tgl_lhr') is-invalid @enderror"
                                                                name="kepada_tgl_lhr" value="{{ old('kepada_tgl_lhr') }}">
                                                            @error('kepada_tgl_lhr')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Jenis Kelamin Anak --}}
                                                    <div class="row mb-3">
                                                        <label for="kepada_gender"
                                                            class="col-md-3 col-form-label text-md-end">Jenis Kelamin
                                                            Anak</label>
                                                        <div class="col-md-8">
                                                            <select
                                                                class="form-control @error('kepada_gender') is-invalid @enderror"
                                                                id="kepada_gender" name="kepada_gender"
                                                                data-placeholder="Jenis Kelamin">
                                                            </select>
                                                            @error('kepada_gender')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Hubungan dengan Wali --}}
                                                    <div class="row mb-3">
                                                        <label for="kepada_hubungan"
                                                            class="col-md-3 col-form-label text-md-end">Hubungan Dengan
                                                            Wali</label>
                                                        <div class="col-md-8">
                                                            <select
                                                                class="form-control @error('kepada_hubungan') is-invalid @enderror"
                                                                id="kepada_hubungan" name="kepada_hubungan"
                                                                data-placeholder="Hubungan">
                                                                <option value=""></option>
                                                                <option value="Putranya" @selected(old('kepada_hubungan') === 'Putranya')>
                                                                    Putranya</option>
                                                                <option value="Putrinya" @selected(old('kepada_hubungan') === 'Putrinya')>
                                                                    Putrinya</option>
                                                                <option value="Cucunya" @selected(old('kepada_hubungan') === 'Cucunya')>
                                                                    Cucunya
                                                                </option>
                                                                <option value="Keluarga" @selected(old('kepada_hubungan') === 'Keluarga')>
                                                                    Keluarga</option>
                                                            </select>
                                                            @error('kepada_hubungan')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Nama Sekolah --}}
                                                    <div class="row mb-3">
                                                        <label for="kepada_sekolah"
                                                            class="col-md-3 col-form-label text-md-end">Nama
                                                            Sekolah</label>
                                                        <div class="col-md-8">
                                                            <input id="kepada_sekolah" type="text"
                                                                class="form-control @error('kepada_sekolah') is-invalid @enderror"
                                                                name="kepada_sekolah"
                                                                value="{{ old('kepada_sekolah') }}">
                                                            @error('kepada_sekolah')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Kelas/Semester --}}
                                                    <div class="row mb-3">
                                                        <label for="kepada_kelas"
                                                            class="col-md-3 col-form-label text-md-end">Kelas/Semester</label>
                                                        <div class="col-md-8">
                                                            <input id="kepada_kelas" type="text"
                                                                class="form-control @error('kepada_kelas') is-invalid @enderror"
                                                                name="kepada_kelas" value="{{ old('kepada_kelas') }}">
                                                            @error('kepada_kelas')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Alamat Sekolah --}}
                                                    <div class="row mb-3">
                                                        <label for="kepada_alamat_sekolah"
                                                            class="col-md-3 col-form-label text-md-end">Alamat
                                                            Sekolah</label>
                                                        <div class="col-md-8">
                                                            <textarea class="form-control @error('kepada_alamat_sekolah') is-invalid @enderror" id="kepada_alamat_sekolah"
                                                                name="kepada_alamat_sekolah" rows="3">{{ old('kepada_alamat_sekolah') }}</textarea>
                                                            @error('kepada_alamat_sekolah')
                                                                <span class="invalid-feedback"
                                                                    role="alert"><strong>{{ $message }}</strong></span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                </div> {{-- end: input-sekolah --}}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ===== KANAN: Peruntukan + Kategori + Pengantar ===== --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">

                                                {{-- Peruntukan --}}
                                                <x-peruntukan>
                                                    <x-slot:peruntukan></x-slot:peruntukan>
                                                </x-peruntukan>

                                                {{-- Kategori --}}
                                                <div class="row mb-3">
                                                    <label for="kategori"
                                                        class="col-md-3 col-form-label ms-2">Kategori</label>
                                                    <div class="col-md-8">
                                                        <select
                                                            class="form-control @error('kategori') is-invalid @enderror"
                                                            id="kategori" name="kategori" data-placeholder="Kategori">
                                                            <option value=""></option>
                                                            <option value="DTKS" @selected(old('kategori') === 'DTKS')>DTKS
                                                            </option>
                                                            <option value="Non DTKS" @selected(old('kategori') === 'Non DTKS')>Non DTKS
                                                            </option>
                                                        </select>
                                                        @error('kategori')
                                                            <span class="invalid-feedback"
                                                                role="alert"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Pengantar --}}
                                                <x-pengantar></x-pengantar>

                                            </div>
                                        </div>
                                    </div>

                                </div> {{-- end: row dua card --}}
                                {{-- Tombol Ajukan --}}
                                <div class="d-flex justify-content-center" style="margin-top: 100px;">
                                    <button type="button" class="btn-ajukan" data-bs-toggle="modal"
                                        data-bs-target="#modalSktm">
                                        Ajukan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    {{-- Modal Konfirmasi --}}
                    <x-confirm-ajukan modalId="modalSktm" formId="formSktm"
                        title="Apakah Anda Yakin Ingin Mengirim Ajuan Ini ?"
                        message="Pastikan isian ajuan telah sesuai. Kesalahan pengisian data atau lampiran dokumen dapat mengakibatkan ajuan ditolak saat proses verifikasi."
                        agreeLabel="Saya menyatakan bahwa isian formulir dan dokumen yang terlampir pada ajuan ini telah sesuai"
                        cancelText="Periksa Kembali" confirmText="Kirim Ajuan" />
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ asset('assets/js/personal.js') }}"></script>
    <script>
        $(document).ready(function() {

            $("#kategori").select2({
                theme: "bootstrap-5",
                width: $(this).data("width") ?
                    $(this).data("width") : $(this).hasClass("w-100") ?
                    "100%" : "style",
                placeholder: $(this).data("placeholder"),
            });

            $("#kepada_gender").select2({
                theme: "bootstrap-5",
                width: $(this).data("width") ?
                    $(this).data("width") : $(this).hasClass("w-100") ?
                    "100%" : "style",
                placeholder: $(this).data("placeholder"),
                ajax: {
                    url: route("gender.index"),
                    dataType: "json",
                    processResults: function(response) {
                        return {
                            results: response,
                        };
                    },
                },
            });


            $("#kepada_hubungan").select2({
                theme: "bootstrap-5",
                width: $(this).data("width") ?
                    $(this).data("width") : $(this).hasClass("w-100") ?
                    "100%" : "style",
                placeholder: $(this).data("placeholder"),
            });
        });
        const handleChangeRegisterAs = (value) => {
            if (value == 'sekolah') {
                $('#input-sekolah').removeClass('d-none');
            } else {
                $('#input-sekolah').addClass('d-none');
            }
        }
    </script>
@endpush
