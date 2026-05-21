<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Log_surat;
use App\Models\Skpd;
use App\Models\SuratPengajuan;
use App\Traits\GetNoSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SuperAdminController extends Controller
{
    use GetNoSurat;

    public function login()
    {
        if (auth()->check() && (int) auth()->user()->role_id === 7) {
            return redirect()->route('super-admin.dashboard');
        }

        return view('super_admin.login', [
            'title' => 'Login Super Admin',
        ]);
    }

    public function dashboard()
    {
        $counts = [
            'surat' => SuratPengajuan::count(),
            'akun' => DB::table('users')->count(),
            'menunggu' => SuratPengajuan::whereIn('status', [0, 1, 2, 3, 8, 11])->count(),
            'selesai' => SuratPengajuan::where(function ($q) {
                $q->where('status', 9)
                    ->orWhere(function ($qq) {
                        $qq->where('status', 4)->where('jenis_surat', '<>', 'sktm');
                    });
            })->count(),
            'ditolak' => SuratPengajuan::where('status', 6)->count(),
            'tte' => SuratPengajuan::where(function ($q) {
                $q->where('status', 3)
                    ->orWhere(function ($qq) {
                        $qq->where('jenis_surat', 'sktm')->where('status', 8);
                    });
            })->count(),
        ];

        $statusRows = SuratPengajuan::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => (int) $row->status,
                'label' => $this->statusLabel((int) $row->status),
                'total' => (int) $row->total,
            ]);

        $roleRows = DB::table('users')
            ->leftJoin('user_roles', 'user_roles.id', '=', 'users.role_id')
            ->select('users.role_id', DB::raw('COALESCE(user_roles.name, CONCAT("Role ", users.role_id)) as role_name'), DB::raw('COUNT(*) as total'))
            ->groupBy('users.role_id', 'user_roles.name')
            ->orderBy('users.role_id')
            ->get()
            ->map(function ($row) {
                $row->role_name = $row->role_name === 'Client' ? 'Warga' : $row->role_name;
                return $row;
            });

        $recentSurat = SuratPengajuan::with(['kelurahan'])
            ->orderByDesc(DB::raw('COALESCE(updated_at, created_at)'))
            ->limit(8)
            ->get();

        return view('super_admin.dashboard', [
            'title' => 'Dashboard Super Admin',
            'counts' => $counts,
            'statusRows' => $statusRows,
            'roleRows' => $roleRows,
            'recentSurat' => $recentSurat,
        ]);
    }

    public function surat(Request $request)
    {
        $query = SuratPengajuan::with(['kelurahan'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $keyword = trim((string) $request->q);
                $q->where(function ($qq) use ($keyword) {
                    $qq->where('nik', 'like', '%' . $keyword . '%')
                        ->orWhere('kepada', 'like', '%' . $keyword . '%')
                        ->orWhere('peruntukan', 'like', '%' . $keyword . '%')
                        ->orWhere('jenis_surat', 'like', '%' . $keyword . '%')
                        ->orWhere('id', $keyword);
                });
            })
            ->when($request->filled('jenis'), fn ($q) => $q->where('jenis_surat', $request->jenis))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('id_kel'), fn ($q) => $q->where('id_kel', $request->id_kel))
            ->orderByDesc(DB::raw('COALESCE(updated_at, created_at)'))
            ->orderByDesc('id');

        return view('super_admin.surat', [
            'title' => 'Kontrol Semua Surat',
            'surats' => $query->paginate(15)->withQueryString(),
            'jenisOptions' => SuratPengajuan::query()->select('jenis_surat')->distinct()->orderBy('jenis_surat')->pluck('jenis_surat'),
            'statusOptions' => $this->statusOptions(),
            'kelurahans' => $this->skpdOptions(true),
        ]);
    }

    public function pelayanan(Request $request)
    {
        $query = SuratPengajuan::with(['kelurahan'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $keyword = trim((string) $request->q);
                $q->where(function ($qq) use ($keyword) {
                    $qq->where('nik', 'like', '%' . $keyword . '%')
                        ->orWhere('kepada', 'like', '%' . $keyword . '%')
                        ->orWhere('peruntukan', 'like', '%' . $keyword . '%')
                        ->orWhere('id', $keyword);
                });
            })
            ->when($request->filled('jenis'), fn ($q) => $q->where('jenis_surat', $request->jenis))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('id_kec'), function ($q) use ($request) {
                $skpdIds = Skpd::where('id_kec', $request->id_kec)->pluck('id');
                $q->whereIn('id_kel', $skpdIds);
            })
            ->when($request->filled('id_kel'), fn ($q) => $q->where('id_kel', $request->id_kel))
            ->orderByDesc(DB::raw('COALESCE(updated_at, created_at)'))
            ->orderByDesc('id');

        $selectedKelurahan = $request->filled('id_kel') ? Skpd::find($request->id_kel) : null;

        return view('super_admin.pelayanan', [
            'title' => 'Pelayanan Warga Super Admin',
            'surats' => $query->paginate(15)->withQueryString(),
            'jenisOptions' => $this->jenisSuratOptions(),
            'statusOptions' => $this->statusOptions(),
            'kecamatans' => DB::table('kecamatans')->whereIn('id', Skpd::query()->select('id_kec')->where('id_kec', '<>', '')->distinct())->orderBy('id')->get(['id', 'nama']),
            'kelurahans' => $this->skpdOptions(true, $request->get('id_kec')),
            'selectedKelurahan' => $selectedKelurahan,
        ]);
    }

    public function createPelayanan(Request $request, string $jenis)
    {
        $request->validate([
            'id_kel' => ['required', 'integer', 'exists:skpds,id'],
        ], [
            'id_kel.required' => 'Pilih kelurahan terlebih dahulu sebelum membuat surat.',
        ]);

        $jenis = strtolower($jenis);
        $skpd = Skpd::findOrFail($request->id_kel);
        $title = 'Tambah Pengajuan Surat ' . strtoupper($jenis) . ' - ' . $skpd->nama;

        $mapKodeJenis = [
            'skbn'        => 'SKBN',
            'sktm'        => 'SKTM',
            'skdom'       => 'SKDOM',
            'skusaha'     => 'SKUSAHA',
            'skhsl'       => 'SKHSL',
            'skboro'      => 'SKBORO',
            'skkelahiran' => 'SKKELAHIRAN',
            'skkematian'  => 'SKKEMATIAN',
            'suket'       => 'SUKET',
        ];

        if (! array_key_exists($jenis, $mapKodeJenis)) {
            abort(404, 'Jenis surat tidak ditemukan.');
        }

        $kd_jenis_surat = $mapKodeJenis[$jenis];
        $lastSurat = SuratPengajuan::where('jenis_surat', $jenis)
            ->where('id_kel', $skpd->id)
            ->whereYear('tgl_surat', date('Y'))
            ->orderByDesc('no_urut_surat')
            ->first();

        $no_urut_surat = $lastSurat ? ((int) $lastSurat->no_urut_surat + 1) : 1;
        $var = $this->mapVar($jenis);

        $currentUser = new \stdClass();
        $currentUser->id_instansi = $skpd->id;
        $currentUser->id_rw = null;
        $currentUser->id_rt = null;
        $currentUser->skpd = $skpd;

        return view('super_admin.surat_create', compact(
            'title',
            'jenis',
            'currentUser',
            'kd_jenis_surat',
            'no_urut_surat',
            'var',
            'skpd'
        ));
    }

    public function actionSurat(Request $request, $id)
    {
        $request->validate([
            'action' => ['required', 'string'],
            'komentar' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'integer'],
        ]);

        $surat = SuratPengajuan::findOrFail($id);
        $action = $request->action;
        $oldStatus = (int) $surat->status;
        $isSktm = strtolower((string) $surat->jenis_surat) === 'sktm';
        $nextStatus = null;
        $message = 'Aksi berhasil dijalankan.';

        if ($action === 'proses') {
            $nextStatus = 1;
            $message = 'Surat berhasil diproses oleh Super Admin.';
        } elseif ($action === 'naik') {
            if (in_array($oldStatus, [0, 1], true)) {
                $nextStatus = 2;
                $message = 'Surat berhasil dinaikkan ke Sekkel.';
            } elseif ($oldStatus === 2) {
                $nextStatus = 3;
                $message = 'Surat berhasil dinaikkan ke Lurah.';
            } elseif ($oldStatus === 4 && $isSktm) {
                $nextStatus = 11;
                $message = 'SKTM berhasil dinaikkan ke Sekcam.';
            } elseif ($oldStatus === 11 && $isSktm) {
                $nextStatus = 8;
                $message = 'SKTM berhasil dinaikkan ke Camat.';
            }
        } elseif ($action === 'turunkan') {
            $nextStatus = match ($oldStatus) {
                2 => 1,
                3 => 2,
                11 => 4,
                8 => 11,
                default => null,
            };
            $message = 'Surat berhasil diturunkan satu tahap.';
        } elseif ($action === 'tolak') {
            $nextStatus = 6;
            $message = 'Surat berhasil ditolak oleh Super Admin.';
        } elseif ($action === 'setujui') {
            $nextStatus = $isSktm ? 9 : 4;
            $message = $isSktm
                ? 'SKTM berhasil ditandai selesai/Disetujui Camat oleh Super Admin.'
                : 'Surat berhasil ditandai selesai/Disetujui Lurah oleh Super Admin.';
        } elseif ($action === 'set_status') {
            $nextStatus = (int) $request->status;
            if (!array_key_exists($nextStatus, $this->statusOptions())) {
                return back()->with('error', 'Status yang dipilih tidak valid.');
            }
            $message = 'Status surat berhasil diubah oleh Super Admin.';
        }

        if ($nextStatus === null) {
            return back()->with('error', 'Status surat saat ini tidak bisa menjalankan aksi tersebut.');
        }

        DB::transaction(function () use ($surat, $nextStatus, $request) {
            $variable = $this->variableArray($surat);
            $variable['submitter_type'] = $variable['submitter_type'] ?? (((int) $surat->status === 0) ? 'warga' : 'admin');
            $variable['super_admin_last_action_by'] = auth()->id();
            $variable['super_admin_last_action_at'] = now()->toDateTimeString();

            if ($nextStatus === 6) {
                $variable['alasan_penolakan'] = trim((string) ($request->komentar ?: 'Ditolak oleh Super Admin.'));
                $variable['ditolak_pada'] = now()->toDateTimeString();
            }

            $surat->update([
                'status' => $nextStatus,
                'variable' => $variable,
            ]);

            $this->writeLog($surat, $nextStatus);
        });

        return back()->with('status', $message);
    }

    public function destroySurat($id)
    {
        $surat = SuratPengajuan::findOrFail($id);

        DB::transaction(function () use ($surat) {
            $this->writeLog($surat, 7);
            $surat->delete();
        });

        return back()->with('status', 'Surat berhasil dihapus oleh Super Admin.');
    }


    public function profilInstansi()
    {
        $skpdLogin = $this->currentLoginSkpd();

        if (! $skpdLogin) {
            abort(404, 'SKPD pada akun login Super Admin tidak ditemukan. Cek kolom users.id_instansi.');
        }

        $kelurahanIds = Skpd::query()->whereRaw('CHAR_LENGTH(id_region) = 13')->pluck('id');
        $kelurahanRegionIds = Skpd::query()->whereRaw('CHAR_LENGTH(id_region) = 13')->pluck('id_region');

        $counts = [
            'surat' => SuratPengajuan::count(),
            'selesai' => SuratPengajuan::where(function ($q) {
                $q->where('status', 9)
                    ->orWhere(function ($qq) {
                        $qq->where('status', 4)->where('jenis_surat', '<>', 'sktm');
                    });
            })->count(),
            'akun' => DB::table('users')->count(),
            'warga' => DB::table('users')->where('role_id', 2)->count(),
            'kecamatan' => Skpd::query()->whereRaw('CHAR_LENGTH(id_region) = 8')->count(),
            'kelurahan' => $kelurahanIds->count(),
            'rw' => DB::table('rt_rws')->whereIn('kode_kelurahan', $kelurahanRegionIds)->select('kode_kelurahan', 'rw')->distinct()->count(),
            'rt' => DB::table('rt_rws')->whereIn('kode_kelurahan', $kelurahanRegionIds)->count(),
        ];

        return view('super_admin.profil_instansi', [
            'title' => 'Profil Instansi Super Admin',
            'skpd' => $skpdLogin,
            'counts' => $counts,
        ]);
    }

    public function updateProfilInstansi(Request $request)
    {
        $skpdLogin = $this->currentLoginSkpd();

        if (! $skpdLogin) {
            abort(404, 'SKPD pada akun login Super Admin tidak ditemukan. Cek kolom users.id_instansi.');
        }

        $validated = $request->validate([
            'instansi_alamat' => ['nullable', 'string', 'max:500'],
            'instansi_telp' => ['nullable', 'regex:/^[0-9]+$/', 'max:20'],
            'instansi_fax' => ['nullable', 'regex:/^[0-9]+$/', 'max:20'],
            'instansi_email' => ['nullable', 'email', 'max:120'],
            'instansi_kode_pos' => ['nullable', 'regex:/^[0-9]+$/', 'max:10'],
        ], [
            'instansi_telp.regex' => 'Telepon hanya boleh angka.',
            'instansi_fax.regex' => 'Fax hanya boleh angka.',
            'instansi_kode_pos.regex' => 'Kode pos hanya boleh angka.',
            'instansi_email.email' => 'Format email tidak valid.',
        ]);

        $payload = [];
        foreach ($validated as $column => $value) {
            if (Schema::hasColumn('skpds', $column)) {
                $payload[$column] = $column === 'instansi_alamat'
                    ? mb_strtoupper(trim((string) $value), 'UTF-8')
                    : trim((string) $value);
            }
        }

        if ($payload) {
            $skpdLogin->update($payload);
        }

        return redirect()->route('super-admin.profil-instansi')->with('status', 'Profil Instansi Super Admin berhasil diperbarui sesuai akun login.');
    }


    private function currentLoginSkpd(): ?Skpd
    {
        $user = auth()->user();

        $skpdId = (int) (
            $user->id_instansi
            ?? $user->skpd_id
            ?? $user->id_skpd
            ?? $user->instansi_id
            ?? 0
        );

        if ($skpdId > 0) {
            return Skpd::query()->where('id', $skpdId)->first();
        }

        return null;
    }

    public function statusOptions(): array
    {
        return [
            0 => 'Pengajuan Warga',
            1 => 'Diproses Admin',
            2 => 'Dinaikkan ke Sekkel',
            3 => 'Dinaikkan ke Lurah / Menunggu TTE Lurah',
            4 => 'Disetujui Lurah',
            5 => 'Dinilai',
            6 => 'Ditolak',
            7 => 'Dihapus / TTD Basah',
            8 => 'Dinaikkan ke Camat / Menunggu TTE Camat',
            9 => 'Disetujui Camat / Final SKTM',
            11 => 'SKTM Sudah Naik Sekcam',
        ];
    }

    public function statusLabel(int $status): string
    {
        return $this->statusOptions()[$status] ?? 'Status ' . $status;
    }

    private function jenisSuratOptions(): array
    {
        return [
            'skbn' => 'Surat Keterangan Belum Menikah',
            'sktm' => 'Surat Keterangan Tidak Mampu',
            'skdom' => 'Surat Keterangan Domisili',
            'skusaha' => 'Surat Keterangan Usaha',
            'skhsl' => 'Surat Keterangan Penghasilan',
            'skboro' => 'Surat Keterangan Boro',
            'skkelahiran' => 'Surat Keterangan Kelahiran',
            'skkematian' => 'Surat Keterangan Kematian',
            'suket' => 'Surat Keterangan',
        ];
    }

    private function mapVar(string $jenis): array
    {
        $map = [
            'skbn' => ['bin_binti','nama_pasangan','nik_pasangan','tempat_lahir_pasangan','tgl_lahir_pasangan','agama_pasangan','pekerjaan_pasangan','alamat_pasangan'],
            'sktm' => ['nama_orang_tua','pekerjaan_orang_tua','alamat_orang_tua','keperluan_bantuan'],
            'skdom' => ['alamat_domisili','status_tempat_tinggal','lama_tinggal'],
            'skusaha' => ['nama_usaha','jenis_usaha','alamat_usaha','lama_usaha'],
            'skhsl' => ['kepada_tempat_lhr','kepada_tgl_lhr','kepada_gender','kepada_gender_nm','kepada_hubungan','kepada_sekolah','kepada_kelas','kepada_alamat_sekolah','penghasilan','terbilang','keperluan','surat_keperluan'],
            'skboro' => ['nama_ayah','nama_ibu','alamat_asal'],
            'skkelahiran' => ['nama_bayi','jenis_kelamin_bayi','tempat_lahir','tanggal_lahir','jam_lahir','nama_ayah','nama_ibu'],
            'skkematian' => ['nama_meninggal','hari_meninggal','tanggal_meninggal','tempat_meninggal','penyebab_meninggal'],
            'suket' => ['keterangan_tambahan'],
        ];

        return $map[$jenis] ?? [];
    }

    private function variableArray(SuratPengajuan $surat): array
    {
        $value = $surat->variable ?? [];
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    private function writeLog(SuratPengajuan $surat, int $status): void
    {
        Log_surat::create([
            'nik' => $surat->nik,
            'tabel_surat' => 'surat_pengajuans',
            'nama_surat' => strtoupper((string) $surat->jenis_surat),
            'id_surat' => $surat->id,
            'status_surat' => $status,
        ]);
    }

    private function skpdOptions(bool $kelurahanOnly = false, ?string $idKec = null)
    {
        return Skpd::with(['kelurahan', 'kecamatan'])
            ->when($kelurahanOnly, fn ($q) => $q->whereRaw('CHAR_LENGTH(id_region) = 13'))
            ->when($idKec, fn ($q) => $q->where('id_kec', $idKec))
            ->orderBy('id')
            ->get()
            ->map(function ($skpd) {
                $idRegion = (string) $skpd->id_region;
                $kecamatanNama = optional($skpd->kecamatan)->nama;
                $kelurahanNama = optional($skpd->kelurahan)->nama ?: $skpd->nama;

                if (strlen($idRegion) === 13) {
                    $skpd->super_label = $skpd->id . ' - Kec. ' . ($kecamatanNama ?: '-') . ' / Kel. ' . $kelurahanNama;
                } elseif (strlen($idRegion) === 8) {
                    $skpd->super_label = $skpd->id . ' - Kec. ' . ($kecamatanNama ?: $skpd->nama);
                } else {
                    $skpd->super_label = $skpd->id . ' - ' . $skpd->nama;
                }

                return $skpd;
            });
    }


}
