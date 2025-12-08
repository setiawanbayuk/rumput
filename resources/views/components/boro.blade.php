@props([
    'prov_boro_nm' => '',
    'kabko_boro_nm' => '',
    'kec_boro_nm' => '',
    'kel_boro_nm' => '',
    'alamat_boro' => '',
    'tgl_awal' => '',
    'tgl_akhir' => '',
    'prov_boro' => '',
    'kabko_boro' => '',
    'kec_boro' => '',
    'kel_boro' => '',
    'readonly' => false
])

<div id="data_boro">
    {{-- PROVINSI --}}
    <div class="row mb-3">
        <label class="col-md-3 col-form-label text-md-start ms-2">Provinsi</label>
        <div class="col-md-8">
            @if($readonly)
                <div class="form-control-plaintext border rounded bg-light px-3 py-2">
                    {{ $prov_boro_nm }}
                </div>
            @else
                <select class="form-control" id="provinsi_boro" name="provinsi_boro">
                    @if($prov_boro)
                        <option value="{{ $prov_boro }}" selected>{{ $prov_boro_nm }}</option>
                    @endif
                </select>
            @endif
        </div>
    </div>

    {{-- KABUPATEN/KOTA --}}
    <div class="row mb-3">
        <label class="col-md-3 col-form-label text-md-start ms-2">Kabupaten/Kota</label>
        <div class="col-md-8">
            @if($readonly)
                <div class="form-control-plaintext border rounded bg-light px-3 py-2">
                    {{ $kabko_boro_nm }}
                </div>
            @else
                <select class="form-control" id="kabko_boro" name="kabko_boro">
                    @if($kabko_boro)
                        <option value="{{ $kabko_boro }}" selected>{{ $kabko_boro_nm }}</option>
                    @endif
                </select>
            @endif
        </div>
    </div>

    {{-- KECAMATAN --}}
    <div class="row mb-3">
        <label class="col-md-3 col-form-label text-md-start ms-2">Kecamatan</label>
        <div class="col-md-8">
            @if($readonly)
                <div class="form-control-plaintext border rounded bg-light px-3 py-2">
                    {{ $kec_boro_nm }}
                </div>
            @else
                <select class="form-control" id="kecamatan_boro" name="kecamatan_boro">
                    @if($kec_boro)
                        <option value="{{ $kec_boro }}" selected>{{ $kec_boro_nm }}</option>
                    @endif
                </select>
            @endif
        </div>
    </div>

    {{-- KELURAHAN --}}
    <div class="row mb-3">
        <label class="col-md-3 col-form-label text-md-start ms-2">Kelurahan</label>
        <div class="col-md-8">
            @if($readonly)
                <div class="form-control-plaintext border rounded bg-light px-3 py-2">
                    {{ $kel_boro_nm }}
                </div>
            @else
                <select class="form-control" id="kelurahan_boro" name="kelurahan_boro">
                    @if($kel_boro)
                        <option value="{{ $kel_boro }}" selected>{{ $kel_boro_nm }}</option>
                    @endif
                </select>
            @endif
        </div>
    </div>

    {{-- ALAMAT --}}
    <div class="row mb-3">
        <label class="col-md-3 col-form-label text-md-start ms-2">Alamat</label>
        <div class="col-md-8">
            @if($readonly)
                <div class="form-control-plaintext border rounded bg-light px-3 py-2" style="white-space: pre-line;">
                    {{ $alamat_boro }}
                </div>
            @else
                <textarea class="form-control" id="alamat_boro" name="alamat_boro">{{ $alamat_boro }}</textarea>
            @endif
        </div>
    </div>

    {{-- TANGGAL --}}
    <div class="row mb-3">
        <label class="col-md-3 col-form-label text-md-start ms-2">Pada Tgl.</label>

        <div class="col-md-3">
            @if($readonly)
                <div class="form-control-plaintext border rounded bg-light px-3 py-2">
                    {{ \Carbon\Carbon::parse($tgl_awal)->isoFormat('D MMMM Y') }}
                </div>
            @else
                <input type="date" class="form-control" name="tgl_awal" value="{{ $tgl_awal }}">
            @endif
        </div>

        <label class="col-md-2 col-form-label text-md-center">s/d</label>

        <div class="col-md-3">
            @if($readonly)
                <div class="form-control-plaintext border rounded bg-light px-3 py-2">
                    {{ \Carbon\Carbon::parse($tgl_akhir)->isoFormat('D MMMM Y') }}
                </div>
            @else
                <input type="date" class="form-control" name="tgl_akhir" value="{{ $tgl_akhir }}">
            @endif
        </div>
    </div>

</div>
