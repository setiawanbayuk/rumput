{{-- resources/views/tambahWarga/addwarga_kematian.blade.php --}}
@extends('layouts.create')

@section('title', $title ?? 'Tambah Surat Keterangan Kematian')

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
                                USULAN PENGAJUAN SURAT KETERANGAN KEMATIAN
                            </h5>
                        </div>

                        <div class="card-body">
                            <form id="formSkkematian" method="POST" enctype="multipart/form-data"
                                action="{{ route('skkematian.save') }}">
                                @csrf
                                {{-- nik pemohon (akun warga) jika perlu di controller --}}
                                <input type="hidden" id="nik_pelapor" name="nik_pelapor" value="{{ auth()->user()->nik }}">

                                {{-- ===== Grid dua kolom ===== --}}
                                <div class="row g-3" style="min-height: 420px">

                                    {{-- KIRI --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">

                                                {{-- === DATA SAKSI 1 === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Saksi 1
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="nik_saksi1" class="col-md-4 col-form-label">NIK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="nik_saksi1" name="nik_saksi1"
                                                            class="form-control @error('nik_saksi1') is-invalid @enderror"
                                                            placeholder="16 digit NIK" value="{{ old('nik_saksi1') }}"
                                                            maxlength="16" aria-label="NIK Saksi">
                                                        @error('nik_saksi1')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="kk_saksi1" class="col-md-4 col-form-label">No. KK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="kk_saksi1" name="kk_saksi1"
                                                            class="form-control @error('kk_saksi1') is-invalid @enderror"
                                                            value="{{ old('kk_saksi1') }}">
                                                        @error('kk_saksi1')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="name_saksi1" class="col-md-4 col-form-label">Nama</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="name_saksi1" name="name_saksi1"
                                                            class="form-control @error('name_saksi1') is-invalid @enderror"
                                                            value="{{ old('name_saksi1') }}">
                                                        @error('name_saksi1')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <label for="kewarganegaraan_saksi1"
                                                        class="col-md-4 col-form-label">Kewarganegaraan</label>
                                                    <div class="col-md-8">
                                                        <select id="kewarganegaraan_saksi1" name="kewarganegaraan_saksi1"
                                                            class="form-control @error('kewarganegaraan_saksi1') is-invalid @enderror"
                                                            data-placeholder="Pilih kewarganegaraan"></select>
                                                        @error('kewarganegaraan_saksi1')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- === DATA SAKSI 2 === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Saksi 2
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="nik_saksi2" class="col-md-4 col-form-label">NIK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="nik_saksi2" name="nik_saksi2"
                                                            class="form-control @error('nik_saksi2') is-invalid @enderror"
                                                            placeholder="16 digit NIK" value="{{ old('nik_saksi2') }}">
                                                        @error('nik_saksi2')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="kk_saksi2" class="col-md-4 col-form-label">No. KK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="kk_saksi2" name="kk_saksi2"
                                                            class="form-control @error('kk_saksi2') is-invalid @enderror"
                                                            value="{{ old('kk_saksi2') }}">
                                                        @error('kk_saksi2')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="name_saksi2" class="col-md-4 col-form-label">Nama</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="name_saksi2" name="name_saksi2"
                                                            class="form-control @error('name_saksi2') is-invalid @enderror"
                                                            value="{{ old('name_saksi2') }}">
                                                        @error('name_saksi2')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-0">
                                                    <label for="kewarganegaraan_saksi2"
                                                        class="col-md-4 col-form-label">Kewarganegaraan</label>
                                                    <div class="col-md-8">
                                                        <select id="kewarganegaraan_saksi2" name="kewarganegaraan_saksi2"
                                                            class="form-control @error('kewarganegaraan_saksi2') is-invalid @enderror"
                                                            data-placeholder="Pilih kewarganegaraan"></select>
                                                        @error('kewarganegaraan_saksi2')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    {{-- KANAN --}}
                                    <div class="col-md-6">
                                        <div class="card h-100 border-1 shadow-sm">
                                            <div class="card-body">

                                                {{-- === DATA JENAZAH === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Jenazah
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="nik" class="col-md-4 col-form-label">NIK
                                                        Jenazah</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="nik" name="nik"
                                                            class="form-control @error('nik') is-invalid @enderror"
                                                            placeholder="16 digit NIK" value="{{ old('nik') }}">
                                                        @error('nik')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="name" class="col-md-4 col-form-label">Nama</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="name" name="name"
                                                            class="form-control @error('name') is-invalid @enderror"
                                                            value="{{ old('name') }}">
                                                        @error('name')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label class="col-md-4 col-form-label">Tgl. & Jam Kematian</label>
                                                    <div class="col-md-4">
                                                        <input type="date" id="tgl_kematian" name="tgl_kematian"
                                                            class="form-control @error('tgl_kematian') is-invalid @enderror"
                                                            value="{{ old('tgl_kematian') }}">
                                                        @error('tgl_kematian')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="time" id="jam_kematian" name="jam_kematian"
                                                            class="form-control @error('jam_kematian') is-invalid @enderror"
                                                            value="{{ old('jam_kematian') }}">
                                                        @error('jam_kematian')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="sebab_kematian" class="col-md-4 col-form-label">Sebab
                                                        Kematian</label>
                                                    <div class="col-md-8">
                                                        <select id="sebab_kematian" name="sebab_kematian"
                                                            class="form-control @error('sebab_kematian') is-invalid @enderror"
                                                            data-placeholder="Sebab Kematian">
                                                            <option value=""></option>
                                                            <option value="Sakit Biasa/Tua">Sakit Biasa/Tua</option>
                                                            <option value="Wabah Penyakit">Wabah Penyakit</option>
                                                            <option value="Kecelakaan">Kecelakaan</option>
                                                            <option value="Kriminalitas">Kriminalitas</option>
                                                            <option value="Bunuh Diri">Bunuh Diri</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                        @error('sebab_kematian')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="tempat_kematian" class="col-md-4 col-form-label">Tempat
                                                        Kematian</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="tempat_kematian" name="tempat_kematian"
                                                            class="form-control @error('tempat_kematian') is-invalid @enderror"
                                                            value="{{ old('tempat_kematian') }}">
                                                        @error('tempat_kematian')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <label for="yang_menerangkan" class="col-md-4 col-form-label">Yang
                                                        Menerangkan</label>
                                                    <div class="col-md-8">
                                                        <select id="yang_menerangkan" name="yang_menerangkan"
                                                            class="form-control @error('yang_menerangkan') is-invalid @enderror"
                                                            data-placeholder="Yang Menerangkan">
                                                            <option value=""></option>
                                                            <option value="Dokter">Dokter</option>
                                                            <option value="Tenaga Kesehatan">Tenaga Kesehatan</option>
                                                            <option value="Kepolisian">Kepolisian</option>
                                                            <option value="Lainnya">Lainnya</option>
                                                        </select>
                                                        @error('yang_menerangkan')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- === DATA ORANG TUA (opsional tapi sesuai form admin) === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Ayah
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="nik_ayah" class="col-md-4 col-form-label">NIK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="nik_ayah" name="nik_ayah"
                                                            class="form-control @error('nik_ayah') is-invalid @enderror"
                                                            placeholder="16 digit NIK" value="{{ old('nik_ayah') }}">
                                                        @error('nik_ayah')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="kk_ayah" class="col-md-4 col-form-label">No. KK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="kk_ayah" name="kk_ayah"
                                                            class="form-control @error('kk_ayah') is-invalid @enderror"
                                                            value="{{ old('kk_ayah') }}">
                                                        @error('kk_ayah')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="name_ayah" class="col-md-4 col-form-label">Nama</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="name_ayah" name="name_ayah"
                                                            class="form-control @error('name_ayah') is-invalid @enderror"
                                                            value="{{ old('name_ayah') }}">
                                                        @error('name_ayah')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label class="col-md-4 col-form-label">Tempat / Tgl. Lahir</label>
                                                    <div class="col-md-5">
                                                        <input type="text" id="tempat_lhr_ayah" name="tempat_lhr_ayah"
                                                            class="form-control @error('tempat_lhr_ayah') is-invalid @enderror"
                                                            value="{{ old('tempat_lhr_ayah') }}">
                                                        @error('tempat_lhr_ayah')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input id="tgl_lhr_ayah" type="date"
                                                            class="form-control @error('tgl_lhr_ayah') is-invalid @enderror"
                                                            name="tgl_lhr_ayah" value="{{ old('tgl_lhr_ayah') }}"
                                                            autocomplete="tgl_lhr_ayah" autofocus>

                                                        @error('tgl_lhr_ayah')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <label for="kewarganegaraan_ayah"
                                                        class="col-md-4 col-form-label">Kewarganegaraan</label>
                                                    <div class="col-md-8">
                                                        <select id="kewarganegaraan_ayah" name="kewarganegaraan_ayah"
                                                            class="form-control @error('kewarganegaraan_ayah') is-invalid @enderror"
                                                            data-placeholder="Pilih kewarganegaraan"></select>
                                                        @error('kewarganegaraan_ayah')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Data Ibu
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="nik_ibu" class="col-md-4 col-form-label">NIK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="nik_ibu" name="nik_ibu"
                                                            class="form-control @error('nik_ibu') is-invalid @enderror"
                                                            placeholder="16 digit NIK" value="{{ old('nik_ibu') }}">
                                                        @error('nik_ibu')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="kk_ibu" class="col-md-4 col-form-label">No. KK</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="kk_ibu" name="kk_ibu"
                                                            class="form-control @error('kk_ibu') is-invalid @enderror"
                                                            value="{{ old('kk_ibu') }}">
                                                        @error('kk_ibu')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="name_ibu" class="col-md-4 col-form-label">Nama</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="name_ibu" name="name_ibu"
                                                            class="form-control @error('name_ibu') is-invalid @enderror"
                                                            value="{{ old('name_ibu') }}">
                                                        @error('name_ibu')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label class="col-md-4 col-form-label">Tempat / Tgl. Lahir</label>
                                                    <div class="col-md-5">
                                                        <input type="text" id="tempat_lhr_ibu" name="tempat_lhr_ibu"
                                                            class="form-control @error('tempat_lhr_ibu') is-invalid @enderror"
                                                            value="{{ old('tempat_lhr_ibu') }}">
                                                        @error('tempat_lhr_ibu')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input id="tgl_lhr_ibu" type="date"
                                                            class="form-control @error('tgl_lhr_ibu') is-invalid @enderror"
                                                            name="tgl_lhr_ibu" value="{{ old('tgl_lhr_ibu') }}"
                                                            autocomplete="tgl_lhr_ibu" autofocus>

                                                        @error('tgl_lhr_ibu')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <label for="kewarganegaraan_ibu"
                                                        class="col-md-4 col-form-label">Kewarganegaraan</label>
                                                    <div class="col-md-8">
                                                        <select id="kewarganegaraan_ibu" name="kewarganegaraan_ibu"
                                                            class="form-control @error('kewarganegaraan_ibu') is-invalid @enderror"
                                                            data-placeholder="Pilih kewarganegaraan"></select>
                                                        @error('kewarganegaraan_ibu')
                                                            <span
                                                                class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- === LAMPIRAN === --}}
                                                <div class="card-header bg-transparent mb-3 text-center fw-bold">
                                                    Lampiran
                                                </div>
                                                <x-pengantar></x-pengantar>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tombol Ajukan --}}
                                <div class="d-flex justify-content-center" style="margin-top: 100px;">
                                    <button type="button" class="btn-ajukan" data-bs-toggle="modal"
                                        data-bs-target="#modalSkkematian">
                                        Ajukan
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>

                    {{-- Modal Konfirmasi --}}
                    <x-confirm-ajukan modalId="modalSkkematian" formId="formSkkematian"
                        title="Apakah Anda Yakin Ingin Mengirim Ajuan Ini?"
                        message="Pastikan isian ajuan telah sesuai. Kesalahan pengisian data atau lampiran dokumen dapat mengakibatkan ajuan ditolak saat proses verifikasi."
                        agreeLabel="Saya menyatakan bahwa isian formulir dan dokumen yang terlampir pada ajuan ini telah sesuai"
                        cancelText="Periksa Kembali" confirmText="Kirim Ajuan" />
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- <script type="text/javascript" src="{{ asset('assets/js/personal.js') }}"></script> --}}
        <script>
            let isProcessingPelapor = false;
            $("#nik_pelapor").keyup(function() {
                if ($(this).val().length == 16) {
                    if (isProcessingPelapor) return; // ⛔ cegah dobel
                    isProcessingPelapor = true;
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#kk_pelapor").val(response.kk);
                            $("#name_pelapor").val(response.name);
                            $("#kewarganegaraan_pelapor").select2("trigger", "select", {
                                data: {
                                    id: response.kewarganegaraan,
                                    text: response.kewarganegaraan_nm,
                                },
                            });

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            // const response = JSON.parse(xhr.responseText);
                            // // console.log('hey error', response.message);
                            // Swal.fire({
                            //     title: "Ooopppsss...",
                            //     text: response.message,
                            //     icon: "error",
                            //     confirmButtonText: "OK",
                            // });
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                        complete: function () {
                            isProcessingPelapor = false; // ✅ Allow next request
                        }
                    });
                }
            });

            let isProcessingSaksi1 = false;
            $("#nik_saksi1").keyup(function() {
                if ($(this).val().length == 16) {
                    if (isProcessingSaksi1) return; // ⛔ cegah dobel
                    isProcessingSaksi1 = true;
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#kk_saksi1").val(response.kk);
                            $("#name_saksi1").val(response.name);
                            $("#kewarganegaraan_saksi1").select2("trigger", "select", {
                                data: {
                                    id: response.kewarganegaraan,
                                    text: response.kewarganegaraan_nm,
                                },
                            });

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                        complete: function () {
                            isProcessingSaksi1 = false; // ✅ Allow next request
                        }
                    });
                }
            });

            let isProcessingSaksi2 = false;
            $("#nik_saksi2").keyup(function() {
                if ($(this).val().length == 16) {
                    if (isProcessingSaksi2) return; // ⛔ cegah dobel
                    isProcessingSaksi2 = true;
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#kk_saksi2").val(response.kk);
                            $("#name_saksi2").val(response.name);
                            $("#kewarganegaraan_saksi2").select2("trigger", "select", {
                                data: {
                                    id: response.kewarganegaraan,
                                    text: response.kewarganegaraan_nm,
                                },
                            });

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                        complete: function () {
                            isProcessingSaksi2 = false; // ✅ Allow next request
                        }
                    });
                }
            });

            let isProcessingAyah = false;
            $("#nik_ayah").keyup(function() {
                if ($(this).val().length == 16) {
                    if (isProcessingAyah) return; // ⛔ cegah dobel
                    isProcessingAyah = true;
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#kk_ayah").val(response.kk);
                            $("#name_ayah").val(response.name);
                            $("#tempat_lhr_ayah").val(response.tempat_lhr);
                            $("#tgl_lhr_ayah").val(response.tgl_lhr);
                            $("#kewarganegaraan_ayah").select2("trigger", "select", {
                                data: {
                                    id: response.kewarganegaraan,
                                    text: response.kewarganegaraan_nm,
                                },
                            });

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                        complete: function () {
                            isProcessingAyah = false; // ✅ Allow next request
                        }
                    });
                }
            });

            let isProcessingIbu = false;
            $("#nik_ibu").keyup(function() {
                if ($(this).val().length == 16) {
                    if (isProcessingIbu) return; // ⛔ cegah dobel
                    isProcessingIbu = true;
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#kk_ibu").val(response.kk);
                            $("#name_ibu").val(response.name);
                            $("#tempat_lhr_ibu").val(response.tempat_lhr);
                            $("#tgl_lhr_ibu").val(response.tgl_lhr);
                            $("#kewarganegaraan_ibu").select2("trigger", "select", {
                                data: {
                                    id: response.kewarganegaraan,
                                    text: response.kewarganegaraan_nm,
                                },
                            });

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                        complete: function () {
                            isProcessingIbu = false // ✅ Allow next request
                        }
                    });
                }
            });

            let isProcessingAlm = false;
            $("#nik").keyup(function() {
                if ($(this).val().length == 16) {
                    if (isProcessingAlm) return; // ⛔ cegah dobel
                    isProcessingAlm = true;
                    let nik = this.value;
                    let web = '{{ env('APP_URL') }}';
                    $.ajax({
                        url: web + "/api/personal?nik=" + nik,
                        success: function(response) {
                            $("#name").val(response.name);

                            Toastify({
                                text: "Data ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(25, 135, 84, 1)",
                                },
                            }).showToast();
                        },
                        error: function(xhr) {
                            Toastify({
                                text: "Data tidak ditemukan!",
                                duration: 1000,
                                close: true,
                                gravity: "top", // `top` or `bottom`
                                position: "center", // `left`, `center` or `right`
                                stopOnFocus: true, // Prevents dismissing of toast on hover
                                style: {
                                    background: "rgba(255, 0, 0, 1)",
                                },
                            }).showToast();
                        },
                        complete: function () {
                            isProcessingAlm = false; // ✅ Allow next request
                        }
                    });
                }
            });

            $(document).ready(function() {
                $("#kewarganegaraan_pelapor").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#kewarganegaraan_saksi1").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#kewarganegaraan_saksi2").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#kewarganegaraan_ayah").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#kewarganegaraan_ibu").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                    ajax: {
                        url: route("kewarganegaraan.index"),
                        dataType: "json",
                        processResults: function(response) {
                            return {
                                results: response,
                            };
                        },
                    },
                });

                $("#sebab_kematian").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                });

                $("#yang_menerangkan").select2({
                    theme: "bootstrap-5",
                    width: $(this).data("width") ?
                        $(this).data("width") : $(this).hasClass("w-100") ?
                        "100%" : "style",
                    placeholder: $(this).data("placeholder"),
                    minimumInputLenght: 2,
                });
            });
        </script>
    @endpush
@endsection
