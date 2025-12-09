<?php

namespace App\Http\Controllers;

use App\Http\Resources\Kelurahan_resource;
use App\Http\Resources\Pejabat_resource;
use App\Http\Resources\User_resource;
use App\Http\Resources\Skpd_resource;
use App\Models\Gender;
use App\Models\Kelurahan;
use App\Models\JenisSurat;
use App\Models\Kewarganegaraan;
use App\Models\Log_surat;
use App\Models\Pejabat;
use App\Models\Resident;
use App\Models\SuratKelahiran;
use App\Models\Skpd;
use App\Models\User;
use App\Traits\GetNoSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class SkkelahiranController extends Controller
{
    use GetNoSurat;
    public function index()
    {
        $user = auth()->user();
        $rt = $user->id_rt;
        $rw = $user->id_rw;
        $resident = Resident::where('nik', $user->nik)->first();
        $penduduk = unserialize($resident->data);
        $kelurahan = $penduduk['kelurahan_nm'];
        if (request()->ajax()) {
            $query = SuratKelahiran::query();

            // Kalau role RT → filter berdasarkan id_kel, id_rw, dan id_rt
            if ($user->role_id == 8) { // contoh: RT
                $query->where('id_kel', $user->id_instansi)
                    ->where('id_rw', $user->id_rw)
                    ->where('id_rt', $user->id_rt);
            }
            // Kalau role Sekkel, Lurah, Sekcam, atau Camat → filter berdasarkan id_kel
            elseif (in_array($user->role_id, [3, 4, 5, 6])) { // sesuaikan ID role-mu
                $query->where('id_kel', $user->id_instansi);
            }
            // Role lain (admin, warga, dll) → tanpa filter tambahan

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($user) {
                    $nomorSurat = $this->getNoSrt($row);
                    $id = $row->id;
                    $route = 'skkelahiran.edit';
                    $status = $row->status;
                    $jenis = 'skkelahiran';
                    $role = $user->role_id;

                    if (in_array($role, [1, 8, 9])) {
                        return view('includes.button-admin', compact('id', 'route', 'status'));
                    } elseif (in_array($role, [3, 5])) {
                        return view('includes.button-kaopd', compact('id', 'status', 'nomorSurat', 'jenis', 'role', 'route'));
                    } else {
                        return view('includes.button-verifikator', compact('id', 'status', 'role', 'route'));
                    }
                })
                ->addColumn('no_surat', function ($row) {
                    return $this->getNoSrt($row);
                })
                ->rawColumns(['action', 'no_surat'])
                ->make(true);
        };
        $title = "Surat Keterangan Kelahiran";
        return view('skkelahiran.index', compact('title', 'rt', 'rw', 'kelurahan'));
    }

    public function warga()
    {
        // dd(auth()->user()->nik);
        // Bagian halaman utama
        $user  = auth()->user();
        $nik   = $user->nik;
        $q     = request('q'); // ← ambil keyword
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KELAHIRAN";
        $nama  = "SURAT KETERANGAN KELAHIRAN";
        $jenis = 'skkelahiran';

        // kartu pilihan surat & detail surat skbn
        $surat        = JenisSurat::where('is_active', true)->get(['jenis', 'assets', 'name']);
        $detail_surat = JenisSurat::where(['jenis' => 'skkelahiran', 'is_active' => true])->get();

        // info SKPD untuk header
        $skpd = new Skpd_resource(Skpd::find($user->id_instansi));

        // validasi data resident
        $resident = Resident::where('nik', $nik)->first();
        if (!$resident) {
            return redirect()
                ->route('profile')
                ->with('status', 'Lengkapi data pribadi dahulu! Terima kasih');
        }

        // ===== BASE QUERY: surat_skbn milik user + search OPTIONAL =====
        $items = SuratKelahiran::query()
            ->where('nik_pelapor', $nik)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('no_urut_surat', 'like', "%{$q}%")
                        ->orWhere('nama_anak',   'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at');

        // ===== TAB: Sedang Proses (0..4) =====
        $sedangProses = (clone $items)
            ->whereIn('status', [0, 1, 2, 3, 4])
            ->paginate(10, ['*'], 'proses_page');

        $sedangProses->getCollection()->transform(function ($surat) {
            $surat->nomor_surat = $this->getNoSrt($surat);
            return $surat;
        });
        
        // ===== TAB: Riwayat (5) =====
        $riwayat = (clone $items)
            ->whereIn('status', [5, 6])
            ->paginate(10, ['*'], 'riwayat_page');

        $riwayat->getCollection()->transform(function ($surat) {
            $surat->nomor_surat = $this->getNoSrt($surat);
            return $surat;
        });
        
        return view('skkelahiran.warga', compact(
            'title',
            'nama',
            'nik',
            'surat',
            'detail_surat',
            'jenis',
            'skpd',
            'sedangProses',
            'riwayat'
        ));
    }

    public function addwarga()
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KELAHIRAN";
        $nik   = auth()->user()->nik;

        // konsisten dengan pengecekan di warga()
        $resident = Resident::where('nik', $nik)->first();
        if (!$resident) {
            return redirect()->route('profile')
                ->with('status', 'Lengkapi data pribadi dahulu! Terima kasih');
        }

        return view('skkelahiran.addwarga', compact('title', 'nik'));
    }

    public function editwarga($id)
    {
        // dd($id);
        $title = "SURAT KETERANGAN KELAHIRAN";
        $suratKeterangan = SuratKelahiran::find($id);

        return view('skkelahiran.editwarga', compact('title', 'suratKeterangan'));
    }

    public function updatewarga(Request $request, $id)
    {
        $suratKeterangan = SuratKelahiran::findOrFail($id);

        $rules = [
            'nik_pelapor' => ['required', 'min:16'],
            'nik_saksi1' => ['required', 'min:16'],
            'kk_saksi1' => ['required', 'min:16'],
            'name_saksi1' => ['required', 'string'],
            'kewarganegaraan_saksi2' => ['required', 'string'],
            'nik_saksi2' => ['required', 'min:16'],
            'kk_saksi2' => ['required', 'min:16'],
            'name_saksi2' => ['required', 'string'],
            'kewarganegaraan_saksi2' => ['required', 'string'],
            'nik_ayah' => ['required', 'min:16'],
            'kk_ayah' => ['required', 'min:16'],
            'name_ayah' => ['required', 'string'],
            'tempat_lhr_ayah' => ['required', 'string'],
            'tgl_lhr_ayah' =>  ['required', 'date'],
            'kewarganegaraan_ayah' => ['required', 'string'],
            'nik_ibu' => ['required', 'min:16'],
            'kk_ibu' => ['required', 'min:16'],
            'name_ibu' => ['required', 'string'],
            'tempat_lhr_ibu' => ['required', 'string'],
            'tgl_lhr_ibu' =>  ['required', 'date'],
            'kewarganegaraan_ibu' => ['required', 'string'],
            'name_anak' => ['required', 'string'],
            'gender_anak' => ['required', 'string'],
            'tempat_dilahirkan_anak' => ['required', 'string'],
            'tempat_kelahiran_anak' => ['required', 'string'],
            'hari_lhr_anak' => ['required', 'string'],
            'tgl_lhr_anak' =>  ['required', 'date'],
            'jam_lhr_anak' =>  ['required'],
            'jenis_klhr_anak' => ['required', 'string'],
            'klhr_ke_anak' => ['required', 'string'],
            'penolong_klhr_anak' => ['required', 'string'],
            'bb_anak' => ['required', 'string'],
            'tb_anak' => ['required', 'string'],
        ];

        if ($request->hasFile('pengantar')) {
            $rules['pengantar'] = 'mimes:jpg,jpeg,png';
        }

        $request->validate($rules);

        // === HANDLE FILE PENGANTAR ===
        $fileLocation = $suratKeterangan->pengantar; // default: pakai file lama

        if ($request->hasFile('pengantar')) {

            // Hapus file lama jika ada
            if ($suratKeterangan->pengantar && Storage::exists(str_replace('/storage/', 'public/', $suratKeterangan->pengantar))) {
                Storage::delete(str_replace('/storage/', 'public/', $suratKeterangan->pengantar));
            }

            // Upload file baru
            $path = '/public/pengantar/' . date('Y') . '/skkelahiran';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skkelahiran/' . $fileName;

            $request->file('pengantar')->storeAs($path, $fileName);
        }

        $nik_pelapor = $suratKeterangan->nik_pelapor;
        $nama_pelapor = $suratKeterangan->nama_pelapor;
        $kk_pelapor = $suratKeterangan->kk_pelapor;
        $gender_anak = Gender::find($request->gender_anak);
        $kewarganegaraan_pelapor = $suratKeterangan->kewarganegaraan_pelapor;
        $kewarganegaraan_pelapor_nm = $suratKeterangan->kewarganegaraan_pelapor_nm;
        $kewarganegaraan_saksi1 = Kewarganegaraan::find($request->kewarganegaraan_saksi1);
        $kewarganegaraan_saksi2 = Kewarganegaraan::find($request->kewarganegaraan_saksi2);
        $kewarganegaraan_ayah = Kewarganegaraan::find($request->kewarganegaraan_ayah);
        $kewarganegaraan_ibu = Kewarganegaraan::find($request->kewarganegaraan_ibu);

        // === UPDATE DATA ===
        $suratKeterangan->update([
            'nama_pelapor' => $nama_pelapor,
            'nik_pelapor' => $nik_pelapor,
            'kk_pelapor' => $kk_pelapor,
            'kewarganegaraan_pelapor' => $kewarganegaraan_pelapor,
            'kewarganegaraan_pelapor_nm' => $kewarganegaraan_pelapor_nm,
            'nama_saksi1' => $request->name_saksi1,
            'nik_saksi1' => $request->nik_saksi1,
            'kk_saksi1' => $request->kk_saksi1,
            'kewarganegaraan_saksi1' => $request->kewarganegaraan_saksi1,
            'kewarganegaraan_saksi1_nm' => $kewarganegaraan_saksi1->nama,
            'nama_saksi2' => $request->name_saksi2,
            'nik_saksi2' => $request->nik_saksi2,
            'kk_saksi2' => $request->kk_saksi2,
            'kewarganegaraan_saksi2' => $request->kewarganegaraan_saksi2,
            'kewarganegaraan_saksi2_nm' => $kewarganegaraan_saksi2->nama,
            'nama_ayah' => $request->name_ayah,
            'nik_ayah' => $request->nik_ayah,
            'kk_ayah' => $request->kk_ayah,
            'tempat_lhr_ayah' => $request->tempat_lhr_ayah,
            'tgl_lhr_ayah' => $request->tgl_lhr_ayah,
            'kewarganegaraan_ayah' => $request->kewarganegaraan_ayah,
            'kewarganegaraan_ayah_nm' => $kewarganegaraan_ayah->nama,
            'nama_ibu' => $request->name_ibu,
            'nik_ibu' => $request->nik_ibu,
            'kk_ibu' => $request->kk_ibu,
            'tempat_lhr_ibu' => $request->tempat_lhr_ibu,
            'tgl_lhr_ibu' => $request->tgl_lhr_ibu,
            'kewarganegaraan_ibu' => $request->kewarganegaraan_ibu,
            'kewarganegaraan_ibu_nm' => $kewarganegaraan_ibu->nama,
            'nama_anak' => $request->name_anak,
            'gender_anak' => $request->gender_anak,
            'gender_anak_nm' => $gender_anak->nama,
            'tempat_dilahirkan_anak' => $request->tempat_dilahirkan_anak,
            'tempat_kelahiran_anak' => $request->tempat_kelahiran_anak,
            'hari_lhr_anak' => $request->hari_lhr_anak,
            'tgl_lhr_anak' => $request->tgl_lhr_anak,
            'jam_lhr_anak' => $request->jam_lhr_anak,
            'jenis_klhr_anak' => $request->jenis_klhr_anak,
            'klhr_ke_anak' => $request->klhr_ke_anak,
            'penolong_klhr_anak' => $request->penolong_klhr_anak,
            'bb_anak' => $request->bb_anak,
            'tb_anak' => $request->tb_anak,
            'pengantar' => $fileLocation
        ]);

        // === RESPONSE ===
        if ($request->segment(1) == 'api') {
            return response()->json(['message' => 'Surat berhasil diperbarui!'], 200);
        }

        return redirect()->route('skkelahiran.warga')->with('success', 'Data berhasil diperbarui');
    }


    public function show($id)
    {
        $title = "SURAT KETERANGAN KELAHIRAN";
        $suratKeterangan = SuratKelahiran::findOrFail($id);

        return view('skkelahiran.show', compact('suratKeterangan', 'title'));
    }

    public function add()
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KELAHIRAN";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $no_urut_surat = SuratKelahiran::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
        $no_urut_surat = intval($no_urut_surat) + 1;
        return view('skkelahiran.add', compact('title', 'currentUser', 'no_urut_surat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_jenis_surat' => ['required', 'string'],
            'no_urut_surat' => ['required', 'string'],
            'id_instansi' => ['required', 'string'],
            'tahun' => ['required', 'string'],
            'tgl_surat' => ['required', 'date'],
            'nik_pelapor' => ['required', 'min:16'],
            'kk_pelapor' => ['required', 'min:16'],
            'name_pelapor' => ['required', 'string'],
            'kewarganegaraan_pelapor' => ['required', 'string'],
            'nik_saksi1' => ['required', 'min:16'],
            'kk_saksi1' => ['required', 'min:16'],
            'name_saksi1' => ['required', 'string'],
            'kewarganegaraan_saksi2' => ['required', 'string'],
            'nik_saksi2' => ['required', 'min:16'],
            'kk_saksi2' => ['required', 'min:16'],
            'name_saksi2' => ['required', 'string'],
            'kewarganegaraan_saksi2' => ['required', 'string'],
            'nik_ayah' => ['required', 'min:16'],
            'kk_ayah' => ['required', 'min:16'],
            'name_ayah' => ['required', 'string'],
            'tempat_lhr_ayah' => ['required', 'string'],
            'tgl_lhr_ayah' =>  ['required', 'date'],
            'kewarganegaraan_ayah' => ['required', 'string'],
            'nik_ibu' => ['required', 'min:16'],
            'kk_ibu' => ['required', 'min:16'],
            'name_ibu' => ['required', 'string'],
            'tempat_lhr_ibu' => ['required', 'string'],
            'tgl_lhr_ibu' =>  ['required', 'date'],
            'kewarganegaraan_ibu' => ['required', 'string'],
            'name_anak' => ['required', 'string'],
            'gender_anak' => ['required', 'string'],
            'tempat_dilahirkan_anak' => ['required', 'string'],
            'tempat_kelahiran_anak' => ['required', 'string'],
            'hari_lhr_anak' => ['required', 'string'],
            'tgl_lhr_anak' =>  ['required', 'date'],
            'jam_lhr_anak' =>  ['required'],
            'jenis_klhr_anak' => ['required', 'string'],
            'klhr_ke_anak' => ['required', 'string'],
            'penolong_klhr_anak' => ['required', 'string'],
            'bb_anak' => ['required', 'string'],
            'tb_anak' => ['required', 'string'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png']
        ]);

        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skkelahiran', 0755);
            $path = '/public/pengantar/' . date('Y') . '/skkelahiran';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skkelahiran/' . $fileName;
            $request->file('pengantar')->storeAs($path, $fileName);
        }


        $gender_anak = Gender::find($request->gender_anak);
        $kewarganegaraan_pelapor = Kewarganegaraan::find($request->kewarganegaraan_pelapor);
        $kewarganegaraan_saksi1 = Kewarganegaraan::find($request->kewarganegaraan_saksi1);
        $kewarganegaraan_saksi2 = Kewarganegaraan::find($request->kewarganegaraan_saksi2);
        $kewarganegaraan_ayah = Kewarganegaraan::find($request->kewarganegaraan_ayah);
        $kewarganegaraan_ibu = Kewarganegaraan::find($request->kewarganegaraan_ibu);

        // dd($kewarganegaraan_pelapor->nama);

        $suket = SuratKelahiran::create([
            'id_kel'    => auth()->user()->id_instansi,
            'kd_jenis_surat' => $request->kd_jenis_surat,
            'no_urut_surat' => $request->no_urut_surat,

            'tgl_surat' => $request->tgl_surat,
            'nama_pelapor' => $request->name_pelapor,
            'nik_pelapor' => $request->nik_pelapor,
            'kk_pelapor' => $request->kk_pelapor,
            'kewarganegaraan_pelapor' => $request->kewarganegaraan_pelapor,
            'kewarganegaraan_pelapor_nm' => $kewarganegaraan_pelapor->nama,
            'no_dokumen_perjalanan' => $request->no_dokumen_perjalanan,
            'nama_saksi1' => $request->name_saksi1,
            'nik_saksi1' => $request->nik_saksi1,
            'kk_saksi1' => $request->kk_saksi1,
            'kewarganegaraan_saksi1' => $request->kewarganegaraan_saksi1,
            'kewarganegaraan_saksi1_nm' => $kewarganegaraan_saksi1->nama,
            'nama_saksi2' => $request->name_saksi2,
            'nik_saksi2' => $request->nik_saksi2,
            'kk_saksi2' => $request->kk_saksi2,
            'kewarganegaraan_saksi2' => $request->kewarganegaraan_saksi2,
            'kewarganegaraan_saksi2_nm' => $kewarganegaraan_saksi2->nama,
            'nama_ayah' => $request->name_ayah,
            'nik_ayah' => $request->nik_ayah,
            'kk_ayah' => $request->kk_ayah,
            'tempat_lhr_ayah' => $request->tempat_lhr_ayah,
            'tgl_lhr_ayah' => $request->tgl_lhr_ayah,
            'kewarganegaraan_ayah' => $request->kewarganegaraan_ayah,
            'kewarganegaraan_ayah_nm' => $kewarganegaraan_ayah->nama,
            'nama_ibu' => $request->name_ibu,
            'nik_ibu' => $request->nik_ibu,
            'kk_ibu' => $request->kk_ibu,
            'tempat_lhr_ibu' => $request->tempat_lhr_ibu,
            'tgl_lhr_ibu' => $request->tgl_lhr_ibu,
            'kewarganegaraan_ibu' => $request->kewarganegaraan_ibu,
            'kewarganegaraan_ibu_nm' => $kewarganegaraan_ibu->nama,
            'nama_anak' => $request->name_anak,
            'gender_anak' => $request->gender_anak,
            'gender_anak_nm' => $gender_anak->nama,
            'tempat_dilahirkan_anak' => $request->tempat_dilahirkan_anak,
            'tempat_kelahiran_anak' => $request->tempat_kelahiran_anak,
            'hari_lhr_anak' => $request->hari_lhr_anak,
            'tgl_lhr_anak' => $request->tgl_lhr_anak,
            'jam_lhr_anak' => $request->jam_lhr_anak,
            'jenis_klhr_anak' => $request->jenis_klhr_anak,
            'klhr_ke_anak' => $request->klhr_ke_anak,
            'penolong_klhr_anak' => $request->penolong_klhr_anak,
            'bb_anak' => $request->bb_anak,
            'tb_anak' => $request->tb_anak,
            'status' => 1,
            'pengantar' => $request->file('pengantar') ? $fileLocation : '',
        ]);

        Log_surat::create([
            'nik' => $request->nik_pelapor,
            'tabel_surat' => 'surat_kelahirans',
            'nama_surat' => 'SURAT KETERANGAN KELAHIRAN',
            'id_surat' => $suket->id,
            'status_surat' => 1,
        ]);

        return redirect()->route('skkelahiran.index');
    }

    public function edit($id)
    {
        // dd($id);
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KELAHIRAN";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));

        $suratKeterangan = SuratKelahiran::find($id);
        if ($suratKeterangan->no_urut_surat == 0) {
            $no_urut_surat = SuratKelahiran::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
            $suratKeterangan->no_urut_surat = intval($no_urut_surat) + 1;
        }

        return view('skkelahiran.edit', compact('title', 'currentUser', 'suratKeterangan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kd_jenis_surat' => ['required', 'string'],
            'no_urut_surat' => ['required', 'string'],
            'id_instansi' => ['required', 'string'],
            'tahun' => ['required', 'string'],
            'tgl_surat' => ['required', 'date'],
            'nik_pelapor' => ['required', 'min:16'],
            'kk_pelapor' => ['required', 'min:16'],
            'name_pelapor' => ['required', 'string'],
            'kewarganegaraan_pelapor' => ['required', 'string'],
            'nik_saksi1' => ['required', 'min:16'],
            'kk_saksi1' => ['required', 'min:16'],
            'name_saksi1' => ['required', 'string'],
            'kewarganegaraan_saksi2' => ['required', 'string'],
            'nik_saksi2' => ['required', 'min:16'],
            'kk_saksi2' => ['required', 'min:16'],
            'name_saksi2' => ['required', 'string'],
            'kewarganegaraan_saksi2' => ['required', 'string'],
            'nik_ayah' => ['required', 'min:16'],
            'kk_ayah' => ['required', 'min:16'],
            'name_ayah' => ['required', 'string'],
            'tempat_lhr_ayah' => ['required', 'string'],
            'tgl_lhr_ayah' =>  ['required', 'date'],
            'kewarganegaraan_ayah' => ['required', 'string'],
            'nik_ibu' => ['required', 'min:16'],
            'kk_ibu' => ['required', 'min:16'],
            'name_ibu' => ['required', 'string'],
            'tempat_lhr_ibu' => ['required', 'string'],
            'tgl_lhr_ibu' =>  ['required', 'date'],
            'kewarganegaraan_ibu' => ['required', 'string'],
            'name_anak' => ['required', 'string'],
            'gender_anak' => ['required', 'string'],
            'tempat_dilahirkan_anak' => ['required', 'string'],
            'tempat_kelahiran_anak' => ['required', 'string'],
            'hari_lhr_anak' => ['required', 'string'],
            'tgl_lhr_anak' =>  ['required', 'date'],
            'jam_lhr_anak' =>  ['required'],
            'jenis_klhr_anak' => ['required', 'string'],
            'klhr_ke_anak' => ['required', 'string'],
            'penolong_klhr_anak' => ['required', 'string'],
            'bb_anak' => ['required', 'string'],
            'tb_anak' => ['required', 'string'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png']
        ]);

        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skkelahiran', 0755);
            $path = '/public/pengantar/' . date('Y') . '/skkelahiran';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skkelahiran/' . $fileName;
            $request->file('pengantar')->storeAs($path, $fileName);
        }

        $gender_anak = Gender::find($request->gender_anak);
        $kewarganegaraan_pelapor = Kewarganegaraan::find($request->kewarganegaraan_pelapor);
        $kewarganegaraan_saksi1 = Kewarganegaraan::find($request->kewarganegaraan_saksi1);
        $kewarganegaraan_saksi2 = Kewarganegaraan::find($request->kewarganegaraan_saksi2);
        $kewarganegaraan_ayah = Kewarganegaraan::find($request->kewarganegaraan_ayah);
        $kewarganegaraan_ibu = Kewarganegaraan::find($request->kewarganegaraan_ibu);

        $suratKeterangan = SuratKelahiran::find($id);

        if ($suratKeterangan) {
            $suratKeterangan->update([
                'kd_jenis_surat' => $request->kd_jenis_surat,
                'no_urut_surat' => $request->no_urut_surat,
                'tgl_surat' => $request->tgl_surat,
                'nama_pelapor' => $request->name_pelapor,
                'nik_pelapor' => $request->nik_pelapor,
                'kk_pelapor' => $request->kk_pelapor,
                'kewarganegaraan_pelapor' => $request->kewarganegaraan_pelapor,
                'kewarganegaraan_pelapor_nm' => $kewarganegaraan_pelapor->nama,
                'no_dokumen_perjalanan' => $request->no_dokumen_perjalanan,
                'nama_saksi1' => $request->name_saksi1,
                'nik_saksi1' => $request->nik_saksi1,
                'kk_saksi1' => $request->kk_saksi1,
                'kewarganegaraan_saksi1' => $request->kewarganegaraan_saksi1,
                'kewarganegaraan_saksi1_nm' => $kewarganegaraan_saksi1->nama,
                'nama_saksi2' => $request->name_saksi2,
                'nik_saksi2' => $request->nik_saksi2,
                'kk_saksi2' => $request->kk_saksi2,
                'kewarganegaraan_saksi2' => $request->kewarganegaraan_saksi2,
                'kewarganegaraan_saksi2_nm' => $kewarganegaraan_saksi2->nama,
                'nama_ayah' => $request->name_ayah,
                'nik_ayah' => $request->nik_ayah,
                'kk_ayah' => $request->kk_ayah,
                'tempat_lhr_ayah' => $request->tempat_lhr_ayah,
                'tgl_lhr_ayah' => $request->tgl_lhr_ayah,
                'kewarganegaraan_ayah' => $request->kewarganegaraan_ayah,
                'kewarganegaraan_ayah_nm' => $kewarganegaraan_ayah->nama,
                'nama_ibu' => $request->name_ibu,
                'nik_ibu' => $request->nik_ibu,
                'kk_ibu' => $request->kk_ibu,
                'tempat_lhr_ibu' => $request->tempat_lhr_ibu,
                'tgl_lhr_ibu' => $request->tgl_lhr_ibu,
                'kewarganegaraan_ibu' => $request->kewarganegaraan_ibu,
                'kewarganegaraan_ibu_nm' => $kewarganegaraan_ibu->nama,
                'nama_anak' => $request->name_anak,
                'gender_anak' => $request->gender_anak,
                'gender_anak_nm' => $gender_anak->nama,
                'tempat_dilahirkan_anak' => $request->tempat_dilahirkan_anak,
                'tempat_kelahiran_anak' => $request->tempat_kelahiran_anak,
                'hari_lhr_anak' => $request->hari_lhr_anak,
                'tgl_lhr_anak' => $request->tgl_lhr_anak,
                'jam_lhr_anak' => $request->jam_lhr_anak,
                'jenis_klhr_anak' => $request->jenis_klhr_anak,
                'klhr_ke_anak' => $request->klhr_ke_anak,
                'penolong_klhr_anak' => $request->penolong_klhr_anak,
                'bb_anak' => $request->bb_anak,
                'tb_anak' => $request->tb_anak,
                'pengantar' => $request->file('pengantar') ? $fileLocation : $suratKeterangan->pengantar
            ]);

            return redirect()->route('skkelahiran.index');
        } else {
            return redirect()->route('skkelahiran.index');
        }
    }

    public function proses($id)
    {
        $suratKeterangan = SuratKelahiran::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 1]);

            Log_surat::create([
                'nik' => $suratKeterangan->nik_pelapor,
                'tabel_surat' => 'surat_kelahirans',
                'nama_surat' => 'SURAT KETERANGAN KELAHIRAN',
                'id_surat' => $id,
                'status_surat' => 1,
            ]);

            return response()->json(['message' => 'Data updated successfully.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }

    public function naik($id)
    {
        $suratKeterangan = SuratKelahiran::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 2]);

            Log_surat::create([
                'nik' => $suratKeterangan->nik_pelapor,
                'tabel_surat' => 'surat_kelahirans',
                'nama_surat' => 'SURAT KETERANGAN KELAHIRAN',
                'id_surat' => $id,
                'status_surat' => 2,
            ]);

            return response()->json(['message' => 'Data updated successfully.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }

    public function naikLurah($id)
    {
        $suratKeterangan = SuratKelahiran::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 3]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_kelahirans',
                'nama_surat' => 'SURAT KETERANGAN KELAHIRAN',
                'id_surat' => $id,
                'status_surat' => 3,
            ]);
            return response()->json(['message' => 'Data updated successfully.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }

    public function preview($id)
    {
        $surat = SuratKelahiran::find($id);
        $user = new User_resource(User::with('skpd')->find(Auth::id()));
        $pejabat = new Pejabat_resource(Pejabat::where('id_skpd', $user->id_instansi)->first());
        $tglSurat = Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
        $nomorSurat = $this->getNoSrt($surat);

        $url = '';

        $tgl_lhr_ayah = explode('-', $surat->tgl_lhr_ayah);

        $y_lhr_ayah = $tgl_lhr_ayah[0];
        $m_lhr_ayah = $tgl_lhr_ayah[1];
        $d_lhr_ayah = $tgl_lhr_ayah[2];

        $tgl_lhr_ibu = explode('-', $surat->tgl_lhr_ibu);

        $y_lhr_ibu = $tgl_lhr_ibu[0];
        $m_lhr_ibu = $tgl_lhr_ibu[1];
        $d_lhr_ibu = $tgl_lhr_ibu[2];


        $tgl_lhr_anak = explode('-', $surat->tgl_lhr_anak);

        $y_lhr_anak = $tgl_lhr_anak[0];
        $m_lhr_anak = $tgl_lhr_anak[1];
        $d_lhr_anak = $tgl_lhr_anak[2];


        $jam_lhr_anak = explode(':', $surat->jam_lhr_anak);

        $hh_lhr_anak = $jam_lhr_anak[0];
        $mm_lhr_anak = $jam_lhr_anak[1];


        $num = $surat->klhr_ke_anak;
        $num_padded = sprintf("%02d", $num);

        $pdf = Pdf::loadView('skkelahiran.pdf', compact(
            'surat',
            'nomorSurat',
            'pejabat',
            'tglSurat',
            'url',
            'y_lhr_ayah',
            'm_lhr_ayah',
            'd_lhr_ayah',
            'y_lhr_ibu',
            'm_lhr_ibu',
            'd_lhr_ibu',
            'y_lhr_anak',
            'm_lhr_anak',
            'd_lhr_anak',
            'hh_lhr_anak',
            'mm_lhr_anak',
            'num_padded'
        ))->setPaper('legal', 'portrait');
        return $pdf->stream();
    }

    public function cetak($id)
    {
        $surat = SuratKelahiran::find($id);
        return response()->json(['file' => asset($surat->file)]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'nik_pelapor' => ['required', 'min:16'],
            'nik_saksi1' => ['required', 'min:16'],
            'kk_saksi1' => ['required', 'min:16'],
            'name_saksi1' => ['required', 'string'],
            'kewarganegaraan_saksi2' => ['required', 'string'],
            'nik_saksi2' => ['required', 'min:16'],
            'kk_saksi2' => ['required', 'min:16'],
            'name_saksi2' => ['required', 'string'],
            'kewarganegaraan_saksi2' => ['required', 'string'],
            'nik_ayah' => ['required', 'min:16'],
            'kk_ayah' => ['required', 'min:16'],
            'name_ayah' => ['required', 'string'],
            'tempat_lhr_ayah' => ['required', 'string'],
            'tgl_lhr_ayah' =>  ['required', 'date'],
            'kewarganegaraan_ayah' => ['required', 'string'],
            'nik_ibu' => ['required', 'min:16'],
            'kk_ibu' => ['required', 'min:16'],
            'name_ibu' => ['required', 'string'],
            'tempat_lhr_ibu' => ['required', 'string'],
            'tgl_lhr_ibu' =>  ['required', 'date'],
            'kewarganegaraan_ibu' => ['required', 'string'],
            'name_anak' => ['required', 'string'],
            'gender_anak' => ['required', 'string'],
            'tempat_dilahirkan_anak' => ['required', 'string'],
            'tempat_kelahiran_anak' => ['required', 'string'],
            'hari_lhr_anak' => ['required', 'string'],
            'tgl_lhr_anak' =>  ['required', 'date'],
            'jam_lhr_anak' =>  ['required'],
            'jenis_klhr_anak' => ['required', 'string'],
            'klhr_ke_anak' => ['required', 'string'],
            'penolong_klhr_anak' => ['required', 'string'],
            'bb_anak' => ['required', 'string'],
            'tb_anak' => ['required', 'string'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png']
        ]);

        //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skkelahiran', 0755);
        $path = '/public/pengantar/' . date('Y') . '/skkelahiran';
        $fileName = $request->file('pengantar')->hashName();
        $fileLocation = '/storage/pengantar/' . date('Y') . '/skkelahiran/' . $fileName;
        $request->file('pengantar')->storeAs($path, $fileName);


        // ✅ Ambil data pelapor (bukan jenazah)
        $nik_pelapor = $request->nik_pelapor ?? auth()->user()->nik;
        $resident   = Resident::where('nik', $nik_pelapor)->first();

        if (!$resident) {
            return back()->with('error', 'Data pelapor tidak ditemukan di tabel Resident.');
        }
        // $resident = Resident::where('nik', $request->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
        $regional = new Kelurahan_resource(Kelurahan::find($penduduk['kelurahan']));

        // $nik_pelapor = $resident->nik;
        $kk_pelapor = $resident->kk;
        $nama_pelapor = $penduduk['name'] ?? '';
        $kewarganegaraan_pelapor = $penduduk['kewarganegaraan'] ?? null;
        $kewarganegaraan_pelapor_nm = $penduduk['kewarganegaraan_nm'] ?? '';

        $gender_anak = Gender::find($request->gender_anak);
        // $kewarganegaraan_pelapor = Kewarganegaraan::find($request->kewarganegaraan_pelapor);
        $kewarganegaraan_saksi1 = Kewarganegaraan::find($request->kewarganegaraan_saksi1);
        $kewarganegaraan_saksi2 = Kewarganegaraan::find($request->kewarganegaraan_saksi2);
        $kewarganegaraan_ayah = Kewarganegaraan::find($request->kewarganegaraan_ayah);
        $kewarganegaraan_ibu = Kewarganegaraan::find($request->kewarganegaraan_ibu);

        $suket = SuratKelahiran::create([
            'id_kel'    => auth()->user()->id_instansi,
            'id_rw'    => auth()->user()->id_rw,
            'id_rt'    => auth()->user()->id_rt,
            'kd_jenis_surat' => 0,
            'no_urut_surat' => 0,
            'id_instansi' => $regional['skpd']->instansi_kode,
            'tahun' => date('Y'),
            'tgl_surat' => date('Y-m-d'),
            'nama_pelapor' => $nama_pelapor,
            'nik_pelapor' => $nik_pelapor,
            'kk_pelapor' => $kk_pelapor,
            'kewarganegaraan_pelapor' => $kewarganegaraan_pelapor,
            'kewarganegaraan_pelapor_nm' => $kewarganegaraan_pelapor_nm,
            'no_dokumen_perjalanan' => 0,
            'nama_saksi1' => $request->name_saksi1,
            'nik_saksi1' => $request->nik_saksi1,
            'kk_saksi1' => $request->kk_saksi1,
            'kewarganegaraan_saksi1' => $request->kewarganegaraan_saksi1,
            'kewarganegaraan_saksi1_nm' => $kewarganegaraan_saksi1->nama,
            'nama_saksi2' => $request->name_saksi2,
            'nik_saksi2' => $request->nik_saksi2,
            'kk_saksi2' => $request->kk_saksi2,
            'kewarganegaraan_saksi2' => $request->kewarganegaraan_saksi2,
            'kewarganegaraan_saksi2_nm' => $kewarganegaraan_saksi2->nama,
            'nama_ayah' => $request->name_ayah,
            'nik_ayah' => $request->nik_ayah,
            'kk_ayah' => $request->kk_ayah,
            'tempat_lhr_ayah' => $request->tempat_lhr_ayah,
            'tgl_lhr_ayah' => $request->tgl_lhr_ayah,
            'kewarganegaraan_ayah' => $request->kewarganegaraan_ayah,
            'kewarganegaraan_ayah_nm' => $kewarganegaraan_ayah->nama,
            'nama_ibu' => $request->name_ibu,
            'nik_ibu' => $request->nik_ibu,
            'kk_ibu' => $request->kk_ibu,
            'tempat_lhr_ibu' => $request->tempat_lhr_ibu,
            'tgl_lhr_ibu' => $request->tgl_lhr_ibu,
            'kewarganegaraan_ibu' => $request->kewarganegaraan_ibu,
            'kewarganegaraan_ibu_nm' => $kewarganegaraan_ibu->nama,
            'nama_anak' => $request->name_anak,
            'gender_anak' => $request->gender_anak,
            'gender_anak_nm' => $gender_anak->nama,
            'tempat_dilahirkan_anak' => $request->tempat_dilahirkan_anak,
            'tempat_kelahiran_anak' => $request->tempat_kelahiran_anak,
            'hari_lhr_anak' => $request->hari_lhr_anak,
            'tgl_lhr_anak' => $request->tgl_lhr_anak,
            'jam_lhr_anak' => $request->jam_lhr_anak,
            'jenis_klhr_anak' => $request->jenis_klhr_anak,
            'klhr_ke_anak' => $request->klhr_ke_anak,
            'penolong_klhr_anak' => $request->penolong_klhr_anak,
            'bb_anak' => $request->bb_anak,
            'tb_anak' => $request->tb_anak,
            'status' => 0,
            'pengantar' => $fileLocation
        ]);

        Log_surat::create([
            'nik' => $nik_pelapor,
            'tabel_surat' => 'surat_kelahirans',
            'nama_surat' => 'SURAT KETERANGAN KELAHIRAN',
            'id_surat' => $suket->id,
            'status_surat' => 0,
        ]);

        if ($request->segment(1) == 'api') {
            return response()->json(['message' => 'Pengajuan Surat Keterangan Berhasil!'], 200);
        } else {

            return redirect()->route('skkelahiran.warga');
        }
    }

    public function get(Request $request)
    {
        $surat = SuratKelahiran::with(['history' => function ($query) {
            return $query->where('tabel_surat', 'surat_kelahirans');
        }])->where('nik', $request->nik)->orderBy('id', 'desc')->get();
        return response()->json($surat);
    }

    public function nilai(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
        ]);

        $surat = SuratKelahiran::find($id);

        if (!$surat) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // simpan rating & komentar
        $surat->update([
            'status'   => 5,
            'rating'   => $request->rating,
            'komentar' => $request->komentar,
        ]);

        // log status
        Log_surat::create([
            'nik'          => $surat->nik,
            'tabel_surat'  => 'surat_kelahirans',
            'nama_surat'   => 'SURAT KETERANGAN KELAHIRAN',
            'id_surat'     => $id,
            'status_surat' => 5,
        ]);

        return response()->json([
            'message' => 'Penilaian berhasil disimpan.',
            'data' => $id
        ]);
    }

    public function lihatNilai($id)
    {
        // Ambil data surat
        $surat = SuratKelahiran::find($id);

        if (!$surat || $surat->rating === null) {
            return response()->json(['message' => 'Belum ada penilaian'], 404);
        }

        // Ambil waktu nilai dari log_surat (status_surat = 5)
        $log = Log_surat::where('tabel_surat', 'surat_kelahirans')
                    ->where('id_surat', $id)
                    ->where('status_surat', 5)
                    ->orderBy('id', 'DESC')
                    ->first();

        return response()->json([
            'rating'   => $surat->rating,
            'komentar' => $surat->komentar ?? '-',
            'tanggal'  => optional($log?->created_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i'),
        ]);
    }

    public function tolak($id)
    {
        $suratKeterangan = SuratKelahiran::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 6]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_kelahirans',
                'nama_surat' => 'SURAT KETERANGAN KELAHIRAN',
                'id_surat' => $id,
                'status_surat' => 6,
            ]);
            return response()->json(['message' => 'Data updated successfully.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }

    public function hapus($id)
    {
        $suratKeterangan = SuratKelahiran::find($id);
        if (!$suratKeterangan) return response()->json(['message' => 'Data tidak ditemukan.'], 404);

        if (in_array($suratKeterangan->status, ['1', '2', '3', '4', '5'])) {
            return response()->json(['message' => 'Surat sudah selesai atau dinilai, tidak bisa dihapus.'], 422);
        }
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 7]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_kelahirans',
                'nama_surat' => 'SURAT KETERANGAN KELAHIRAN',
                'id_surat' => $id,
                'status_surat' => 7,
            ]);
            return response()->json(['message' => 'Pengajuan berhasil dihapus.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Gagal menghapus.']);
        }
    }
}
