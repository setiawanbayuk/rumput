<div class="row mb-3">
    <label for="peruntukan"
        class="col-md-3 col-form-label text-md-end">{{ __('Dipergunakan Untuk') }}</label>

    <div class="col-md-8">
        <textarea class="form-control @error('peruntukan') is-invalid @enderror" id="peruntukan" name="peruntukan"
            autocomplete="peruntukan" autofocus>{{ old('peruntukan', $peruntukan) }}</textarea>
        @error('peruntukan')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>
