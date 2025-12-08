@props(['pengantar' => null, 'readonly' => false])

<div class="row mb-3">
    <label for="pengantar"
        class="col-md-3 col-form-label text-md-start ms-2">
        {{ __('Surat Pengantar') }}
    </label>

    <div class="col-md-8">
        {{-- ======================== 1. MODE READONLY ======================== --}}
        @if ($readonly)
            @if ($pengantar)
                {{-- Kondisi 1: Tampilkan label + file --}}
                <a href="{{ asset($pengantar) }}" target="_blank">
                    <img src="{{ asset($pengantar) }}" class="img-fluid rounded border" style="max-height: 180px;">
                </a>
            @else
                <div class="text-muted fst-italic">Tidak ada lampiran</div>
            @endif
        {{-- ======================== 2 & 3. MODE EDITABLE ======================== --}}
        @else
            {{-- INPUT FILE SELALU MUNCUL PADA MODE EDIT --}}
            <input class="form-control @error('pengantar') is-invalid @enderror"
                type="file" id="pengantar" name="pengantar"
                accept="image/jpeg, image/jpg, image/png">
            @error('pengantar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            {{-- Kondisi 2: Ada file lama → tampilkan preview --}}
            @if ($pengantar)
                <a href="{{ asset($pengantar) }}" target="_blank">
                    <img src="{{ asset($pengantar) }}"
                        class="img-fluid rounded border mt-3"
                        style="max-height: 180px;">
                </a>
            @endif
            {{-- Kondisi 3: Tidak ada file lama → preview tidak muncul --}}
        @endif
    </div>
</div>
