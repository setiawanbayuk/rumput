@props(['keterangan' => '', 'readonly' => false])

<div class="row mb-3">
    <label for="keterangan"
        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Keterangan') }}</label>

    <div class="col-md-8">

        @if ($readonly)
            {{-- Mode READONLY --}}
            <div class="form-control-plaintext border rounded px-3 py-2 bg-light" style="white-space: pre-line;">
                {{ $keterangan }}
            </div>
        @else
            {{-- Mode EDITABLE --}}
            <textarea class="form-control @error('keterangan') is-invalid @enderror"
                id="keterangan"
                name="keterangan"
                autocomplete="keterangan"
                autofocus>{{ old('keterangan', $keterangan) }}</textarea>

            @error('keterangan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        @endif

    </div>
</div>
