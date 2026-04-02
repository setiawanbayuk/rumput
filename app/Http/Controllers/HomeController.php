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
            $data = $service->getAllForUser($user);

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

                    if (in_array($role, [1, 8, 9])) {
                        return view('includes.button-admin', compact('id', 'route', 'status'));
                    } elseif (in_array($role, [3, 5])) {
                        $nomorSurat = $this->getNoSrt($row->raw);
                        return view('includes.button-kaopd', compact('id', 'status', 'nomorSurat', 'jenis', 'role', 'route'));
                    } else {
                        return view('includes.button-verifikator', compact('id', 'status', 'role', 'route'));
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

    public function chartDrilldown(SuratCollection $service)
    {
        $user = auth()->user();

        // Ambil semua data sesuai role
        $surat = $service->getAllForUser($user);

        // Ambil daftar semua jenis dari SuratCollection
        $allJenis = array_keys($service->getConfig());

        // Ambil daftar semua status
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

            // Hitung status yang ada
            $statusCounts = $items->groupBy('status')
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
