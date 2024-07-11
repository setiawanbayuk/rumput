<div class="row mb-3">
    <label for="kepada"
        class="col-md-3 col-form-label text-md-end">{{ __('Diberikan Kepada') }}</label>

    <div class="col-md-8">
        <input id="kepada" type="text"
            class="form-control @error('kepada') is-invalid @enderror" name="kepada"
            value="{{ old('kepada', $kepada) }}" autocomplete="kepada">

        @error('kepada')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
