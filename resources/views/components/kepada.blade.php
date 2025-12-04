@props(['kepada' => '', 'readonly' => false])

<div class="row mb-3">
    <label for="kepada"
        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Diberikan Kepada') }}</label>

    <div class="col-md-8">

        @if ($readonly)
            {{-- Mode READONLY --}}
            <div class="form-control-plaintext border rounded px-3 py-2 bg-light">
                {{ $kepada }}
            </div>
        @else
            {{-- Mode EDITABLE --}}
            <input id="kepada" type="text"
                class="form-control @error('kepada') is-invalid @enderror"
                name="kepada"
                value="{{ old('kepada', $kepada) }}"
                autocomplete="kepada">

            @error('kepada')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        @endif

    </div>
</div>
