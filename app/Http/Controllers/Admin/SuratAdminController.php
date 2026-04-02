<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratPengajuan;
use App\Models\Resident;
use App\Models\Log_surat;
use App\Models\Kelurahan;
use App\Traits\GeneratePDF;
use App\Traits\GetNoSurat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SuratAdminController extends Controller
{
    use GetNoSurat, GeneratePDF;
    public function index()
    {
        $user = auth()->user();

        if (request()->ajax()) {
            // Query ke tabel tunggal
            $query = SuratPengajuan::with('penduduk');

            // Filter berdasarkan Role (RT/RW hanya lihat wilayahnya)
            if ($user->role_id == 8) {
                $query->where('id_kel', $user->id_instansi)
                    ->where('id_rw', $user->id_rw)
                    ->where('id_rt', $user->id_rt);
            } elseif (in_array($user->role_id, [3, 4, 5, 6])) {
                $query->where('id_kel', $user->id_instansi);
            }

            // Filter berdasarkan Jenis Surat (Opsional jika ingin difilter via URL)
            if (request()->has('jenis') && request()->jenis != '') {
                $query->where('jenis_surat', request()->jenis);
            }
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('no_surat', function ($row) {
                    // Gunakan fungsi penomoran universal Anda
                    return $this->getNoSrt($row);
                })
                ->addColumn('tipe', function ($row) {
                    // Menampilkan label jenis surat (SKBN, SKTM, dll)
                    return '<span class="badge bg-info text-dark">' . strtoupper($row->jenis_surat) . '</span>';
                })->editColumn('tgl_surat', function ($row) {
                    return Carbon::parse($row->tgl_surat)->isoFormat('D MMMM Y');
                })
                ->addColumn('action', function ($row) use ($user) {
                    $id         = $row->id;
                    $status     = $row->status;
                    $role       = $user->role_id;
                    $nomorSurat = $this->getNoSrt($row);
                    $jenis      = $row->jenis_surat; // Dinamis dari kolom database
                    $route      = 'admin.surat.edit'; // Route universal

                    if ($role == 1) {
                        return view('includes.button-admin', compact('id', 'route', 'status'));
                    } elseif (in_array($role, [3, 5])) {
                        return view('includes.button-kaopd', compact('id', 'status', 'nomorSurat', 'jenis', 'role', 'route'));
                    } else {
                        return view('includes.button-verifikator', compact('id', 'status', 'role', 'route'));
                    }
                })
                ->rawColumns(['action', 'tipe'])
                ->make(true);
        }

        $title = "Daftar Pengajuan Surat";
        return view('admin.surat.index', compact('title'));
    }

    // Form input surat baru oleh Admin
    public function create($jenis)
    {
        // $jenis bisa: skbn, sktm, skdom, dll
        return view('admin.surat.create', ['jenis' => $jenis]);
    }

    // Simpan data dari Web Admin
    public function store(Request $request)
    {
        $request->validate([
            'jenis_surat' => 'required',
            'nik'         => 'required|digits:16',
            'peruntukan'  => 'required',
            'kepada'      => 'required',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $user = auth()->user();

                // Ambil data resident
                $resident = Resident::where('nik', $request->nik)->firstOrFail();
                $regional = Kelurahan::with('skpd')->find($resident->data['kelurahan']);

                // Pisahkan data kolom utama vs data JSON variable
                $allInput = $request->all();
                $mainColumns = ['_token', 'jenis_surat', 'nik', 'peruntukan', 'kepada', 'pengantar'];
                $variableData = array_diff_key($allInput, array_flip($mainColumns));

                // Admin biasanya tidak wajib upload pengantar, atau bisa null
                $fileUrl = null;
                if ($request->hasFile('pengantar')) {
                    $path = $request->file('pengantar')->store("public/pengantar/" . date('Y') . "/{$request->jenis_surat}");
                    $fileUrl = str_replace('public/', '/storage/', $path);
                }

                $surat = SuratPengajuan::create([
                    'jenis_surat' => $request->jenis_surat,
                    'nik'         => $request->nik,
                    'id_kel'      => $user->id_instansi,
                    'id_rw'       => $user->id_rw,
                    'id_rt'       => $user->id_rt,
                    'tahun'       => date('Y'),
                    'tgl_surat'   => now(),
                    'peruntukan'  => $request->peruntukan,
                    'kepada'      => $request->kepada,
                    'status'      => 2, // Admin kelurahan input biasanya langsung status "Verifikasi Kelurahan"
                    'pengantar'   => $fileUrl,
                    'variable'    => $variableData
                ]);

                Log_surat::create([
                    'nik' => $request->nik,
                    'tabel_surat' => 'surat_pengajuans',
                    'nama_surat' => strtoupper($request->jenis_surat),
                    'id_surat' => $surat->id,
                    'status_surat' => 2,
                ]);

                return redirect()->route('admin.surat.index')->with('success', 'Surat berhasil dibuat.');
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function tolak($id)
    {
        // 1. Cari data di tabel tunggal
        $surat = SuratPengajuan::find($id);

        if ($surat) {
            // 2. Update status ke 6 (Tolak) sesuai StatusSuratTrait
            $surat->update(['status' => 6]);

            // 3. Catat ke Log secara dinamis
            Log_surat::create([
                'nik'          => $surat->nik,
                'tabel_surat'  => 'surat_pengajuans', // Sekarang semua tabelnya sama
                'nama_surat'   => strtoupper($surat->jenis_surat), // Mengambil jenis surat (skbn/sktm/dll)
                'id_surat'     => $surat->id,
                'status_surat' => 6,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Pengajuan berhasil ditolak.',
                'data'    => [
                    'id'    => $id,
                    'jenis' => $surat->jenis_surat
                ]
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Data tidak ditemukan atau gagal diperbarui.'
        ], 404);
    }

    public function proses($id)
    {
        // 1. Cari data di tabel tunggal (surat_pengajuans)
        $surat = SuratPengajuan::find($id);

        if ($surat) {
            // 2. Update status ke 1 (Proses)
            $surat->update(['status' => 1]);

            // 3. Catat Log secara dinamis menggunakan jenis_surat dari database
            Log_surat::create([
                'nik'          => $surat->nik,
                'tabel_surat'  => 'surat_pengajuans',
                'nama_surat'   => strtoupper($surat->jenis_surat),
                'id_surat'     => $surat->id,
                'status_surat' => 1,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Surat berhasil diproses.',
                'data'    => $id
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal memproses: Data tidak ditemukan.'
        ], 404);
    }
}
