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
            'bulanOptions' => $this->bulanOptions(),
            'currentYear' => (int) now('Asia/Jakarta')->year,
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
            'bulan' => ['nullable', 'integer', 'between:1,12'],
            'tahun' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $jenis = strtolower((string) $request->jenis);
        $bulanFilter = (int) $request->input('bulan', 0);
        $tahunFilter = (int) $request->input('tahun', now('Asia/Jakarta')->year);

        if ($bulanFilter < 1 || $bulanFilter > 12) {
            $bulanFilter = null;
        }

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

        if ($bulanFilter !== null) {
            $query->whereRaw('MONTH(COALESCE(tgl_surat, created_at)) = ?', [$bulanFilter])
                ->whereRaw('YEAR(COALESCE(tgl_surat, created_at)) = ?', [$tahunFilter]);
        }

        $rows = $query->orderByDesc('updated_at')->orderByDesc('created_at')->get();

        $html = view('tools.rekap-excel', [
            'rows' => $rows,
            'jenis' => $jenis,
            'label' => $config['label'],
            'filterSkbn' => $request->skbn_kategori,
            'filterBulan' => $bulanFilter,
            'filterTahun' => $tahunFilter,
            'scope' => $scope,
        ])->render();

        $namaFile = 'rekap_' . $jenis;
        if ($jenis === 'skbn' && $request->filled('skbn_kategori')) {
            $namaFile .= '_' . $request->skbn_kategori;
        }
        if ($bulanFilter !== null) {
            $namaFile .= '_bulan_' . str_pad((string) $bulanFilter, 2, '0', STR_PAD_LEFT) . '_' . $tahunFilter;
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


    public function rating(Request $request)
    {
        $this->ensureRatingRole();

        $scope = $this->scopeInstansi();
        $roleId = (int) auth()->user()->role_id;
        $jenisOptions = $this->ratingJenisOptions();
        $bulanOptions = $this->bulanOptions();
        $jenisFilter = strtolower(trim((string) $request->get('jenis', '')));
        $bulanFilter = (int) $request->get('bulan', 0);
        $tahunFilter = (int) $request->get('tahun', now('Asia/Jakarta')->year);

        if ($jenisFilter !== '' && ! array_key_exists($jenisFilter, $jenisOptions)) {
            $jenisFilter = '';
        }

        if ($bulanFilter < 1 || $bulanFilter > 12) {
            $bulanFilter = null;
            $tahunFilter = null;
        }

        $ratingExpr = $this->ratingValueExpression();
        $commentExpr = $this->ratingCommentExpression();
        $ratingReady = $ratingExpr !== null;

        $kelurahanIds = collect($scope['kelurahan_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $baseSummary = [
            'avg' => null,
            'total' => 0,
            'star_counts' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
        ];

        if (! $ratingReady || empty($kelurahanIds)) {
            return view('tools.rating', [
                'title' => 'Rating Pelayanan',
                'scope' => $scope,
                'roleId' => $roleId,
                'jenisOptions' => $jenisOptions,
                'jenisFilter' => $jenisFilter,
                'bulanOptions' => $bulanOptions,
                'bulanFilter' => $bulanFilter,
                'tahunFilter' => $tahunFilter,
                'ratingReady' => $ratingReady,
                'summary' => $baseSummary,
                'ratings' => collect(),
                'kelurahanRatings' => collect(),
            ]);
        }

        $summaryQuery = $this->ratingQuery($kelurahanIds, $ratingExpr, $jenisFilter, $bulanFilter, $tahunFilter);
        $totalPenilai = (clone $summaryQuery)->count();
        $avgRatingRaw = $totalPenilai > 0 ? (clone $summaryQuery)->selectRaw('AVG(' . $ratingExpr . ') as avg_rating')->value('avg_rating') : null;
        $avgRating = $avgRatingRaw !== null ? round((float) $avgRatingRaw, 1) : null;

        // Jangan gunakan GROUP BY untuk ekspresi JSON/variable karena MySQL ONLY_FULL_GROUP_BY
        // bisa menolak kolom `variable` yang dipakai di dalam expression rating.
        // Hitung distribusi bintang dengan aggregate SUM(CASE...) agar aman di MySQL strict mode.
        $starCountsRow = (clone $summaryQuery)
            ->selectRaw('SUM(CASE WHEN ROUND(' . $ratingExpr . ') = 1 THEN 1 ELSE 0 END) as star_1')
            ->selectRaw('SUM(CASE WHEN ROUND(' . $ratingExpr . ') = 2 THEN 1 ELSE 0 END) as star_2')
            ->selectRaw('SUM(CASE WHEN ROUND(' . $ratingExpr . ') = 3 THEN 1 ELSE 0 END) as star_3')
            ->selectRaw('SUM(CASE WHEN ROUND(' . $ratingExpr . ') = 4 THEN 1 ELSE 0 END) as star_4')
            ->selectRaw('SUM(CASE WHEN ROUND(' . $ratingExpr . ') = 5 THEN 1 ELSE 0 END) as star_5')
            ->first();

        $starCounts = [
            1 => (int) ($starCountsRow->star_1 ?? 0),
            2 => (int) ($starCountsRow->star_2 ?? 0),
            3 => (int) ($starCountsRow->star_3 ?? 0),
            4 => (int) ($starCountsRow->star_4 ?? 0),
            5 => (int) ($starCountsRow->star_5 ?? 0),
        ];

        $summary = [
            'avg' => $avgRating,
            'total' => $totalPenilai,
            'star_counts' => $starCounts,
        ];

        // Role Lurah: tampil detail semua penilaian warga wilayah kelurahannya, termasuk bintang dan komentar.
        if ($roleId === 3) {
            $ratings = $this->ratingQuery($kelurahanIds, $ratingExpr, $jenisFilter, $bulanFilter, $tahunFilter)
                ->with(['penduduk', 'kelurahan'])
                ->select('surat_pengajuans.*')
                ->selectRaw($ratingExpr . ' as rating_value')
                ->selectRaw(($commentExpr ?: "''") . ' as komentar_value')
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString();

            return view('tools.rating', [
                'title' => 'Rating Pelayanan',
                'scope' => $scope,
                'roleId' => $roleId,
                'jenisOptions' => $jenisOptions,
                'jenisFilter' => $jenisFilter,
                'bulanOptions' => $bulanOptions,
                'bulanFilter' => $bulanFilter,
                'tahunFilter' => $tahunFilter,
                'ratingReady' => true,
                'summary' => $summary,
                'ratings' => $ratings,
                'kelurahanRatings' => collect(),
            ]);
        }

        // Role Camat: cukup menampilkan rekap rating per kelurahan wilayah kecamatan, tanpa komentar warga.
        $aggregateRows = $this->ratingQuery($kelurahanIds, $ratingExpr, $jenisFilter, $bulanFilter, $tahunFilter)
            ->select('id_kel')
            ->selectRaw('COUNT(*) as total_penilai')
            ->selectRaw('AVG(' . $ratingExpr . ') as avg_rating')
            ->groupBy('id_kel')
            ->get()
            ->keyBy(fn ($row) => (int) $row->id_kel);

        $kelurahanMap = collect($scope['kelurahans'] ?? collect())
            ->map(function ($kel) use ($aggregateRows) {
                $idKel = (int) ($kel->id ?? 0);
                $row = $aggregateRows->get($idKel);

                return (object) [
                    'id_kel' => $idKel,
                    'nama' => $kel->nama ?? '-',
                    'total_penilai' => (int) ($row->total_penilai ?? 0),
                    'avg_rating' => isset($row->avg_rating) ? round((float) $row->avg_rating, 1) : null,
                ];
            })
            ->sortByDesc(fn ($row) => $row->avg_rating ?? -1)
            ->values();

        return view('tools.rating', [
            'title' => 'Rating Pelayanan',
            'scope' => $scope,
            'roleId' => $roleId,
            'jenisOptions' => $jenisOptions,
            'jenisFilter' => $jenisFilter,
            'bulanOptions' => $bulanOptions,
            'bulanFilter' => $bulanFilter,
            'tahunFilter' => $tahunFilter,
            'ratingReady' => true,
            'summary' => $summary,
            'ratings' => collect(),
            'kelurahanRatings' => $kelurahanMap,
        ]);
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


    private function ratingQuery(array $kelurahanIds, string $ratingExpr, string $jenisFilter = '', ?int $bulanFilter = null, ?int $tahunFilter = null)
    {
        $query = SuratPengajuan::query()
            ->whereIn('id_kel', $kelurahanIds)
            ->whereRaw('(' . $ratingExpr . ') BETWEEN 1 AND 5');

        if ($jenisFilter !== '') {
            $query->whereRaw("LOWER(COALESCE(jenis_surat,'')) = ?", [$jenisFilter]);
        }

        if ($bulanFilter !== null) {
            $query->whereMonth('updated_at', $bulanFilter);

            if ($tahunFilter !== null) {
                $query->whereYear('updated_at', $tahunFilter);
            }
        }

        return $query;
    }

    private function ratingValueExpression(): ?string
    {
        $sources = [];

        if (Schema::hasColumn('surat_pengajuans', 'rating')) {
            $sources[] = "NULLIF(rating, '')";
        }

        if (Schema::hasColumn('surat_pengajuans', 'variable')) {
            $sources[] = "CASE WHEN variable IS NOT NULL AND variable <> '' AND JSON_VALID(variable) THEN NULLIF(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.rating')), '') ELSE NULL END";
            $sources[] = "CASE WHEN variable IS NOT NULL AND variable <> '' AND JSON_VALID(variable) THEN NULLIF(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.bintang')), '') ELSE NULL END";
        }

        if (empty($sources)) {
            return null;
        }

        return 'CAST(COALESCE(' . implode(', ', $sources) . ') AS DECIMAL(4,2))';
    }

    private function ratingCommentExpression(): ?string
    {
        $sources = [];

        if (Schema::hasColumn('surat_pengajuans', 'komentar')) {
            $sources[] = "NULLIF(komentar, '')";
        }

        if (Schema::hasColumn('surat_pengajuans', 'variable')) {
            $sources[] = "CASE WHEN variable IS NOT NULL AND variable <> '' AND JSON_VALID(variable) THEN NULLIF(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.komentar')), '') ELSE NULL END";
            $sources[] = "CASE WHEN variable IS NOT NULL AND variable <> '' AND JSON_VALID(variable) THEN NULLIF(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.comment')), '') ELSE NULL END";
            $sources[] = "CASE WHEN variable IS NOT NULL AND variable <> '' AND JSON_VALID(variable) THEN NULLIF(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.coment')), '') ELSE NULL END";
        }

        if (empty($sources)) {
            return null;
        }

        return 'COALESCE(' . implode(', ', $sources) . ')';
    }

    private function bulanOptions(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    private function ratingJenisOptions(): array
    {
        return [
            'skbn' => 'SKBN - Surat Keterangan Belum Menikah',
            'suket' => 'SUKET - Surat Keterangan',
            'sktm' => 'SKTM - Surat Keterangan Tidak Mampu',
            'skdom' => 'SKDOM - Surat Keterangan Domisili',
            'skusaha' => 'SKUSAHA - Surat Keterangan Usaha',
            'skhsl' => 'SKHSL - Surat Keterangan Penghasilan',
            'skboro' => 'SKBORO - Surat Boro',
            'skkelahiran' => 'SK Kelahiran',
            'skkematian' => 'SK Kematian',
        ];
    }

    private function ensureRatingRole(): void
    {
        abort_unless(auth()->check() && in_array((int) auth()->user()->role_id, [3, 5], true), 403);
    }

    private function ensurePetugas(): void
    {
        abort_unless(auth()->check() && (int) auth()->user()->role_id !== 2, 403);
    }
}
