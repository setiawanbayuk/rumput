@props(['pengantar' => '', 'readonly' => false])

<div class="row mb-3">
    <label for="pengantar"
        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Surat Pengantar') }}</label>

    <div class="col-md-8">

        @if ($readonly)
            {{-- Mode READONLY --}}
            @if ($pengantar)
                <a href="{{ $pengantar }}" target="_blank">
                    <img src="{{ $pengantar }}" alt="Surat Pengantar"
                         class="img-fluid rounded border" style="max-height: 180px;">
                </a>
            @else
                <div class="text-muted fst-italic">Tidak ada lampiran</div>
            @endif

        @else
            {{-- Mode EDITABLE (input file) --}}
            <input class="form-control" type="file" id="pengantar" name="pengantar"
                accept="image/jpeg, image/jpg, image/png">
        @endif

    </div>
</div>
