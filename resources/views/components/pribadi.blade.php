<div id="pribadi" name="pribadi">
    <div class="row mb-3">
        <label for="nik" class="col-md-3 col-form-label text-md-start ms-2">{{ __('NIK') }}</label>
        <div class="col-md-8">
            <div class="input-group">
                <input
    type="text"
    class="form-control @error('nik') is-invalid @enderror"
    id="nik"
    name="nik"
    value="{{ old('nik') }}"
    placeholder="Masukkan 16 digit NIK"
    aria-label="NIK"
    aria-describedby="basic-addon2"
    maxlength="16"
    minlength="16"
    inputmode="numeric"
    pattern="[0-9]{16}"
    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,16)"
>
                <button type="button" class="input-group-text btn btn-subtle-primary"
                    onclick="checkNIK()">CARI</button>

                @error('nik')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <label for="kk" class="col-md-3 col-form-label text-md-start ms-2">{{ __('No. KK') }}</label>

        <div class="col-md-8">
            <input
    id="kk"
    type="text"
    class="form-control @error('kk') is-invalid @enderror"
    name="kk"
    value="{{ old('kk') }}"
    autocomplete="kk"
    autofocus
    maxlength="16"
    minlength="16"
    inputmode="numeric"
    pattern="[0-9]{16}"
    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,16)"
>

            @error('kk')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="name" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Nama') }}</label>

        <div class="col-md-8">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                value="{{ old('name') }}" autocomplete="name" autofocus>

            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="gender" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Jenis Kelamin') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender"
                data-placeholder="Jenis Kelamin">
            </select>
            @error('gender')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="status_kwn" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Status Perkawinan') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('status_kwn') is-invalid @enderror" id="status_kwn" name="status_kwn"
                data-placeholder="Status Perkawinan">
            </select>
            @error('status_kwn')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="kewarganegaraan" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Kewarganegaraan') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('kewarganegaraan') is-invalid @enderror" id="kewarganegaraan"
                name="kewarganegaraan" data-placeholder="Kewarganegaraan">
            </select>
            @error('kewarganegaraan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="ttl" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Tempat/Tgl. Lahir') }}</label>

        <div class="col-md-5">
            <input id="tempat_lhr" type="text" class="form-control @error('tempat_lhr') is-invalid @enderror"
                name="tempat_lhr" value="{{ old('tempat_lhr') }}" autocomplete="tempat_lhr" autofocus>

            @error('tempat_lhr')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="col-md-3">
            <input id="tgl_lhr" type="date" class="form-control @error('tgl_lhr') is-invalid @enderror"
                name="tgl_lhr" value="{{ old('tgl_lhr') }}" autocomplete="tgl_lhr" autofocus>

            @error('tgl_lhr')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="agama" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Agama') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('agama') is-invalid @enderror" id="agama" name="agama"
                data-placeholder="Agama">
            </select>
            @error('agama')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="pendidikan" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Pendidikan Terakhir') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('pendidikan') is-invalid @enderror" id="pendidikan" name="pendidikan"
                data-placeholder="Pendidikan Terakhir">
            </select>
            @error('pendidikan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="pekerjaan" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Pekerjaan') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('pekerjaan') is-invalid @enderror" id="pekerjaan" name="pekerjaan"
                data-placeholder="Pekerjaan">
            </select>
            @error('pekerjaan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="provinsi" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Provinsi') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi"
                data-placeholder="Provinsi">
            </select>
            @error('provinsi')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="kabko" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Kabupaten/Kota') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('kabko') is-invalid @enderror" id="kabko" name="kabko"
                data-placeholder="Kabupaten/Kota">
            </select>
            @error('kabko')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="kecamatan" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Kecamatan') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('kecamatan') is-invalid @enderror" id="kecamatan" name="kecamatan"
                data-placeholder="Kecamatan">
            </select>
            @error('kecamatan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="kelurahan" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Kelurahan') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('kelurahan') is-invalid @enderror" id="kelurahan" name="kelurahan"
                data-placeholder="Kelurahan">
            </select>
            @error('kelurahan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="rw" class="col-md-3 col-form-label text-md-start ms-2">{{ __('RW') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('rw') is-invalid @enderror" id="rw" name="rw"
                data-placeholder="RW">
            </select>
            @error('rw')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="rt" class="col-md-3 col-form-label text-md-start ms-2">{{ __('RT') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('rt') is-invalid @enderror" id="rt" name="rt"
                data-placeholder="RT">
            </select>
            @error('rt')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="alamat" class="col-md-3 col-form-label text-md-start ms-2">{{ __('Alamat') }}</label>

        <div class="col-md-8">
            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat"
                autocomplete="alamat" autofocus>{{ old('alamat') }}</textarea>
            @error('alamat')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
<input type="hidden" name="gender_nm" id="gender_nm" value="{{ old('gender_nm') }}">
<input type="hidden" name="status_kwn_nm" id="status_kwn_nm" value="{{ old('status_kwn_nm') }}">
<input type="hidden" name="kewarganegaraan_nm" id="kewarganegaraan_nm" value="{{ old('kewarganegaraan_nm') }}">
<input type="hidden" name="agama_nm" id="agama_nm" value="{{ old('agama_nm') }}">
<input type="hidden" name="pendidikan_nm" id="pendidikan_nm" value="{{ old('pendidikan_nm') }}">
<input type="hidden" name="pekerjaan_nm" id="pekerjaan_nm" value="{{ old('pekerjaan_nm') }}">
<input type="hidden" name="provinsi_nm" id="provinsi_nm" value="{{ old('provinsi_nm') }}">
<input type="hidden" name="kabko_nm" id="kabko_nm" value="{{ old('kabko_nm') }}">
<input type="hidden" name="kecamatan_nm" id="kecamatan_nm" value="{{ old('kecamatan_nm') }}">
<input type="hidden" name="kelurahan_nm" id="kelurahan_nm" value="{{ old('kelurahan_nm') }}">
<input type="hidden" name="rw_nm" id="rw_nm" value="{{ old('rw_nm') }}">
<input type="hidden" name="rt_nm" id="rt_nm" value="{{ old('rt_nm') }}">	
</div>
