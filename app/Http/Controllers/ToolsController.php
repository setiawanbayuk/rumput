<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Resident;
use App\Models\RtRw;
use App\Models\Skpd;
use App\Models\SuratPengajuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ToolsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function rekap()
    {
        $this->ensurePetugas();
        $scope = $this->scopeInstansi();

        return view('tools.rekap', [
            'title' => 'Rekap Surat',
            'jenisSurat' => $this->jenisSuratOptions(),
            'scope' => $scope,
            'isKecamatan' => ($scope['type'] ?? '') === 'kecamatan',
        ]);
    }

    public function exportRekap(Request $request)
    {
        $this->ensurePetugas();

        $scope = $this->scopeInstansi();
        $allowedJenis = array_keys($this->jenisSuratOptions());

        $request->validate([
            'jenis' => ['required', 'string', Rule::in($allowedJenis)],
            'skbn_kategori' => ['nullable', 'in:menikah,lainnya'],
        ]);

        $jenis = strtolower((string) $request->jenis);

        // Sekcam dan Camat hanya boleh download rekap SKTM.
        if (($scope['type'] ?? '') === 'kecamatan' && $jenis !== 'sktm') {
            return redirect()->route('tools.rekap')->with('error', 'Akun Sekcam/Camat hanya dapat mengunduh Rekap SKTM.');
        }

        $config = $this->jenisSuratOptions()[$jenis];

        $query = SuratPengajuan::query()
            ->whereRaw('LOWER(COALESCE(jenis_surat,\'\')) = ?', [$jenis])
            ->whereIn('id_kel', $scope['kelurahan_ids'])
            ->where(function ($q) {
                // Mengikuti logika Beranda: final jika TTE Lurah/Camat selesai atau TTD Basah sudah upload bukti.
                $q->where('status', 9)
                    ->orWhere(function ($qq) {
                        $qq->where('status', 4)->whereRaw("LOWER(COALESCE(jenis_surat,'')) <> 'sktm'");
                    })
                    ->orWhere(function ($qq) {
                        $qq->whereRaw("variable IS NOT NULL AND variable <> '' AND JSON_VALID(variable) AND COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.bukti_ttd_basah')), ''), '') <> ''");
                    });
            });

        if ($jenis === 'skbn') {
            if ($request->skbn_kategori === 'menikah') {
                $query->where(function ($q) {
                    $q->where('peruntukan', 'like', '%menikah%')
                        ->orWhereRaw("variable IS NOT NULL AND JSON_VALID(variable) AND LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.peruntukan')), JSON_UNQUOTE(JSON_EXTRACT(variable, '$.keperluan')), JSON_UNQUOTE(JSON_EXTRACT(variable, '$.surat_keperluan')), '')) LIKE '%menikah%'");
                });
            } elseif ($request->skbn_kategori === 'lainnya') {
                $query->where(function ($q) {
                    $q->where(function ($qq) {
                        $qq->whereNull('peruntukan')->orWhere('peruntukan', 'not like', '%menikah%');
                    })->whereRaw("NOT (variable IS NOT NULL AND JSON_VALID(variable) AND LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.peruntukan')), JSON_UNQUOTE(JSON_EXTRACT(variable, '$.keperluan')), JSON_UNQUOTE(JSON_EXTRACT(variable, '$.surat_keperluan')), '')) LIKE '%menikah%')");
                });
            }
        }

        $rows = $query->orderByDesc('updated_at')->orderByDesc('created_at')->get();

        $html = view('tools.rekap-excel', [
            'rows' => $rows,
            'jenis' => $jenis,
            'label' => $config['label'],
            'filterSkbn' => $request->skbn_kategori,
            'scope' => $scope,
        ])->render();

        $namaFile = 'rekap_' . $jenis;
        if ($jenis === 'skbn' && $request->filled('skbn_kategori')) {
            $namaFile .= '_' . $request->skbn_kategori;
        }
        $namaFile .= '_' . date('Ymd_His') . '.xls';

        return Response::make($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $namaFile . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function profilInstansi()
    {
        $this->ensurePetugas();

        $skpd = Skpd::with(['kelurahan', 'kecamatan', 'rtrw'])->find(auth()->user()->id_instansi);
        $scope = $this->scopeInstansi($skpd);
        $counts = $this->summaryCounts($scope);

        return view('tools.profil-instansi', [
            'title' => 'Profil Instansi',
            'skpd' => $skpd,
            'scope' => $scope,
            'counts' => $counts,
        ]);
    }

    public function updateProfilInstansi(Request $request)
    {
        $this->ensurePetugas();

        $skpd = Skpd::findOrFail(auth()->user()->id_instansi);

        $validated = $request->validate([
            'instansi_alamat' => ['nullable', 'string', 'max:500'],
            'instansi_telp' => ['required', 'digits_between:5,20'],
            'instansi_fax' => ['required', 'digits_between:5,20'],
            'instansi_email' => ['required', 'email:rfc,dns', 'max:120'],
            'instansi_kode_pos' => ['required', 'digits_between:4,10'],
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
            DB::table('skpds')->where('id', $skpd->id)->update($payload);
        }

        return redirect()->route('tools.profil-instansi')->with('status', 'Profil instansi berhasil diperbarui.');
    }

    private function summaryCounts(array $scope): array
    {
        $kelurahanIds = $scope['kelurahan_ids'];
        $regionIds = $scope['kelurahan_region_ids'];

        $suratSelesai = SuratPengajuan::query()
            ->whereIn('id_kel', $kelurahanIds)
            ->where(function ($q) {
                $q->where('status', 9)
                    ->orWhere(function ($qq) {
                        $qq->where('status', 4)->whereRaw("LOWER(COALESCE(jenis_surat,'')) <> 'sktm'");
                    })
                    ->orWhereRaw("variable IS NOT NULL AND variable <> '' AND JSON_VALID(variable) AND COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.bukti_ttd_basah')), ''), '') <> ''");
            })
            ->count();

        $rtrw = RtRw::query()->whereIn('kode_kelurahan', $regionIds)->get(['kode_kelurahan', 'rw', 'rt']);

        return [
            'warga' => User::query()->where('role_id', 2)->whereIn('id_instansi', $kelurahanIds)->count(),
            'rt' => $rtrw->map(fn ($item) => $item->kode_kelurahan . '-' . $item->rw . '-' . $item->rt)->filter()->unique()->count(),
            'rw' => $rtrw->map(fn ($item) => $item->kode_kelurahan . '-' . $item->rw)->filter()->unique()->count(),
            'surat' => $suratSelesai,
            'kelurahan' => count($kelurahanIds),
        ];
    }

    private function scopeInstansi(?Skpd $skpd = null): array
    {
        $skpd = $skpd ?: Skpd::with(['kelurahan', 'kecamatan'])->find(auth()->user()->id_instansi);
        $roleId = (int) auth()->user()->role_id;
        $idRegion = trim((string) ($skpd->id_region ?? ''));
        $idKec = trim((string) ($skpd->id_kec ?? ''));

        $isKecamatan = in_array($roleId, [5, 6], true) || (strlen($idRegion) === 8 && $idRegion !== '');

        if ($isKecamatan) {
            $kelurahansQuery = Skpd::query()->whereRaw('CHAR_LENGTH(id_region) = 13');

            if ($idRegion !== '' && strlen($idRegion) === 8) {
                $kelurahansQuery->where('id_region', 'like', $idRegion . '%');
            } elseif ($idKec !== '') {
                $kelurahansQuery->where('id_kec', $idKec);
            } else {
                $kelurahansQuery->whereRaw('1 = 0');
            }

            $kelurahans = $kelurahansQuery->orderBy('nama')->get(['id', 'nama', 'id_region', 'id_kec']);

            $kecamatanName = $skpd->nama ?? optional($skpd->kecamatan)->nama;
            if (! $kecamatanName && $idRegion !== '') {
                $kecamatanName = optional(Kecamatan::find($idRegion))->nama;
            }

            return [
                'type' => 'kecamatan',
                'label' => 'Profil Kecamatan',
                'unit_name' => $skpd->nama ?? 'Kecamatan',
                'kecamatan_name' => $kecamatanName,
                'kelurahan_name' => null,
                'kelurahan_ids' => $kelurahans->pluck('id')->map(fn ($v) => (int) $v)->values()->all(),
                'kelurahan_region_ids' => $kelurahans->pluck('id_region')->filter()->values()->all(),
                'kelurahans' => $kelurahans,
            ];
        }

        $kelurahan = null;
        if ($idRegion !== '') {
            $kelurahan = Kelurahan::find($idRegion);
        }

        $kecamatanName = optional($skpd->kecamatan)->nama;
        $kecamatanId = $idKec;

        // Perbaikan utama: jika relasi skpds.id_kec kosong, Kecamatan tetap dibaca dari kode_kecamatan Kelurahan.
        if ($kelurahan) {
            $kecamatanId = $kecamatanId ?: (string) $kelurahan->kode_kecamatan;
            $kecamatanName = $kecamatanName ?: optional(Kecamatan::find($kelurahan->kode_kecamatan))->nama;
        }

        // Fallback kode wilayah: kode kecamatan biasanya 8 digit pertama dari kode kelurahan.
        if (! $kecamatanName && strlen($idRegion) >= 8) {
            $kecamatanName = optional(Kecamatan::find(substr($idRegion, 0, 8)))->nama;
        }

        return [
            'type' => 'kelurahan',
            'label' => 'Profil Kelurahan',
            'unit_name' => $skpd->nama ?? 'Kelurahan',
            'kecamatan_name' => $kecamatanName ?: '-',
            'kelurahan_name' => optional($kelurahan)->nama ?? optional($skpd->kelurahan)->nama ?? $skpd->nama ?? null,
            'kelurahan_ids' => $skpd ? [(int) $skpd->id] : [],
            'kelurahan_region_ids' => $idRegion ? [$idRegion] : [],
            'kelurahans' => collect($skpd ? [$skpd] : []),
        ];
    }

    private function jenisSuratOptions(): array
    {
        $roleId = (int) auth()->user()->role_id;

        if (in_array($roleId, [5, 6], true)) {
            return [
                'sktm' => ['label' => 'SKTM - Surat Keterangan Tidak Mampu'],
            ];
        }

        // Surat Kematian dan Surat Kelahiran sengaja dihilangkan dari Rekap Tools.
        return [
            'skbn' => ['label' => 'SKBN - Surat Keterangan Belum Menikah'],
            'suket' => ['label' => 'SUKET - Surat Keterangan'],
            'sktm' => ['label' => 'SKTM - Surat Keterangan Tidak Mampu'],
            'skdom' => ['label' => 'SKDOM - Surat Keterangan Domisili'],
            'skusaha' => ['label' => 'SKUSAHA - Surat Keterangan Usaha'],
            'skhsl' => ['label' => 'SKHSL - Surat Keterangan Penghasilan'],
            'skboro' => ['label' => 'SKBORO - Surat Boro'],
        ];
    }

    private function ensurePetugas(): void
    {
        abort_unless(auth()->check() && (int) auth()->user()->role_id !== 2, 403);
    }
}
