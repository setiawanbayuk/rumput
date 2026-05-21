@extends('layouts.main')

@section('title', 'Edit Data Warga')

@section('content')
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .mw-page {
                --mw-ink: #1f2937;
                --mw-muted: #6b7280;
                --mw-line: #e5e7eb;
                --mw-soft: #f8fafc;
                --mw-gold: #b7791f;
            }
            .mw-title { font-weight: 800; color: var(--mw-ink); letter-spacing: -.02em; }
            .mw-subtitle { color: var(--mw-muted); }
            .mw-heading {
                border: 1px solid var(--mw-line);
                border-radius: 16px;
                background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
                padding: 18px 20px;
                box-shadow: 0 10px 26px rgba(15, 23, 42, .045);
            }
            .mw-heading-icon {
                width: 46px;
                height: 46px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 14px;
                color: #8a5a10;
                background: linear-gradient(135deg, #fff7e6, #f8fafc);
                border: 1px solid #f1dfb8;
                font-size: 1.35rem;
            }
            .mw-card {
                border: 1px solid var(--mw-line);
                border-radius: 16px;
                box-shadow: 0 12px 30px rgba(15, 23, 42, .05);
                overflow: hidden;
            }
            .mw-card::before {
                content: '';
                display: block;
                height: 3px;
                background: linear-gradient(90deg, #b7791f, #e9d8a6, #2563eb);
            }
            .section-title {
                font-size: .82rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: .055em;
                color: #475569;
                padding-left: 12px;
                border-left: 4px solid var(--mw-gold);
            }
            .form-label { font-weight: 650; color: #334155; font-size: .9rem; }
            .form-control,
            .form-select {
                border-radius: 10px;
                border-color: #dbe1ea;
            }
            .form-control:focus,
            .form-select:focus {
                border-color: #d6b56d;
                box-shadow: 0 0 0 .18rem rgba(183, 121, 31, .12);
            }
            .select2-container { width: 100% !important; }
            .select2-container--default .select2-selection--single {
                min-height: 38px;
                border: 1px solid #dbe1ea;
                border-radius: 10px;
                padding-top: 4px;
            }
            .select2-container--default.select2-container--focus .select2-selection--single {
                border-color: #d6b56d;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow { top: 6px; }
            .text-uppercase-input { text-transform: uppercase; }
            .btn { border-radius: 10px; }
            .btn-primary {
                background: linear-gradient(135deg, #1d4ed8, #2563eb);
                border: none;
                box-shadow: 0 8px 18px rgba(37, 99, 235, .18);
            }
            .btn-light { border: 1px solid #e5e7eb; background: #fff; }
        </style>
    @endpush

    <div class="container-fluid mw-page">
        <div class="mw-heading d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="mw-heading-icon"><i class="ri-edit-box-line"></i></div>
                <div>
                    <h3 class="mw-title mb-1">{{ $title ?? 'Edit Data Warga' }}</h3>
                    <div class="mw-subtitle small">Perubahan Akan Disimpan Jika Merubah Alamat Maka Warga Akan Otomatis Masuk Dalam Wilayah Kelurahan Terbaru.</div>
                </div>
            </div>
            <a href="{{ route('admin.manajemen-warga.index') }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Data belum bisa disimpan.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.manajemen-warga.update', $user->id) }}" id="formWarga">
            @csrf
            @method('PUT')

            <div class="card mw-card mb-3">
                <div class="card-body">
                    <div class="section-title mb-3">Akun Warga</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" inputmode="numeric" pattern="[0-9]{16}" minlength="16" maxlength="16" name="nik" class="form-control only-number @error('nik') is-invalid @enderror" value="{{ old('nik', $user->nik) }}" required>
                            <div class="form-text">Wajib 16 angka.</div>
                            @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. KK <span class="text-danger">*</span></label>
                            <input type="text" inputmode="numeric" pattern="[0-9]{16}" minlength="16" maxlength="16" name="kk" class="form-control only-number @error('kk') is-invalid @enderror" value="{{ old('kk', $resident->kk ?? ($penduduk['kk'] ?? '')) }}" required>
                            <div class="form-text">Wajib 16 angka.</div>
                            @error('kk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control text-uppercase-input force-uppercase @error('name') is-invalid @enderror" value="{{ old('name', $penduduk['name'] ?? $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. HP</label>
                            <input type="text" inputmode="numeric" name="phone" class="form-control only-phone @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak diganti">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mw-card mb-3">
                <div class="card-body">
                    <div class="section-title mb-3">Data Pribadi Residents</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" class="form-select">
                                <option value="">- Pilih -</option>
                                @foreach ($genders as $item)
                                    <option value="{{ $item->id }}" @selected(old('gender', $penduduk['gender'] ?? '') == $item->id)>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status Perkawinan</label>
                            <select name="status_kwn" class="form-select">
                                <option value="">- Pilih -</option>
                                @foreach ($statusKwns as $item)
                                    <option value="{{ $item->id }}" @selected(old('status_kwn', $penduduk['status_kwn'] ?? '') == $item->id)>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kewarganegaraan</label>
                            <select name="kewarganegaraan" class="form-select">
                                <option value="">- Pilih -</option>
                                @foreach ($kewarganegaraans as $item)
                                    <option value="{{ $item->id }}" @selected(old('kewarganegaraan', $penduduk['kewarganegaraan'] ?? '') == $item->id)>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lhr" class="form-control text-uppercase-input force-uppercase" value="{{ old('tempat_lhr', $penduduk['tempat_lhr'] ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tgl_lhr" class="form-control" value="{{ old('tgl_lhr', $penduduk['tgl_lhr'] ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Agama</label>
                            <select name="agama" class="form-select">
                                <option value="">- Pilih -</option>
                                @foreach ($agamas as $item)
                                    <option value="{{ $item->id }}" @selected(old('agama', $penduduk['agama'] ?? '') == $item->id)>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pendidikan</label>
                            <select name="pendidikan" class="form-select">
                                <option value="">- Pilih -</option>
                                @foreach ($pendidikans as $item)
                                    <option value="{{ $item->id }}" @selected(old('pendidikan', $penduduk['pendidikan'] ?? '') == $item->id)>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pekerjaan</label>
                            <select name="pekerjaan" class="form-select">
                                <option value="">- Pilih -</option>
                                @foreach ($pekerjaans as $item)
                                    <option value="{{ $item->id }}" @selected(old('pekerjaan', $penduduk['pekerjaan'] ?? '') == $item->id)>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mw-card mb-3">
                <div class="card-body">
                    <div class="section-title mb-3">Alamat Wilayah</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Provinsi</label>
                            <select name="provinsi" id="provinsi" class="form-select ajax-select" data-placeholder="Cari provinsi"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kab/Kota</label>
                            <select name="kabko" id="kabko" class="form-select ajax-select" data-placeholder="Cari kab/kota"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kecamatan</label>
                            <select name="kecamatan" id="kecamatan" class="form-select ajax-select" data-placeholder="Cari kecamatan"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kelurahan</label>
                            <select name="kelurahan" id="kelurahan" class="form-select ajax-select" data-placeholder="Cari kelurahan"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">RW</label>
                            <select name="rw" id="rw" class="form-select"><option value="">- Pilih RW -</option></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">RT</label>
                            <select name="rt" id="rt" class="form-select"><option value="">- Pilih RT -</option></select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" rows="3" class="form-control text-uppercase-input force-uppercase" required>{{ old('alamat', $penduduk['alamat'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('admin.manajemen-warga.index') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ri-save-3-line me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            const selectedWilayah = @json($selectedWilayah ?? []);
            const endpoints = {
                provinsi: "{{ route('admin.manajemen-warga.options.provinsi') }}",
                kabko: "{{ route('admin.manajemen-warga.options.kabko') }}",
                kecamatan: "{{ route('admin.manajemen-warga.options.kecamatan') }}",
                kelurahan: "{{ route('admin.manajemen-warga.options.kelurahan') }}",
                rtrw: "{{ route('admin.manajemen-warga.options.rtrw') }}",
            };

            function addInitialOption(selector, item) {
                if (item && item.id && item.text) {
                    const option = new Option(item.text, item.id, true, true);
                    $(selector).append(option).trigger('change');
                }
            }

            function initSelect2(selector, url, extraDataCallback) {
                $(selector).select2({
                    placeholder: $(selector).data('placeholder') || 'Cari data',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return Object.assign({ q: params.term || '' }, extraDataCallback ? extraDataCallback() : {});
                        },
                        processResults: function (data) { return data; }
                    }
                });
            }

            function resetSelect(selector) {
                $(selector).val(null).empty().trigger('change');
            }

            function loadRtRw(kelurahanId, selectedRw = null, selectedRt = null) {
                $('#rw').html('<option value="">- Pilih RW -</option>');
                $('#rt').html('<option value="">- Pilih RT -</option>');
                if (!kelurahanId) return;

                $.getJSON(endpoints.rtrw, { kelurahan: kelurahanId }, function (data) {
                    (data.rws || []).forEach(function (item) {
                        $('#rw').append(new Option(item.text, item.id, String(item.id) === String(selectedRw), String(item.id) === String(selectedRw)));
                    });
                    (data.rts || []).forEach(function (item) {
                        $('#rt').append(new Option(item.text, item.id, String(item.id) === String(selectedRt), String(item.id) === String(selectedRt)));
                    });
                });
            }

            $(function () {
                initSelect2('#provinsi', endpoints.provinsi, function () { return {}; });
                initSelect2('#kabko', endpoints.kabko, function () { return { provinsi: $('#provinsi').val() }; });
                initSelect2('#kecamatan', endpoints.kecamatan, function () { return { kabko: $('#kabko').val() }; });
                initSelect2('#kelurahan', endpoints.kelurahan, function () { return { kecamatan: $('#kecamatan').val() }; });

                addInitialOption('#provinsi', selectedWilayah.provinsi);
                addInitialOption('#kabko', selectedWilayah.kabko);
                addInitialOption('#kecamatan', selectedWilayah.kecamatan);
                addInitialOption('#kelurahan', selectedWilayah.kelurahan);

                if (selectedWilayah.kelurahan && selectedWilayah.kelurahan.id) {
                    loadRtRw(selectedWilayah.kelurahan.id, selectedWilayah.rw, selectedWilayah.rt);
                }

                $('#provinsi').on('change', function () {
                    resetSelect('#kabko'); resetSelect('#kecamatan'); resetSelect('#kelurahan'); loadRtRw(null);
                });
                $('#kabko').on('change', function () {
                    resetSelect('#kecamatan'); resetSelect('#kelurahan'); loadRtRw(null);
                });
                $('#kecamatan').on('change', function () {
                    resetSelect('#kelurahan'); loadRtRw(null);
                });
                $('#kelurahan').on('change', function () {
                    loadRtRw($(this).val());
                });

                $('.only-number').on('input', function () { this.value = this.value.replace(/\D/g, '').slice(0, 16); });
                $('.only-phone').on('input', function () { this.value = this.value.replace(/\D/g, ''); });
                $('.force-uppercase').on('input', function () { this.value = this.value.toUpperCase(); });
                $('#formWarga').on('submit', function () {
                    $('.force-uppercase').each(function () { this.value = this.value.toUpperCase(); });
                });
            });
        </script>
    @endpush
@endsection
