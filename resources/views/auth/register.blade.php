@extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card overflow-hidden my-4"
                    style="border: 3px solid transparent; border-radius: 70px; background: linear-gradient(white, white) padding-box, linear-gradient(to bottom, #AEA07A, #7896B2) border-box;">
                    {{-- <div class="card-header">{{ __('Register') }}</div> --}}
                    <div class="card-body p-0">
                        <div class="d-flex flex-row justify-content-between p-0">
                            <div class="d-flex flex-column" style="width: 800px">
                                <div class="d-flex flex-row justify-content-evenly align-items-center">
                                    <div class="p-4">
                                        <img src="{{ asset('assets/logo.png') }}" class="object-fit-contain"
                                            style="width: 80px; margin-right: 30px;" alt="">
                                    </div>
                                    <div class="p-2">
                                        <h2 class="display-5" style="color: #AEA07A; font-weight: 550; margin-right: 90px;">
                                            Register Form
                                        </h2>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <p class="text-dark" style="margin-left: 18px; font-size: 0.9rem">
                                                Silahkan daftarkan identitas Anda.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-center">
                                        <label for="name" class="col-md-4 col-form-label text-md-end"
                                            style="font-size: 0.8rem">Nama</label>
                                        <div class="col-md-7">
                                            <div class="fi fi--a">
                                                <input id="name" name="name" type="text"
                                                    value="{{ old('name') }}" placeholder=" " {{-- penting untuk :placeholder-shown --}}
                                                    required class="fi-input @error('name') is-invalid @enderror"
                                                    autocomplete="name">
                                                <label for="name" class="fi-label">Nama Lengkap</label>

                                                @error('name')
                                                    <div class="invalid-feedback d-block"><strong>{{ $message }}</strong>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center">
                                        <label for="email" class="col-md-4 col-form-label text-md-end"
                                            style="font-size: 0.8rem">Email</label>
                                        <div class="col-md-7">
                                            <div class="fi fi--a">
                                                <input id="email" name="email" type="email"
                                                    value="{{ old('email') }}" placeholder=" " required
                                                    class="fi-input @error('email') is-invalid @enderror"
                                                    autocomplete="email">
                                                <label for="email" class="fi-label">Alamat Email</label>
                                                @error('email')
                                                    <div class="invalid-feedback d-block"><strong>{{ $message }}</strong>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center">
                                        <label for="phone" class="col-md-4 col-form-label text-md-end"
                                            style="font-size: 0.8rem">No HP
                                            (WhatsApp)</label>
                                        <div class="col-md-7">
                                            <div class="fi fi--a">
                                                <input id="phone" name="phone" type="tel"
                                                    value="{{ old('phone') }}" placeholder=" " required
                                                    class="fi-input @error('phone') is-invalid @enderror"
                                                    autocomplete="tel">
                                                <label for="phone" class="fi-label">08xxxxxxxxxx</label>
                                                @error('phone')
                                                    <div class="invalid-feedback d-block"><strong>{{ $message }}</strong>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center">
                                        <label for="nik" class="col-md-4 col-form-label text-md-end"
                                            style="font-size: 0.8rem">NIK</label>
                                        <div class="col-md-7">
                                            <div class="fi fi--a">
                                                <input id="nik" name="nik" type="text"
                                                    value="{{ old('nik') }}" placeholder=" " required pattern="[0-9]{16}"
                                                    title="Masukkan 16 digit angka"
                                                    class="fi-input @error('nik') is-invalid @enderror" autocomplete="nik">
                                                <label for="nik" class="fi-label">Masukkan 16 Digit NIK</label>
                                                @error('nik')
                                                    <div class="invalid-feedback d-block"><strong>{{ $message }}</strong>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="id_instansi" class="col-md-4 col-form-label text-md-end"
                                            style="font-size: 0.8rem">{{ __('Kelurahan') }}</label>
                                        <div class="col-md-7">
                                            <select class="form-select @error('id_instansi') is-invalid @enderror"
                                                id="id_instansi" name="id_instansi" data-placeholder="Kelurahan">
                                            </select>
                                            @error('id_instansi')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    {{-- RW (dependent on Kelurahan) --}}
                                    <div class="row mb-3">
                                        <label for="id_rw" class="col-md-4 col-form-label text-md-end"
                                            style="font-size:.8rem">{{ __('RW') }}</label>
                                        <div class="col-md-7">
                                            <select class="form-select" @error('id_rw') is-invalid @enderror
                                                id="id_rw" name="id_rw" data-placeholder="Pilih RW" disabled></select>
                                        </div>
                                        @error('id_rw')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    {{-- RT (dependent on RW) --}}
                                    <div class="row mb-3">
                                        <label for="id_rt" class="col-md-4 col-form-label text-md-end"
                                            style="font-size:.8rem">{{ __('RT') }}</label>
                                        <div class="col-md-7">
                                            <select class="form-select" @error('id_rt') is-invalid @enderror
                                                id="id_rt" name="id_rt" data-placeholder="Pilih RT" disabled></select>
                                        </div>
                                        @error('id_rt')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="row mb-2 align-items-center">
                                        <label for="password" class="col-md-4 col-form-label text-md-end"
                                            style="font-size: 0.8rem">
                                            Password
                                        </label>

                                        <div class="col-md-7">
                                            <div class="fi fi--a">
                                                <input id="password" name="password" type="password" placeholder=" "
                                                    required class="fi-input @error('password') is-invalid @enderror">
                                                <label for="password" class="fi-label">Password</label>
                                            </div>
                                        </div>

                                        <!-- Aturan password, dibuat sejajar input -->
                                        <div class="col-md-7 offset-md-4" style="margin-left: 205px">
                                            <ul class="form-text password-rules">
                                                <li>Panjang minimal 8 karakter.</li>
                                                <li>Harus mengandung campuran huruf besar dan kecil (A-a)</li>
                                                <li>Harus menyertakan angka (1, 2, 3, dst.)</li>
                                                <li>Harus menyertakan simbol ((.), (_), (#), dst.)</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center">
                                        <label for="password_confirmation" class="col-md-4 col-form-label text-md-end"
                                            style="font-size: 0.8rem">Confirm Password</label>
                                        <div class="col-md-7">
                                            <div class="fi fi--a">
                                                <input id="password_confirmation" name="password_confirmation"
                                                    type="password" placeholder=" " required class="fi-input"
                                                    autocomplete="new-password">
                                                <label for="password_confirmation" class="fi-label">Konfirmasi
                                                    Password Anda</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-7 offset-md-4">
                                            <button type="submit" class="btn-daftar">
                                                {{ __('Daftar Sekarang') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="p-0" style="width: 780px">
                                <img src="{{ asset('assets/register.png') }}" class="object-fit-contain"
                                    style="width: 100%" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(function() {
        const $kel = $("#id_instansi"); // = skpds.id (id_kel)
        const $rw = $("#id_rw");
        const $rt = $("#id_rt");

        function resetRW() {
            $rw.prop("disabled", true).empty().append(new Option("Pilih RW", ""));
            resetRT();
        }

        function resetRT() {
            $rt.prop("disabled", true).empty().append(new Option("Pilih RT", ""));
        }
        resetRW();

        // ---- Instansi (SKPD) -> Select2 AJAX ----
        $kel.select2({
            theme: "bootstrap-5",
            width: $kel.data("width") ? $kel.data("width") : ($kel.hasClass("w-100") ?
                "100%" : "style"),
            placeholder: $kel.data("placeholder") || "Pilih Kelurahan",
            minimumInputLength: 2,
            ajax: {
                url: route("skpd.index"), // Ziggy
                dataType: "json",
                delay: 250,
                data: params => ({
                    q: params.term || ""
                }),
                processResults: resp => ({
                    results: Array.isArray(resp) ? resp : (resp.data || [])
                })
            }
        });

        // ---- RW (non-AJAX Select2, isi manual) ----
        $rw.select2({
            theme: "bootstrap-5",
            width: "100%",
            placeholder: "Pilih RW",
            minimumResultsForSearch: Infinity
        });

        // ---- RT (non-AJAX Select2, isi manual) ----
        $rt.select2({
            theme: "bootstrap-5",
            width: "100%",
            placeholder: "Pilih RT",
            minimumResultsForSearch: Infinity
        });

        // Saat instansi dipilih -> load RW
        $kel.on("select2:select", function(e) {
            const idKel = e.params?.data?.id; // skpds.id
            if (!idKel) return resetRW();

            $rw.prop("disabled", false).empty().append(new Option("Memuat RW...", "")).trigger(
            "change");
            resetRT();

            $.getJSON(route('regional.rw', idKel), function(resp) {
                const list = Array.isArray(resp) ? resp : (resp.data || []);
                $rw.empty().append(new Option("Pilih RW", ""));
                list.forEach(r => $rw.append(new Option(`RW ${r.rw}`, r.rw)));
                $rw.prop("disabled", false).trigger("change");
            }).fail(() => resetRW());
        });

        // Saat RW dipilih -> load RT
        $rw.on("change", function() {
            const rwVal = $(this).val();
            const idKel = ($kel.select2('data')[0] || {}).id;
            if (!idKel || !rwVal) return resetRT();

            $rt.prop("disabled", false).empty().append(new Option("Memuat RT...", "")).trigger(
            "change");

            $.getJSON(route('regional.rt', [idKel, rwVal]), function(resp) {
                const list = Array.isArray(resp) ? resp : (resp.data || []);
                $rt.empty().append(new Option("Pilih RT", ""));
                list.forEach(r => $rt.append(new Option(`RT ${r.rt}`, r.rt)));
                $rt.prop("disabled", false).trigger("change");
            }).fail(() => resetRT());
        });
    });
</script>
