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
use App\Models\SuratDomisili;
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

class SkdomController extends Controller
{
    use GetNoSurat, GeneratePDF;

    protected function residentData($resident): array
    {
        $data = $resident->data ?? [];

        if (is_array($data)) {
            return $data;
        }

        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            $unserialized = decode_json_data($data);
            if (is_array($unserialized)) {
                return $unserialized;
            }
        }

        return [];
    }

    protected function suratVariable($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            $unserialized = decode_json_data($value);
            if (is_array($unserialized)) {
                return $unserialized;
            }
        }

        return [];
    }

    public function index()
    {
        $user = auth()->user();
        $rt = $user->id_rt;
        $rw = $user->id_rw;
        $resident = Resident::where('nik', $user->nik)->first();
        $penduduk = $this->residentData($resident);
        $kelurahan = $penduduk['kelurahan_nm'];
        if (request()->ajax()) {
            $query = SuratDomisili::query();

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
                    $route = 'skdom.edit';
                    $status = $row->status;
                    $jenis = 'skdom';
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
        $title = "Surat Keterangan Domisili";
        return view('skdom.index', compact('title', 'rt', 'rw', 'kelurahan'));
    }

    public function warga()
    {
        // dd(auth()->user()->nik);
        // Bagian halaman utama
        $user = auth()->user();
        $nik  = $user->nik;
        $title = "USULAN PENGAJUAN SURAT KETERANGAN DOMISILI";
        $nama  = "SURAT KETERANGAN DOMISILI";
        $jenis = 'skdom';

        // kartu pilihan surat & detail surat skbn
        $surat        = JenisSurat::where('is_active', true)->get(['jenis', 'assets', 'name']);
        $detail_surat = JenisSurat::where(['jenis' => 'skdom', 'is_active' => true])->get();
        $q            = request('q'); // ← ambil keyword

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
        $items = SuratDomisili::query()
            ->where('nik', $nik)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('no_urut_surat', 'like', "%{$q}%")
                        ->orWhere('peruntukan',   'like', "%{$q}%")
                        ->orWhere('kepada',       'like', "%{$q}%")
                        ->orWhere('nama_perusahaan',   'like', "%{$q}%");
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
        return view('skdom.warga', compact(
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
        $title = "USULAN PENGAJUAN SURAT KETERANGAN DOMISILI";
        $nik   = auth()->user()->nik;

        // konsisten dengan pengecekan di warga()
        $resident = Resident::where('nik', $nik)->first();
        if (!$resident) {
            return redirect()->route('profile')
                ->with('status', 'Lengkapi data pribadi dahulu! Terima kasih');
        }

        return view('skdom.addwarga', compact('title', 'nik'));
    }

    public function editwarga($id)
    {
        // dd($id);
        $title = "SURAT KETERANGAN DOMISILI WARGA";
        $suratKeterangan = SuratDomisili::find($id);

        return view('skdom.editwarga', compact('title', 'suratKeterangan'));
    }

    public function updatewarga(Request $request, $id)
    {
        $suratKeterangan = SuratDomisili::findOrFail($id);

        $rules = [
            'nik'         => 'required|min:16',
            'peruntukan' => ['required', 'max:100'],
            'kepada' => ['required'],
            'register_as'   => ['required', 'string'],
            'nama_perusahaan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'status_bangunan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'jumlah_karyawan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'alamat_domisili' => ['required', 'string'],
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
            $path = '/public/pengantar/' . date('Y') . '/skdom';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skdom/' . $fileName;

            $request->file('pengantar')->storeAs($path, $fileName);
        }

        // === UPDATE DATA ===
        $suratKeterangan->update([
            'nik' => $request->nik,
            'jenis' => $request->register_as,
            'kepada' => $request->kepada,
            'nama_perusahaan' => $request->nama_perusahaan,
            'status_bangunan' => $request->status_bangunan,
            'jumlah_karyawan' => $request->jumlah_karyawan,
            'alamat_domisili' => $request->alamat_domisili,
            'tgl_berlaku' => $request->tgl_berlaku,
            'peruntukan' => $request->peruntukan,
            'pengantar' => $request->pengantar,
            'kepada' => $request->kepada,
            'pengantar' => $fileLocation,
        ]);

        // === RESPONSE ===
        if ($request->segment(1) == 'api') {
            return response()->json(['message' => 'Surat berhasil diperbarui!'], 200);
        }

        return redirect()->route('skdom.warga')->with('success', 'Data berhasil diperbarui');
    }


    public function show($id)
    {
        $title = "SURAT KETERANGAN DOMISILI";
        $suratKeterangan = SuratDomisili::findOrFail($id);

        return view('skdom.show', compact('suratKeterangan', 'title'));
    }

    public function add()
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN DOMISILI";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $no_urut_surat = SuratDomisili::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
        $no_urut_surat = intval($no_urut_surat) + 1;
        $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'skdom'])->first();
        if (isset($template)) {
            $var = decode_json_data($template->variable);
            // dd($var);
            return view('skdom.add', compact('title', 'currentUser', 'no_urut_surat', 'var'));
        } else {
            return view('skdom.add', compact('title', 'currentUser', 'no_urut_surat'));
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
            'kepada' => ['required', 'string'],
            'peruntukan' => ['required', 'string'],
            'nama_perusahaan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'status_bangunan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'jumlah_karyawan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'alamat_domisili' => ['required', 'string'],
            'tgl_berlaku' => ['nullable', 'required_if:register_as,perusahaan', 'date'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png'],
        ]);

        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skdom', 0755);
            $path = '/public/pengantar/' . date('Y') . '/skdom';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skdom/' . $fileName;
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

        $datapemohon = encode_json_data([
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

        $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'skdom'])->first();
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
            $datavar = encode_json_data($var);
        }

        $suket = SuratDomisili::create([
            'id_kel' => auth()->user()->id_instansi,
            'kd_jenis_surat' => $request->kd_jenis_surat,
            'no_urut_surat' => $request->no_urut_surat,

            'tgl_surat' => $request->tgl_surat,
            'nik' => $request->nik,
            'jenis' => $request->register_as,
            'kepada' => $request->kepada,
            'nama_perusahaan' => $request->nama_perusahaan,
            'status_bangunan' => $request->status_bangunan,
            'jumlah_karyawan' => $request->jumlah_karyawan,
            'alamat_domisili' => $request->alamat_domisili,
            'tgl_berlaku' => $request->tgl_berlaku,
            'peruntukan' => $request->peruntukan,
            'pengantar' => $request->pengantar,
            'variable' => isset($template) ? $datavar : '',
            'status' => 1,
            'pengantar' => $request->file('pengantar') ? $fileLocation : ''
        ]);

        Log_surat::create([
            'nik' => $request->nik,
            'tabel_surat' => 'surat_domisilis',
            'nama_surat' => 'SURAT KETERANGAN DOMISILI',
            'id_surat' => $suket->id,
            'status_surat' => 1,
        ]);

        return redirect()->route('skdom.index');
    }

    public function edit($id)
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN DOMISILI";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $suratKeterangan = SuratDomisili::find($id);
        if ($suratKeterangan->no_urut_surat == 0) {
            $no_urut_surat = SuratDomisili::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
            $suratKeterangan->no_urut_surat = intval($no_urut_surat) + 1;
        }
        $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'skdom'])->first();
        if (isset($template)) {
            $var = decode_json_data($template->variable);
            $var_value = decode_json_data($suratKeterangan->variable);
            return view('skdom.edit', compact('title', 'currentUser', 'suratKeterangan', 'var', 'var_value'));
        } else {
            return view('skdom.edit', compact('title', 'currentUser', 'suratKeterangan'));
            // $var = decode_json_data($template->variable);
            // $var = is_array($var) ? $var : [];

            // $var_value = decode_json_data($suratKeterangan->variable);
            // $var_value = is_array($var_value) ? $var_value : [];

            // return view('skdom.edit', compact('title', 'currentUser', 'suratKeterangan', 'var', 'var_value'));
        }
    }

    public function update(Request $request, $id)
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
            'kepada' => ['required', 'string'],
            'peruntukan' => ['required', 'string'],
            'nama_perusahaan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'status_bangunan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'jumlah_karyawan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'alamat_domisili' => ['required', 'string'],
            'tgl_berlaku' => ['nullable', 'required_if:register_as,perusahaan', 'date'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png'],
        ]);


        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skdom', 0755);
            $path = '/public/pengantar/' . date('Y') . '/skdom';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skdom/' . $fileName;
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

        $suratKeterangan = SuratDomisili::find($id);

        if ($suratKeterangan) {

            $datapemohon = encode_json_data([
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
            $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'skdom'])->first();
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
                $datavar = encode_json_data($var);
            }

            $suratKeterangan->update([
                'kd_jenis_surat' => $request->kd_jenis_surat,
                'no_urut_surat' => $request->no_urut_surat,
                'tgl_surat' => $request->tgl_surat,
                'nik' => $request->nik,
                'jenis' => $request->register_as,
                'kepada' => $request->kepada,
                'nama_perusahaan' => $request->nama_perusahaan,
                'status_bangunan' => $request->status_bangunan,
                'jumlah_karyawan' => $request->jumlah_karyawan,
                'alamat_domisili' => $request->alamat_domisili,
                'tgl_berlaku' => $request->tgl_berlaku,
                'peruntukan' => $request->peruntukan,
                'pengantar' => $request->pengantar,
                // 'variable' => isset($template) ? $datavar : '',
                'variable' => isset($template) ? $datavar : $suratKeterangan->variable,
                'pengantar' => $request->file('pengantar') ? $fileLocation : $suratKeterangan->pengantar
            ]);

            return redirect()->route('skdom.index');
        } else {
            return redirect()->route('skdom.index');
        }
    }

    public function proses($id)
    {
        $suratKeterangan = SuratDomisili::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 1]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_domisilis',
                'nama_surat' => 'SURAT KETERANGAN DOMISILI',
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
        $suratKeterangan = SuratDomisili::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 2]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_domisilis',
                'nama_surat' => 'SURAT KETERANGAN DOMISILI',
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
        $suratKeterangan = SuratDomisili::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 3]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_domisilis',
                'nama_surat' => 'SURAT KETERANGAN DOMISILI',
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
        $surat = SuratDomisili::find($id);
        $surat['tgl_berlaku'] = Carbon::parse($surat['tgl_berlaku'])->isoFormat('D MMMM Y');
        $resident = Resident::where('nik', $surat->nik)->first();
        $penduduk = $this->residentData($resident);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
        $user = new User_resource(User::with('skpd')->find(Auth::id()));
        $pejabat = new Pejabat_resource(Pejabat::where('id_skpd', $user->id_instansi)->first());
        $tglSurat = Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
        $nomorSurat = $this->getNoSrt($surat);
        $url = env('APP_URL', 'http://rumput.test') . '/verify/' . 'skdom/' . $id;
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
            'surat_keterangan' => $surat['jenis'] == 'perorangan' ?
                'Bahwa nama tersebut di atas benar - benar berdomisili di ' . $surat['alamat_domisili'] . ', Kel. ' . ucfirst(strtolower($penduduk['kelurahan_nm'])) . ' Kec. ' . ucfirst(strtolower($penduduk['kecamatan_nm'])) . ' ' .  ucwords(strtolower($penduduk['kabko_nm'])) :
                'Pendiri / pemilik usaha ' . $surat['nama_perusahaan'] . ' yang bertempat di ' . $surat['alamat_domisili'] . ', Kel. ' . ucfirst(strtolower($penduduk['kelurahan_nm'])) . ' Kec. ' . ucfirst(strtolower($penduduk['kecamatan_nm'])) . ' ' .  ucwords(strtolower($penduduk['kabko_nm'])) . ' yang berstatus bangunan ' . $surat['status_bangunan'] . ' dengan karyawan berjumlah ' . $surat['jumlah_karyawan'] . ' orang.',
            'surat_kepada' => $surat->kepada,
            'surat_peruntukan' => $surat->peruntukan,
            'surat_tgl' => $tglSurat,
            'link' => $url
        ];
        // Path template .docx
        $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'skdom'])->first();
        if (isset($template) && ($surat->variable != "")) {
            $var = $this->suratVariable($surat->variable);
            $templateFile = public_path($template->path_docs);
            $data = array_merge($data, $var);
        } else {
            $templateFile = public_path('templates/SKDOM.docx');
        }
        $outputPdf = hash('sha256', 'SKDOM_' . $id);
        // Generate PDF dari template
        $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);

        return response()->file($pdfPath);
    }

    public function cetak($id)
    {
        $surat = SuratDomisili::find($id);
        return response()->json(['file' => asset($surat->file)]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'min:16'],
            'peruntukan' => ['required', 'max:100'],
            'kepada' => ['required'],
            'pengantar' => ['required', 'mimes:jpg,bmp,png'],
            'register_as' => ['required', 'string'],
            'nama_perusahaan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'status_bangunan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'jumlah_karyawan' => ['nullable', 'required_if:register_as,perusahaan', 'string'],
            'alamat_domisili' => ['required', 'string'],
        ]);
        try {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skdom', 0755);
            $path = '/public/pengantar/' . date('Y') . '/skdom';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skdom/' . $fileName;
            $request->file('pengantar')->storeAs($path, $fileName);
            $resident = Resident::where('nik', $request->nik)->first();
            $penduduk = $this->residentData($resident);

            $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
            $regional = new Kelurahan_resource(Kelurahan::find($penduduk['kelurahan']));

            $suket = SuratDomisili::create([
                'id_kel'    => auth()->user()->id_instansi,
                'id_rw'    => auth()->user()->id_rw,
                'id_rt'    => auth()->user()->id_rt,
                'kd_jenis_surat' => 0,
                'no_urut_surat' => 0,
                'id_instansi' => $regional['skpd']->instansi_kode,
                'tahun' => date('Y'),
                'tgl_surat' => date('Y-m-d'),
                'nik' => $request->nik,
                'jenis' => $request->register_as,
                'kepada' => $request->kepada,
                'nama_perusahaan' => $request->nama_perusahaan,
                'status_bangunan' => $request->status_bangunan,
                'jumlah_karyawan' => $request->jumlah_karyawan,
                'alamat_domisili' => $request->alamat_domisili,
                'tgl_berlaku' => $request->tgl_berlaku,
                'peruntukan' => $request->peruntukan,
                'pengantar' => $request->pengantar,
                'kepada' => $request->kepada,
                'status' => 0,
                'pengantar' => $fileLocation
            ]);

            Log_surat::create([
                'nik' => $suket->nik,
                'tabel_surat' => 'surat_domisilis',
                'nama_surat' => 'SURAT KETERANGAN DOMISILI',
                'id_surat' => $suket->id,
                'status_surat' => 0,
            ]);
            if ($request->segment(1) == 'api') {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Pengajuan Surat Keterangan Berhasil!',
                    'data'    => $suket
                ], 201); // 201 Created
            }

            return redirect()->route('skdom.warga');
        } catch (\Exception $e) {
            if ($request->segment(1) == 'api') {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function get(Request $request)
    {
        try {
            // JIKA INGIN DETAIL BERDASARKAN ID
            if ($request->has('id')) {
                $surat = SuratDomisili::with(['history' => function ($query) {
                    $query->where('tabel_surat', 'surat_keterangans');
                }])->findOrFail($request->id);

                return response()->json([
                    'status' => 'success',
                    'data'   => $surat
                ], 200);
            }

            // JIKA INGIN LIST DENGAN SEARCH & PAGINATION
            $query = SuratDomisili::with(['history' => function ($q) {
                $q->where('tabel_surat', 'surat_keterangans');
            }]);

            // Filter berdasarkan NIK (wajib untuk warga)
            if ($request->has('nik')) {
                $query->where('nik', $request->nik);
            }

            // Fitur Search (berdasarkan keterangan atau peruntukan)
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                        ->orWhere('peruntukan', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                });
            }

            // Urutkan terbaru
            $query->orderBy('id', 'desc');

            // Pagination (default 10 data per halaman)
            $perPage = $request->get('limit', 10);
            $surat = $query->paginate($perPage);

            return response()->json([
                'status'     => 'success',
                'message'    => 'Data berhasil diambil',
                'data'       => $surat->items(), // Mengambil list data saja
                'pagination' => [
                    'total'        => $surat->total(),
                    'count'        => $surat->count(),
                    'per_page'     => $surat->perPage(),
                    'current_page' => $surat->currentPage(),
                    'total_pages'  => $surat->lastPage()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan atau terjadi kesalahan.'
            ], 404);
        }
    }

    public function nilai(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
        ]);

        $surat = SuratDomisili::find($id);

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
            'tabel_surat'  => 'surat_domisilis',
            'nama_surat'   => 'SURAT KETERANGAN DOMISILI',
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
        $surat = SuratDomisili::find($id);

        if (!$surat || $surat->rating === null) {
            return response()->json(['message' => 'Belum ada penilaian'], 404);
        }

        // Ambil waktu nilai dari log_surat (status_surat = 5)
        $log = Log_surat::where('tabel_surat', 'surat_domisilis')
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
        $suratKeterangan = SuratDomisili::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 6]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_domisilis',
                'nama_surat' => 'SURAT KETERANGAN DOMISILI',
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
        $suratKeterangan = SuratDomisili::find($id);
        if (!$suratKeterangan) return response()->json(['message' => 'Data tidak ditemukan.'], 404);

        if (in_array($suratKeterangan->status, ['1', '2', '3', '4', '5'])) {
            return response()->json(['message' => 'Surat sudah selesai atau dinilai, tidak bisa dihapus.'], 422);
        }
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 7]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_domisilis',
                'nama_surat' => 'SURAT KETERANGAN DOMISILI',
                'id_surat' => $id,
                'status_surat' => 7,
            ]);
            return response()->json(['message' => 'Pengajuan berhasil dihapus.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Gagal menghapus.']);
        }
    }
}
