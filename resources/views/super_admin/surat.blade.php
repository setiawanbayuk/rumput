@extends('layouts.main')

@section('content')
    <style>
        .super-card { border: 1px solid #eef0f4; border-radius: 22px; box-shadow: 0 16px 35px rgba(15, 23, 42, .06); }
        .super-pill { display:inline-flex; align-items:center; border-radius:999px; padding:6px 11px; background:#f8fafc; border:1px solid #eef2f7; font-size:12px; font-weight:800; color:#64748b; }
        .action-row form { display:inline-block; margin: 2px; }
        .table td { vertical-align: middle; }
        .small-muted { font-size: 12px; color:#64748b; }
    </style>

    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <span class="super-pill mb-2">SUPER ADMIN / SURAT</span>
                <h3 class="fw-bold mb-1">Kontrol Semua Surat</h3>
                <div class="text-muted">Lihat seluruh pengajuan, ubah status, tolak, hapus, preview, cetak, dan jalankan TTE sesuai level surat.</div>
            </div>
            <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-dark rounded-4 fw-semibold">
                <i class="ri-arrow-left-line me-1"></i> Dashboard
            </a>
        </div>

        @if (session('status'))
            <div class="alert alert-success rounded-4">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger rounded-4">{{ $errors->first() }}</div>
        @endif

        <div class="card super-card mb-3">
            <div class="card-body p-4">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Cari</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-4" placeholder="ID, NIK, jenis, kepada, peruntukan">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">Jenis Surat</label>
                        <select name="jenis" class="form-select rounded-4">
                            <option value="">Semua</option>
                            @foreach ($jenisOptions as $jenis)
                                <option value="{{ $jenis }}" @selected(request('jenis') == $jenis)>{{ strtoupper($jenis) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select rounded-4">
                            <option value="">Semua</option>
                            @foreach ($statusOptions as $key => $label)
                                <option value="{{ $key }}" @selected((string) request('status') === (string) $key)>{{ $key }} - {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-semibold">Kelurahan/SKPD</label>
                        <select name="id_kel" class="form-select rounded-4">
                            <option value="">Semua</option>
                            @foreach ($kelurahans as $kel)
                                <option value="{{ $kel->id }}" @selected((string) request('id_kel') === (string) $kel->id)>{{ $kel->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-12 d-flex gap-2">
                        <button class="btn btn-dark rounded-4 fw-semibold w-100" type="submit">Filter</button>
                        <a href="{{ route('super-admin.surat.index') }}" class="btn btn-outline-secondary rounded-4">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card super-card">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width:70px">ID</th>
                                <th>No Surat</th>
                                <th>Jenis</th>
                                <th>NIK</th>
                                <th>Kelurahan</th>
                                <th>Status</th>
                                <th>Update</th>
                                <th style="min-width:420px">Kontrol</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($surats as $row)
                                @php
                                    $isSktm = strtolower((string) $row->jenis_surat) === 'sktm';
                                    $statusName = $row->st['name'] ?? ($statusOptions[(int) $row->status] ?? 'Status '.$row->status);
                                    $canTteLurah = (int) $row->status === 3;
                                    $canTteCamat = $isSktm && (int) $row->status === 8;
                                @endphp
                                <tr>
                                    <td class="fw-bold">#{{ $row->id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $row->kd_jenis_surat }}/{{ $row->no_urut_surat }}/{{ $row->tahun }}</div>
                                        <div class="small-muted">{{ $row->tgl_surat ? \Carbon\Carbon::parse($row->tgl_surat)->format('d-m-Y') : '-' }}</div>
                                    </td>
                                    <td><span class="badge bg-info text-dark">{{ strtoupper($row->jenis_surat) }}</span></td>
                                    <td>{{ $row->nik }}</td>
                                    <td>{{ optional($row->kelurahan)->nama ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $statusName }}</span>
                                        <div class="small-muted">status: {{ $row->status }}</div>
                                    </td>
                                    <td>{{ optional($row->updated_at ?? $row->created_at)->format('d-m-Y H:i') }}</td>
                                    <td class="action-row">
                                        <a href="{{ route('admin.surat.preview', $row->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-3" title="Preview">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @if (!empty($row->file))
                                            <a href="{{ route('admin.surat.cetak', $row->id) }}" target="_blank" class="btn btn-sm btn-success rounded-3" title="Cetak/Download">
                                                <i class="ri-download-2-line"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.surat.edit', $row->id) }}" class="btn btn-sm btn-warning rounded-3" title="Edit Detail Surat">
                                            <i class="ri-edit-2-line"></i>
                                        </a>

                                        <form method="POST" action="{{ route('super-admin.surat.action', $row->id) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="proses">
                                            <button class="btn btn-sm btn-outline-dark rounded-3" type="submit">Proses</button>
                                        </form>
                                        <form method="POST" action="{{ route('super-admin.surat.action', $row->id) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="naik">
                                            <button class="btn btn-sm btn-outline-dark rounded-3" type="submit">Naik</button>
                                        </form>
                                        <form method="POST" action="{{ route('super-admin.surat.action', $row->id) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="turunkan">
                                            <button class="btn btn-sm btn-outline-secondary rounded-3" type="submit">Turun</button>
                                        </form>
                                        <form method="POST" action="{{ route('super-admin.surat.action', $row->id) }}" onsubmit="return confirm('Setujui status surat ini tanpa proses TTE? Untuk tanda tangan elektronik, gunakan tombol TTE.');">
                                            @csrf
                                            <input type="hidden" name="action" value="setujui">
                                            <button class="btn btn-sm btn-outline-success rounded-3" type="submit">Setujui</button>
                                        </form>

                                        @if ($canTteLurah || $canTteCamat)
                                            <button type="button"
                                                class="btn btn-sm btn-primary rounded-3 btn-tte"
                                                data-id="{{ $row->id }}"
                                                data-jenis="{{ $row->jenis_surat }}"
                                                data-role="{{ $canTteCamat ? 5 : 3 }}"
                                                data-title="{{ strtoupper($row->jenis_surat) }} #{{ $row->id }}">
                                                TTE {{ $canTteCamat ? 'Camat' : 'Lurah' }}
                                            </button>
                                        @endif

                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $row->id }}">Tolak</button>

                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" data-bs-toggle="modal" data-bs-target="#modalStatus{{ $row->id }}">Status</button>

                                        <form method="POST" action="{{ route('super-admin.surat.destroy', $row->id) }}" onsubmit="return confirm('Yakin hapus surat ini? Data surat akan hilang dari tabel surat_pengajuans.');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger rounded-3" type="submit"><i class="ri-delete-bin-line"></i></button>
                                        </form>

                                        <div class="modal fade" id="modalTolak{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form class="modal-content" method="POST" action="{{ route('super-admin.surat.action', $row->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="action" value="tolak">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Surat #{{ $row->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label">Alasan Penolakan</label>
                                                        <textarea name="komentar" class="form-control rounded-4" rows="4" required>Ditolak oleh Super Admin.</textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light rounded-4" data-bs-dismiss="modal">Batal</button>
                                                        <button class="btn btn-danger rounded-4" type="submit">Tolak Surat</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="modalStatus{{ $row->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form class="modal-content" method="POST" action="{{ route('super-admin.surat.action', $row->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="action" value="set_status">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Ubah Status Surat #{{ $row->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label class="form-label">Status Baru</label>
                                                        <select name="status" class="form-select rounded-4" required>
                                                            @foreach ($statusOptions as $key => $label)
                                                                <option value="{{ $key }}" @selected((int) $row->status === (int) $key)>{{ $key }} - {{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="form-text">Gunakan hanya untuk koreksi darurat Super Admin agar alur lama tidak terganggu.</div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light rounded-4" data-bs-dismiss="modal">Batal</button>
                                                        <button class="btn btn-dark rounded-4" type="submit">Simpan Status</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-4">Data surat tidak ditemukan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $surats->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTteSuperAdmin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="formTteSuperAdmin">
                @csrf
                <input type="hidden" name="_id" id="tte_id">
                <input type="hidden" name="jenis" id="tte_jenis">
                <input type="hidden" name="role" id="tte_role">
                <div class="modal-header">
                    <h5 class="modal-title">TTE <span id="tte_title"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning rounded-4 small mb-3">
                        Super Admin hanya membuka kontrol. NIK dan passphrase tetap memakai pejabat penandatangan sesuai level TTE.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NIK Penandatangan</label>
                        <input type="text" name="nik" class="form-control rounded-4" maxlength="16" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Passphrase</label>
                        <input type="password" name="passphrase" class="form-control rounded-4" required>
                    </div>
                    <div id="tte_result" class="small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-4 fw-semibold">Proses TTE</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-tte').forEach((btn) => {
        btn.addEventListener('click', function () {
            document.getElementById('tte_id').value = this.dataset.id;
            document.getElementById('tte_jenis').value = this.dataset.jenis;
            document.getElementById('tte_role').value = this.dataset.role;
            document.getElementById('tte_title').innerText = this.dataset.title + ' sebagai ' + (this.dataset.role === '5' ? 'Camat' : 'Lurah');
            document.getElementById('tte_result').innerHTML = '';
            new bootstrap.Modal(document.getElementById('modalTteSuperAdmin')).show();
        });
    });

    document.getElementById('formTteSuperAdmin')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const result = document.getElementById('tte_result');
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Memproses...';
        result.innerHTML = '<div class="text-muted">Mengirim TTE...</div>';

        try {
            const response = await fetch(@json(route('admin.esign.sign')), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'Accept': 'application/json'
                },
                body: new URLSearchParams(new FormData(this)).toString()
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || data.status === 'error') {
                result.innerHTML = '<div class="alert alert-danger rounded-4">' + (data.message || 'TTE gagal diproses.') + '</div>';
            } else {
                result.innerHTML = '<div class="alert alert-success rounded-4">' + (data.message || 'TTE berhasil.') + '</div>';
                setTimeout(() => window.location.reload(), 900);
            }
        } catch (err) {
            result.innerHTML = '<div class="alert alert-danger rounded-4">TTE gagal: ' + err.message + '</div>';
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = 'Proses TTE';
        }
    });
</script>
@endpush
