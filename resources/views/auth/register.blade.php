@extends('layouts.app')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card rounded-5 overflow-hidden">
                    {{-- <div class="card-header">{{ __('Register') }}</div> --}}

                    <div class="card-body p-0">
                        <div class="d-flex flex-row justify-content-between p-0">
                            <div class="d-flex flex-column mb-3" style="width: 750px">
                                <div class="d-flex flex-row mb-3 justify-content-evenly align-items-center">
                                    <div class="p-2"><img src="{{ asset('assets/logo.png') }}" class="object-fit-contain"
                                            style="width: 100px" alt=""></div>
                                    <div class="p-2">
                                        <h2 class="display-4"> Register Form</h1>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf

                                    <div class="row mb-3">
                                        <label for="name"
                                            class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                                        <div class="col-md-6">
                                            <input id="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror" name="name"
                                                value="{{ old('name') }}" required autocomplete="name" autofocus>

                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="email"
                                            class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                        <div class="col-md-6">
                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror" name="email"
                                                value="{{ old('email') }}" required autocomplete="email">

                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="phone"
                                            class="col-md-4 col-form-label text-md-end">{{ __('No HP (WhatsApp)') }}</label>

                                        <div class="col-md-6">
                                            <input id="phone" type="phone"
                                                class="form-control @error('phone') is-invalid @enderror" name="phone"
                                                value="{{ old('phone') }}" required autocomplete="phone"
                                                placeholder="08XXXXXXXXXX">

                                            @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="nik"
                                            class="col-md-4 col-form-label text-md-end">{{ __('NIK') }}</label>

                                        <div class="col-md-6">
                                            <input id="nik" type="nik"
                                                class="form-control @error('nik') is-invalid @enderror" name="nik"
                                                value="{{ old('nik') }}" required autocomplete="nik">

                                            @error('nik')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="id_instansi"
                                            class="col-md-4 col-form-label text-md-end">{{ __('Kelurahan') }}</label>

                                        <div class="col-md-6">
                                            <select class="form-control @error('id_instansi') is-invalid @enderror"
                                                id="id_instansi" name="id_instansi" data-placeholder="Kelurahan">
                                            </select>
                                            @error('id_instansi')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="password"
                                            class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror" name="password"
                                                required autocomplete="new-password">

                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                            <ul class="form-text text-primary">
                                                <li>Panjang minimal 8 karakter.</li>
                                                <li>Harus mengandung campuran huruf besar dan kecil.</li>
                                                <li>Harus menyertakan angka.</li>
                                                <li>Harus menyertakan simbol.</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="password-confirm"
                                            class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                        <div class="col-md-6">
                                            <input id="password-confirm" type="password" class="form-control"
                                                name="password_confirmation" required autocomplete="new-password">
                                        </div>
                                    </div>

                                    <div class="row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-primary btn-lg col-6">
                                                {{ __('Daftar Sekarang') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="p-0" style="width:500px"><img src="{{ asset('assets/register.png') }}"
                                    class="object-fit-contain" style="width: 100%" alt=""></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $("#id_instansi").select2({
            theme: "bootstrap-5",
            width: $(this).data("width") ?
                $(this).data("width") : $(this).hasClass("w-100") ?
                "100%" : "style",
            placeholder: $(this).data("placeholder"),
            minimumInputLenght: 2,
            ajax: {
                url: route("skpd.index"),
                dataType: "json",
                processResults: function(response) {
                    return {
                        results: response,
                    };
                },
            },
        });
    });
</script>
