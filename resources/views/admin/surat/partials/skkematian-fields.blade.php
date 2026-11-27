{{-- Field F-2.29 khusus SK Kematian; alur CRUD dan komponen upload mengikuti surat lainnya. --}}
@php
    $kelompokKematian = \App\Support\SkKematianFields::GROUPS;
    $tanggalKematian = ['tanggal_meninggal', 'ayah_tgl_lahir', 'ibu_tgl_lahir', 'pelapor_tgl_lahir', 'saksi1_tgl_lahir', 'saksi2_tgl_lahir'];
    $nikKematian = ['nomor_kk', 'ayah_nik', 'ibu_nik', 'pelapor_nik', 'saksi1_nik', 'saksi2_nik'];
    $wajibKematian = \App\Support\SkKematianFields::REQUIRED;
@endphp
<input type="hidden" name="peruntukan" id="peruntukan" value="Pencatatan Kematian">
<div class="small text-muted mb-3">Identitas <strong>Jenazah</strong> diisi melalui Form Identitas dan OCR KTP di sebelah kiri. Lengkapi data F-2.29 berikut.</div>
@foreach ($kelompokKematian as $judul => $fields)
    <h6 class="fw-semibold mt-3 mb-3 pb-2 border-bottom" style="color:#7f704c">{{ $judul }}</h6>
    @if (in_array($judul, ['Ayah', 'Ibu'], true))
        <small class="text-muted d-block mb-2">Data {{ strtolower($judul) }} (termasuk nama dan NIK) boleh dikosongkan jika tidak diketahui.</small>
    @endif
    @foreach ($fields as $field => $label)
        @php
            $tipe = in_array($field, $tanggalKematian, true) ? 'date' : ($field === 'jam_meninggal' ? 'time' : (in_array($field, ['jenazah_umur','jenazah_anak_ke','ayah_umur','ibu_umur','pelapor_umur','saksi1_umur','saksi2_umur'], true) ? 'number' : 'text'));
            $nilai = old($field, data_get($variableData, $field, ''));
            $wajib = in_array($field, $wajibKematian, true);
        @endphp
        <div class="row mb-3">
            <label for="{{ $field }}" class="col-md-3 col-form-label text-md-start ms-2">{{ $label }} @if($wajib)<span class="text-danger">*</span>@endif</label>
            <div class="col-md-8">
                <input type="{{ $tipe }}"
                       class="form-control @error($field) is-invalid @enderror"
                       name="{{ $field }}" id="{{ $field }}"
                       value="{{ $nilai }}"
                       @if(in_array($field, $nikKematian, true)) maxlength="16" inputmode="numeric" pattern="[0-9]{16}" @endif
                       @if($wajib) required @endif>
                @error($field)<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
            </div>
        </div>
    @endforeach
@endforeach

@once
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const from = document.getElementById('kk');
        const to = document.getElementById('nomor_kk');
        if (!from || !to) return;
        function copyIfEmpty() { if (!to.value.trim() && from.value.trim()) to.value = from.value.trim(); }
        from.addEventListener('change', copyIfEmpty);
        from.addEventListener('input', copyIfEmpty);
        copyIfEmpty();
    });
    </script>
    @endpush
@endonce
