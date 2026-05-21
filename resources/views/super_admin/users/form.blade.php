@extends('layouts.main')

@section('content')
    <style>
        .super-card { border: 1px solid #eef0f4; border-radius: 22px; box-shadow: 0 16px 35px rgba(15, 23, 42, .06); }
        .super-pill { display:inline-flex; align-items:center; border-radius:999px; padding:6px 11px; background:#f8fafc; border:1px solid #eef2f7; font-size:12px; font-weight:800; color:#64748b; }
        .upper-input { text-transform: uppercase; }
        .section-title { font-weight: 900; letter-spacing: -.02em; }
        .hint-box { background:#f8fafc; border:1px dashed #cbd5e1; border-radius:18px; padding:12px 14px; color:#64748b; font-size:13px; }
        .locked-box { background:#f1f5f9; border:1px solid #e2e8f0; border-radius:18px; padding:10px 14px; font-weight:800; color:#334155; min-height:45px; display:flex; align-items:center; }
    </style>

    @php
        $selectedRole = (int) old('role_id', $userData->role_id ?: 2);
        $isEdit = $mode === 'edit';
        $defaultJabatan = old('id_jabatan', optional($pejabatData)->id_jabatan ?? ($roleJabatanMap[$selectedRole] ?? ''));
        $selectedKelurahan = old('kelurahan', $selectedWilayah['kelurahan']['id'] ?? '');
        $selectedRw = old('rw', $selectedWilayah['rw'] ?? '');
        $selectedRt = old('rt', $selectedWilayah['rt'] ?? '');
        $oldInstansi = old('id_instansi', $userData->id_instansi);
    @endphp

    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <span class="super-pill mb-2">SUPER ADMIN / AKUN</span>
                <h3 class="fw-bold mb-1">{{ $title }}</h3>
                <div class="text-muted"></div>
            </div>
            <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-dark rounded-4 fw-semibold">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger rounded-4">
                <div class="fw-bold mb-1">Periksa kembali input:</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card super-card">
            <div class="card-body p-4">
                <form method="POST" action="{{ $mode === 'create' ? route('super-admin.users.store') : route('super-admin.users.update', $userData->id) }}" id="superUserForm">
                    @csrf
                    @if ($mode === 'edit')
                        @method('PUT')
                    @endif
                    <input type="hidden" name="pejabat_id" value="{{ optional($pejabatData)->id }}">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis/Role Akun</label>
                            <select name="role_id" id="role_id" class="form-select rounded-4" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected((string) old('role_id', $userData->role_id ?: 2) === (string) $role->id)>{{ $role->id }} - {{ $role->display_name ?? $role->name }}</option>
                                @endforeach
                            </select>
                            <div></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Instansi/SKPD</label>
                            <select name="id_instansi" id="id_instansi" class="form-select rounded-4" required>
                                @foreach ($skpds as $skpd)
                                    <option value="{{ $skpd->id }}" data-region="{{ $skpd->id_region }}" data-kec="{{ $skpd->id_kec }}" @selected((string) $oldInstansi === (string) $skpd->id)>{{ $skpd->super_label }}</option>
                                @endforeach
                            </select>
                            <div id="lockedInstansiText" class="form-text text-primary fw-semibold d-none">Terkunci otomatis mengikuti Kelurahan yang dipilih.</div>
                            <div class="form-text"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Password {{ $isEdit ? '(kosongkan jika tidak diganti)' : '' }}</label>
                            <input type="password" name="password" class="form-control rounded-4" {{ $isEdit ? '' : 'required' }} minlength="8">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama</label>
                            <input type="text" name="name" value="{{ old('name', $userData->name) }}" class="form-control rounded-4 upper-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIK</label>
                            @if ($isEdit)
                                <input type="text" value="{{ $userData->nik }}" class="form-control rounded-4 bg-light" readonly disabled>
                                <input type="hidden" name="nik" value="{{ $userData->nik }}">
                                <div class="form-text text-danger fw-semibold"></div>
                            @else
                                <input type="text" name="nik" value="{{ old('nik', $userData->nik) }}" class="form-control rounded-4 js-digits" maxlength="16" minlength="16" inputmode="numeric" pattern="[0-9]{16}" required>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" value="{{ old('email', $userData->email) }}" class="form-control rounded-4" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor HP</label>
                            <input type="text" name="phone" value="{{ old('phone', $userData->phone) }}" class="form-control rounded-4 js-digits" inputmode="numeric" pattern="[0-9]+" required>
                        </div>
                    </div>

                    <div id="wargaSection" class="mt-4">
                        <hr>
                        <h5 class="section-title mb-1">Data Lengkap Warga</h5>
                     

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Provinsi</label>
                                <select name="provinsi" id="provinsi" class="form-select rounded-4 warga-required" data-selected="{{ old('provinsi', $selectedWilayah['provinsi']['id'] ?? '35') }}">
                                    <option value="{{ old('provinsi', $selectedWilayah['provinsi']['id'] ?? '35') }}">{{ $selectedWilayah['provinsi']['text'] ?? 'JAWA TIMUR' }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Kab/Kota</label>
                                <select name="kabko" id="kabko" class="form-select rounded-4 warga-required" data-selected="{{ old('kabko', $selectedWilayah['kabko']['id'] ?? '35.71') }}">
                                    <option value="{{ old('kabko', $selectedWilayah['kabko']['id'] ?? '35.71') }}">{{ $selectedWilayah['kabko']['text'] ?? 'KOTA KEDIRI' }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Kecamatan</label>
                                <select name="kecamatan" id="kecamatan" class="form-select rounded-4 warga-required" data-selected="{{ old('kecamatan', $selectedWilayah['kecamatan']['id'] ?? '') }}">
                                    <option value="{{ old('kecamatan', $selectedWilayah['kecamatan']['id'] ?? '') }}">{{ $selectedWilayah['kecamatan']['text'] ?? 'Pilih Kecamatan' }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Kelurahan</label>
                                <select name="kelurahan" id="kelurahan" class="form-select rounded-4 warga-required" data-selected="{{ $selectedKelurahan }}">
                                    <option value="{{ $selectedKelurahan }}">{{ $selectedWilayah['kelurahan']['text'] ?? 'Pilih Kelurahan' }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">RW</label>
                                <select name="rw" id="rw" class="form-select rounded-4 warga-required" data-selected="{{ $selectedRw }}">
                                    <option value="">Pilih RW</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">RT</label>
                                <select name="rt" id="rt" class="form-select rounded-4 warga-required" data-selected="{{ $selectedRt }}">
                                    <option value="">Pilih RT</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nomor KK</label>
                                <input type="text" name="kk" value="{{ old('kk', $resident->kk ?? ($penduduk['kk'] ?? '')) }}" class="form-control rounded-4 js-digits warga-required" maxlength="16" minlength="16" inputmode="numeric" pattern="[0-9]{16}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tempat Lahir</label>
                                <input type="text" name="tempat_lhr" value="{{ old('tempat_lhr', $penduduk['tempat_lhr'] ?? '') }}" class="form-control rounded-4 upper-input warga-required">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Lahir</label>
                                <input type="date" name="tgl_lhr" value="{{ old('tgl_lhr', $penduduk['tgl_lhr'] ?? '') }}" class="form-control rounded-4">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <select name="gender" class="form-select rounded-4">
                                    <option value="">Pilih</option>
                                    @foreach ($genders as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('gender', $penduduk['gender'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Agama</label>
                                <select name="agama" class="form-select rounded-4">
                                    <option value="">Pilih</option>
                                    @foreach ($agamas as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('agama', $penduduk['agama'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Status Kawin</label>
                                <select name="status_kwn" class="form-select rounded-4">
                                    <option value="">Pilih</option>
                                    @foreach ($statusKwns as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('status_kwn', $penduduk['status_kwn'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Kewarganegaraan</label>
                                <select name="kewarganegaraan" class="form-select rounded-4">
                                    <option value="">Pilih</option>
                                    @foreach ($kewarganegaraans as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('kewarganegaraan', $penduduk['kewarganegaraan'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Pendidikan</label>
                                <select name="pendidikan" class="form-select rounded-4">
                                    <option value="">Pilih</option>
                                    @foreach ($pendidikans as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('pendidikan', $penduduk['pendidikan'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Pekerjaan</label>
                                <select name="pekerjaan" class="form-select rounded-4">
                                    <option value="">Pilih</option>
                                    @foreach ($pekerjaans as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('pekerjaan', $penduduk['pekerjaan'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Alamat</label>
                                <input type="text" name="alamat" value="{{ old('alamat', $penduduk['alamat'] ?? '') }}" class="form-control rounded-4 upper-input warga-required">
                            </div>
                        </div>
                    </div>

                    <div id="pejabatSection" class="mt-4">
                        <hr>
                        <h5 class="section-title mb-1">Data Pegawai/Pejabat</h5>
                        <div></div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">NIP</label>
                                <input type="text" name="nip" value="{{ old('nip', optional($pejabatData)->nip) }}" class="form-control rounded-4 js-digits pejabat-required" inputmode="numeric" pattern="[0-9]{18}" maxlength="18" minlength="18">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jabatan</label>
                                <select name="id_jabatan" id="id_jabatan" class="form-select rounded-4 pejabat-required">
                                    <option value="">Pilih Jabatan</option>
                                    @foreach ($jabatans as $jabatan)
                                        <option value="{{ $jabatan->id }}" @selected((string) $defaultJabatan === (string) $jabatan->id)>{{ $jabatan->id }} - {{ $jabatan->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Pangkat/Golongan</label>
                                <select name="id_pangkat" class="form-select rounded-4 pejabat-required">
                                    <option value="">Pilih Pangkat</option>
                                    @foreach ($pangkats as $pangkat)
                                        <option value="{{ $pangkat->id }}" @selected((string) old('id_pangkat', optional($pejabatData)->id_pangkat) === (string) $pangkat->id)>{{ $pangkat->id }} - {{ $pangkat->nama }} / {{ $pangkat->gol_pns }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('super-admin.users.index') }}" class="btn btn-light rounded-4">Batal</a>
                        <button class="btn btn-dark rounded-4 fw-semibold" type="submit">Simpan Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function() {
    const pejabatRoleMap = @json($roleJabatanMap);
    const kelurahanMeta = @json($kelurahanMeta);

    const roleInput = document.getElementById('role_id');
    const wargaSection = document.getElementById('wargaSection');
    const pejabatSection = document.getElementById('pejabatSection');
    const jabatanInput = document.getElementById('id_jabatan');
    const skpdSelect = document.getElementById('id_instansi');
    const skpdLockedText = document.getElementById('lockedInstansiText');

    const provinsiSelect = document.getElementById('provinsi');
    const kabkoSelect = document.getElementById('kabko');
    const kecamatanSelect = document.getElementById('kecamatan');
    const kelurahanSelect = document.getElementById('kelurahan');
    const rwSelect = document.getElementById('rw');
    const rtSelect = document.getElementById('rt');

    const urls = {
        provinsi: @json(route('super-admin.users.options.provinsi')),
        kabko: @json(route('super-admin.users.options.kabko')),
        kecamatan: @json(route('super-admin.users.options.kecamatan')),
        kelurahan: @json(route('super-admin.users.options.kelurahan')),
        rtrw: @json(route('super-admin.users.options.rtrw')),
    };

    function setRequired(selector, required) {
        document.querySelectorAll(selector).forEach(el => el.required = required);
    }

    function fillOptions(select, items, placeholder, selected) {
        const selectedString = selected ? String(selected) : '';
        select.innerHTML = '<option value="">' + placeholder + '</option>';
        (items || []).forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.text;
            if (selectedString && String(item.id) === selectedString) option.selected = true;
            select.appendChild(option);
        });
    }

    function loadSelect(select, url, params, placeholder, selected) {
        const target = new URL(url, window.location.origin);
        Object.keys(params || {}).forEach(key => {
            if (params[key]) target.searchParams.set(key, params[key]);
        });

        return fetch(target.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.json())
            .then(data => {
                fillOptions(select, data.results || [], placeholder, selected || select.dataset.selected || select.value);
            })
            .catch(() => {
                // Biarkan opsi lama tetap ada kalau jaringan/route belum siap.
            });
    }

    function syncSections() {
        const role = String(roleInput.value);
        const isWarga = role === '2';
        const isPejabat = Object.prototype.hasOwnProperty.call(pejabatRoleMap, role);

        wargaSection.style.display = isWarga ? '' : 'none';
        pejabatSection.style.display = isPejabat ? '' : 'none';
        setRequired('.warga-required', isWarga);
        setRequired('.pejabat-required', isPejabat);

        if (isPejabat && jabatanInput && pejabatRoleMap[role]) {
            jabatanInput.value = pejabatRoleMap[role];
        }

        skpdSelect.disabled = isWarga;
        skpdSelect.classList.toggle('bg-light', isWarga);
        skpdLockedText.classList.toggle('d-none', !isWarga);

        if (isWarga) {
            syncInstansiFromKelurahan();
        }
    }

    function syncInstansiFromKelurahan() {
        const meta = kelurahanMeta[kelurahanSelect.value] || null;
        if (!meta) {
            skpdLockedText.textContent = 'Instansi/SKPD akan terkunci setelah Kelurahan dipilih.';
            return;
        }

        skpdSelect.value = String(meta.skpd_id);
       
    }

    function loadRtRw(resetRt = false) {
        const kelurahan = kelurahanSelect.value;
        const rw = rwSelect.value;
        if (!kelurahan) {
            fillOptions(rwSelect, [], 'Pilih RW', '');
            fillOptions(rtSelect, [], 'Pilih RT', '');
            return;
        }
        const url = new URL(urls.rtrw, window.location.origin);
        url.searchParams.set('kelurahan', kelurahan);
        if (rw) url.searchParams.set('rw', rw);

        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.json())
            .then(data => {
                const selectedRw = rwSelect.dataset.selected || rwSelect.value;
                const selectedRt = resetRt ? '' : (rtSelect.dataset.selected || rtSelect.value);
                fillOptions(rwSelect, data.rws || [], 'Pilih RW', selectedRw);
                fillOptions(rtSelect, data.rts || [], 'Pilih RT', selectedRt);
                rwSelect.dataset.selected = '';
                rtSelect.dataset.selected = '';
            })
            .catch(() => {
                fillOptions(rwSelect, [], 'RW tidak terbaca', '');
                fillOptions(rtSelect, [], 'RT tidak terbaca', '');
            });
    }

    function resetAfter(selects) {
        selects.forEach(select => {
            select.dataset.selected = '';
            select.innerHTML = '<option value="">Pilih</option>';
        });
    }

    roleInput.addEventListener('change', syncSections);

    provinsiSelect.addEventListener('change', function() {
        resetAfter([kabkoSelect, kecamatanSelect, kelurahanSelect, rwSelect, rtSelect]);
        loadSelect(kabkoSelect, urls.kabko, { provinsi: provinsiSelect.value }, 'Pilih Kab/Kota', '');
    });

    kabkoSelect.addEventListener('change', function() {
        resetAfter([kecamatanSelect, kelurahanSelect, rwSelect, rtSelect]);
        loadSelect(kecamatanSelect, urls.kecamatan, { kabko: kabkoSelect.value }, 'Pilih Kecamatan', '');
    });

    kecamatanSelect.addEventListener('change', function() {
        resetAfter([kelurahanSelect, rwSelect, rtSelect]);
        loadSelect(kelurahanSelect, urls.kelurahan, { kecamatan: kecamatanSelect.value }, 'Pilih Kelurahan', '')
            .then(syncInstansiFromKelurahan);
    });

    kelurahanSelect.addEventListener('change', function() {
        rwSelect.dataset.selected = '';
        rtSelect.dataset.selected = '';
        syncInstansiFromKelurahan();
        loadRtRw(true);
    });

    rwSelect.addEventListener('change', function() {
        rtSelect.dataset.selected = '';
        loadRtRw(true);
    });

    document.querySelectorAll('.js-digits').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    });

    document.querySelectorAll('.upper-input').forEach(input => {
        input.addEventListener('input', function() {
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(start, end);
        });
    });

    const selectedProv = provinsiSelect.dataset.selected || provinsiSelect.value || '35';
    const selectedKab = kabkoSelect.dataset.selected || kabkoSelect.value || '35.71';
    const selectedKec = kecamatanSelect.dataset.selected || kecamatanSelect.value;
    const selectedKel = kelurahanSelect.dataset.selected || kelurahanSelect.value;

    loadSelect(provinsiSelect, urls.provinsi, {}, 'Pilih Provinsi', selectedProv)
        .then(() => loadSelect(kabkoSelect, urls.kabko, { provinsi: selectedProv }, 'Pilih Kab/Kota', selectedKab))
        .then(() => loadSelect(kecamatanSelect, urls.kecamatan, { kabko: selectedKab }, 'Pilih Kecamatan', selectedKec))
        .then(() => loadSelect(kelurahanSelect, urls.kelurahan, { kecamatan: selectedKec }, 'Pilih Kelurahan', selectedKel))
        .then(() => {
            syncSections();
            if (kelurahanSelect.value) {
                syncInstansiFromKelurahan();
                loadRtRw(false);
            }
        });

    syncSections();
})();
</script>
@endpush
