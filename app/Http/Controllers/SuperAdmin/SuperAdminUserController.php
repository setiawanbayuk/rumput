<?php

namespace App\Http\Controllers\SuperAdmin;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperAdminUserController extends Controller
{
    private array $pejabatRoleToJabatan = [
        1 => 5, // Admin/Kasi Pemerintahan
        3 => 1, // Lurah
        4 => 3, // Sekkel/Verifikator Kelurahan
        5 => 2, // Camat
        6 => 4, // Sekcam/Verifikator Kecamatan
    ];

    public function index(Request $request)
    {
        $query = User::with(['user_role', 'skpd'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $keyword = trim((string) $request->q);
                $q->where(function ($qq) use ($keyword) {
                    $qq->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%')
                        ->orWhere('nik', 'like', '%' . $keyword . '%')
                        ->orWhere('phone', 'like', '%' . $keyword . '%');
                });
            })
            ->when($request->filled('role_id'), fn ($q) => $q->where('role_id', $request->role_id))
            ->when($request->filled('id_instansi'), fn ($q) => $q->where('id_instansi', $request->id_instansi))
            ->orderBy('role_id')
            ->orderBy('name');

        return view('super_admin.users.index', [
            'title' => 'Kontrol Semua Akun',
            'users' => $query->paginate(15)->withQueryString(),
            'roles' => $this->rolesForView(),
            'skpds' => $this->skpdOptions(false),
        ]);
    }

    public function create(Request $request)
    {
        $user = new User();
        $requestedRole = (int) $request->get('role_id', 2);
        $user->role_id = $requestedRole === 7 ? 2 : $requestedRole;

        return view('super_admin.users.form', array_merge($this->formOptions([], $user), [
            'title' => 'Tambah Akun',
            'userData' => $user,
            'resident' => null,
            'penduduk' => [],
            'pejabatData' => null,
            'roles' => $this->rolesForForm($user->role_id),
            'skpds' => $this->skpdOptions(false),
            'kelurahanSkpds' => $this->skpdOptions(true),
            'kelurahanMeta' => $this->kelurahanMeta(),
            'jabatans' => Jabatan::query()->orderBy('id')->get(),
            'pangkats' => Pangkat::query()->orderBy('id')->get(),
            'mode' => 'create',
            'roleJabatanMap' => $this->pejabatRoleToJabatan,
        ]));
    }

    public function store(Request $request)
    {
        $roleId = (int) $request->role_id;
        if ($roleId === 7) {
            return back()->withInput()->with('error', 'Akun Super Admin tidak dibuat manual dari aplikasi. Gunakan data resmi dari database agar aman.');
        }

        $isWarga = $roleId === 2;
        $isPejabat = array_key_exists($roleId, $this->pejabatRoleToJabatan);
        $this->prepareWargaInstansi($request, $isWarga);

        $validated = $this->validateUserPayload($request, null, $isWarga, $isPejabat);

        DB::transaction(function () use ($request, $validated, $roleId, $isWarga, $isPejabat) {
            $user = User::create([
                'name' => $this->upper($validated['name']),
                'nik' => $validated['nik'],
                'email' => $this->lower($validated['email']),
                'phone' => $this->digitsOnly($validated['phone'] ?? ''),
                'role_id' => $roleId,
                'id_instansi' => $validated['id_instansi'],
                'id_rw' => $isWarga ? ($validated['rw'] ?? null) : null,
                'id_rt' => $isWarga ? ($validated['rt'] ?? null) : null,
                'password' => Hash::make($validated['password']),
            ]);

            if ($isWarga) {
                $this->saveResident($request, $user->nik);
            }

            if ($isPejabat) {
                $this->savePejabat($request, $user);
            }
        });

        return redirect()->route('super-admin.users.index')
            ->with('status', 'Akun baru berhasil dibuat dan data terkait berhasil disinkronkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $resident = ((int) $user->role_id === 2) ? Resident::where('nik', $user->nik)->first() : null;
        $penduduk = $resident ? decode_json_data($resident->data) : [];
        $pejabat = $this->findPejabatForUser($user);

        return view('super_admin.users.form', array_merge($this->formOptions($penduduk, $user), [
            'title' => 'Edit Akun',
            'userData' => $user,
            'resident' => $resident,
            'penduduk' => $penduduk,
            'pejabatData' => $pejabat,
            'roles' => $this->rolesForForm((int) $user->role_id),
            'skpds' => $this->skpdOptions(false),
            'kelurahanSkpds' => $this->skpdOptions(true),
            'kelurahanMeta' => $this->kelurahanMeta(),
            'jabatans' => Jabatan::query()->orderBy('id')->get(),
            'pangkats' => Pangkat::query()->orderBy('id')->get(),
            'mode' => 'edit',
            'roleJabatanMap' => $this->pejabatRoleToJabatan,
        ]));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roleId = (int) $request->role_id;

        if ((int) $user->role_id !== 7 && $roleId === 7) {
            return back()->withInput()->with('error', 'Role Super Admin tidak boleh diberikan lewat form aplikasi. Gunakan database resmi jika benar-benar diperlukan.');
        }

        $isWarga = $roleId === 2;
        $isPejabat = array_key_exists($roleId, $this->pejabatRoleToJabatan);
        $this->prepareWargaInstansi($request, $isWarga);

        $validated = $this->validateUserPayload($request, $user->id, $isWarga, $isPejabat, $user);

        DB::transaction(function () use ($request, $user, $validated, $roleId, $isWarga, $isPejabat) {
            $payload = [
                'name' => $this->upper($validated['name']),
                // NIK sengaja dikunci pada mode edit. Jika NIK salah, hapus akun lalu buat ulang.
                'nik' => $user->nik,
                'email' => $this->lower($validated['email']),
                'phone' => $this->digitsOnly($validated['phone'] ?? ''),
                'role_id' => $roleId,
                'id_instansi' => $validated['id_instansi'],
                'id_rw' => $isWarga ? ($validated['rw'] ?? null) : null,
                'id_rt' => $isWarga ? ($validated['rt'] ?? null) : null,
            ];

            if ($request->filled('password')) {
                $payload['password'] = Hash::make($validated['password']);
            }

            $user->update($payload);

            if ($isWarga) {
                $this->saveResident($request, $user->nik);
            } else {
                Resident::where('nik', $user->nik)->delete();
            }

            if ($isPejabat) {
                $this->savePejabat($request, $user);
            }
        });

        return redirect()->route('super-admin.users.index')
            ->with('status', 'Akun berhasil diperbarui. Untuk Warga, tabel users dan residents ikut tersinkron.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ((int) $user->id === (int) auth()->id()) {
            return back()->with('error', 'Akun yang sedang login tidak boleh dihapus.');
        }

        if ((int) $user->role_id === 7) {
            return back()->with('error', 'Akun Super Admin tidak boleh dihapus dari aplikasi. Hapus langsung dari database hanya jika benar-benar diperlukan.');
        }

        DB::transaction(function () use ($user) {
            if ((int) $user->role_id === 2) {
                Resident::where('nik', $user->nik)->delete();
            }

            $pejabat = $this->findPejabatForUser($user);
            if ($pejabat) {
                $pejabat->delete();
            }

            $user->delete();
        });

        return back()->with('status', 'Akun berhasil dihapus. Data Warga/Pejabat terkait ikut dibersihkan jika cocok.');
    }

    public function provinsiOptions(Request $request)
    {
        return $this->wilayahResponse(Provinsi::query(), $request);
    }

    public function kabkoOptions(Request $request)
    {
        return $this->wilayahResponse(Kabko::query()
            ->when($request->provinsi, fn ($q) => $q->where('kode_provinsi', $request->provinsi)), $request);
    }

    public function kecamatanOptions(Request $request)
    {
        return $this->wilayahResponse(Kecamatan::query()
            ->when($request->kabko, fn ($q) => $q->where('kode_kabkota', $request->kabko)), $request);
    }

    public function kelurahanOptions(Request $request)
    {
        return $this->wilayahResponse(Kelurahan::query()
            ->where('kode_kabkota', '35.71')
            ->when($request->kecamatan, fn ($q) => $q->where('kode_kecamatan', $request->kecamatan)), $request);
    }

    public function rtRwOptions(Request $request)
    {
        $kodeKelurahan = (string) $request->kelurahan;
        if ($kodeKelurahan !== '' && ctype_digit($kodeKelurahan)) {
            $kodeKelurahan = (string) optional(Skpd::find($kodeKelurahan))->id_region;
        }

        $items = RtRw::query()
            ->when($kodeKelurahan !== '', fn ($q) => $q->where('kode_kelurahan', $kodeKelurahan))
            ->orderByRaw('CAST(rw AS UNSIGNED) ASC')
            ->orderByRaw('CAST(rt AS UNSIGNED) ASC')
            ->get(['rw', 'rt']);

        $selectedRw = trim((string) $request->rw);
        $rtItems = $selectedRw !== ''
            ? $items->where('rw', $selectedRw)
            : $items;

        return response()->json([
            'rws' => $items->pluck('rw')->filter()->unique()->values()->map(fn ($rw) => ['id' => (string) $rw, 'text' => 'RW ' . $rw])->values(),
            'rts' => $rtItems->pluck('rt')->filter()->unique()->values()->map(fn ($rt) => ['id' => (string) $rt, 'text' => 'RT ' . $rt])->values(),
        ]);
    }

    private function validateUserPayload(Request $request, ?int $ignoreUserId, bool $isWarga, bool $isPejabat, ?User $existingUser = null): array
    {
        $passwordRule = $ignoreUserId ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'];
        $nikRule = $ignoreUserId
            ? ['nullable', 'regex:/^[0-9]{16}$/']
            : ['required', 'regex:/^[0-9]{16}$/', Rule::unique('users', 'nik')];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'nik' => $nikRule,
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($ignoreUserId)],
            'phone' => ['required', 'regex:/^[0-9]+$/', 'max:20'],
            'role_id' => ['required', 'integer', 'exists:user_roles,id'],
            'id_instansi' => ['required', 'integer', 'exists:skpds,id'],
            'password' => $passwordRule,
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
                'provinsi' => ['nullable', 'string'],
                'kabko' => ['nullable', 'string'],
                'kecamatan' => ['nullable', 'string'],
                'kelurahan' => ['required', 'string', 'exists:kelurahans,id'],
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
            'kelurahan.required' => 'Kelurahan wajib dipilih agar Instansi/SKPD otomatis terisi.',
            'rw.required' => 'RW wajib dipilih dari daftar database.',
            'rt.required' => 'RT wajib dipilih dari daftar database.',
        ];

        $validated = $request->validate($rules, $messages);

        if ($ignoreUserId && $existingUser) {
            $validated['nik'] = $existingUser->nik;
        }

        return $validated;
    }

    private function prepareWargaInstansi(Request $request, bool $isWarga): void
    {
        if (! $isWarga) {
            return;
        }

        $selectedSkpd = $this->skpdFromKelurahan($request->kelurahan);
        if (! $selectedSkpd) {
            return;
        }

        $request->merge([
            'id_instansi' => $selectedSkpd->id,
            'provinsi' => '35',
            'kabko' => '35.71',
            'kecamatan' => $selectedSkpd->id_kec ?: substr((string) $selectedSkpd->id_region, 0, 8),
        ]);
    }

    private function saveResident(Request $request, string $lockedNik): void
    {
        $selectedSkpd = $this->skpdFromKelurahan($request->kelurahan);
        $targetInstansi = $selectedSkpd->id ?? $request->id_instansi;
        $kecamatanId = $selectedSkpd->id_kec ?: ($request->kecamatan ?: substr((string) $request->kelurahan, 0, 8));

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
            'provinsi' => '35',
            'provinsi_nm' => 'JAWA TIMUR',
            'kabko' => '35.71',
            'kabko_nm' => 'KOTA KEDIRI',
            'kecamatan' => $kecamatanId,
            'kecamatan_nm' => $this->nama(Kecamatan::class, $kecamatanId),
            'kelurahan' => $request->kelurahan,
            'kelurahan_nm' => $this->nama(Kelurahan::class, $request->kelurahan),
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
            'id_instansi' => $targetInstansi,
            'id_rw' => $request->rw,
            'id_rt' => $request->rt,
        ]);
    }

    private function savePejabat(Request $request, User $user): void
    {
        $payload = [
            'id_skpd' => $request->id_instansi,
            'nip' => $this->digitsOnly($request->nip),
            'nama' => $this->upper($request->name),
            'id_jabatan' => (int) $request->id_jabatan,
            'id_pangkat' => (int) $request->id_pangkat,
        ];

        $pejabat = $request->filled('pejabat_id') ? Pejabat::find($request->pejabat_id) : $this->findPejabatForUser($user);

        if ($pejabat) {
            $pejabat->update($payload);
        } else {
            Pejabat::updateOrCreate(['nip' => $payload['nip']], $payload);
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
                  ->orWhere('nama', $this->upper($user->name));
            })
            ->latest('id')
            ->first();
    }

    private function formOptions(array $penduduk, User $user): array
    {
        $skpd = Skpd::with(['kelurahan', 'kecamatan', 'rtrw'])->find($user->id_instansi)
            ?: Skpd::with(['kelurahan', 'kecamatan', 'rtrw'])->whereRaw('CHAR_LENGTH(id_region) = 13')->orderBy('id')->first();

        $selectedKelurahan = old('kelurahan', $penduduk['kelurahan'] ?? optional($skpd?->kelurahan)->id);
        if (! $selectedKelurahan && $skpd && strlen((string) $skpd->id_region) === 13) {
            $selectedKelurahan = $skpd->id_region;
        }

        $selected = [
            'provinsi' => old('provinsi', $penduduk['provinsi'] ?? '35'),
            'kabko' => old('kabko', $penduduk['kabko'] ?? '35.71'),
            'kecamatan' => old('kecamatan', $penduduk['kecamatan'] ?? ($skpd->id_kec ?? optional($skpd?->kelurahan)->kode_kecamatan)),
            'kelurahan' => $selectedKelurahan,
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
            ->limit(200)
            ->get(['id', 'nama'])
            ->map(fn ($item) => ['id' => (string) $item->id, 'text' => $item->nama]);

        return response()->json(['results' => $items]);
    }

    private function skpdFromKelurahan($kelurahanId): ?Skpd
    {
        if (! $kelurahanId) {
            return null;
        }

        return Skpd::with(['kelurahan', 'kecamatan'])->where('id_region', $kelurahanId)->first();
    }

    private function nama(string $modelClass, $id): ?string
    {
        if ($id === null || $id === '') {
            return null;
        }

        $model = $modelClass::find($id);
        return $model->nama ?? null;
    }

    private function rolesForView()
    {
        return User_role::query()->orderBy('id')->get()->map(function ($role) {
            $role->display_name = $this->roleLabel($role->name);
            return $role;
        });
    }

    private function rolesForForm(int $currentRole = 2)
    {
        return User_role::query()
            ->when($currentRole !== 7, fn ($q) => $q->where('id', '<>', 7))
            ->orderBy('id')
            ->get()
            ->map(function ($role) {
                $role->display_name = $this->roleLabel($role->name);
                return $role;
            });
    }

    private function roleLabel(?string $name): string
    {
        return trim((string) $name) === 'Client' ? 'Warga' : (string) $name;
    }

    private function skpdOptions(bool $kelurahanOnly = false)
    {
        return Skpd::with(['kelurahan', 'kecamatan'])
            ->when($kelurahanOnly, fn ($q) => $q->whereRaw('CHAR_LENGTH(id_region) = 13'))
            ->orderBy('id')
            ->get()
            ->map(function ($skpd) {
                $skpd->super_label = $this->skpdLabel($skpd);
                $skpd->kecamatan_nama = optional($skpd->kecamatan)->nama ?: $this->nama(Kecamatan::class, $skpd->id_kec);
                $skpd->kelurahan_nama = optional($skpd->kelurahan)->nama ?: $skpd->nama;
                return $skpd;
            });
    }

    private function kelurahanMeta(): array
    {
        return $this->skpdOptions(true)->mapWithKeys(function ($skpd) {
            $kodeKelurahan = (string) $skpd->id_region;
            $kodeKecamatan = $skpd->id_kec ?: substr($kodeKelurahan, 0, 8);
            return [
                $kodeKelurahan => [
                    'skpd_id' => (int) $skpd->id,
                    'skpd_label' => $skpd->super_label,
                    'kecamatan_id' => $kodeKecamatan,
                    'kecamatan_nama' => $skpd->kecamatan_nama,
                    'kelurahan_nama' => $skpd->kelurahan_nama,
                ],
            ];
        })->toArray();
    }

    private function skpdLabel(Skpd $skpd): string
    {
        $idRegion = (string) $skpd->id_region;
        $kecamatanNama = optional($skpd->kecamatan)->nama ?: $this->nama(Kecamatan::class, $skpd->id_kec);
        $kelurahanNama = optional($skpd->kelurahan)->nama ?: $skpd->nama;

        if (strlen($idRegion) === 13) {
            return $skpd->id . ' - Kec. ' . ($kecamatanNama ?: '-') . ' / Kel. ' . $kelurahanNama;
        }

        if (strlen($idRegion) === 8) {
            return $skpd->id . ' - Kec. ' . ($kecamatanNama ?: $skpd->nama);
        }

        return $skpd->id . ' - ' . $skpd->nama;
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
