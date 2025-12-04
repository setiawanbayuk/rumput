@props(['peruntukan' => '', 'readonly' => false])

<div class="row mb-3">
    <label for="peruntukan"
        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Dipergunakan Untuk') }}</label>

    <div class="col-md-8">

        @if ($readonly)
            {{-- Mode READONLY --}}
            <div class="form-control-plaintext border rounded px-3 py-2 bg-light" style="white-space: pre-line;">
                {{ $peruntukan }}
            </div>
        @else
            {{-- Mode EDITABLE --}}
            <textarea class="form-control @error('peruntukan') is-invalid @enderror"
                id="peruntukan"
                name="peruntukan"
                autocomplete="peruntukan"
                autofocus>{{ old('peruntukan', $peruntukan) }}</textarea>

            @error('peruntukan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        @endif

    </div>
</div>
