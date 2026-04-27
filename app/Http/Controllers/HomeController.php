<?php

namespace App\Http\Controllers;

use App\Http\Resources\Skpd_resource;
use App\Models\JenisSurat;
use App\Models\Skpd;
use App\Models\Log_surat;
use App\Models\SuratBoro;
use App\Models\SuratDomisili;
use App\Models\SuratKelahiran;
use App\Models\SuratKematian;
use App\Models\SuratKeterangan;
use App\Models\SuratPenghasilan;
use App\Models\SuratSkbn;
use App\Models\SuratSktm;
use App\Models\SuratUsaha;
use App\Models\Resident;
use App\Services\SuratCollection;
use App\Traits\GeneratePDF;
use App\Traits\GetNoSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;

class HomeController extends Controller
{
    use GetNoSurat;
    use GeneratePDF;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['landing']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(SuratCollection $service)
    {
        $user = auth()->user();
        $rt = $user->id_rt;
        $rw = $user->id_rw;

        if ($user->role_id == 2) {
            return redirect()->route('warga');
        }

        $resident = Resident::where('nik', $user->nik)->first();
        if ($user->role_id != 2) {
            // if (!$resident) {
            //     return redirect()
            //         ->route('profile')
            //         ->with('status', 'Lengkapi data pribadi dahulu! Terima kasih');
            // }
        }
        if ($user->role_id == 2) {
            return redirect()->route('warga');
        }

        if (request()->ajax()) {
            // TABEL BERANDA:
            // Hanya tampilkan surat yang sudah final/disetujui saja.
            // Grafik tetap ALL status; filter ini hanya untuk tabel di bawah grafik.
            $data = $service->getAllForUser($user)
                ->filter(fn($row) => $this->isFinalForHomeTable($row))
                ->values();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('no_surat', function ($row) {
                    return $this->getNoSrt($row->raw);
                })
                ->addColumn('jenis', function ($row) {
                    return $row->jenis_label;
                })
                ->addColumn('peruntukan', function ($row) {
                    // SK KELAHIRAN → tampilkan nama anak
                    if ($row->jenis == 'skkelahiran') {
                        return $row->raw->nama_anak ?? '-';
                    }

                    // SK KEMATIAN → tampilkan nama alm/almh
                    if ($row->jenis == 'skkematian') {
                        return $row->raw->nama ?? '-';
                    }

                    // default untuk jenis surat lain
                    return $row->peruntukan ?? '-';
                })
                // status (pakai accessor st dari model surat)
                ->addColumn('st', function ($row) {
                    // asumsi semua model surat punya accessor getStAttribute() yang return ['name' => ..., 'color' => ...]
                    return $row->raw->st ?? null;
                })
                ->addColumn('action', function ($row) use ($user) {
                    $id     = $row->id;
                    $status = $row->status;
                    $role   = $user->role_id;
                    $jenis  = $row->jenis;
                    $route  = $row->route_edit;

                    /**
                     * Aksi tabel Beranda harus khusus final saja.
                     * Untuk TTD Basah yang sudah upload bukti, aksi wajib hanya:
                     * - Preview TTD Basah
                     * - Preview Bukti Upload
                     *
                     * Karena tabel Beranda memang sudah difilter oleh isFinalForHomeTable(),
                     * maka jangan munculkan tombol proses seperti naikkan/edit/delete/tolak.
                     */
                    $variable = $this->rowVariableArray($row);
                    $statusName = strtolower(trim((string) data_get($row, 'raw.st.name', data_get($row, 'st.name', ''))));
                    $submitter_type = $variable['submitter_type'] ?? data_get($row, 'raw.submitter_type', (((int) $status === 0) ? 'warga' : 'admin'));
                    $manual_signature = !empty($variable['manual_signature'])
                        || (($variable['signature_mode'] ?? null) === 'manual')
                        || $statusName === 'sudah upload bukti'
                        || $statusName === 'ttd basah - bukti uploaded';
                    $bukti_ttd_basah = $variable['bukti_ttd_basah']
                        ?? data_get($row, 'raw.bukti_ttd_basah')
                        ?? data_get($row, 'raw.bukti_upload')
                        ?? data_get($row, 'raw.bukti');

                    if (in_array($role, [1, 8, 9])) {
                        return view('includes.button-admin', compact('id', 'route', 'status', 'submitter_type', 'manual_signature', 'bukti_ttd_basah'));
                    } elseif (in_array($role, [3, 5])) {
                        $nomorSurat = $this->getNoSrt($row->raw);
                        return view('includes.button-kaopd', compact('id', 'status', 'nomorSurat', 'jenis', 'role', 'route', 'submitter_type', 'manual_signature', 'bukti_ttd_basah'));
                    } else {
                        return view('includes.button-verifikator', compact('id', 'status', 'role', 'route', 'submitter_type', 'manual_signature', 'bukti_ttd_basah'));
                    }
                })
                ->rawColumns(['action', 'no_surat'])
                ->make(true);
        }

        $title = "Dashboard";
        return view('home', compact('title'));
    }

    public function landing()
    {
        $title = "E-Suket Kota Kediri";
        $url = 'https://api-splp.layanan.go.id/t/kedirikota.go.id/web_kediri_kota/1.0/api/berita';
        try {
            // Tambahkan timeout 5 detik agar user tidak menunggu terlalu lama jika API down
            $response = Http::withoutVerifying()->timeout(5)->get($url);

            if ($response->successful()) {
                $berita = json_decode($response->json()['berita'], true);
                foreach ($berita as &$item) {
                    $item['deskripsi'] = strip_tags($item['deskripsi']);
                }
            } else {
                $berita = [];
            }
        } catch (\Exception $e) {
            $berita = []; // Jika API mati, tampilkan halaman tanpa berita agar tetap bisa diakses
        }

        return view('landing', compact('berita', 'title'));
    }
    public function warga()
    {
        $url = 'https://api-splp.layanan.go.id/t/kedirikota.go.id/web_kediri_kota/1.0/api/berita';
        $response = Http::withoutVerifying()->get($url);
        if ($response->status() !== 200) {
            $berita = [];
        } else {
            $berita = json_decode($response->json()['berita'], true);
            foreach ($berita as &$item) {
                $item['deskripsi'] = strip_tags($item['deskripsi']);
            }
        }
        $surat = JenisSurat::where(['is_active' => true])->get();
        $skpd = new Skpd_resource(Skpd::find(auth()->user()->id_instansi));
        // dd($skpd);
        return view('warga', compact('berita', 'surat', 'skpd'));
    }

    public function activity()
    {
        return Activity::all();
    }


    public function last_activity()
    {
        return Activity::all()->last();
    }

    public function ajukan(SuratCollection $service)
    {
        $user  = auth()->user();
        $q     = strtolower(request('q'));

        $surat = JenisSurat::where('is_active', true)->get(['jenis', 'assets', 'name']);
        $items = $service->getAllForUser($user);

        $items = $items->map(function ($row) {
            $row->nomor_surat = app(HomeController::class)->getNoSrt($row->raw);
            return $row;
        });

        if ($q) {
            $items = $items->filter(function ($row) use ($q) {
                return str_contains(strtolower($row->jenis_label), $q)
                    || str_contains(strtolower($row->peruntukan ?? ''), $q)
                    || str_contains(strtolower($row->kepada ?? ''), $q)
                    || str_contains(strtolower($row->nama_anak ?? ''), $q)
                    || str_contains(strtolower($row->nama ?? ''), $q);
            });
        }

        $items = $items->sortByDesc(fn($i) => $i->raw->created_at)->values();

        $sedangProses = $items->filter(
            fn($i) =>
            in_array($i->raw->status, [0, 1, 2, 3, 4, 8, 9])
        );

        $sedangProses = paginate_collection($sedangProses, 5, 'proses_page');

        $riwayat = $items->filter(
            fn($i) =>
            in_array($i->raw->status, [5, 6])
        );

        $riwayat      = paginate_collection($riwayat, 5, 'riwayat_page');

        return view('ajukan', compact('surat', 'sedangProses', 'riwayat'));
    }


    public function tracking(string $jenisSurat, int $id, Request $request)
    {
        $jenisSurat = Str::lower($jenisSurat);

        // Normalisasi input ke alias pendek
        $aliasMap = [
            'surat_skbns'        => 'skbn',
            'surat_boros'        => 'skboro',
            'surat_domisilis'    => 'skdom',
            'surat_penghasilans' => 'skhsl',
            'surat_sktms'        => 'sktm',
            'surat_usahas'       => 'skusaha',
            'surat_keterangans'  => 'suket',
            'surat_kelahirans'   => 'skkelahiran',
            'surat_kematians'    => 'skkematian',
        ];

        if (isset($aliasMap[$jenisSurat])) {
            $jenisSurat = $aliasMap[$jenisSurat];
        }

        $alias = $jenisSurat;

        // Alias → nama tabel surat
        $aliasToTable = [
            'skbn'        => 'surat_skbns',
            'skboro'      => 'surat_boros',
            'skdom'       => 'surat_domisilis',
            'skhsl'       => 'surat_penghasilans',
            'sktm'        => 'surat_sktms',
            'skusaha'     => 'surat_usahas',
            'suket'       => 'surat_keterangans',
            'skkelahiran' => 'surat_kelahirans',
            'skkematian'  => 'surat_kematians',
        ];

        $table = $aliasToTable[$jenisSurat];
        $dataSurat = DB::table($table)->where('id', $id)->first();

        if (!$dataSurat) {
            abort(404, 'Data surat tidak ditemukan.');
        }

        $nik = $dataSurat->nik_pelapor ?? $dataSurat->nik ?? null;

        if (auth()->user()->role_id == 2) {
            if ($nik !== auth()->user()->nik) {
                abort(403, 'Anda tidak memiliki akses ke surat ini.');
            }
        }

        $logs = Log_surat::where('tabel_surat', $table)
            ->where('id_surat', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($logs->isEmpty()) {
            abort(404, 'Riwayat surat tidak ditemukan.');
        }

        $current = $logs->first();
        $status  = $current->status_surat;
        $nomorSurat = $this->getNoSrt($dataSurat);
        $isSKTM = ($jenisSurat == 'sktm');

        $step = match (true) {
            $status <= 0                        => 1,
            in_array($status, [1, 2, 3])          => 2,
            $isSKTM && in_array($status, [4, 8]) => 2,
            $status == 4                        => 3,
            $isSKTM && $status == 9             => 3,
            $status == 5                        => 4,
            default                             => 1,
        };

        $wib = fn($log) => $log?->created_at?->timezone('Asia/Jakarta');
        $times = [
            'diajukan' => $wib($logs->first(fn($l) => $l->status_surat == 0)),
            'proses'   => $wib($logs->firstWhere('status_surat', 1)),
            'selesai'  => $wib($logs->first(fn($l) => $l->status_surat == 4 || ($isSKTM && $l->status_surat == 9))),
            'nilai'    => $wib($logs->firstWhere('status_surat', 5)),
        ];

        $ditolak = ($status == 6);
        $selesai = (!$ditolak && !is_null($times['selesai']));
        $dinilai = !is_null($times['nilai']);

        $bar = match (true) {
            $ditolak => 'DITOLAK',
            $dinilai => 'DINILAI',
            $selesai => 'SELESAI',
            $step == 2 => 'DIPROSES',
            default => 'DIAJUKAN',
        };

        if (view()->exists("tracking.$jenisSurat.warga")) {
            return view("tracking.$jenisSurat.warga", compact(
                'logs',
                'step',
                'bar',
                'times',
                'alias',
                'id',
                'nomorSurat'
            ));
        }

        return view('tracking', compact(
            'jenisSurat',
            'logs',
            'step',
            'bar',
            'times',
            'alias',
            'id',
            'nomorSurat'
        ));
    }



    /**
     * Ambil variable surat sebagai array, aman untuk JSON baru, array cast Laravel,
     * JSON double-encoded lama, dan serialize lama.
     */
    private function rowVariableArray(object $row): array
    {
        $value = data_get($row, 'raw.variable', data_get($row, 'variable'));

        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return json_decode(json_encode($value), true) ?: [];
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_string($decoded)) {
                    $decodedAgain = json_decode($decoded, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedAgain)) {
                        return $decodedAgain;
                    }
                }

                if (is_array($decoded)) {
                    return $decoded;
                }
            }

            $unserialized = @unserialize($value);
            if ($unserialized !== false && is_array($unserialized)) {
                return $unserialized;
            }
        }

        return [];
    }

    /**
     * Filter khusus tabel Beranda.
     * Tabel di bawah grafik hanya menampilkan data final/disetujui.
     *
     * Status final TTE:
     * - 4 = selesai/disetujui lurah
     * - 9 = selesai/disetujui camat/SKTM
     *
     * Status final TTD Basah / Cetak Mandiri:
     * - label accessor st = "Sudah Upload Bukti"
     *   karena alur TTD Basah tidak melewati TTE sekkel/lurah, tetapi selesai setelah bukti diupload.
     */
    private function isFinalForHomeTable(object $row): bool
    {
        $status = (int) ($row->status ?? data_get($row, 'raw.status', -999));
        $statusName = strtolower(trim((string) data_get($row, 'raw.st.name', data_get($row, 'st.name', ''))));

        return in_array($status, [4, 9], true)
            || $statusName === 'sudah upload bukti';
    }

    /**
     * Bucket status khusus grafik Beranda.
     * Grafik harus menghitung ALL data, tetapi label manual TTD Basah perlu dirapikan:
     * - "TTD Basah - Belum Upload Bukti" => Diproses
     * - "Sudah Upload Bukti"             => Selesai
     *
     * Fungsi ini hanya untuk tampilan grafik dan tidak mengubah database.
     */
    private function chartStatusBucket(object $row): int
    {
        $statusName = strtolower(trim((string) data_get($row, 'raw.st.name', data_get($row, 'st.name', ''))));

        if ($statusName === 'ttd basah - belum upload bukti') {
            return 1; // Diproses
        }

        if ($statusName === 'sudah upload bukti') {
            return 4; // Selesai
        }

        return (int) ($row->status ?? data_get($row, 'raw.status', 0));
    }

    public function chartDrilldown(SuratCollection $service)
    {
        $user = auth()->user();

        // Ambil semua data sesuai role
        $surat = $service->getAllForUser($user);

        // Ambil daftar semua jenis dari SuratCollection
        $allJenis = array_keys($service->getConfig());

        // Ambil daftar semua status untuk grafik.
        // Catatan khusus TTD Basah / Cetak Mandiri:
        // - Belum memilih TTE/TTD Basah mengikuti status asli, biasanya Diajukan.
        // - TTD Basah - Belum Upload Bukti dihitung sebagai Diproses.
        // - Sudah Upload Bukti dihitung sebagai Selesai.
        $statusLabel = [
            0 => 'Diajukan',
            1 => 'Diproses',
            2 => 'Dinaikkan ke Sekkel',
            3 => 'Dinaikkan ke Lurah',
            4 => 'Selesai',
            5 => 'Dinilai',
            6 => 'Ditolak',
            7 => 'Dihapus Warga',
            8 => 'Dinaikkan ke Camat (SKTM)',
            9 => 'Selesai (SKTM)',
        ];

        // ============================
        // LEVEL 1 — TOTAL PER JENIS
        // ============================
        $level1 = [];

        foreach ($allJenis as $jenis) {
            $level1[$jenis] = $surat->where('jenis', $jenis)->count();
        }

        // ============================
        // LEVEL 2 — STATUS PER JENIS
        // ============================
        $level2 = [];

        foreach ($allJenis as $jenis) {

            // Ambil data hanya untuk jenis ini
            $items = $surat->where('jenis', $jenis);

            // Hitung status untuk grafik memakai bucket khusus.
            // Ini tidak mengubah status database; hanya cara pengelompokan di grafik.
            $statusCounts = $items->groupBy(fn($row) => $this->chartStatusBucket($row))
                ->map(fn($r) => $r->count())
                ->toArray();

            $data = [];

            // Generate SEMUA status meskipun 0
            foreach ($statusLabel as $code => $label) {
                $data[] = [
                    $label,
                    $statusCounts[$code] ?? 0
                ];
            }

            $level2[] = [
                'id'   => strtolower($jenis),
                'name' => strtoupper($jenis),
                'data' => $data,
            ];
        }

        return response()->json([
            'level1' => $level1,
            'level2' => $level2,
        ]);
    }
}
