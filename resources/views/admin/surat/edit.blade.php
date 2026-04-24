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
                                        @if ($jenis !== 'sktm')
                                            <x-kepada>
                                                <x-slot:kepada>{{ $kepadaValue }}</x-slot:kepada>
                                            </x-kepada>
                                        @endif

                                        <x-peruntukan>
                                            <x-slot:peruntukan>{{ $peruntukanValue }}</x-slot:peruntukan>
                                        </x-peruntukan>

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
@endpush
@endsection
