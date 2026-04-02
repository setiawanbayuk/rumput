@props(['peruntukan' => '', 'readonly' => false])

<div class="row mb-3">
    <label for="peruntukan"
        class="col-md-3 col-form-label text-md-start ms-2">{{ __('Dipergunakan Untuk') }}</label>

    <div class="col-md-8">

        @if ($readonly)
            <div class="form-control-plaintext border rounded px-3 py-2 bg-light" style="white-space: pre-line;">
                {{ $peruntukan }}
            </div>
        @else
            <select class="form-control @error('peruntukan') is-invalid @enderror"
                id="peruntukan"
                name="peruntukan">
                <option value="">-- Pilih Peruntukan --</option>
                <option value="menikah" {{ old('peruntukan', $peruntukan) == 'menikah' ? 'selected' : '' }}>Menikah</option>
                <option value="umum" {{ old('peruntukan', $peruntukan) == 'umum' ? 'selected' : '' }}>Umum</option>
                <option value="rumah" {{ old('peruntukan', $peruntukan) == 'rumah' ? 'selected' : '' }}>Pengajuan Rumah</option>
                <option value="kendaraan" {{ old('peruntukan', $peruntukan) == 'kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                <option value="lainnya" {{ old('peruntukan', $peruntukan) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>

            @error('peruntukan')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        @endif

    </div>
</div>