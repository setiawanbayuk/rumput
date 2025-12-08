<?php

namespace App\Http\Controllers;

use App\Http\Resources\Kelurahan_resource;
use App\Http\Resources\Pejabat_resource;
use App\Http\Resources\Regional_resource;
use App\Http\Resources\User_resource;
use App\Http\Resources\Skpd_resource;
use App\Models\Agama;
use App\Models\Gender;
use App\Models\JenisSurat;
use App\Models\Kabko;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kewarganegaraan;
use App\Models\Log_surat;
use App\Models\Pejabat;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Provinsi;
use App\Models\Regional;
use App\Models\Resident;
use App\Models\StatusKwn;
use App\Models\SuratSktm;
use App\Models\Skpd;
use App\Models\SuratTemplate;
use App\Models\User;
use App\Traits\GetNoSurat;
use App\Traits\GeneratePDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\DataTables;

class SktmController extends Controller
{
    use GetNoSurat, GeneratePDF;

    public function index()
    {
        $user = auth()->user();
        $rt = $user->id_rt;
        $rw = $user->id_rw;
        $resident = Resident::where('nik', $user->nik)->first();
        $penduduk = unserialize($resident->data);
        $kelurahan = $penduduk['kelurahan_nm'];
        $kecamatan = $penduduk['kecamatan_nm'];
        if (request()->ajax()) {
            $query = SuratSktm::query();

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
                    $route = 'sktm.edit';
                    $status = $row->status;
                    $jenis = 'sktm';
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
        $title = "Surat Keterangan Miskin";
        return view('sktm.index', compact('title', 'rt', 'rw', 'kelurahan', 'kecamatan'));
    }

    public function warga()
    {
        // Bagian halaman utama
        $user  = auth()->user();
        $nik   = $user->nik;
        $q     = request('q'); // ← ambil keyword
        $title = "USULAN PENGAJUAN SURAT KETERANGAN MISKIN";
        $nama  = "SURAT KETERANGAN MISKIN";
        $jenis = 'sktm';

        // kartu pilihan surat & detail surat skbn
        $surat        = JenisSurat::where('is_active', true)->get(['jenis', 'assets', 'name']);
        $detail_surat = JenisSurat::where(['jenis' => 'sktm', 'is_active' => true])->get();

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
        $items = SuratSktm::query()
            ->where('nik', $nik)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('no_urut_surat', 'like', "%{$q}%")
                        ->orWhere('peruntukan',   'like', "%{$q}%")
                        ->orWhere('kepada',       'like', "%{$q}%")
                        ->orWhere('kepada_sekolah',   'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at');

        // ===== TAB: Sedang Proses (0..4) =====
        $sedangProses = (clone $items)
            ->whereIn('status', [0, 1, 2, 3, 4, 8, 9])
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

        return view('sktm.warga', compact(
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
        $title = "USULAN PENGAJUAN SURAT KETERANGAN MISKIN";
        $nik   = auth()->user()->nik;

        // konsisten dengan pengecekan di warga()
        $resident = Resident::where('nik', $nik)->first();
        if (!$resident) {
            return redirect()->route('profile')
                ->with('status', 'Lengkapi data pribadi dahulu! Terima kasih');
        }

        return view('sktm.addwarga', compact('title', 'nik'));
    }

    public function editwarga($id)
    {
        // dd($id);
        $title = "USULAN PENGAJUAN SURAT KETERANGAN MISKIN";
        $suratKeterangan = SuratSktm::find($id);

        return view('sktm.editwarga', compact('title', 'suratKeterangan'));
    }

    public function updatewarga(Request $request, $id)
    {
        $suratKeterangan = SuratSktm::findOrFail($id);

        $rules = [
            'nik'         => 'required|min:16',
            'peruntukan' => ['required', 'max:100'],
            'register_as' => ['required', 'string'],
            'kepada' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_tempat_lhr' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_tgl_lhr' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_gender' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_hubungan' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_sekolah' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_kelas' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_alamat_sekolah' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kategori' => ['required', 'string'],
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
            $path = '/public/pengantar/' . date('Y') . '/sktm';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/sktm/' . $fileName;

            $request->file('pengantar')->storeAs($path, $fileName);
        }

        $kepada_gender = Gender::find($request->kepada_gender);

        // === UPDATE DATA ===
        if ($request->register_as == 'sekolah') {
            $suratKeterangan->update([
                'nik' => $request->nik,
                'jenis' => $request->register_as,
                'kepada' => $request->kepada,
                'kepada_tempat_lhr' => $request->kepada_tempat_lhr,
                'kepada_tgl_lhr' => $request->kepada_tgl_lhr,
                'kepada_gender' => $request->kepada_gender,
                'kepada_gender_nm' => isset($kepada_gender) ? $kepada_gender->nama : '',
                'kepada_hubungan' => $request->kepada_hubungan,
                'kepada_sekolah' => $request->kepada_sekolah,
                'kepada_kelas' => $request->kepada_kelas,
                'kepada_alamat_sekolah' => $request->kepada_alamat_sekolah,
                'peruntukan' => $request->peruntukan,
                'kategori' => $request->kategori,
                'pengantar' => $fileLocation,
            ]);
        } else {
            $suratKeterangan->update([
                'nik' => $request->nik,
                'jenis' => $request->register_as,
                'kepada' => $request->kepada,
                'peruntukan' => $request->peruntukan,
                'kategori' => $request->kategori,
                'pengantar' => $fileLocation,
            ]);
        }

        // === RESPONSE ===
        if ($request->segment(1) == 'api') {
            return response()->json(['message' => 'Surat berhasil diperbarui!'], 200);
        }

        return redirect()->route('sktm.warga')->with('success', 'Data berhasil diperbarui');
    }

    public function show($id)
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN MISKIN";
        $suratKeterangan = SuratSktm::findOrFail($id);

        return view('sktm.show', compact('suratKeterangan', 'title'));
    }

    public function add($jenis)
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN MISKIN";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $no_urut_surat = SuratSktm::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
        $no_urut_surat = intval($no_urut_surat) + 1;
        // $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'sktm'])->first();
        if ($jenis == 'sekolah') {
            $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'sktm_sekolah'])->first();
        } else {
            $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'sktm_perorangan'])->first();
        }
        if (isset($template)) {
            $var = unserialize($template->variable);
            // dd($var);
            return view('sktm.add', compact('title', 'currentUser', 'no_urut_surat', 'var'));
        } else {
            return view('sktm.add', compact('title', 'currentUser', 'no_urut_surat'));
        }
    }

    public function store(Request $request)
    {

        $request->validate([
            'kd_jenis_surat' => ['required', 'string'],
            'no_urut_surat' => ['required', 'string'],
            'id_instansi' => ['required', 'string'],
            'tahun' => ['required', 'string'],
            'tgl_surat' => ['required', 'date'],
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
            'register_as' => ['required', 'string'],
            'kepada' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_tempat_lhr' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_tgl_lhr' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_gender' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_hubungan' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_sekolah' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_kelas' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_alamat_sekolah' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'peruntukan' => ['required', 'string'],
            'kategori' => ['required', 'string'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png'],
        ]);
        dd($request->register_as, $request->all());

        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/sktm', 0755);
            $path = '/public/pengantar/' . date('Y') . '/sktm';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/sktm/' . $fileName;
            $request->file('pengantar')->storeAs($path, $fileName);
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

        $datapemohon = serialize([
            'kk' => $request->kk,
            'name' => $request->name,
            'gender' => $request->gender,
            'gender_nm' => $gender->nama,
            'status_kwn' => $request->status_kwn,
            'status_kwn_nm' => $status_kwn->nama,
            'kewarganegaraan' => $request->kewarganegaraan,
            'kewarganegaraan_nm' => $kewarganegaraan->nama,
            'tempat_lhr' => $request->tempat_lhr,
            'tgl_lhr' =>  $request->tgl_lhr,
            'agama' => $request->agama,
            'agama_nm' => $agama->nama,
            'pendidikan' => $request->pendidikan,
            'pendidikan_nm' => $pendidikan->nama,
            'pekerjaan' => $request->pekerjaan,
            'pekerjaan_nm' => $pekerjaan->nama,
            'provinsi' => $request->provinsi,
            'provinsi_nm' => $provinsi->nama,
            'kabko' => $request->kabko,
            'kabko_nm' => $kabko->nama,
            'kecamatan' => $request->kecamatan,
            'kecamatan_nm' => $kecamatan->nama,
            'kelurahan' => $request->kelurahan,
            'kelurahan_nm' => $kelurahan->nama,
            'rw' => $request->rw,
            'rw_nm' => 'RW ' . $request->rw,
            'rt' => $request->rt,
            'rt_nm' => 'RT ' . $request->rt,
            'alamat' => $request->alamat
        ]);

        $resident = Resident::where('nik', $request->nik)->first();

        if (!$resident) {
            Resident::create([
                'nik' => $request->nik,
                'kk' => $request->kk,
                'data' => $datapemohon
            ]);
        } else {
            if ($datapemohon != $resident->data) {
                $resident->update([
                    'nik' => $request->nik,
                    'kk' => $request->kk,
                    'data' => $datapemohon
                ]);
            }
        }


        $kepada_gender = Gender::find($request->kepada_gender);
        if ($request->register_as == 'sekolah') {
            $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'sktm_sekolah'])->first();
        } else {
            $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'sktm_perorangan'])->first();
        }
        if (isset($template)) {
            $templateFile = public_path($template->path_docs);
            $templateProcessor = new TemplateProcessor($templateFile);
            $inputword = $templateProcessor->getVariables();
            $inputpost = [];
            foreach ($request->all() as $key => $in) {
                $inputpost[] = $key;
            }
            $arr_intersect = array_values(array_intersect($inputword, $inputpost));
            $var = array();
            foreach ($arr_intersect as $key => $value) {
                $var[$value] = $request[$value];
            }
            $datavar = serialize($var);
        }
        $suket = SuratSktm::create([
            'id_kel' => auth()->user()->id_instansi,
            'kd_jenis_surat' => $request->kd_jenis_surat,
            'no_urut_surat' => $request->no_urut_surat,

            'tgl_surat' => $request->tgl_surat,
            'nik' => $request->nik,
            'jenis' => $request->register_as,
            'kepada' => $request->kepada,
            'kepada_tempat_lhr' => $request->kepada_tempat_lhr,
            'kepada_tgl_lhr' => $request->kepada_tgl_lhr,
            'kepada_gender' => $request->kepada_gender,
            'kepada_gender_nm' => $kepada_gender->nama,
            'kepada_hubungan' => $request->kepada_hubungan,
            'kepada_sekolah' => $request->kepada_sekolah,
            'kepada_kelas' => $request->kepada_kelas,
            'kepada_alamat_sekolah' => $request->kepada_alamat_sekolah,
            'peruntukan' => $request->peruntukan,
            'kategori' => $request->kategori,
            'pengantar' => $request->pengantar,
            'variable' => isset($template) ? $datavar : '',
            'status' => 1,
            'pengantar' => $request->file('pengantar') ? $fileLocation : ''
        ]);

        Log_surat::create([
            'nik' => $request->nik,
            'tabel_surat' => 'surat_sktms',
            'nama_surat' => 'SURAT KETERANGAN MISKIN',
            'id_surat' => $suket->id,
            'status_surat' => 1,
        ]);

        return redirect()->route('sktm.index');
    }

    public function edit($id)
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN MISKIN";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $suratKeterangan = SuratSktm::find($id);
        if ($suratKeterangan->no_urut_surat == 0) {
            $no_urut_surat = SuratSktm::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
            $suratKeterangan->no_urut_surat = intval($no_urut_surat) + 1;
        }
        return view('sktm.edit', compact('title', 'currentUser', 'suratKeterangan'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'kd_jenis_surat' => ['required', 'string'],
            'no_urut_surat' => ['required', 'string'],
            'id_instansi' => ['required', 'string'],
            'tahun' => ['required', 'string'],
            'tgl_surat' => ['required', 'date'],
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
            'register_as' => ['required', 'string'],
            'kepada' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_tempat_lhr' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_tgl_lhr' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_gender' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_hubungan' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_sekolah' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_kelas' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_alamat_sekolah' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'peruntukan' => ['required', 'string'],
            'kategori' => ['required', 'string'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png'],
        ]);


        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/sktm', 0755);
            $path = '/public/pengantar/' . date('Y') . '/sktm';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/sktm/' . $fileName;
            $request->file('pengantar')->storeAs($path, $fileName);
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

        $suratKeterangan = SuratSktm::find($id);

        if ($suratKeterangan) {

            $datapemohon = serialize([
                'kk' => $request->kk,
                'name' => $request->name,
                'gender' => $request->gender,
                'gender_nm' => $gender->nama,
                'status_kwn' => $request->status_kwn,
                'status_kwn_nm' => $status_kwn->nama,
                'kewarganegaraan' => $request->kewarganegaraan,
                'kewarganegaraan_nm' => $kewarganegaraan->nama,
                'tempat_lhr' => $request->tempat_lhr,
                'tgl_lhr' =>  $request->tgl_lhr,
                'agama' => $request->agama,
                'agama_nm' => $agama->nama,
                'pendidikan' => $request->pendidikan,
                'pendidikan_nm' => $pendidikan->nama,
                'pekerjaan' => $request->pekerjaan,
                'pekerjaan_nm' => $pekerjaan->nama,
                'provinsi' => $request->provinsi,
                'provinsi_nm' => $provinsi->nama,
                'kabko' => $request->kabko,
                'kabko_nm' => $kabko->nama,
                'kecamatan' => $request->kecamatan,
                'kecamatan_nm' => $kecamatan->nama,
                'kelurahan' => $request->kelurahan,
                'kelurahan_nm' => $kelurahan->nama,
                'rw' => $request->rw,
                'rw_nm' => 'RW ' . $request->rw,
                'rt' => $request->rt,
                'rt_nm' => 'RT ' . $request->rt,
                'alamat' => $request->alamat
            ]);

            $resident = Resident::where('nik', $request->nik)->first();

            if (!$resident) {
                Resident::create([
                    'nik' => $request->nik,
                    'kk' => $request->kk,
                    'data' => $datapemohon
                ]);
            } else {
                if ($datapemohon != $resident->data) {
                    $resident->update([
                        'nik' => $request->nik,
                        'kk' => $request->kk,
                        'data' => $datapemohon
                    ]);
                }
            }

            $kepada_gender = Gender::find($request->kepada_gender);
            if ($request->register_as == 'sekolah') {
                $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'sktm_sekolah'])->first();
            } else {
                $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'sktm_perorangan'])->first();
            }
            if (isset($template)) {
                $templateFile = public_path($template->path_docs);
                $templateProcessor = new TemplateProcessor($templateFile);
                $inputword = $templateProcessor->getVariables();
                $inputpost = [];
                foreach ($request->all() as $key => $in) {
                    $inputpost[] = $key;
                }
                $arr_intersect = array_values(array_intersect($inputword, $inputpost));
                $var = array();
                foreach ($arr_intersect as $key => $value) {
                    $var[$value] = $request[$value];
                }
                $datavar = serialize($var);
            }

            if ($request->register_as == 'sekolah') {
                $suratKeterangan->update([
                    'kd_jenis_surat' => $request->kd_jenis_surat,
                    'no_urut_surat' => $request->no_urut_surat,
                    'tgl_surat' => $request->tgl_surat,
                    'nik' => $request->nik,
                    'jenis' => $request->register_as,
                    'kepada' => $request->kepada,
                    'kepada_tempat_lhr' => $request->kepada_tempat_lhr,
                    'kepada_tgl_lhr' => $request->kepada_tgl_lhr,
                    'kepada_gender' => $request->kepada_gender,
                    'kepada_gender_nm' => $kepada_gender->nama,
                    'kepada_hubungan' => $request->kepada_hubungan,
                    'kepada_sekolah' => $request->kepada_sekolah,
                    'kepada_kelas' => $request->kepada_kelas,
                    'kepada_alamat_sekolah' => $request->kepada_alamat_sekolah,
                    'peruntukan' => $request->peruntukan,
                    'kategori' => $request->kategori,
                    'pengantar' => $request->pengantar,
                    // 'variable' => isset($template) ? $datavar : '',
                    'variable' => isset($template) ? $datavar : $suratKeterangan->variable,
                    'pengantar' => $request->file('pengantar') ? $fileLocation : $suratKeterangan->pengantar
                ]);
            } else {
                $suratKeterangan->update([
                    'kd_jenis_surat' => $request->kd_jenis_surat,
                    'no_urut_surat' => $request->no_urut_surat,
                    'tgl_surat' => $request->tgl_surat,
                    'nik' => $request->nik,
                    'jenis' => $request->register_as,
                    'kepada' => $request->kepada,
                    'peruntukan' => $request->peruntukan,
                    'kategori' => $request->kategori,
                    'pengantar' => $request->pengantar,
                    // 'variable' => isset($template) ? $datavar : '',
                    'variable' => isset($template) ? $datavar : $suratKeterangan->variable,
                    'status' => 1,
                    'pengantar' => $request->file('pengantar') ? $fileLocation : $suratKeterangan->pengantar
                ]);
            }

            return redirect()->route('sktm.index');
        } else {
            return redirect()->route('sktm.index');
        }
    }

    public function proses($id)
    {
        $suratKeterangan = SuratSktm::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 1]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_sktms',
                'nama_surat' => 'SURAT KETERANGAN MISKIN',
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
        $suratKeterangan = SuratSktm::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 2]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_sktms',
                'nama_surat' => 'SURAT KETERANGAN MISKIN',
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
        $suratKeterangan = SuratSktm::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 3]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_sktms',
                'nama_surat' => 'SURAT KETERANGAN MISKIN',
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
        $surat = SuratSktm::find($id);
        $surat['kepada_tgl_lhr'] = Carbon::parse($surat['kepada_tgl_lhr'])->isoFormat('D MMMM Y');
        $resident = Resident::where('nik', $surat->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
        $user = new User_resource(User::with('skpd')->find(Auth::id()));
        $pejabat = new Pejabat_resource(Pejabat::where('id_skpd', $user->id_instansi)->first());
        $tglSurat = Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
        $nomorSurat = $this->getNoSrt($surat);
        $url = env('APP_URL', 'http://rumput.test') . '/verify/' . 'sktm/' . $id;
        // dd($surat);
        if ($surat->jenis == 'sekolah') {
            $data = [
                'skpd_kec' => strtoupper($pejabat->skpd->kecamatan->nama),
                'skpd_kel' => strtoupper($pejabat->skpd->nama),
                'skpd_alamat' => $pejabat->skpd->instansi_alamat,
                'skpd_telp' => $pejabat->skpd->instansi_telp,
                'skpd_pos' => $pejabat->skpd->instansi_kode_pos,
                'skpd_kepala' => $pejabat->nama,
                'skpd_nip_kepala' => $pejabat->nip,
                'skpd_jabatan' => ucfirst($pejabat->jabatan->nama) . ' ' . ucfirst(strtolower($pejabat->skpd->nama)),
                'surat_no' => $nomorSurat,
                'surat_nama' => $penduduk['name'],
                'surat_nik' => $surat->nik,
                'surat_tmpl' => $penduduk['tempat_lhr'],
                'surat_tgll' => strtoupper($penduduk['tgl_lhr']),
                'surat_gender' => $penduduk['gender_nm'],
                'surat_perkawinan' => $penduduk['status_kwn_nm'],
                'surat_agama' => $penduduk['agama_nm'],
                'surat_pekerjaan' => $penduduk['pekerjaan_nm'],
                'surat_pendidikan' => $penduduk['pendidikan_nm'],
                'surat_alamat' => $penduduk['alamat'] . ' KEL. ' . $penduduk['kelurahan_nm'] . ' KEC. ' . $penduduk['kecamatan_nm'] . ' ' .  $penduduk['kabko_nm'],
                'surat_keterangan' => 'Benar-benar dalam keadaan miskin.',
                'surat_kepada' => $surat->kepada,
                'surat_kepada_tempat_lhr' => $surat->kepada_tempat_lhr,
                'surat_kepada_tgl_lhr' => $surat->kepada_tgl_lhr,
                'surat_kepada_sekolah' => $surat->kepada_sekolah,
                'surat_kepada_kelas' => $surat->kepada_kelas,
                'surat_kepada_gender_nm' => ucfirst(strtolower($surat->kepada_gender_nm)),
                'surat_kepada_hubungan' => $surat->kepada_hubungan,
                'surat_peruntukan' => $surat->peruntukan,
                'surat_tgl' => $tglSurat,
                'surat_kategori' => $surat->kategori,
                'link' => $url
            ];
            // Path template .docx
            $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'sktm_sekolah'])->first();
            if (isset($template) && ($surat->variable != "")) {
                $var = unserialize($surat->variable);
                $templateFile = public_path($template->path_docs);
                $data = array_merge($data, $var);
            } else {
                $templateFile = public_path('templates/SKTM_SEKOLAH.docx');
            }
        } else {
            $data = [
                'skpd_kec' => strtoupper($pejabat->skpd->kecamatan->nama),
                'skpd_kel' => strtoupper($pejabat->skpd->nama),
                'skpd_alamat' => $pejabat->skpd->instansi_alamat,
                'skpd_telp' => $pejabat->skpd->instansi_telp,
                'skpd_pos' => $pejabat->skpd->instansi_kode_pos,
                'skpd_kepala' => $pejabat->nama,
                'skpd_nip_kepala' => $pejabat->nip,
                'skpd_jabatan' => ucfirst($pejabat->jabatan->nama) . ' ' . ucfirst(strtolower($pejabat->skpd->nama)),
                'surat_no' => $nomorSurat,
                'surat_nama' => $penduduk['name'],
                'surat_nik' => $surat->nik,
                'surat_tmpl' => $penduduk['tempat_lhr'],
                'surat_tgll' => strtoupper($penduduk['tgl_lhr']),
                'surat_gender' => $penduduk['gender_nm'],
                'surat_perkawinan' => $penduduk['status_kwn_nm'],
                'surat_agama' => $penduduk['agama_nm'],
                'surat_pekerjaan' => $penduduk['pekerjaan_nm'],
                'surat_pendidikan' => $penduduk['pendidikan_nm'],
                'surat_alamat' => $penduduk['alamat'] . ' KEL. ' . $penduduk['kelurahan_nm'] . ' KEC. ' . $penduduk['kecamatan_nm'] . ' ' .  $penduduk['kabko_nm'],
                'surat_keterangan' => 'Benar-benar dalam keadaan miskin.',
                'surat_peruntukan' => $surat->peruntukan,
                'surat_tgl' => $tglSurat,
                'surat_kategori' => $surat->kategori,
                'link' => $url
            ];
            // Path template .docx
            $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'sktm_perorangan'])->first();
            if (isset($template) && ($surat->variable != "")) {
                $var = unserialize($surat->variable);
                $templateFile = public_path($template->path_docs);
                $data = array_merge($data, $var);
            } else {
                $templateFile = public_path('templates/SKTM_PERORANGAN.docx');
            }
        }

        $outputPdf = hash('sha256', 'SKTM_' . $id);
        // Generate PDF dari template
        $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);
        return response()->file($pdfPath);
    }

    public function cetak($id)
    {
        $surat = SuratSktm::find($id);
        return response()->json(['file' => asset($surat->file)]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'min:16'],
            'peruntukan' => ['required', 'max:100'],
            'register_as' => ['required', 'string'],
            'kepada' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_tempat_lhr' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_tgl_lhr' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_gender' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_hubungan' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_sekolah' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_kelas' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kepada_alamat_sekolah' => ['nullable', 'required_if:register_as,sekolah', 'string'],
            'kategori' => ['required', 'string'],
            'pengantar' => ['required', 'mimes:jpg,bmp,png']
        ]);

        //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/sktm', 0755);
        $path = '/public/pengantar/' . date('Y') . '/sktm';
        $fileName = $request->file('pengantar')->hashName();
        $fileLocation = '/storage/pengantar/' . date('Y') . '/sktm/' . $fileName;
        $request->file('pengantar')->storeAs($path, $fileName);
        $resident = Resident::where('nik', $request->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
        $regional = new Kelurahan_resource(Kelurahan::find($penduduk['kelurahan']));
        $kepada_gender = Gender::find($request->kepada_gender);

        // dd(isset($kepada_gender) ? $kepada_gender->nama : '');

        $suket = SuratSktm::create([
            'id_kel'    => auth()->user()->id_instansi,
            'id_rw'    => auth()->user()->id_rw,
            'id_rt'    => auth()->user()->id_rt,
            'kd_jenis_surat' => 0,
            'no_urut_surat' => 0,
            'id_instansi' => $regional['skpd']->instansi_kode,
            'tahun' => date('Y'),
            'tgl_surat' => date('Y-m-d'),
            'nik' => $request->nik,
            'peruntukan' => $request->peruntukan,
            'jenis' => $request->register_as,
            'kepada' => $request->kepada,
            'kepada_tempat_lhr' => $request->kepada_tempat_lhr,
            'kepada_tgl_lhr' => $request->kepada_tgl_lhr,
            'kepada_gender' => $request->kepada_gender,
            'kepada_gender_nm' => isset($kepada_gender) ? $kepada_gender->nama : '',
            'kepada_hubungan' => $request->kepada_hubungan,
            'kepada_sekolah' => $request->kepada_sekolah,
            'kepada_kelas' => $request->kepada_kelas,
            'kepada_alamat_sekolah' => $request->kepada_alamat_sekolah,
            'kategori' => $request->kategori,
            'status' => 0,
            'pengantar' => $fileLocation
        ]);

        Log_surat::create([
            'nik' => $suket->nik,
            'tabel_surat' => 'surat_sktms',
            'nama_surat' => 'SURAT KETERANGAN MISKIN',
            'id_surat' => $suket->id,
            'status_surat' => 0,
        ]);
        if ($request->segment(1) == 'api') {
            return response()->json(['message' => 'Pengajuan Surat Keterangan Berhasil!'], 200);
        } else {

            return redirect()->route('sktm.warga');
        }
    }

    public function get(Request $request)
    {
        if (isset($request->nik)) {
            $surat = SuratSktm::with(['history' => function ($query) {
                return $query->where('tabel_surat', 'surat_sktms');
            }])->where('nik', $request->nik)->orderBy('id', 'desc')->get();
        } else if (isset($request->id)) {
            $surat = SuratSktm::with(['history' => function ($query) {
                return $query->where('tabel_surat', 'surat_sktms');
            }])->findOrFail($request->id);
        }

        return response()->json($surat);
    }


    public function nilai(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
        ]);

        $surat = SuratSktm::find($id);

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
            'tabel_surat'  => 'surat_sktms',
            'nama_surat'   => 'SURAT KETERANGAN MISKIN',
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
        $surat = SuratSktm::find($id);

        if (!$surat || $surat->rating === null) {
            return response()->json(['message' => 'Belum ada penilaian'], 404);
        }

        // Ambil waktu nilai dari log_surat (status_surat = 5)
        $log = Log_surat::where('tabel_surat', 'surat_sktms')
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
        $suratKeterangan = SuratSktm::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 6]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_sktms',
                'nama_surat' => 'SURAT KETERANGAN MISKIN',
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
        $suratKeterangan = SuratSktm::find($id);
        if (!$suratKeterangan) return response()->json(['message' => 'Data tidak ditemukan.'], 404);

        if (in_array($suratKeterangan->status, ['1', '2', '3', '4', '5', '8', '9'])) {
            return response()->json(['message' => 'Surat sudah selesai atau dinilai, tidak bisa dihapus.'], 422);
        }
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 7]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_sktms',
                'nama_surat' => 'SURAT KETERANGAN MISKIN',
                'id_surat' => $id,
                'status_surat' => 7,
            ]);
            return response()->json(['message' => 'Pengajuan berhasil dihapus.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Gagal menghapus.']);
        }
    }

    public function register(Request $request)
    {
        parse_str($request->getContent(), $output);
        $suratKeterangan = SuratSktm::find($output['_id']);
        // dd($suratKeterangan);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 8, 'no_register' => $output['register']]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_sktms',
                'nama_surat' => 'SURAT KETERANGAN MISKIN',
                'id_surat' => $output['_id'],
                'status_surat' => 8
            ]);
            return response()->json(['message' => 'Data updated successfully.', 'data' => $output['_id']]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }
}
