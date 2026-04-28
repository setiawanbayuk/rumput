@extends('layouts.main')

@section('title', $title)

@section('content')
@php
    $variableData = [];
    if (isset($surat->variable)) {
        if (is_array($surat->variable)) {
            $variableData = $surat->variable;
        } elseif (is_string($surat->variable)) {
            $decoded = json_decode($surat->variable, true);
            $variableData = is_array($decoded) ? $decoded : [];
        }
    }

    $residentData = $residentData ?? [];
    $resident = $resident ?? null;
    $jenis = $jenis ?? ($surat->jenis_surat ?? '');
    $kdJenisSurat = old('kd_jenis_surat', $surat->kd_jenis_surat ?? ($kd_jenis_surat ?? ''));
    $noUrutSurat = old('no_urut_surat', $surat->no_urut_surat ?? ($no_urut_surat ?? ''));
    $tglSuratValue = old('tgl_surat', !empty($surat->tgl_surat) ? \Carbon\Carbon::parse($surat->tgl_surat)->format('Y-m-d') : '');
    $peruntukanValue = old('peruntukan', $surat->peruntukan ?? '');
    $kepadaValue = old('kepada', $surat->kepada ?? '');
    $keperluanLainnyaValue = old('keperluan_lainnya', data_get($variableData, 'keperluan_lainnya', ''));

    $skhslValues = [
        'kepada' => old('kepada', $surat->kepada ?? data_get($variableData, 'kepada', '')),
        'kepada_tempat_lhr' => old('kepada_tempat_lhr', data_get($variableData, 'kepada_tempat_lhr', '')),
        'kepada_tgl_lhr' => old('kepada_tgl_lhr', data_get($variableData, 'kepada_tgl_lhr', '')),
        'kepada_gender' => old('kepada_gender', data_get($variableData, 'kepada_gender_nm', data_get($variableData, 'kepada_gender', ''))),
        'kepada_hubungan' => old('kepada_hubungan', data_get($variableData, 'kepada_hubungan', '')),
        'kepada_sekolah' => old('kepada_sekolah', data_get($variableData, 'kepada_sekolah', '')),
        'kepada_kelas' => old('kepada_kelas', data_get($variableData, 'kepada_kelas', '')),
        'kepada_alamat_sekolah' => old('kepada_alamat_sekolah', data_get($variableData, 'kepada_alamat_sekolah', '')),
        'penghasilan' => old('penghasilan', data_get($variableData, 'penghasilan', '')),
        'terbilang' => old('terbilang', data_get($variableData, 'terbilang', '')),
        'peruntukan' => old('peruntukan', $surat->peruntukan ?? data_get($variableData, 'keperluan', '')),
    ];

    $suratDataJs = [
        'jenis_surat'    => $surat->jenis_surat ?? '',
        'kd_jenis_surat' => $kdJenisSurat ?? '',
        'no_urut_surat'  => $noUrutSurat ?? '',
        'nik'            => old('nik', $surat->nik ?? ''),
        'kk'             => old('kk', $resident->kk ?? ''),
        'tgl_surat'      => $tglSuratValue ?? '',
        'kepada'         => $kepadaValue ?? '',
        'peruntukan'     => $peruntukanValue ?? '',
        'pengantar'      => $surat->pengantar ?? '',
        'keperluan_lainnya' => $keperluanLainnyaValue ?? '',
    ];
@endphp

<div class="container mt-2">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header border-0 pt-3 pb-2" style="background: #AEA07A; border-radius: 1rem 1rem 0 0">
                    <h5 class="my-3 fw-bold text-white text-center" style="letter-spacing: .5px">{{ $title }}</h5>
                </div>

                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.surat.update', $surat->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="jenis_surat" value="{{ $jenis }}">

                        <div class="row" style="min-height: 500px;">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 border-1 shadow-sm rounded-4" style="background: #fff; border-color: #AEA07A">
                                    <div class="card-body">
                                        <x-nosrt>
                                            <x-slot:kd_jenis_surat>{{ $kdJenisSurat }}</x-slot:kd_jenis_surat>
                                            <x-slot:no_urut_surat>{{ $noUrutSurat }}</x-slot:no_urut_surat>
                                            <x-slot:instansi_kode>{{ $currentUser->skpd->instansi_kode ?? '' }}</x-slot:instansi_kode>
                                            <x-slot:tgl_surat>{{ $tglSuratValue }}</x-slot:tgl_surat>
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
                                                    <label for="bin_binti" class="col-md-3 col-form-label text-md-start ms-2">Bin/Binti</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control @error('bin_binti') is-invalid @enderror" name="bin_binti" id="bin_binti" value="{{ old('bin_binti', data_get($variableData, 'bin_binti', '')) }}">
                                                        @error('bin_binti')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="field-pasangan" style="display:none;">
                                                @foreach (['nama_pasangan','nik_pasangan','tempat_lahir_pasangan','tgl_lahir_pasangan','agama_pasangan','pekerjaan_pasangan','alamat_pasangan'] as $item)
                                                    <div class="row mb-3">
                                                        <label for="{{ $item }}" class="col-md-3 col-form-label text-md-start ms-2">{{ ucwords(str_replace('_', ' ', $item)) }}</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control @error($item) is-invalid @enderror" name="{{ $item }}" id="{{ $item }}" value="{{ old($item, data_get($variableData, $item, '')) }}" @if($item === 'nik_pasangan') maxlength="16" inputmode="numeric" pattern="[0-9]{16}" @endif>
                                                            @error($item)<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif ($jenis === 'sktm')
                                            @php
                                                $registerAsValue = old('register_as', data_get($variableData, 'register_as', 'perorangan'));
                                            @endphp
                                            <div class="row mb-3">
                                                <label class="col-md-3 col-form-label text-md-start ms-2">Jenis SKTM</label>
                                                <div class="col-md-8 d-flex align-items-center gap-3 pt-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="register_as" id="radioPeroranganAdmin" value="perorangan" {{ $registerAsValue === 'perorangan' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="radioPeroranganAdmin">Perorangan</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="register_as" id="radioSekolahAdmin" value="sekolah" {{ $registerAsValue === 'sekolah' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="radioSekolahAdmin">Sekolah</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="field-sktm-perorangan">
                                                @foreach (['kategori','keterangan'] as $item)
                                                    <div class="row mb-3">
                                                        <label for="{{ $item }}" class="col-md-3 col-form-label text-md-start ms-2">{{ ucwords(str_replace('_', ' ', $item)) }}</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control @error($item) is-invalid @enderror" name="{{ $item }}" id="{{ $item }}" value="{{ old($item, data_get($variableData, $item, '')) }}">
                                                            @error($item)<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div id="field-sktm-sekolah" style="display:none;">
                                                @php $fieldMap = ['kepada' => 'Nama Siswa','kepada_tempat_lhr' => 'Tempat Lahir','kepada_tgl_lhr' => 'Tanggal Lahir','kepada_gender' => 'Jenis Kelamin','kepada_hubungan' => 'Hubungan Keluarga','kepada_sekolah' => 'Sekolah','kepada_kelas' => 'Kelas / Semester','kepada_alamat_sekolah' => 'Alamat Sekolah']; @endphp
                                                @foreach (['kategori','keterangan','kepada','kepada_tempat_lhr','kepada_tgl_lhr','kepada_gender','kepada_hubungan','kepada_sekolah','kepada_kelas','kepada_alamat_sekolah'] as $item)
                                                    <div class="row mb-3">
                                                        <label for="{{ $item }}" class="col-md-3 col-form-label text-md-start ms-2">{{ $fieldMap[$item] ?? ucwords(str_replace('_', ' ', $item)) }}</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control @error($item) is-invalid @enderror" name="{{ $item }}" id="{{ $item }}" value="{{ old($item, $item === 'kepada' ? ($surat->kepada ?? data_get($variableData, 'kepada', '')) : data_get($variableData, $item, '')) }}">
                                                            @error($item)<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
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
                                                            value="{{ $skhslValues[$item] ?? '' }}"
                                                            @if($item === 'penghasilan') inputmode="numeric" placeholder="Contoh: 3000000" @endif
                                                            @if($item === 'terbilang') placeholder="Contoh: Tiga Juta Rupiah" @endif
                                                            required>
                                                        @error($item)<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                    </div>
                                                </div>
                                            @endforeach

                                            <input type="hidden" name="surat_keperluan" id="surat_keperluan" value="{{ old('surat_keperluan', data_get($variableData, 'surat_keperluan', $skhslValues['peruntukan'] ?? '')) }}">
                                        @elseif (in_array($jenis, ['skboro', 'boro']))
                                            @php
                                                $tglAwalBoro = old('tgl_awal', data_get($variableData, 'tgl_awal', data_get($surat, 'tgl_awal', '')));
                                                $tglAkhirBoro = old('tgl_akhir', data_get($variableData, 'tgl_akhir', data_get($surat, 'tgl_akhir', '')));
                                                $provinsiBoro = old('provinsi_boro', data_get($variableData, 'provinsi_boro', data_get($surat, 'prov_boro_nm', data_get($surat, 'prov_boro', ''))));
                                                $kabkoBoro = old('kabko_boro', data_get($variableData, 'kabko_boro', data_get($surat, 'kabko_boro_nm', data_get($surat, 'kabko_boro', ''))));
                                                $kecamatanBoro = old('kecamatan_boro', data_get($variableData, 'kecamatan_boro', data_get($surat, 'kec_boro_nm', data_get($surat, 'kec_boro', ''))));
                                                $kelurahanBoro = old('kelurahan_boro', data_get($variableData, 'kelurahan_boro', data_get($surat, 'kel_boro_nm', data_get($surat, 'kel_boro', ''))));
                                                $alamatBoro = old('alamat_boro', data_get($variableData, 'alamat_boro', data_get($surat, 'alamat_boro', '')));
                                                $peruntukanBoro = old('peruntukan', data_get($variableData, 'peruntukan', data_get($surat, 'peruntukan', '')));
                                                $jumlahPengikutBoro = old('jumlah_pengikut', data_get($variableData, 'jumlah_pengikut', data_get($variableData, 'surat_jml_pengikut', data_get($surat, 'pengikut', 0))));
                                            @endphp

                                           

                                            <div class="row mb-3">
                                                <label for="tgl_awal" class="col-md-3 col-form-label text-md-start ms-2">Mulai Berlaku</label>
                                                <div class="col-md-8">
                                                    <input type="date" class="form-control @error('tgl_awal') is-invalid @enderror" name="tgl_awal" id="tgl_awal" value="{{ $tglAwalBoro }}" required>
                                                    @error('tgl_awal')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="tgl_akhir" class="col-md-3 col-form-label text-md-start ms-2">Sampai Tanggal</label>
                                                <div class="col-md-8">
                                                    <input type="date" class="form-control @error('tgl_akhir') is-invalid @enderror" name="tgl_akhir" id="tgl_akhir" value="{{ $tglAkhirBoro }}" required>
                                                    @error('tgl_akhir')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                </div>
                                            </div>

                                            @foreach ([
                                                'provinsi_boro' => ['label' => 'Provinsi Tujuan', 'value' => $provinsiBoro],
                                                'kabko_boro' => ['label' => 'Kabupaten/Kota Tujuan', 'value' => $kabkoBoro],
                                                'kecamatan_boro' => ['label' => 'Kecamatan Tujuan', 'value' => $kecamatanBoro],
                                                'kelurahan_boro' => ['label' => 'Desa/Kelurahan Tujuan', 'value' => $kelurahanBoro],
                                                'alamat_boro' => ['label' => 'Alamat Tujuan', 'value' => $alamatBoro]
                                            ] as $item => $field)
                                                <div class="row mb-3">
                                                    <label for="{{ $item }}" class="col-md-3 col-form-label text-md-start ms-2">{{ $field['label'] }}</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control @error($item) is-invalid @enderror" name="{{ $item }}" id="{{ $item }}" value="{{ $field['value'] }}" required>
                                                        @error($item)<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="row mb-3">
                                                <label for="peruntukan" class="col-md-3 col-form-label text-md-start ms-2">Keperluan</label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control @error('peruntukan') is-invalid @enderror" name="peruntukan" id="peruntukan" value="{{ $peruntukanBoro }}" required>
                                                    @error('peruntukan')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label for="jumlah_pengikut" class="col-md-3 col-form-label text-md-start ms-2">Jumlah Pengikut</label>
                                                <div class="col-md-8">
                                                    <input type="number" min="0" class="form-control" name="jumlah_pengikut" id="jumlah_pengikut" value="{{ $jumlahPengikutBoro }}">
                                                </div>
                                            </div>

                                            <input type="hidden" name="surat_tgl_berlaku" id="surat_tgl_berlaku" value="{{ old('surat_tgl_berlaku', data_get($variableData, 'surat_tgl_berlaku', '')) }}">
                                            <input type="hidden" name="surat_tujuan" id="surat_tujuan" value="{{ old('surat_tujuan', data_get($variableData, 'surat_tujuan', '')) }}">
                                            <input type="hidden" name="surat_keperluan" id="surat_keperluan" value="{{ old('surat_keperluan', data_get($variableData, 'surat_keperluan', '')) }}">
                                            <input type="hidden" name="surat_jml_pengikut" id="surat_jml_pengikut" value="{{ old('surat_jml_pengikut', data_get($variableData, 'surat_jml_pengikut', $jumlahPengikutBoro)) }}">
                                        @else
                                            @isset($var)
                                                @foreach ($var as $item)
                                                    <div class="row mb-3">
                                                        <label for="{{ $item }}" class="col-md-3 col-form-label text-md-start ms-2">{{ ucwords(str_replace('_', ' ', $item)) }}</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control @error($item) is-invalid @enderror" name="{{ $item }}" id="{{ $item }}" value="{{ old($item, data_get($variableData, $item, '')) }}" @if($item === 'nik_pasangan') maxlength="16" inputmode="numeric" pattern="[0-9]{16}" @endif>
                                                            @error($item)<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endisset
                                        @endif
                                        @if (!in_array($jenis, ['sktm', 'skhsl', 'skboro', 'boro']))
                                            <x-kepada>
                                                <x-slot:kepada>{{ $kepadaValue }}</x-slot:kepada>
                                            </x-kepada>
                                        @endif

                                        @if (!in_array($jenis, ['skhsl', 'skboro', 'boro']))
                                            <x-peruntukan>
                                                <x-slot:peruntukan>{{ $peruntukanValue }}</x-slot:peruntukan>
                                            </x-peruntukan>
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
                                                        value="{{ $keperluanLainnyaValue }}"
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

                                        @if (!empty($surat->pengantar))
                                            <div class="row mb-3">
                                                <label class="col-md-3 col-form-label text-md-start ms-2">File Lama</label>
                                                <div class="col-md-8 d-flex align-items-center">
                                                    <a href="{{ asset($surat->pengantar) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Pengantar Lama</a>
                                                </div>
                                            </div>
                                        @endif
                                        @php $showProofField = !empty(data_get($variableData, 'manual_signature')) || !empty(data_get($variableData, 'bukti_ttd_basah')) || data_get($variableData, 'signature_mode') === 'manual'; @endphp
                                        @if ($showProofField)
                                            <div class="row mb-3">
                                                <label class="col-md-3 col-form-label text-md-start ms-2">Bukti TTD Basah</label>
                                                <div class="col-md-8">
                                                    <input type="file" name="bukti_ttd_basah" class="form-control @error('bukti_ttd_basah') is-invalid @enderror">
                                                    @error('bukti_ttd_basah')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                                                </div>
                                            </div>
                                            @if (!empty(data_get($variableData, 'bukti_ttd_basah')))
                                                <div class="row mb-3">
                                                    <label class="col-md-3 col-form-label text-md-start ms-2">Bukti Lama</label>
                                                    <div class="col-md-8 d-flex align-items-center">
                                                        <a href="{{ asset(data_get($variableData, 'bukti_ttd_basah')) }}" target="_blank" class="btn btn-sm btn-outline-success">Lihat Bukti TTD Basah</a>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    <a href="{{ route('admin.surat.index') }}" class="btn btn-danger py-2 px-4 me-2" style="border-radius: 8px">
                                        <i class="ri-close-line me-1"></i>
                                        <span>Batal</span>
                                    </a>

                                    <button type="submit" class="btn text-white py-2 px-4" style="background: #7896B2; border-radius: 8px;">
                                        <i class="ri-save-3-fill me-1"></i>
                                        <span>Update</span>
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
    const residentData = @json($residentData ?? []);
    const suratData = @json($suratDataJs);

    function appendAndSelect(selector, value, text) {
        const $el = window.jQuery ? $(selector) : null;
        if (!$el || !$el.length || value === null || value === undefined || value === '') return false;

        const label = (text !== null && text !== undefined && text !== '') ? text : value;
        const exists = $el.find('option').filter(function () {
            return String($(this).val()) === String(value);
        }).length > 0;

        if (!exists) {
            const option = new Option(label, value, true, true);
            $el.append(option);
        }

        $el.val(String(value)).trigger('change');
        return true;
    }

    function setInputValue(id, value) {
        const el = document.getElementById(id) || document.querySelector(`[name="${id}"]`);
        if (!el || value === null || value === undefined || value === '') return;
        el.value = value;
    }

    function syncHidden(id, value) {
        const el = document.getElementById(id);
        if (el && value !== null && value !== undefined) {
            el.value = value;
        }
    }

    setInputValue('kd_jenis_surat', suratData.kd_jenis_surat);
    setInputValue('no_urut_surat', suratData.no_urut_surat);
    setInputValue('tgl_surat', suratData.tgl_surat);
    setInputValue('nik', suratData.nik);
    setInputValue('kk', suratData.kk);
    setInputValue('name', @json(old('name', $residentData['name'] ?? '')));
    setInputValue('tempat_lhr', @json(old('tempat_lhr', $residentData['tempat_lhr'] ?? '')));
    setInputValue('tgl_lhr', @json(old('tgl_lhr', $residentData['tgl_lhr'] ?? '')));
    setInputValue('alamat', @json(old('alamat', $residentData['alamat'] ?? '')));
    setInputValue('kepada', suratData.kepada);
    setInputValue('peruntukan', suratData.peruntukan);
    setInputValue('keperluan_lainnya', suratData.keperluan_lainnya);

    const selects = {
        gender: {
            value: @json(old('gender', $residentData['gender'] ?? '')),
            text: @json(old('gender_nm', $residentData['gender_nm'] ?? '')),
            hidden: 'gender_nm'
        },
        status_kwn: {
            value: @json(old('status_kwn', $residentData['status_kwn'] ?? '')),
            text: @json(old('status_kwn_nm', $residentData['status_kwn_nm'] ?? '')),
            hidden: 'status_kwn_nm'
        },
        kewarganegaraan: {
            value: @json(old('kewarganegaraan', $residentData['kewarganegaraan'] ?? '')),
            text: @json(old('kewarganegaraan_nm', $residentData['kewarganegaraan_nm'] ?? '')),
            hidden: 'kewarganegaraan_nm'
        },
        agama: {
            value: @json(old('agama', $residentData['agama'] ?? '')),
            text: @json(old('agama_nm', $residentData['agama_nm'] ?? '')),
            hidden: 'agama_nm'
        },
        pendidikan: {
            value: @json(old('pendidikan', $residentData['pendidikan'] ?? '')),
            text: @json(old('pendidikan_nm', $residentData['pendidikan_nm'] ?? '')),
            hidden: 'pendidikan_nm'
        },
        pekerjaan: {
            value: @json(old('pekerjaan', $residentData['pekerjaan'] ?? '')),
            text: @json(old('pekerjaan_nm', $residentData['pekerjaan_nm'] ?? '')),
            hidden: 'pekerjaan_nm'
        }
    };

    Object.keys(selects).forEach(function (key) {
        appendAndSelect('#' + key, selects[key].value, selects[key].text);
        syncHidden(selects[key].hidden, selects[key].text);
    });

    const regionData = {
        provinsi: {
            value: @json(old('provinsi', $residentData['provinsi'] ?? '')),
            text: @json(old('provinsi_nm', $residentData['provinsi_nm'] ?? '')),
            hidden: 'provinsi_nm'
        },
        kabko: {
            value: @json(old('kabko', $residentData['kabko'] ?? '')),
            text: @json(old('kabko_nm', $residentData['kabko_nm'] ?? '')),
            hidden: 'kabko_nm'
        },
        kecamatan: {
            value: @json(old('kecamatan', $residentData['kecamatan'] ?? '')),
            text: @json(old('kecamatan_nm', $residentData['kecamatan_nm'] ?? '')),
            hidden: 'kecamatan_nm'
        },
        kelurahan: {
            value: @json(old('kelurahan', $residentData['kelurahan'] ?? '')),
            text: @json(old('kelurahan_nm', $residentData['kelurahan_nm'] ?? '')),
            hidden: 'kelurahan_nm'
        },
        rw: {
            value: @json(old('rw', $residentData['rw'] ?? '')),
            text: @json(old('rw_nm', $residentData['rw_nm'] ?? '')),
            hidden: 'rw_nm'
        },
        rt: {
            value: @json(old('rt', $residentData['rt'] ?? '')),
            text: @json(old('rt_nm', $residentData['rt_nm'] ?? '')),
            hidden: 'rt_nm'
        }
    };

    function waitSetRegion(key, nextCallback, tries = 0) {
        const item = regionData[key];
        if (!item || !item.value) {
            if (typeof nextCallback === 'function') nextCallback();
            return;
        }

        const $el = $('#' + key);
        if ($el.length) {
            appendAndSelect('#' + key, item.value, item.text);
            syncHidden(item.hidden, item.text);
            setTimeout(function () {
                if (typeof nextCallback === 'function') nextCallback();
            }, 500);
            return;
        }

        if (tries > 20) {
            if (typeof nextCallback === 'function') nextCallback();
            return;
        }

        setTimeout(function () {
            waitSetRegion(key, nextCallback, tries + 1);
        }, 300);
    }

    setTimeout(function () {
        waitSetRegion('provinsi', function () {
            waitSetRegion('kabko', function () {
                waitSetRegion('kecamatan', function () {
                    waitSetRegion('kelurahan', function () {
                        waitSetRegion('rw', function () {
                            waitSetRegion('rt');
                        });
                    });
                });
            });
        });
    }, 800);

    const peruntukan = document.getElementById('peruntukan') || document.querySelector('[name="peruntukan"]');
    const fieldPasangan = document.getElementById('field-pasangan');
    const fieldBinBinti = document.getElementById('field-bin-binti');
    const fieldKeperluanLainnya = document.getElementById('field-keperluan-lainnya');
    const keperluanLainnya = document.getElementById('keperluan_lainnya');
    const binBinti = document.getElementById('bin_binti');
    const nikPasangan = document.getElementById('nik_pasangan');

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
                fieldPasangan.querySelectorAll('input, select, textarea').forEach(function (el) {
                    if (!el.dataset.keepOnLoad) {
                        el.value = '';
                    }
                });
            }
        }

        if (!isMenikah && binBinti && !binBinti.dataset.keepOnLoad) {
            binBinti.value = '';
        }

        if (nikPasangan) {
            if (isMenikah) {
                nikPasangan.setAttribute('required', 'required');
            } else {
                nikPasangan.removeAttribute('required');
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
                if (!keperluanLainnya.dataset.keepOnLoad) {
                    keperluanLainnya.value = '';
                }
            }
        }
    }

    [binBinti, nikPasangan, keperluanLainnya].forEach(function (el) {
        if (el && el.value) {
            el.dataset.keepOnLoad = '1';
        }
    });

    if (fieldPasangan) {
        fieldPasangan.querySelectorAll('input, select, textarea').forEach(function (el) {
            if (el.value) {
                el.dataset.keepOnLoad = '1';
            }
        });
    }

    if (nikPasangan) {
        nikPasangan.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
        });
    }

    function toggleSktmAdminFields() {
        const selected = document.querySelector('input[name="register_as"]:checked')?.value || 'perorangan';
        const sekolah = document.getElementById('field-sktm-sekolah');
        const perorangan = document.getElementById('field-sktm-perorangan');
        if (!sekolah || !perorangan) return;
        sekolah.style.display = selected === 'sekolah' ? 'block' : 'none';
        perorangan.style.display = selected === 'sekolah' ? 'none' : 'block';
    }

    document.querySelectorAll('input[name="register_as"]').forEach(function(el){
        el.addEventListener('change', toggleSktmAdminFields);
    });

    if (peruntukan) {
        peruntukan.addEventListener('change', function () {
            if (fieldPasangan) {
                fieldPasangan.querySelectorAll('input, select, textarea').forEach(function (el) {
                    delete el.dataset.keepOnLoad;
                });
            }
            if (binBinti) delete binBinti.dataset.keepOnLoad;
            if (keperluanLainnya) delete keperluanLainnya.dataset.keepOnLoad;
            toggleFieldPasangan();
        });
        toggleFieldPasangan();
    }
    toggleSktmAdminFields();
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
