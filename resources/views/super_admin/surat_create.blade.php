@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="container mt-2">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header border-0 pt-3 pb-2" style="background: #AEA07A; border-radius: 1rem 1rem 0 0">
                    <h5 class="my-3 fw-bold text-white text-center" style="letter-spacing: .5px">{{ $title }}</h5>
                </div>

                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.surat.store') }}">
                        @csrf
                        <input type="hidden" name="jenis_surat" value="{{ $jenis }}">
                        <input type="hidden" name="id_kel" value="{{ $skpd->id }}">
                        <div class="alert alert-info rounded-4 fw-semibold">Super Admin membuat surat untuk Kelurahan/SKPD: {{ $skpd->nama }}</div>

                        <div class="row" style="min-height: 500px;">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-1 shadow-sm rounded-4" style="background: #fff; border-color: #AEA07A">
                                    <div class="card-body">
                                        <x-nosrt>
                                            <x-slot:kd_jenis_surat>{{ $kd_jenis_surat ?? '' }}</x-slot:kd_jenis_surat>
                                            <x-slot:no_urut_surat>{{ $no_urut_surat ?? '' }}</x-slot:no_urut_surat>
                                            <x-slot:instansi_kode>{{ $currentUser->skpd->instansi_kode ?? '' }}</x-slot:instansi_kode>
                                            <x-slot:tgl_surat></x-slot:tgl_surat>
                                        </x-nosrt>

                                        <x-pribadi></x-pribadi>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-40 border-1 shadow-sm rounded-4" style="background: #fff; border-color: #AEA07A">
                                    <div class="card-body">

                                        @if ($jenis === 'skbn')
                                            <div id="field-bin-binti" style="display:none;">
                                                <div class="row mb-3">
                                                    <label for="bin_binti" class="col-md-3 col-form-label text-md-start ms-2">
                                                        Bin/Binti
                                                    </label>
                                                    <div class="col-md-8">
                                                        <input type="text"
                                                            class="form-control @error('bin_binti') is-invalid @enderror"
                                                            name="bin_binti"
                                                            id="bin_binti"
                                                            value="{{ old('bin_binti') }}">

                                                        @error('bin_binti')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="field-pasangan" style="display:none;">
                                                @foreach ([
                                                    'nama_pasangan',
                                                    'nik_pasangan',
                                                    'tempat_lahir_pasangan',
                                                    'tgl_lahir_pasangan',
                                                    'agama_pasangan',
                                                    'pekerjaan_pasangan',
                                                    'alamat_pasangan'
                                                ] as $item)
                                                    <div class="row mb-3">
                                                        <label for="{{ $item }}" class="col-md-3 col-form-label text-md-start ms-2">
                                                            {{ ucwords(str_replace('_', ' ', $item)) }}
                                                        </label>

                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control @error($item) is-invalid @enderror"
                                                                name="{{ $item }}"
                                                                id="{{ $item }}"
                                                                value="{{ old($item) }}"
                                                                @if($item === 'nik_pasangan') maxlength="16" inputmode="numeric" pattern="[0-9]{16}" @endif>

                                                            @error($item)
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            @if ($jenis === 'sktm')
                                                <div class="row mb-3">
                                                    <label class="col-md-3 col-form-label text-md-start ms-2">Jenis SKTM</label>
                                                    <div class="col-md-8 d-flex align-items-center gap-3 pt-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="register_as" id="radioPeroranganAdmin" value="perorangan" {{ old('register_as', 'perorangan') === 'perorangan' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="radioPeroranganAdmin">Perorangan</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="register_as" id="radioSekolahAdmin" value="sekolah" {{ old('register_as') === 'sekolah' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="radioSekolahAdmin">Sekolah</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="field-sktm-perorangan">
                                                    @foreach ([
                                                        'nama_orang_tua' => 'Nama Orang Tua',
                                                        'pekerjaan_orang_tua' => 'Pekerjaan Orang Tua',
                                                        'alamat_orang_tua' => 'Alamat Orang Tua',
                                                        'keperluan_bantuan' => 'Keperluan Bantuan',
                                                        'kategori' => 'Kategori',
                                                        'keterangan' => 'Keterangan'
                                                    ] as $item => $label)
                                                        <div class="row mb-3">
                                                            <label for="{{ $item }}" class="col-md-3 col-form-label text-md-start ms-2">{{ $label }}</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control @error($item) is-invalid @enderror" name="{{ $item }}" id="{{ $item }}" value="{{ old($item) }}">
                                                                @error($item)
                                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div id="field-sktm-sekolah" style="display:none;">
                                                    @foreach ([
                                                        'kepada' => 'Nama Siswa',
                                                        'kepada_tempat_lhr' => 'Tempat Lahir',
                                                        'kepada_tgl_lhr' => 'Tanggal Lahir',
                                                        'kepada_gender' => 'Jenis Kelamin',
                                                        'kepada_hubungan' => 'Hubungan Keluarga',
                                                        'kepada_sekolah' => 'Sekolah',
                                                        'kepada_kelas' => 'Kelas / Semester',
                                                        'kepada_alamat_sekolah' => 'Alamat Sekolah',
                                                        'kategori' => 'Kategori',
                                                        'keterangan' => 'Keterangan'
                                                    ] as $item => $label)
                                                        <div class="row mb-3">
                                                            <label for="{{ $item }}" class="col-md-3 col-form-label text-md-start ms-2">{{ $label }}</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control @error($item) is-invalid @enderror" name="{{ $item }}" id="{{ $item }}" value="{{ old($item) }}">
                                                                @error($item)
                                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif ($jenis === 'skhsl')
                                                
                                                @foreach ([
                                                    'kepada' => 'Nama Anak / Siswa',
                                                    'kepada_tempat_lhr' => 'Tempat Lahir Anak',
                                                    'kepada_tgl_lhr' => 'Tanggal Lahir Anak',
                                                    'kepada_gender' => 'Jenis Kelamin Anak',
                                                    'kepada_hubungan' => 'Status Keluarga / Hubungan',
                                                    'kepada_sekolah' => 'Nama Sekolah',
                                                    'kepada_kelas' => 'Kelas / Semester',
                                                    'kepada_alamat_sekolah' => 'Alamat Sekolah',
                                                    'penghasilan' => 'Penghasilan Per Bulan',
                                                    'terbilang' => 'Terbilang Penghasilan',
                                                    'peruntukan' => 'Untuk Keperluan'
                                                ] as $item => $label)
                                                    <div class="row mb-3">
                                                        <label for="{{ $item }}" class="col-md-3 col-form-label text-md-start ms-2">{{ $label }}</label>
                                                        <div class="col-md-8">
                                                            <input type="{{ $item === 'kepada_tgl_lhr' ? 'date' : 'text' }}"
                                                                class="form-control @error($item) is-invalid @enderror"
                                                                name="{{ $item }}"
                                                                id="{{ $item }}"
                                                                value="{{ old($item) }}"
                                                                @if($item === 'penghasilan') inputmode="numeric" placeholder="Contoh: 3000000" @endif
                                                                @if($item === 'terbilang') placeholder="Contoh: Tiga Juta Rupiah" @endif
                                                                required>
                                                            @error($item)<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <input type="hidden" name="surat_keperluan" id="surat_keperluan" value="{{ old('surat_keperluan') }}">
                                            @elseif (in_array($jenis, ['skboro', 'boro']))
                                               

                                                <div class="row mb-3">
                                                    <label for="tgl_awal" class="col-md-3 col-form-label text-md-start ms-2">Mulai Berlaku</label>
                                                    <div class="col-md-8">
                                                        <input type="date" class="form-control @error('tgl_awal') is-invalid @enderror" name="tgl_awal" id="tgl_awal" value="{{ old('tgl_awal') }}" required>
                                                        @error('tgl_awal')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="tgl_akhir" class="col-md-3 col-form-label text-md-start ms-2">Sampai Tanggal</label>
                                                    <div class="col-md-8">
                                                        <input type="date" class="form-control @error('tgl_akhir') is-invalid @enderror" name="tgl_akhir" id="tgl_akhir" value="{{ old('tgl_akhir') }}" required>
                                                        @error('tgl_akhir')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                    </div>
                                                </div>

                                                @foreach ([
                                                    'provinsi_boro' => 'Provinsi Tujuan',
                                                    'kabko_boro' => 'Kabupaten/Kota Tujuan',
                                                    'kecamatan_boro' => 'Kecamatan Tujuan',
                                                    'kelurahan_boro' => 'Desa/Kelurahan Tujuan',
                                                    'alamat_boro' => 'Alamat Tujuan'
                                                ] as $item => $label)
                                                    <div class="row mb-3">
                                                        <label for="{{ $item }}" class="col-md-3 col-form-label text-md-start ms-2">{{ $label }}</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control @error($item) is-invalid @enderror" name="{{ $item }}" id="{{ $item }}" value="{{ old($item) }}" required>
                                                            @error($item)<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <div class="row mb-3">
                                                    <label for="peruntukan" class="col-md-3 col-form-label text-md-start ms-2">Keperluan</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control @error('peruntukan') is-invalid @enderror" name="peruntukan" id="peruntukan" value="{{ old('peruntukan') }}" placeholder="Contoh: bekerja / mencari pekerjaan / pindah sementara" required>
                                                        @error('peruntukan')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <label for="jumlah_pengikut" class="col-md-3 col-form-label text-md-start ms-2">Jumlah Pengikut</label>
                                                    <div class="col-md-8">
                                                        <input type="number" min="0" class="form-control" name="jumlah_pengikut" id="jumlah_pengikut" value="{{ old('jumlah_pengikut', 0) }}">
                                                    </div>
                                                </div>

                                                <input type="hidden" name="surat_tgl_berlaku" id="surat_tgl_berlaku" value="{{ old('surat_tgl_berlaku') }}">
                                                <input type="hidden" name="surat_tujuan" id="surat_tujuan" value="{{ old('surat_tujuan') }}">
                                                <input type="hidden" name="surat_keperluan" id="surat_keperluan" value="{{ old('surat_keperluan') }}">
                                                <input type="hidden" name="surat_jml_pengikut" id="surat_jml_pengikut" value="{{ old('surat_jml_pengikut', 0) }}">
                                            @else
                                                @isset($var)
                                                    @foreach ($var as $item)
                                                        <div class="row mb-3">
                                                            <label for="{{ $item }}" class="col-md-3 col-form-label text-md-start ms-2">
                                                                {{ ucwords(str_replace('_', ' ', $item)) }}
                                                            </label>
                                                            <div class="col-md-8">
                                                                <input type="text"
                                                                    class="form-control @error($item) is-invalid @enderror"
                                                                    name="{{ $item }}"
                                                                    id="{{ $item }}"
                                                                    value="{{ old($item) }}"
                                                                    @if($item === 'nik_pasangan') maxlength="16" inputmode="numeric" pattern="[0-9]{16}" @endif>
                                                                @error($item)
                                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endisset
                                            @endif
                                        @endif

                                        @if (!in_array($jenis, ['sktm', 'skhsl', 'skboro', 'boro']))
                                            <x-kepada><x-slot:kepada></x-slot:kepada></x-kepada>
                                        @endif
                                        @if (!in_array($jenis, ['skhsl', 'skboro', 'boro']))
                                            <x-peruntukan><x-slot:peruntukan></x-slot:peruntukan></x-peruntukan>
                                        @endif

                                        <div id="field-keperluan-lainnya" style="display:none;">
                                            <div class="row mb-3">
                                                <label for="keperluan_lainnya" class="col-md-3 col-form-label text-md-start ms-2">
                                                    Keperluan Lainnya
                                                </label>
                                                <div class="col-md-8">
                                                    <input type="text"
                                                        class="form-control @error('keperluan_lainnya') is-invalid @enderror"
                                                        name="keperluan_lainnya"
                                                        id="keperluan_lainnya"
                                                        value="{{ old('keperluan_lainnya') }}"
                                                        placeholder="Contoh: Beasiswa, Administrasi Bank, Visa">

                                                    @error('keperluan_lainnya')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <x-pengantar></x-pengantar>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    <a href="{{ route('admin.surat.index') }}" class="btn btn-danger py-2 px-4 me-2" style="border-radius: 8px">
                                        <i class="ri-close-line me-1"></i>
                                        <span>Batal</span>
                                    </a>

                                    <button type="submit" class="btn text-white py-2 px-4" style="background: #7896B2; border-radius: 8px;">
                                        <i class="ri-save-3-fill me-1"></i>
                                        <span>Simpan</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript" src="{{ asset('assets/js/personal.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const peruntukan = document.getElementById('peruntukan');
    const fieldPasangan = document.getElementById('field-pasangan');
    const fieldBinBinti = document.getElementById('field-bin-binti');
    const fieldKeperluanLainnya = document.getElementById('field-keperluan-lainnya');
    const keperluanLainnya = document.getElementById('keperluan_lainnya');
    const binBinti = document.getElementById('bin_binti');
    const nikPasangan = document.getElementById('nik_pasangan');
    const sktmRadios = document.querySelectorAll('input[name="register_as"]');
    const fieldSktmPerorangan = document.getElementById('field-sktm-perorangan');
    const fieldSktmSekolah = document.getElementById('field-sktm-sekolah');

    function toggleSktmAdminFields() {
        const selected = document.querySelector('input[name="register_as"]:checked')?.value || 'perorangan';
        const isSekolah = selected === 'sekolah';

        if (fieldSktmPerorangan) {
            fieldSktmPerorangan.style.display = isSekolah ? 'none' : 'block';
            fieldSktmPerorangan.querySelectorAll('input, select, textarea').forEach(function (el) {
                el.disabled = isSekolah;
            });
        }

        if (fieldSktmSekolah) {
            fieldSktmSekolah.style.display = isSekolah ? 'block' : 'none';
            fieldSktmSekolah.querySelectorAll('input, select, textarea').forEach(function (el) {
                el.disabled = !isSekolah;
            });
        }
    }

    function toggleFieldPasangan() {
        if (!peruntukan) return;

        const peruntukanValue = (peruntukan.value || '').toLowerCase().trim();
        const isMenikah = peruntukanValue === 'menikah';
        const isLainnya = peruntukanValue === 'lainnya';

        if (fieldBinBinti) {
            fieldBinBinti.style.display = isMenikah ? 'block' : 'none';
        }

        if (fieldPasangan) {
            fieldPasangan.style.display = isMenikah ? 'block' : 'none';

            if (!isMenikah) {
                fieldPasangan.querySelectorAll('input, select, textarea').forEach(el => {
                    el.value = '';
                });
            }
        }

        if (!isMenikah && binBinti) {
            binBinti.value = '';
        }

        if (nikPasangan) {
            if (isMenikah) {
                nikPasangan.setAttribute('required', 'required');
            } else {
                nikPasangan.removeAttribute('required');
                nikPasangan.value = '';
            }
        }

        if (fieldKeperluanLainnya) {
            fieldKeperluanLainnya.style.display = isLainnya ? 'block' : 'none';
        }

        if (keperluanLainnya) {
            if (isLainnya) {
                keperluanLainnya.setAttribute('required', 'required');
            } else {
                keperluanLainnya.removeAttribute('required');
                keperluanLainnya.value = '';
            }
        }
    }

    if (nikPasangan) {
        nikPasangan.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
        });
    }

    if (sktmRadios.length) {
        sktmRadios.forEach(function (el) {
            el.addEventListener('change', toggleSktmAdminFields);
        });
        toggleSktmAdminFields();
    }

    if (peruntukan) {
        peruntukan.addEventListener('change', toggleFieldPasangan);
        toggleFieldPasangan();
    }
});
</script>

<script>
(function () {
    function byId(id) { return document.getElementById(id); }
    function val(id) { return (byId(id)?.value || '').trim(); }
    function updateBoroHiddenFields() {
        if (!byId('surat_tujuan')) return;
        const tglAwal = val('tgl_awal');
        const tglAkhir = val('tgl_akhir');
        const prov = val('provinsi_boro');
        const kabko = val('kabko_boro');
        const kec = val('kecamatan_boro');
        const kel = val('kelurahan_boro');
        const alamat = val('alamat_boro');
        const peruntukan = val('peruntukan');
        const jumlah = val('jumlah_pengikut') || '0';

        if (byId('surat_tgl_berlaku')) byId('surat_tgl_berlaku').value = [tglAwal, tglAkhir].filter(Boolean).join(' s/d ');
        byId('surat_tujuan').value = [
            kel ? 'Desa / Kelurahan : ' + kel : '',
            kec ? 'Kecamatan : ' + kec : '',
            kabko ? 'Kabupaten/Kota : ' + kabko : '',
            prov ? 'Provinsi : ' + prov : '',
            alamat ? 'Alamat : ' + alamat : ''
        ].filter(Boolean).join(' ');
        if (byId('surat_keperluan')) byId('surat_keperluan').value = peruntukan;
        if (byId('surat_jml_pengikut')) byId('surat_jml_pengikut').value = jumlah;
    }

    document.addEventListener('input', function (e) {
        if (['tgl_awal','tgl_akhir','provinsi_boro','kabko_boro','kecamatan_boro','kelurahan_boro','alamat_boro','peruntukan','jumlah_pengikut'].includes(e.target.id)) {
            updateBoroHiddenFields();
        }
    });
    document.addEventListener('change', updateBoroHiddenFields);
    document.addEventListener('DOMContentLoaded', updateBoroHiddenFields);
})();
</script>

@endpush
@endsection