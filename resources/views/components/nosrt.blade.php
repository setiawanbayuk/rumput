<div id="nomor_surat" nama="nomor_surat">
    <div class="row mb-3">
        <label for="no_surat"
            class="col-md-3 col-form-label text-md-end">{{ __('No Surat') }}</label>

        <div class="col-md-2">
            <input id="kd_jenis_surat" type="text"
                class="form-control @error('kd_jenis_surat') is-invalid @enderror"
                name="kd_jenis_surat" value="{{ old('kd_jenis_surat', $kd_jenis_surat) }}"
                autocomplete="kd_jenis_surat" autofocus>

            @error('kd_jenis_surat')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="col-md-2">
            <input id="no_urut_surat" type="text"
                class="form-control @error('no_urut_surat') is-invalid @enderror"
                name="no_urut_surat" value="{{ $no_urut_surat }}"
                autocomplete="no_urut_surat" autofocus>

            @error('no_urut_surat')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="col-md-2">
            <input id="kd_instansi" type="text"
                class="form-control @error('kd_instansi') is-invalid @enderror"
                name="kd_instansi" value="{{ $instansi_kode }}" readonly
                autocomplete="kd_instansi" autofocus>

            @error('kd_instansi')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="col-md-2">
            <input id="tahun" type="text"
                class="form-control @error('tahun') is-invalid @enderror" name="tahun"
                value="{{ date('Y') }}" readonly autocomplete="tahun" autofocus>

            @error('tahun')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <label for="tgl_surat"
            class="col-md-3 col-form-label text-md-end">{{ __('Tgl. Surat') }}</label>
        <div class="col-md-8">
            <input id="tgl_surat" type="date"
                class="form-control @error('tgl_surat') is-invalid @enderror"
                name="tgl_surat" value="{{ old('tgl_surat', $tgl_surat) }}" autocomplete="tgl_surat"
                autofocus>

            @error('tgl_surat')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
