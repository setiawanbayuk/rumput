@extends('layouts.main')

@section('title', $title ?? 'Manajemen Kontrol')

@section('content')
    @push('styles')
        <style>
            .mk-page { --mk-ink:#1f2937; --mk-muted:#64748b; --mk-line:#e5e7eb; --mk-gold:#b7791f; }
            .mk-card { border:1px solid var(--mk-line); border-radius:20px; box-shadow:0 14px 32px rgba(15,23,42,.055); overflow:hidden; }
            .mk-card::before { content:''; display:block; height:3px; background:linear-gradient(90deg,#b7791f,#e9d8a6,#2563eb); }
            .mk-pill { display:inline-flex; align-items:center; border-radius:999px; padding:6px 11px; background:#f8fafc; border:1px solid #eef2f7; font-size:12px; font-weight:800; color:#64748b; }
            .mk-title { font-weight:900; color:var(--mk-ink); letter-spacing:-.025em; }
            .mk-subtitle { color:var(--mk-muted); }
            .mk-section-title { font-size:.82rem; font-weight:900; text-transform:uppercase; letter-spacing:.055em; color:#475569; padding-left:12px; border-left:4px solid var(--mk-gold); }
            .mk-locked { min-height:45px; display:flex; align-items:center; border-radius:14px; padding:10px 13px; background:#f8fafc; border:1px solid #e2e8f0; font-weight:800; color:#334155; }
            .mk-hint { background:#fffdf7; border:1px dashed #e8d7a7; border-radius:16px; padding:12px 14px; color:#64748b; font-size:13px; }
            .form-label { font-weight:700; color:#334155; }
            .form-control, .form-select { border-radius:14px; border-color:#dbe1ea; }
            .form-control:focus, .form-select:focus { border-color:#d6b56d; box-shadow:0 0 0 .18rem rgba(183,121,31,.12); }
            .upper-input { text-transform:uppercase; }
        </style>
    @endpush

    @php
        $isEdit = $mode === 'edit';
        // Role dikunci dari tombol Tambah/Edit. Jangan ambil dari old('role_id') agar tidak berubah saat validasi gagal.
        $selectedRole = (int) ($userData->role_id ?: 2);
        $selectedRoleLabel = $selectedRoleLabel ?? ($roles->firstWhere('id', $selectedRole)->display_name ?? 'Warga');
        $defaultJabatan = $roleJabatanMap[$selectedRole] ?? optional($pejabatData)->id_jabatan ?? '';
        $lockedJabatan = $defaultJabatan ? $jabatans->firstWhere('id', (int) $defaultJabatan) : null;
        $lockedJabatanLabel = $lockedJabatan ? ($lockedJabatan->id . ' - ' . $lockedJabatan->nama) : '-';
        $selectedRw = old('rw', $selectedWilayah['rw'] ?? '');
        $selectedRt = old('rt', $selectedWilayah['rt'] ?? '');
    @endphp

    <div class="container-fluid mk-page">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <span class="mk-pill mb-2">ADMIN KELURAHAN / MANAJEMEN KONTROL</span>
                <h3 class="mk-title mb-1">{{ $title }}</h3>
               @php
				$namaKelurahan = optional($adminSkpd->kelurahan)->nama ?? $adminSkpd->nama ?? 'Wilayah Admin';
				@endphp

					<div class="mw-subtitle small">
						Kelola Akun Warga, Sekkel, Dan Lurah Kelurahan {{ \Illuminate\Support\Str::title(strtolower($namaKelurahan)) }}.
					</div>
            </div>
            <a href="{{ route('admin.manajemen-warga.index') }}" class="btn btn-outline-dark rounded-4 fw-semibold">
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


        <div class="card mk-card mb-3">
            <div class="card-body p-4">
                <form method="POST" action="{{ $mode === 'create' ? route('admin.manajemen-warga.store') : route('admin.manajemen-warga.update', $userData->id) }}" id="mkForm" enctype="multipart/form-data">
                    @csrf
                    @if ($mode === 'edit')
                        @method('PUT')
                    @endif

                    <input type="hidden" name="pejabat_id" value="{{ optional($pejabatData)->id }}">
                    <input type="hidden" name="id_instansi" value="{{ $adminSkpd->id }}">
                    @if ($mode === 'create')
                        <input type="hidden" name="role_token" value="{{ $roleToken }}">
                    @endif

                    <div class="mk-section-title mb-3">Data Akun</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Jenis/Role Akun <span class="text-danger">*</span></label>
                            <input type="hidden" name="role_id" id="role_id" value="{{ $selectedRole }}">
                            <div class="mk-locked">
                                {{ $selectedRole }} - {{ $selectedRoleLabel }}
                            </div>
                            
                            @error('role_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Instansi/SKPD Terkunci</label>
                            <div class="mk-locked">{{ $adminSkpd->id }} - {{ $adminSkpd->nama ?? ($selectedWilayah['kelurahan']['text'] ?? '-') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password {{ $isEdit ? '(Kosongkan Jika Tidak Diganti)' : '*' }}</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $isEdit ? '' : 'required' }} minlength="8">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $userData->name) }}" class="form-control upper-input @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIK <span class="text-danger">*</span></label>
                            @if ($isEdit && $selectedRole === 2)
                                <input type="text" value="{{ $userData->nik }}" class="form-control bg-light" readonly disabled>
                                <input type="hidden" name="nik" value="{{ $userData->nik }}">
                                <div class="form-text text-danger fw-semibold">NIK TIDAK DAPAT DIRUBAH</div>
                            @else
                                <input type="text" name="nik" value="{{ old('nik', $userData->nik) }}" class="form-control js-digits @error('nik') is-invalid @enderror" maxlength="16" minlength="16" inputmode="numeric" pattern="[0-9]{16}" required>
                               
                            @endif
                            @error('nik') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $userData->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $userData->phone) }}" class="form-control js-digits @error('phone') is-invalid @enderror" inputmode="numeric" pattern="[0-9]+" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Foto Akun</label>
                            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/png,image/jpeg,image/jpg,image/webp">
                            <div class="form-text">Upload foto akun opsional. Kosongkan jika tidak ingin mengganti foto.</div>
                            @error('foto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            @if($isEdit && !empty($userData->foto))
                                <div class="mt-2 d-flex align-items-center gap-2 small text-muted">
                                    <img src="{{ asset('storage/' . $userData->foto) }}" alt="Foto akun" style="width:38px;height:38px;border-radius:12px;object-fit:cover;border:1px solid #e2e8f0;">
                                    <span>Foto akun saat ini</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div id="wargaSection" class="mt-4">
                        <hr>
                        <div class="mk-section-title mb-3">Data Lengkap Warga</div>
                       

                        <input type="hidden" name="provinsi" value="{{ $selectedWilayah['provinsi']['id'] ?? '35' }}">
                        <input type="hidden" name="kabko" value="{{ $selectedWilayah['kabko']['id'] ?? '35.71' }}">
                        <input type="hidden" name="kecamatan" value="{{ $selectedWilayah['kecamatan']['id'] ?? '' }}">
                        <input type="hidden" name="kelurahan" id="kelurahan" value="{{ $selectedWilayah['kelurahan']['id'] ?? '' }}">

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Provinsi</label>
                                <div class="mk-locked">{{ $selectedWilayah['provinsi']['text'] ?? 'JAWA TIMUR' }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kab/Kota</label>
                                <div class="mk-locked">{{ $selectedWilayah['kabko']['text'] ?? 'KOTA KEDIRI' }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kecamatan</label>
                                <div class="mk-locked">{{ $selectedWilayah['kecamatan']['text'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kelurahan</label>
                                <div class="mk-locked">{{ $selectedWilayah['kelurahan']['text'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">RW <span class="text-danger">*</span></label>
                                <select name="rw" id="rw" class="form-select warga-required @error('rw') is-invalid @enderror" data-selected="{{ $selectedRw }}">
                                    <option value="">Pilih RW</option>
                                </select>
                                @error('rw') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">RT <span class="text-danger">*</span></label>
                                <select name="rt" id="rt" class="form-select warga-required @error('rt') is-invalid @enderror" data-selected="{{ $selectedRt }}">
                                    <option value="">Pilih RT</option>
                                </select>
                                @error('rt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor KK <span class="text-danger">*</span></label>
                                <input type="text" name="kk" value="{{ old('kk', $resident->kk ?? ($penduduk['kk'] ?? '')) }}" class="form-control js-digits warga-required @error('kk') is-invalid @enderror" maxlength="16" minlength="16" inputmode="numeric" pattern="[0-9]{16}">
                                @error('kk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lhr" value="{{ old('tempat_lhr', $penduduk['tempat_lhr'] ?? '') }}" class="form-control upper-input warga-required @error('tempat_lhr') is-invalid @enderror">
                                @error('tempat_lhr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tgl_lhr" value="{{ old('tgl_lhr', $penduduk['tgl_lhr'] ?? '') }}" class="form-control @error('tgl_lhr') is-invalid @enderror">
                                @error('tgl_lhr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="gender" class="form-select">
                                    <option value="">Pilih</option>
                                    @foreach ($genders as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('gender', $penduduk['gender'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Agama</label>
                                <select name="agama" class="form-select">
                                    <option value="">Pilih</option>
                                    @foreach ($agamas as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('agama', $penduduk['agama'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status Kawin</label>
                                <select name="status_kwn" class="form-select">
                                    <option value="">Pilih</option>
                                    @foreach ($statusKwns as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('status_kwn', $penduduk['status_kwn'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kewarganegaraan</label>
                                <select name="kewarganegaraan" class="form-select">
                                    <option value="">Pilih</option>
                                    @foreach ($kewarganegaraans as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('kewarganegaraan', $penduduk['kewarganegaraan'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pendidikan</label>
                                <select name="pendidikan" class="form-select">
                                    <option value="">Pilih</option>
                                    @foreach ($pendidikans as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('pendidikan', $penduduk['pendidikan'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pekerjaan</label>
                                <select name="pekerjaan" class="form-select">
                                    <option value="">Pilih</option>
                                    @foreach ($pekerjaans as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('pekerjaan', $penduduk['pekerjaan'] ?? '') === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                <input type="text" name="alamat" value="{{ old('alamat', $penduduk['alamat'] ?? '') }}" class="form-control upper-input warga-required @error('alamat') is-invalid @enderror">
                                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div id="pejabatSection" class="mt-4">
                        <hr>
                        <div class="mk-section-title mb-3">Data Sekkel / Lurah</div>
                        

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" name="nip" value="{{ old('nip', optional($pejabatData)->nip) }}" class="form-control js-digits pejabat-required @error('nip') is-invalid @enderror" inputmode="numeric" pattern="[0-9]{18}" maxlength="18" minlength="18">
                                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jabatan Otomatis</label>
                                <input type="hidden" name="id_jabatan" id="id_jabatan" value="{{ $defaultJabatan }}" class="pejabat-required">
                                <div class="mk-locked">{{ $lockedJabatanLabel }}</div>
                       
                                @error('id_jabatan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pangkat/Golongan <span class="text-danger">*</span></label>
                                <select name="id_pangkat" class="form-select pejabat-required @error('id_pangkat') is-invalid @enderror">
                                    <option value="">Pilih Pangkat</option>
                                    @foreach ($pangkats as $pangkat)
                                        <option value="{{ $pangkat->id }}" @selected((string) old('id_pangkat', optional($pejabatData)->id_pangkat) === (string) $pangkat->id)>{{ $pangkat->id }} - {{ $pangkat->nama }} / {{ $pangkat->gol_pns }}</option>
                                    @endforeach
                                </select>
                                @error('id_pangkat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.manajemen-warga.index') }}" class="btn btn-light rounded-4">Batal</a>
                        <button class="btn btn-dark rounded-4 fw-semibold" type="submit">
                            <i class="ri-save-3-line me-1"></i> Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                const pejabatRoleMap = @json($roleJabatanMap);
                const roleInput = document.getElementById('role_id');
                const wargaSection = document.getElementById('wargaSection');
                const pejabatSection = document.getElementById('pejabatSection');
                const jabatanInput = document.getElementById('id_jabatan');
                const rwSelect = document.getElementById('rw');
                const rtSelect = document.getElementById('rt');
                const rtrwUrl = @json(route('admin.manajemen-warga.options.rtrw'));

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
                }

                function loadRtRw(resetRt = false) {
                    const url = new URL(rtrwUrl, window.location.origin);
                    if (rwSelect.value) url.searchParams.set('rw', rwSelect.value);

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

                roleInput.addEventListener('change', syncSections);
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

                syncSections();
                loadRtRw(false);
            })();
        </script>
    @endpush
@endsection
