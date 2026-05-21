<?php

namespace App\Http\Controllers;

use App\Models\Agama;
use App\Models\Gender;
use App\Models\JenisSurat;
use App\Models\Kabko;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kewarganegaraan;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Provinsi;
use App\Models\Resident;
use App\Models\RtRw;
use App\Models\Skpd;
use App\Models\StatusKwn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title = "Profile";
        $user = Auth::user();

        if ((int) $user->role_id !== 2) {
            $skpd = $user->skpd;
            return view('profile.admin', compact('title', 'user', 'skpd'));
        }

        $resident = Resident::where('nik', $user->nik)->first();
        $suratQuery = JenisSurat::query();

        if (Schema::hasColumn('jenis_surats', 'is_active')) {
            $suratQuery->where('is_active', true);
        }

        $surat = $suratQuery->get();
        $penduduk = [];

        if ($resident) {
            $penduduk = decode_json_data($resident->data);
        }

        return view('profile.index', compact('title', 'resident', 'penduduk', 'surat'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        if ((int) $user->role_id !== 2) {
            if ($this->isAdminKelurahan($user)) {
                return $this->updateAdminKelurahanProfile($request, $user);
            }

            if ($this->canUpdatePhotoOnly($user)) {
                return $this->updatePejabatProfilePhoto($request, $user);
            }

            return back()->with('error', 'Profil ini tidak dapat diubah dari halaman ini.');
        }

        $request->validate([
            'nik' => ['required', 'min:16'],
            'kk' => ['required', 'min:16'],
            'name' => ['required', 'string'],
            'gender' => ['required', 'string'],
            'status_kwn' => ['required', 'string'],
            'kewarganegaraan' => ['required', 'string'],
            'tempat_lhr' => ['required', 'string'],
            'tgl_lhr' =>  ['required', 'date'],
            'agama' => ['required', 'string'],
            'pendidikan' => ['required', 'string'],
            'pekerjaan' => ['required', 'string'],
            'provinsi' => ['required', 'string'],
            'kabko' => ['required', 'string'],
            'kecamatan' => ['required', 'string'],
            'kelurahan' => ['required', 'string'],
            'rw' => ['required', 'string'],
            'rt' => ['required', 'string'],
            'alamat' => ['required', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $fotoColumn = $this->profilePhotoColumn();

            if ($fotoColumn) {
                $oldPath = $user->{$fotoColumn};

                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('avatar')->store('users/foto', 'public');
                $user->forceFill([$fotoColumn => $path])->save();
            }
        }

        $gender = Gender::find($request->gender);
        $status_kwn = StatusKwn::find($request->status_kwn);
        $kewarganegaraan = Kewarganegaraan::find($request->kewarganegaraan);
        $agama = Agama::find($request->agama);
        $pendidikan = Pendidikan::find($request->pendidikan);
        $pekerjaan = Pekerjaan::find($request->pekerjaan);
        $provinsi = Provinsi::find($request->provinsi);
        $kabko = Kabko::find($request->kabko);
        $kecamatan = Kecamatan::find($request->kecamatan);
        $kelurahan = Kelurahan::find($request->kelurahan);

        $datapemohon = resident_data_order([
            'kk' => $request->kk,
            'name' => $request->name,
            'gender' => $request->gender,
            'gender_nm' => $gender->nama ?? null,
            'status_kwn' => $request->status_kwn,
            'status_kwn_nm' => $status_kwn->nama ?? null,
            'kewarganegaraan' => $request->kewarganegaraan,
            'kewarganegaraan_nm' => $kewarganegaraan->nama ?? null,
            'tempat_lhr' => $request->tempat_lhr,
            'tgl_lhr' => $request->tgl_lhr,
            'agama' => $request->agama,
            'agama_nm' => $agama->nama ?? null,
            'pendidikan' => $request->pendidikan,
            'pendidikan_nm' => $pendidikan->nama ?? null,
            'pekerjaan' => $request->pekerjaan,
            'pekerjaan_nm' => $pekerjaan->nama ?? null,
            'provinsi' => $request->provinsi,
            'provinsi_nm' => $provinsi->nama ?? null,
            'kabko' => $request->kabko,
            'kabko_nm' => $kabko->nama ?? null,
            'kecamatan' => $request->kecamatan,
            'kecamatan_nm' => $kecamatan->nama ?? null,
            'kelurahan' => $request->kelurahan,
            'kelurahan_nm' => $kelurahan->nama ?? null,
            'rw' => $request->rw,
            'rw_nm' => 'RW ' . $request->rw,
            'rt' => $request->rt,
            'rt_nm' => 'RT ' . $request->rt,
            'alamat' => $request->alamat,
        ]);

        $resident = Resident::where('nik', $request->nik)->first();

        if (! $resident) {
            Resident::create([
                'nik' => $request->nik,
                'kk' => $request->kk,
                'data' => $datapemohon,
            ]);
        } else {
            $oldData = decode_json_data($resident->data);

            if ($datapemohon != $oldData || $request->kk != $resident->kk || $request->nik != $resident->nik) {
                $resident->update([
                    'nik' => $request->nik,
                    'kk' => $request->kk,
                    'data' => $datapemohon,
                ]);
            }
        }

        return redirect()->route('profile')->with('status', 'Data pribadi berhasil di update!');
    }

    private function updateAdminKelurahanProfile(Request $request, User $user)
    {
        if (! $this->isAdminKelurahan($user)) {
            return back()->with('error', 'Edit profil ini khusus untuk Admin Kelurahan.');
        }

        $validated = $request->validate([
            'nik' => ['required', 'regex:/^[0-9]{16}$/', Rule::unique('users', 'nik')->ignore($user->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'regex:/^[0-9]+$/', 'max:20'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'nik.regex' => 'NIK wajib angka dan tepat 16 digit.',
            'phone.regex' => 'Nomor HP hanya boleh angka.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $payload = [
            'nik' => preg_replace('/\D/', '', $validated['nik']),
            'name' => mb_strtoupper(trim($validated['name']), 'UTF-8'),
            'email' => mb_strtolower(trim($validated['email']), 'UTF-8'),
            'phone' => preg_replace('/\D/', '', $validated['phone']),
        ];

        $fotoColumn = $this->profilePhotoColumn();

        if ($fotoColumn && $request->hasFile('foto')) {
            $oldPath = $user->{$fotoColumn};

            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $payload[$fotoColumn] = $request->file('foto')->store('users/foto', 'public');
        }

        // Wilayah, role, RW, dan RT sengaja tidak diambil dari request.
        // Jadi walaupun field dimanipulasi dari browser, akun tetap berada di kelurahan login.
        $user->forceFill($payload)->save();

        return redirect()->route('profile')->with('status', 'Profil Admin Kelurahan berhasil diperbarui. Wilayah tetap terkunci.');
    }

    private function isAdminKelurahan(User $user): bool
    {
        if ((int) $user->role_id !== 1) {
            return false;
        }

        $skpd = Skpd::find($user->id_instansi);

        return $skpd && strlen((string) $skpd->id_region) === 13;
    }

    private function canUpdatePhotoOnly(User $user): bool
    {
        return in_array((int) $user->role_id, [3, 4, 5, 6], true);
    }

    private function updatePejabatProfilePhoto(Request $request, User $user)
    {
        $validated = $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'foto.required' => 'Foto profil wajib dipilih.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto hanya boleh JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $fotoColumn = $this->profilePhotoColumn();

        if (! $fotoColumn) {
            return back()->with('error', 'Kolom foto pada tabel users belum tersedia.');
        }

        $oldPath = $user->{$fotoColumn};

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $user->forceFill([
            $fotoColumn => $request->file('foto')->store('users/foto', 'public'),
        ])->save();

        return redirect()->route('profile')->with('status', 'Foto profil berhasil diperbarui. Data akun, jabatan, role, dan wilayah tetap terkunci.');
    }

    private function profilePhotoColumn(): ?string
    {
        if (Schema::hasColumn('users', 'foto')) {
            return 'foto';
        }

        if (Schema::hasColumn('users', 'avatar')) {
            return 'avatar';
        }

        return null;
    }

    public function akun(Request $request, $id)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
            ],
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Password berhasil di update!');
    }
}
