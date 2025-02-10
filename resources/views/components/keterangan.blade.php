
<div class="row mb-3">
    <label for="keterangan"
        class="col-md-3 col-form-label text-md-end">{{ __('Keterangan') }}</label>

    <div class="col-md-8">
        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
            autocomplete="keterangan" autofocus>{{ old('keterangan', $keterangan) }}</textarea>
        @error('keterangan')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
