@extends('layouts.main')

@section('content')
    <style>
        .super-card { border: 1px solid #eef0f4; border-radius: 22px; box-shadow: 0 16px 35px rgba(15, 23, 42, .06); }
        .super-pill { display:inline-flex; align-items:center; border-radius:999px; padding:6px 11px; background:#f8fafc; border:1px solid #eef2f7; font-size:12px; font-weight:800; color:#64748b; }
        .table thead th { font-size: 12px; text-transform: uppercase; color:#64748b; letter-spacing:.04em; }
    </style>

    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <span class="super-pill mb-2">SUPER ADMIN / AKUN</span>
                <h3 class="fw-bold mb-1">Kontrol Semua Akun</h3>
                <div class="text-muted">Kelola Warga, Admin Kelurahan, Sekkel, Lurah, Sekcam, Camat, dan Operator.</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('super-admin.users.create', ['role_id' => 2]) }}" class="btn btn-dark rounded-4 fw-semibold">
                    <i class="ri-user-add-line me-1"></i> Tambah Warga
                </a>
                <a href="{{ route('super-admin.users.create', ['role_id' => 1]) }}" class="btn btn-outline-dark rounded-4 fw-semibold">
                    <i class="ri-user-star-line me-1"></i> Tambah User Kelurahan/Kecamatan
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success rounded-4">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
        @endif

        <div class="card super-card mb-3">
            <div class="card-body p-4">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-12">
                        <label class="form-label fw-semibold">Pencarian</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-4" placeholder="Nama, email, NIK, atau HP">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Filter Role</label>
                        <select name="role_id" class="form-select rounded-4">
                            <option value="">Semua</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected((string) request('role_id') === (string) $role->id)>{{ $role->id }} - {{ $role->display_name ?? $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold">Instansi/SKPD</label>
                        <select name="id_instansi" class="form-select rounded-4">
                            <option value="">Semua</option>
                            @foreach ($skpds as $skpd)
                                <option value="{{ $skpd->id }}" @selected((string) request('id_instansi') === (string) $skpd->id)>{{ $skpd->super_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 d-flex gap-2">
                        <button class="btn btn-dark rounded-4 fw-semibold w-100" type="submit">Filter</button>
                        <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary rounded-4">Reset</a>
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
                                <th>ID</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Email/HP</th>
                                <th>Role</th>
                                <th>Instansi</th>
                                <th>RT/RW</th>
                                <th style="width:160px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                @php($roleName = optional($user->user_role)->name === 'Client' ? 'Warga' : (optional($user->user_role)->name ?? 'Role '.$user->role_id))
                                <tr>
                                    <td class="fw-bold">#{{ $user->id }}</td>
                                    <td class="fw-semibold">{{ $user->name }}</td>
                                    <td>{{ $user->nik }}</td>
                                    <td>
                                        <div>{{ $user->email }}</div>
                                        <small class="text-muted">{{ $user->phone ?: '-' }}</small>
                                    </td>
                                    <td><span class="badge bg-dark">{{ $roleName }}</span></td>
                                    <td>{{ optional($user->skpd)->nama ? ((optional($user->skpd)->id ?? '') . ' - ' . optional($user->skpd)->nama) : '-' }}</td>
                                    <td>RT {{ $user->id_rt ?: '-' }} / RW {{ $user->id_rw ?: '-' }}</td>
                                    <td>
                                        <a href="{{ route('super-admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning rounded-3" title="Edit">
                                            <i class="ri-edit-2-line"></i>
                                        </a>
                                        <form method="POST" action="{{ route('super-admin.users.destroy', $user->id) }}" class="d-inline" onsubmit="return confirm('Yakin hapus akun ini? Data Warga/Pejabat terkait ikut dihapus jika cocok.');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger rounded-3" type="submit" @disabled(auth()->id() === $user->id) title="Hapus">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-4">Data akun tidak ditemukan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
@endsection
