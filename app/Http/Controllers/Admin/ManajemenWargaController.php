<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agama;
use App\Models\Gender;
use App\Models\Jabatan;
use App\Models\Kabko;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kewarganegaraan;
use App\Models\Pangkat;
use App\Models\Pejabat;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Provinsi;
use App\Models\Resident;
use App\Models\RtRw;
use App\Models\Skpd;
use App\Models\StatusKwn;
use App\Models\User;
use App\Models\User_role;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class ManajemenWargaController extends Controller
{
    /**
     * Admin Kelurahan hanya boleh mengelola akun di bawah wilayah kelurahannya sendiri.
     * Role yang boleh dibuat dari menu ini:
     * 2 = Warga, 3 = Lurah, 4 = Sekkel/Verifikator Kelurahan.
     */
    private array $allowedRoleIds = [2, 3, 4];

    private array $pejabatRoleToJabatan = [
        3 => 1, // Lurah
        4 => 3, // Sekkel / Verifikator Kelurahan
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'role:1']);
    }

    public function index(Request $request)
    {
        $this->ensureAdminKelurahan();
        $adminSkpd = $this->adminSkpd();

        if ($request->ajax()) {
            $query = User::query()
                ->leftJoin('user_roles', 'user_roles.id', '=', 'users.role_id')
                ->leftJoin('residents', 'residents.nik', '=', 'users.nik')
                ->whereIn('users.role_id', $this->allowedRoleIds)
                ->where('users.id_instansi', $adminSkpd->id)
                ->select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.nik',
                    'users.phone',
                    'users.role_id',
                    'users.id_rw',
                    'users.id_rt',
                    'users.updated_at',
                    'user_roles.name as role_name',
                    'residents.data as resident_data',
                ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('nik', fn ($row) => e($row->nik))
                ->addColumn('role_label', fn ($row) => '<span class="badge bg-dark rounded-pill">' . e($this->roleLabelById((int) $row->role_id, $row->role_name)) . '</span>')
                ->addColumn('alamat', function ($row) {
                    if ((int) $row->role_id !== 2) {
                        return '-';
                    }

                    $data = decode_json_data($row->resident_data);
                    return e($data['alamat'] ?? '-');
                })
                ->addColumn('rt_rw', function ($row) {
                    if ((int) $row->role_id !== 2) {
                        return '-';
                    }

                    return 'RT ' . e($row->id_rt ?: '-') . ' / RW ' . e($row->id_rw ?: '-');
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.manajemen-warga.edit', $row->id);
                    $deleteUrl = route('admin.manajemen-warga.destroy', $row->id);
                    $csrf = csrf_token();

                    return '<div class="d-flex gap-1">'
                        . '<a href="' . e($editUrl) . '" class="btn btn-sm btn-warning rounded-3 fw-semibold" title="Edit">'
                        . '<i class="ri-edit-2-line"></i></a>'
                        . '<form method="POST" action="' . e($deleteUrl) . '" onsubmit="return confirm(\'Yakin hapus akun ini? Data Warga/Pejabat terkait ikut dibersihkan jika cocok.\');">'
                        . '<input type="hidden" name="_token" value="' . e($csrf) . '">'
                        . '<input type="hidden" name="_method" value="DELETE">'
                        . '<button class="btn btn-sm btn-danger rounded-3" type="submit" title="Hapus"><i class="ri-delete-bin-line"></i></button>'
                        . '</form></div>';
                })
                ->rawColumns(['role_label', 'action'])
                ->make(true);
        }

        return view('admin.manajemen_warga.index', [
            'title' => 'Manajemen Kontrol',
            'adminSkpd' => $adminSkpd,
            'roles' => $this->rolesForForm(),
            'hasSekkel' => $this->pejabatUserExists($adminSkpd, 4),
            'hasLurah' => $this->pejabatUserExists($adminSkpd, 3),
        ]);
    }

    public function create(Request $request)
    {
        $this->ensureAdminKelurahan();
        $adminSkpd = $this->adminSkpd();

        $requestedRole = (int) $request->get('role_id', 2);
        if (! in_array($requestedRole, $this->allowedRoleIds, true)) {
            $requestedRole = 2;
        }

        if (array_key_exists($requestedRole, $this->pejabatRoleToJabatan) && $this->pejabatUserExists($adminSkpd, $requestedRole)) {
            return redirect()->route('admin.manajemen-warga.index')
                ->with('error', $this->roleLabelById($requestedRole) . ' untuk kelurahan ini sudah ada. Silakan Edit atau Delete akun lama terlebih dahulu.');
        }

        $user = new User();
        $user->role_id = $requestedRole;
        $user->id_instansi = $adminSkpd->id;

        return view('admin.manajemen_warga.form', array_merge($this->formOptions([], $user, $adminSkpd), [
            'title' => 'Tambah Akun Manajemen Kontrol',
            'userData' => $user,
            'resident' => null,
            'penduduk' => [],
            'pejabatData' => null,
            'roles' => $this->rolesForForm(),
            'jabatans' => Jabatan::query()->orderBy('id')->get(),
            'pangkats' => Pangkat::query()->orderBy('id')->get(),
            'mode' => 'create',
            'roleJabatanMap' => $this->pejabatRoleToJabatan,
            'selectedRoleLabel' => $this->roleLabelById($requestedRole),
            'roleToken' => Crypt::encryptString((string) $requestedRole),
            'adminSkpd' => $adminSkpd,
        ]));
    }

    public function store(Request $request)
    {
        $this->ensureAdminKelurahan();
        $adminSkpd = $this->adminSkpd();
        // Role tambah akun dikunci dari token yang dibuat saat halaman create dibuka.
        // Jadi role tidak bisa diganti dari Inspect Element sebelum submit.
        $roleId = $this->lockedCreateRoleFromRequest($request);

        $this->ensureAllowedRole($roleId);

        $isWarga = $roleId === 2;
        $isPejabat = array_key_exists($roleId, $this->pejabatRoleToJabatan);

        if ($isPejabat) {
            $this->ensurePejabatUserSlotAvailable($adminSkpd, $roleId);
        }

        $this->lockRequestToAdminWilayah($request, $adminSkpd, $isWarga, $isPejabat, $roleId);

        $validated = $this->validateUserPayload($request, null, $isWarga, $isPejabat);
        $this->validateRtRwInsideAdminWilayah($validated, $adminSkpd, $isWarga);

        DB::transaction(function () use ($request, $validated, $roleId, $isWarga, $isPejabat, $adminSkpd) {
            $fotoPath = $this->storeUserFoto($request);

            $user = User::create([
                'name' => $this->upper($validated['name']),
                'nik' => $validated['nik'],
                'email' => $this->lower($validated['email'] ?? null),
                'phone' => $this->digitsOnly($validated['phone'] ?? ''),
                'role_id' => $roleId,
                'id_instansi' => $adminSkpd->id,
                'id_rw' => $isWarga ? ($validated['rw'] ?? null) : null,
                'id_rt' => $isWarga ? ($validated['rt'] ?? null) : null,
                'password' => Hash::make($validated['password']),
                'foto' => $fotoPath,
            ]);

            if ($isWarga) {
                $this->saveResident($request, $user->nik, $adminSkpd);
            }

            if ($isPejabat) {
                $this->savePejabat($request, $user, $adminSkpd, $roleId);
            }
        });

        return redirect()->route('admin.manajemen-warga.index')
            ->with('status', 'Akun berhasil ditambahkan. Wilayah otomatis terkunci pada ' . $this->skpdLabel($adminSkpd) . '.');
    }

    public function edit($id)
    {
        $this->ensureAdminKelurahan();
        $adminSkpd = $this->adminSkpd();

        $user = $this->scopedUserQuery($adminSkpd)->findOrFail($id);
        $resident = ((int) $user->role_id === 2) ? Resident::where('nik', $user->nik)->first() : null;
        $penduduk = $resident ? decode_json_data($resident->data) : [];
        $pejabat = $this->findPejabatForUser($user);

        return view('admin.manajemen_warga.form', array_merge($this->formOptions($penduduk, $user, $adminSkpd), [
            'title' => 'Edit Akun Manajemen Kontrol',
            'userData' => $user,
            'resident' => $resident,
            'penduduk' => $penduduk,
            'pejabatData' => $pejabat,
            'roles' => $this->rolesForForm(),
            'jabatans' => Jabatan::query()->orderBy('id')->get(),
            'pangkats' => Pangkat::query()->orderBy('id')->get(),
            'mode' => 'edit',
            'roleJabatanMap' => $this->pejabatRoleToJabatan,
            'selectedRoleLabel' => $this->roleLabelById((int) $user->role_id, optional($user->user_role)->name),
            'adminSkpd' => $adminSkpd,
        ]));
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdminKelurahan();
        $adminSkpd = $this->adminSkpd();

        $user = $this->scopedUserQuery($adminSkpd)->findOrFail($id);
        $oldRoleId = (int) $user->role_id;
        $oldPejabat = $this->findPejabatForUser($user);
        // Role dikunci saat edit. Walaupun request dimanipulasi dari browser,
        // sistem tetap memakai role lama milik akun tersebut.
        $roleId = $oldRoleId;

        $this->ensureAllowedRole($roleId);

        $isWarga = $roleId === 2;
        $isPejabat = array_key_exists($roleId, $this->pejabatRoleToJabatan);
        $this->lockRequestToAdminWilayah($request, $adminSkpd, $isWarga, $isPejabat, $roleId);

        $validated = $this->validateUserPayload($request, $user->id, $isWarga, $isPejabat, $user);
        $this->validateRtRwInsideAdminWilayah($validated, $adminSkpd, $isWarga);

        DB::transaction(function () use ($request, $user, $validated, $roleId, $oldRoleId, $oldPejabat, $isWarga, $isPejabat, $adminSkpd) {
            $payload = [
                'name' => $this->upper($validated['name']),
                // Warga tetap memakai NIK lama agar data residents/riwayat surat tidak pecah.
                // Sekkel/Lurah boleh ganti NIK saat edit karena dipakai untuk pergantian pejabat tanpa membuat akun baru.
                'nik' => $isPejabat ? $validated['nik'] : $user->nik,
                'email' => $this->lower($validated['email'] ?? null),
                'phone' => $this->digitsOnly($validated['phone'] ?? ''),
                'role_id' => $roleId,
                'id_instansi' => $adminSkpd->id,
                'id_rw' => $isWarga ? ($validated['rw'] ?? null) : null,
                'id_rt' => $isWarga ? ($validated['rt'] ?? null) : null,
            ];

            if ($request->filled('password')) {
                $payload['password'] = Hash::make($validated['password']);
            }

            $fotoPath = $this->storeUserFoto($request, $user);
            if ($fotoPath) {
                $payload['foto'] = $fotoPath;
            }

            $user->update($payload);

            if ($isWarga) {
                $this->saveResident($request, $user->nik, $adminSkpd);
            } else {
                Resident::where('nik', $user->nik)->delete();
            }

            if ($oldRoleId !== $roleId && $oldPejabat) {
                $oldPejabat->delete();
                $oldPejabat = null;
            }

            if ($isPejabat) {
                $this->savePejabat($request, $user, $adminSkpd, $roleId);
            } elseif ($oldPejabat) {
                $oldPejabat->delete();
            }
        });

        return redirect()->route('admin.manajemen-warga.index')
            ->with('status', 'Akun berhasil diperbarui. Wilayah tetap terkunci pada ' . $this->skpdLabel($adminSkpd) . '.');
    }

    public function destroy($id)
    {
        $this->ensureAdminKelurahan();
        $adminSkpd = $this->adminSkpd();

        $user = $this->scopedUserQuery($adminSkpd)->findOrFail($id);

        DB::transaction(function () use ($user) {
            if ((int) $user->role_id === 2) {
                Resident::where('nik', $user->nik)->delete();
            }

            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $pejabat = $this->findPejabatForUser($user);
            if ($pejabat) {
                $pejabat->delete();
            }

            $user->delete();
        });

        return back()->with('status', 'Akun berhasil dihapus dari Manajemen Kontrol.');
    }

    public function provinsiOptions(Request $request)
    {
        $adminSkpd = $this->adminSkpd();
        $provinsiId = $adminSkpd->kelurahan->kode_provinsi ?? '35';

        return $this->wilayahResponse(Provinsi::query()->where('id', $provinsiId), $request);
    }

    public function kabkoOptions(Request $request)
    {
        $adminSkpd = $this->adminSkpd();
        $kabkoId = $adminSkpd->kelurahan->kode_kabkota ?? '35.71';

        return $this->wilayahResponse(Kabko::query()->where('id', $kabkoId), $request);
    }

    public function kecamatanOptions(Request $request)
    {
        $adminSkpd = $this->adminSkpd();
        $kecamatanId = $this->adminKecamatanId($adminSkpd);

        return $this->wilayahResponse(Kecamatan::query()->where('id', $kecamatanId), $request);
    }

    public function kelurahanOptions(Request $request)
    {
        $adminSkpd = $this->adminSkpd();

        return $this->wilayahResponse(Kelurahan::query()->where('id', $adminSkpd->id_region), $request);
    }

    public function rtRwOptions(Request $request)
    {
        $adminSkpd = $this->adminSkpd();
        $selectedRw = trim((string) $request->rw);

        $items = RtRw::query()
            ->where('kode_kelurahan', $adminSkpd->id_region)
            ->orderByRaw('CAST(rw AS UNSIGNED) ASC')
            ->orderByRaw('CAST(rt AS UNSIGNED) ASC')
            ->get(['rw', 'rt']);

        $rtItems = $selectedRw !== '' ? $items->where('rw', $selectedRw) : $items;

        return response()->json([
            'rws' => $items->pluck('rw')->filter()->unique()->values()->map(fn ($rw) => ['id' => (string) $rw, 'text' => 'RW ' . $rw])->values(),
            'rts' => $rtItems->pluck('rt')->filter()->unique()->values()->map(fn ($rt) => ['id' => (string) $rt, 'text' => 'RT ' . $rt])->values(),
        ]);
    }

    private function validateUserPayload(Request $request, ?int $ignoreUserId, bool $isWarga, bool $isPejabat, ?User $existingUser = null): array
    {
        $passwordRule = $ignoreUserId ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'];

        if ($ignoreUserId) {
            // Saat edit Warga, NIK dikunci karena terhubung dengan data residents dan riwayat surat.
            // Saat edit Sekkel/Lurah, NIK boleh diganti untuk pergantian pejabat tanpa delete akun lama.
            $nikRule = $isPejabat
                ? ['required', 'regex:/^[0-9]{16}$/', Rule::unique('users', 'nik')->ignore($ignoreUserId)]
                : ['nullable', 'regex:/^[0-9]{16}$/'];
        } else {
            $nikRule = ['required', 'regex:/^[0-9]{16}$/', Rule::unique('users', 'nik')];
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'nik' => $nikRule,
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($ignoreUserId)],
            'phone' => ['required', 'regex:/^[0-9]+$/', 'max:20'],
            'role_id' => ['required', 'integer', Rule::in($this->allowedRoleIds)],
            'id_instansi' => ['required', 'integer', 'exists:skpds,id'],
            'password' => $passwordRule,
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        if ($isWarga) {
            $rules = array_merge($rules, [
                'kk' => ['required', 'regex:/^[0-9]{16}$/'],
                'gender' => ['nullable', 'string'],
                'status_kwn' => ['nullable', 'string'],
                'kewarganegaraan' => ['nullable', 'string'],
                'tempat_lhr' => ['required', 'string', 'max:100'],
                'tgl_lhr' => ['nullable', 'date'],
                'agama' => ['nullable', 'string'],
                'pendidikan' => ['nullable', 'string'],
                'pekerjaan' => ['nullable', 'string'],
                'provinsi' => ['required', 'string'],
                'kabko' => ['required', 'string'],
                'kecamatan' => ['required', 'string'],
                'kelurahan' => ['required', 'string', Rule::in([(string) $this->adminSkpd()->id_region])],
                'rw' => ['required', 'regex:/^[0-9]+$/', 'max:10'],
                'rt' => ['required', 'regex:/^[0-9]+$/', 'max:10'],
                'alamat' => ['required', 'string', 'max:255'],
            ]);
        }

        if ($isPejabat) {
            $rules = array_merge($rules, [
                'nip' => ['required', 'regex:/^[0-9]{18}$/'],
                'id_jabatan' => ['required', 'integer', 'exists:jabatans,id'],
                'id_pangkat' => ['required', 'integer', 'exists:pangkats,id'],
            ]);
        }

        $messages = [
            'nik.regex' => 'NIK wajib angka dan tepat 16 digit.',
            'kk.regex' => 'Nomor KK wajib angka dan tepat 16 digit.',
            'phone.regex' => 'Nomor HP hanya boleh angka.',
            'nip.regex' => 'NIP wajib angka dan tepat 18 digit.',
            'email.email' => 'Format email tidak valid.',
            'kelurahan.in' => 'Kelurahan tidak sesuai dengan wilayah admin login.',
            'rw.required' => 'RW wajib dipilih dari daftar wilayah admin.',
            'rt.required' => 'RT wajib dipilih dari daftar wilayah admin.',
        ];

        $validated = $request->validate($rules, $messages);

        if ($ignoreUserId && $existingUser && ! $isPejabat) {
            $validated['nik'] = $existingUser->nik;
        }

        return $validated;
    }

    private function lockRequestToAdminWilayah(Request $request, Skpd $adminSkpd, bool $isWarga, bool $isPejabat, int $roleId): void
    {
        $request->merge([
            'role_id' => $roleId,
            'id_instansi' => $adminSkpd->id,
        ]);

        if ($isWarga) {
            $request->merge([
                'provinsi' => $adminSkpd->kelurahan->kode_provinsi ?? '35',
                'kabko' => $adminSkpd->kelurahan->kode_kabkota ?? '35.71',
                'kecamatan' => $this->adminKecamatanId($adminSkpd),
                'kelurahan' => (string) $adminSkpd->id_region,
            ]);
        }

        if ($isPejabat) {
            $request->merge([
                'id_jabatan' => $this->pejabatRoleToJabatan[$roleId],
            ]);
        }
    }

    private function validateRtRwInsideAdminWilayah(array $validated, Skpd $adminSkpd, bool $isWarga): void
    {
        if (! $isWarga) {
            return;
        }

        $exists = RtRw::query()
            ->where('kode_kelurahan', $adminSkpd->id_region)
            ->where('rw', $validated['rw'])
            ->where('rt', $validated['rt'])
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'rt' => 'RT/RW tidak sesuai dengan wilayah admin login.',
            ]);
        }
    }

    private function saveResident(Request $request, string $lockedNik, Skpd $adminSkpd): void
    {
        $kecamatanId = $this->adminKecamatanId($adminSkpd);

        $datapemohon = resident_data_order([
            'kk' => $request->kk,
            'name' => $this->upper($request->name),
            'gender' => $request->gender,
            'gender_nm' => $this->nama(Gender::class, $request->gender),
            'status_kwn' => $request->status_kwn,
            'status_kwn_nm' => $this->nama(StatusKwn::class, $request->status_kwn),
            'kewarganegaraan' => $request->kewarganegaraan,
            'kewarganegaraan_nm' => $this->nama(Kewarganegaraan::class, $request->kewarganegaraan),
            'tempat_lhr' => $this->upper($request->tempat_lhr),
            'tgl_lhr' => $request->tgl_lhr,
            'agama' => $request->agama,
            'agama_nm' => $this->nama(Agama::class, $request->agama),
            'pendidikan' => $request->pendidikan,
            'pendidikan_nm' => $this->nama(Pendidikan::class, $request->pendidikan),
            'pekerjaan' => $request->pekerjaan,
            'pekerjaan_nm' => $this->nama(Pekerjaan::class, $request->pekerjaan),
            'provinsi' => $adminSkpd->kelurahan->kode_provinsi ?? '35',
            'provinsi_nm' => $this->nama(Provinsi::class, $adminSkpd->kelurahan->kode_provinsi ?? '35') ?: 'JAWA TIMUR',
            'kabko' => $adminSkpd->kelurahan->kode_kabkota ?? '35.71',
            'kabko_nm' => $this->nama(Kabko::class, $adminSkpd->kelurahan->kode_kabkota ?? '35.71') ?: 'KOTA KEDIRI',
            'kecamatan' => $kecamatanId,
            'kecamatan_nm' => $this->nama(Kecamatan::class, $kecamatanId),
            'kelurahan' => (string) $adminSkpd->id_region,
            'kelurahan_nm' => $this->nama(Kelurahan::class, $adminSkpd->id_region),
            'rw' => $request->rw,
            'rw_nm' => $request->rw ? 'RW ' . $request->rw : null,
            'rt' => $request->rt,
            'rt_nm' => $request->rt ? 'RT ' . $request->rt : null,
            'alamat' => $this->upper($request->alamat),
        ]);

        Resident::updateOrCreate(
            ['nik' => $lockedNik],
            [
                'kk' => $request->kk,
                'data' => $datapemohon,
            ]
        );

        User::where('nik', $lockedNik)->update([
            'id_instansi' => $adminSkpd->id,
            'id_rw' => $request->rw,
            'id_rt' => $request->rt,
        ]);
    }

    private function savePejabat(Request $request, User $user, Skpd $adminSkpd, int $roleId): void
    {
        $payload = [
            'id_skpd' => $adminSkpd->id,
            'nip' => $this->digitsOnly($request->nip),
            'nama' => $this->upper($request->name),
            'id_jabatan' => $this->pejabatRoleToJabatan[$roleId],
            'id_pangkat' => (int) $request->id_pangkat,
        ];

        $pejabat = $request->filled('pejabat_id') ? Pejabat::find($request->pejabat_id) : $this->findPejabatForUser($user);

        if (! $pejabat) {
            $pejabat = Pejabat::query()
                ->where('id_skpd', $adminSkpd->id)
                ->where('id_jabatan', $payload['id_jabatan'])
                ->latest('id')
                ->first();
        }

        if ($pejabat && (int) $pejabat->id_skpd === (int) $adminSkpd->id) {
            $pejabat->update($payload);
        } else {
            Pejabat::updateOrCreate(
                ['id_skpd' => $adminSkpd->id, 'id_jabatan' => $payload['id_jabatan']],
                $payload
            );
        }
    }

    private function findPejabatForUser(User $user): ?Pejabat
    {
        $jabatanId = $this->pejabatRoleToJabatan[(int) $user->role_id] ?? null;
        if (! $jabatanId) {
            return null;
        }

        return Pejabat::query()
            ->where('id_skpd', $user->id_instansi)
            ->where('id_jabatan', $jabatanId)
            ->where(function ($q) use ($user) {
                $q->where('nama', $user->name)
                    ->orWhere('nama', $this->upper($user->name))
                    ->orWhere('nip', $user->nik);
            })
            ->latest('id')
            ->first();
    }

    private function formOptions(array $penduduk, User $user, Skpd $adminSkpd): array
    {
        $kecamatanId = $this->adminKecamatanId($adminSkpd);
        $selected = [
            'provinsi' => $adminSkpd->kelurahan->kode_provinsi ?? '35',
            'kabko' => $adminSkpd->kelurahan->kode_kabkota ?? '35.71',
            'kecamatan' => $kecamatanId,
            'kelurahan' => (string) $adminSkpd->id_region,
            'rw' => old('rw', $penduduk['rw'] ?? $user->id_rw),
            'rt' => old('rt', $penduduk['rt'] ?? $user->id_rt),
        ];

        return [
            'genders' => Gender::orderBy('nama')->get(),
            'statusKwns' => StatusKwn::orderBy('nama')->get(),
            'kewarganegaraans' => Kewarganegaraan::orderBy('nama')->get(),
            'agamas' => Agama::orderBy('nama')->get(),
            'pendidikans' => Pendidikan::orderBy('nama')->get(),
            'pekerjaans' => Pekerjaan::orderBy('nama')->get(),
            'selectedWilayah' => $this->selectedWilayahLabels($selected),
        ];
    }

    private function selectedWilayahLabels(array $selected): array
    {
        return [
            'provinsi' => ['id' => $selected['provinsi'], 'text' => $this->nama(Provinsi::class, $selected['provinsi']) ?: 'JAWA TIMUR'],
            'kabko' => ['id' => $selected['kabko'], 'text' => $this->nama(Kabko::class, $selected['kabko']) ?: 'KOTA KEDIRI'],
            'kecamatan' => ['id' => $selected['kecamatan'], 'text' => $this->nama(Kecamatan::class, $selected['kecamatan'])],
            'kelurahan' => ['id' => $selected['kelurahan'], 'text' => $this->nama(Kelurahan::class, $selected['kelurahan'])],
            'rw' => $selected['rw'],
            'rt' => $selected['rt'],
        ];
    }

    private function wilayahResponse($query, Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $items = $query
            ->when($q !== '', fn ($query) => $query->where('nama', 'like', '%' . $q . '%'))
            ->orderBy('id')
            ->limit(30)
            ->get(['id', 'nama'])
            ->map(fn ($item) => ['id' => (string) $item->id, 'text' => $item->nama]);

        return response()->json(['results' => $items]);
    }

    private function scopedUserQuery(Skpd $adminSkpd)
    {
        return User::query()
            ->whereIn('role_id', $this->allowedRoleIds)
            ->where('id_instansi', $adminSkpd->id);
    }

    private function ensureAdminKelurahan(): void
    {
        abort_unless(auth()->check() && (int) auth()->user()->role_id === 1, 403);
    }

    private function lockedCreateRoleFromRequest(Request $request): int
    {
        try {
            $roleId = (int) Crypt::decryptString((string) $request->role_token);
        } catch (DecryptException $e) {
            throw ValidationException::withMessages([
                'role_id' => 'Jenis/Role Akun tidak valid. Silakan kembali dan pilih tombol tambah akun yang sesuai.',
            ]);
        }

        return $roleId;
    }

    private function ensureAllowedRole(int $roleId): void
    {
        abort_unless(in_array($roleId, $this->allowedRoleIds, true), 403, 'Role ini tidak boleh dibuat dari menu Admin Kelurahan.');
    }

    private function adminSkpd(): Skpd
    {
        $skpd = Skpd::with(['kelurahan', 'kecamatan', 'rtrw'])->find(auth()->user()->id_instansi);

        abort_if(! $skpd, 403, 'Akun admin belum memiliki id_instansi yang valid.');
        abort_if(strlen((string) $skpd->id_region) !== 13, 403, 'Menu ini hanya untuk Admin Kelurahan.');

        return $skpd;
    }

    private function adminKecamatanId(Skpd $adminSkpd): string
    {
        return (string) ($adminSkpd->id_kec ?: ($adminSkpd->kelurahan->kode_kecamatan ?? substr((string) $adminSkpd->id_region, 0, 8)));
    }

    private function rolesForForm()
    {
        return User_role::query()
            ->whereIn('id', $this->allowedRoleIds)
            ->orderBy('id')
            ->get()
            ->map(function ($role) {
                $role->display_name = $this->roleLabelById((int) $role->id, $role->name);
                return $role;
            });
    }

    private function roleLabelById(int $roleId, ?string $name = null): string
    {
        return match ($roleId) {
            2 => 'Warga',
            3 => 'Lurah',
            4 => 'Sekkel',
            default => $this->roleLabel($name),
        };
    }

    private function roleLabel(?string $name): string
    {
        $normalized = mb_strtolower(trim((string) $name), 'UTF-8');

        if ($normalized === 'client') {
            return 'Warga';
        }

        if (str_contains($normalized, 'verifikator')) {
            return 'Sekkel';
        }

        return (string) $name;
    }

    private function pejabatUserExists(Skpd $adminSkpd, int $roleId, ?int $ignoreUserId = null): bool
    {
        if (! array_key_exists($roleId, $this->pejabatRoleToJabatan)) {
            return false;
        }

        return User::query()
            ->where('id_instansi', $adminSkpd->id)
            ->where('role_id', $roleId)
            ->when($ignoreUserId, fn ($query) => $query->where('id', '!=', $ignoreUserId))
            ->exists();
    }

    private function ensurePejabatUserSlotAvailable(Skpd $adminSkpd, int $roleId, ?int $ignoreUserId = null): void
    {
        if (! $this->pejabatUserExists($adminSkpd, $roleId, $ignoreUserId)) {
            return;
        }

        $label = $this->roleLabelById($roleId);

        throw ValidationException::withMessages([
            'role_id' => $label . ' untuk kelurahan ini sudah ada. Silakan Edit akun yang ada, atau Delete dulu jika ingin membuat akun baru.',
        ]);
    }

    private function storeUserFoto(Request $request, ?User $user = null): ?string
    {
        if (! $request->hasFile('foto')) {
            return null;
        }

        if ($user && $user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        return $request->file('foto')->store('users/foto', 'public');
    }

    private function skpdLabel(Skpd $skpd): string
    {
        $kecamatanNama = optional($skpd->kecamatan)->nama ?: $this->nama(Kecamatan::class, $this->adminKecamatanId($skpd));
        $kelurahanNama = optional($skpd->kelurahan)->nama ?: $skpd->nama;

        return 'Kec. ' . ($kecamatanNama ?: '-') . ' / Kel. ' . ($kelurahanNama ?: '-');
    }

    private function nama(string $modelClass, $id): ?string
    {
        if ($id === null || $id === '') {
            return null;
        }

        $model = $modelClass::find($id);
        return $model->nama ?? null;
    }

    private function digitsOnly(?string $value): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $value);
        return $digits === '' ? null : $digits;
    }

    private function upper(?string $value): ?string
    {
        return $value === null ? null : mb_strtoupper(trim($value), 'UTF-8');
    }

    private function lower(?string $value): ?string
    {
        return $value === null ? null : mb_strtolower(trim($value), 'UTF-8');
    }
}
