<div id="data_boro" name="data_boro">
    <div class="row mb-3">
        <label for="provinsi_boro" class="col-md-3 col-form-label text-md-end">{{ __('Provinsi') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('provinsi_boro') is-invalid @enderror" id="provinsi_boro"
                name="provinsi_boro" data-placeholder="Provinsi">
            </select>
            @error('provinsi_boro')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="kabko_boro" class="col-md-3 col-form-label text-md-end">{{ __('Kabupaten/Kota') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('kabko_boro') is-invalid @enderror" id="kabko_boro" name="kabko_boro"
                data-placeholder="Kabupaten/Kota">
            </select>
            @error('kabko_boro')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="kecamatan_boro" class="col-md-3 col-form-label text-md-end">{{ __('Kecamatan') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('kecamatan_boro') is-invalid @enderror" id="kecamatan_boro"
                name="kecamatan_boro" data-placeholder="Kecamatan">
            </select>
            @error('kecamatan_boro')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="kelurahan_boro" class="col-md-3 col-form-label text-md-end">{{ __('Kelurahan') }}</label>

        <div class="col-md-8">
            <select class="form-control @error('kelurahan_boro') is-invalid @enderror" id="kelurahan_boro"
                name="kelurahan_boro" data-placeholder="Kelurahan">
            </select>
            @error('kelurahan_boro')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="alamat_boro" class="col-md-3 col-form-label text-md-end">{{ __('Alamat') }}</label>

        <div class="col-md-8">
            <textarea class="form-control @error('alamat_boro') is-invalid @enderror" id="alamat_boro" name="alamat_boro"
                autocomplete="alamat_boro" autofocus>{{ old('alamat_boro', $alamat_boro) }}</textarea>
            @error('alamat_boro')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <label for="tgl" class="col-md-3 col-form-label text-md-end">{{ __('Pada Tgl.') }}</label>
        <div class="col-md-3">
            <input id="tgl_awal" type="date" class="form-control @error('tgl_awal') is-invalid @enderror"
                name="tgl_awal" value="{{ old('tgl_awal', $tgl_awal) }}" autocomplete="tgl_awal" autofocus>

            @error('tgl_awal')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <label for="tgl" class="col-md-2 col-form-label text-md-center">{{ __('s/d') }}</label>

        <div class="col-md-3">
            <input id="tgl_akhir" type="date" class="form-control @error('tgl_akhir') is-invalid @enderror"
                name="tgl_akhir" value="{{ old('tgl_akhir', $tgl_akhir) }}" autocomplete="tgl_akhir" autofocus>

            @error('tgl_akhir')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
