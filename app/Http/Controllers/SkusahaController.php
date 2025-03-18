<?php

namespace App\Http\Controllers;

use App\Http\Resources\Kelurahan_resource;
use App\Http\Resources\Pejabat_resource;
use App\Http\Resources\User_resource;
use App\Models\Agama;
use App\Models\Gender;
use App\Models\JenisSurat;
use App\Models\Kabko;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kewarganegaraan;
use App\Models\Log_surat;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Provinsi;
use App\Models\Resident;
use App\Models\StatusKwn;
use App\Models\SuratUsaha;
use App\Models\User;
use App\Models\Pejabat;
use App\Models\SuratTemplate;
use App\Traits\GetNoSurat;
use App\Traits\GeneratePDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\DataTables;

class SkusahaController extends Controller
{
    use GetNoSurat, GeneratePDF;

    public function index()
    {
        if (request()->ajax()) {
            $data = SuratUsaha::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $nomorSurat = $this->getNoSrt($row);
                    $id = $row->id;
                    $route = 'skusaha.edit';
                    $status = $row->status;
                    $jenis = 'skusaha';
                    $role = auth()->user()->role_id;
                    if (auth()->user()->role_id == 1) {
                        return view('includes.button-admin', compact('id', 'route', 'status'));
                    } else if (auth()->user()->role_id == 3) {
                        return view('includes.button-kaopd', compact('id', 'status', 'nomorSurat', 'jenis', 'role'));
                    } else {
                        return view('includes.button-verifikator', compact('id', 'status', 'role'));
                    }
                })
                ->addColumn('no_surat', function ($row) {
                    return $this->getNoSrt($row);
                })
                ->rawColumns(['action', 'no_surat'])
                ->make(true);
        };
        $title = "USULAN PENGAJUAN SURAT KETERANGAN USAHA";
        return view('skusaha.index', compact('title'));
    }

    public function warga()
    {
        // dd(auth()->user()->nik);
        if (request()->ajax()) {
            $data = SuratUsaha::query();
            $data->where('nik', auth()->user()->nik);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $nomorSurat = $this->getNoSrt($row);
                    $id = $row->id;
                    $status = $row->status;
                    $jenis = 'skusaha';

                    return view('includes.button-warga', compact('id', 'status'));
                })
                ->addColumn('no_surat', function ($row) {
                    return $this->getNoSrt($row);
                })
                ->rawColumns(['action', 'no_surat'])
                ->make(true);
        };
        $title = "USULAN PENGAJUAN SURAT KETERANGAN USAHA";
        $nik = auth()->user()->nik;
        $surat = JenisSurat::where(['is_active' => true])->get();
        $detail_surat = JenisSurat::where(['jenis' => 'skusaha', 'is_active' => true])->get();
        $resident = Resident::where('nik', $nik)->first();
        if (isset($resident)) {
            return view('skusaha.warga', compact('title', 'nik', 'surat', 'detail_surat'));
        } else {
            return redirect()->route('profile')->with('status', 'Lengkapi data pribadi dahulu! Terima kasih');
        }
    }

    public function add()
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN USAHA";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $no_urut_surat = SuratUsaha::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
        $no_urut_surat = intval($no_urut_surat) + 1;
        $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'skusaha'])->first();
        if (isset($template)) {
            $var = unserialize($template->variable);
            // dd($var);
            return view('skusaha.add', compact('title', 'currentUser', 'no_urut_surat', 'var'));
        } else {
            return view('skusaha.add', compact('title', 'currentUser', 'no_urut_surat'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_jenis_surat' => ['required', 'string'],
            'no_urut_surat' => ['required', 'string'],
            'kd_instansi' => ['required', 'string'],
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
            'alamat' => ['required', 'max:100'],
            'register_as' => ['required', 'string'],
            'nama_usaha' => ['required', 'string'],
            'alamat_usaha' => ['required', 'string'],
            'kepada' => ['required', 'string'],
            'peruntukan' => ['required', 'string'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png'],
        ]);

        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skusaha', 0755);
            $path = '/public/pengantar/' . date('Y') . '/skusaha';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skusaha/' . $fileName;
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
        $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'skusaha'])->first();
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
        $suket = SuratUsaha::create([
            'id_kel' => auth()->user()->id_instansi,
            'kd_jenis_surat' => $request->kd_jenis_surat,
            'no_urut_surat' => $request->no_urut_surat,

            'tgl_surat' => $request->tgl_surat,
            'nik' => $request->nik,
            'jenis' => $request->register_as,
            'nama_usaha' => $request->nama_usaha,
            'alamat_usaha' => $request->alamat_usaha,
            'kepada' => $request->kepada,
            'peruntukan' => $request->peruntukan,
            'pengantar' => $request->pengantar,
            'variable' => isset($template) ? $datavar : '',
            'status' => 1,
            'pengantar' => $request->file('pengantar') ? $fileLocation : ''
        ]);

        Log_surat::create([
            'nik' => $request->nik,
            'tabel_surat' => 'surat_usahas',
            'nama_surat' => 'SURAT KETERANGAN USAHA',
            'id_surat' => $suket->id,
            'status_surat' => 1,
        ]);

        return redirect()->route('skusaha.index');
    }


    public function edit($id)
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN USAHA";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $suratKeterangan = SuratUsaha::find($id);
        if ($suratKeterangan->no_urut_surat == 0) {
            $no_urut_surat = SuratUsaha::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
            $suratKeterangan->no_urut_surat = intval($no_urut_surat) + 1;
        }
        $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'skusaha'])->first();
        if (isset($template)) {
            $var = unserialize($template->variable);
            $var_value = unserialize($suratKeterangan->variable);
            return view('skusaha.edit', compact('title', 'currentUser', 'suratKeterangan', 'var', 'var_value'));
        } else {
            return view('skusaha.edit', compact('title', 'currentUser', 'suratKeterangan'));
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kd_jenis_surat' => ['required', 'string'],
            'no_urut_surat' => ['required', 'string'],
            'kd_instansi' => ['required', 'string'],
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
            'alamat' => ['required', 'max:100'],
            'register_as' => ['required', 'string'],
            'nama_usaha' => ['required', 'string'],
            'alamat_usaha' => ['required', 'string'],
            'kepada' => ['required', 'string'],
            'peruntukan' => ['required', 'string'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png'],
        ]);

        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skusaha', 0755);
            $path = '/public/pengantar/' . date('Y') . '/skusaha';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skusaha/' . $fileName;
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

        $suratKeterangan = SuratUsaha::find($id);

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
            $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'skusaha'])->first();
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
            $suratKeterangan->update([
                'kd_jenis_surat' => $request->kd_jenis_surat,
                'no_urut_surat' => $request->no_urut_surat,
                'tgl_surat' => $request->tgl_surat,
                'nik' => $request->nik,
                'jenis' => $request->register_as,
                'nama_usaha' => $request->nama_usaha,
                'alamat_usaha' => $request->alamat_usaha,
                'kepada' => $request->kepada,
                'peruntukan' => $request->peruntukan,
                'pengantar' => $request->pengantar,
                'variable' => isset($template) ? $datavar : '',
                'status' => 1,
                'pengantar' => $request->file('pengantar') ? $fileLocation : $suratKeterangan->pengantar
            ]);

            return redirect()->route('skusaha.index');
        } else {
            return redirect()->route('skusaha.index');
        }
    }

    public function naik($id)
    {
        $suratKeterangan = SuratUsaha::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 2]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_usahas',
                'nama_surat' => 'SURAT KETERANGAN USAHA',
                'id_surat' => $id,
                'status_surat' => 2,
            ]);
            return response()->json(['message' => 'Data updated successfully.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }

    public function preview($id)
    {
        $surat = SuratUsaha::find($id);
        $surat['kepada_tgl_lhr'] = Carbon::parse($surat['kepada_tgl_lhr'])->isoFormat('D MMMM Y');
        $resident = Resident::where('nik', $surat->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
        $user = new User_resource(User::with('skpd')->find(Auth::id()));
        $pejabat = new Pejabat_resource(Pejabat::where('id_skpd', $user->id_instansi)->first());
        $tglSurat = Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
        $nomorSurat = $this->getNoSrt($surat);
        $url = env('APP_URL', 'http://rumput.test') . '/verify/' . 'skhsl/' . $id;
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
            'surat_keterangan' => 'Menurut pernyataannya memiliki kegiatan / usaha ' . $surat['nama_usaha'] . ' yang beralamat di ' . $surat['alamat_usaha'],
            'surat_kepada' => $surat->kepada,
            'surat_kepada_tempat_lhr' => $surat->kepada_tempat_lhr,
            'surat_kepada_tgl_lhr' => $surat->kepada_tgl_lhr,
            'surat_kepada_sekolah' => $surat->kepada_sekolah,
            'surat_kepada_kelas' => $surat->kepada_kelas,
            'surat_kepada_gender_nm' => ucfirst(strtolower($surat->kepada_gender_nm)),
            'surat_kepada_hubungan' => $surat->kepada_hubungan,
            'surat_peruntukan' => $surat->peruntukan,
            'surat_tgl' => $tglSurat,
            'link' => $url
        ];
        // Path template .docx
        $template = SuratTemplate::where(['id_kel' => auth()->user()->id_instansi, 'jenis' => 'skusaha'])->first();
        if (isset($template) && ($surat->variable != "")) {
            $var = unserialize($surat->variable);
            $templateFile = public_path($template->path_docs);
            $data = array_merge($data, $var);
        } else {
            $templateFile = public_path('templates/SKUSAHA.docx');
        }
        $outputPdf = hash('sha256', 'SKUSAHA_' . $id);
        // Generate PDF dari template
        $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);

        return response()->file($pdfPath);
    }

    public function cetak($id)
    {
        $surat = SuratUsaha::find($id);
        return response()->json(['file' => asset($surat->file)]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'min:16'],
            'register_as' => ['required', 'string'],
            'nama_usaha' => ['required', 'string'],
            'alamat_usaha' => ['required', 'string'],
            'kepada' => ['required', 'string'],
            'peruntukan' => ['required', 'max:100'],
            'pengantar' => ['required', 'mimes:jpg,bmp,png']
        ]);

        //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skusaha', 0755);
        $path = '/public/pengantar/' . date('Y') . '/skusaha';
        $fileName = $request->file('pengantar')->hashName();
        $fileLocation = '/storage/pengantar/' . date('Y') . '/skusaha/' . $fileName;
        $request->file('pengantar')->storeAs($path, $fileName);
        $resident = Resident::where('nik', $request->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
        $regional = new Kelurahan_resource(Kelurahan::find($penduduk['kelurahan']));

        $suket = SuratUsaha::create([
            'id_kel'    => auth()->user()->id_instansi,
            'kd_jenis_surat' => 0,
            'no_urut_surat' => 0,
            'kd_instansi' => $regional['skpd']->instansi_kode,
            'tahun' => date('Y'),
            'tgl_surat' => date('Y-m-d'),
            'nik' => $request->nik,
            'jenis' => $request->register_as,
            'peruntukan' => $request->peruntukan,
            'kepada' => $request->kepada,
            'nama_usaha' => $request->nama_usaha,
            'alamat_usaha' => $request->alamat_usaha,
            'status' => 0,
            'pengantar' => $fileLocation
        ]);

        Log_surat::create([
            'nik' => $suket->nik,
            'tabel_surat' => 'surat_usahas',
            'nama_surat' => 'SURAT KETERANGAN USAHA',
            'id_surat' => $suket->id,
            'status_surat' => 0,
        ]);

        if ($request->segment(1) == 'api') {
            return response()->json(['message' => 'Pengajuan Surat Keterangan Berhasil!'], 200);
        } else {

            return redirect()->route('skusaha.warga');
        }
    }

    public function get(Request $request)
    {
        if (isset($request->nik)) {
            $surat = SuratUsaha::with(['history' => function ($query) {
                return $query->where('tabel_surat', 'surat_usahas');
            }])->where('nik', $request->nik)->orderBy('id', 'desc')->get();
        } else if (isset($request->id)) {
            $surat = SuratUsaha::with(['history' => function ($query) {
                return $query->where('tabel_surat', 'surat_usahas');
            }])->findOrFail($request->id);
        }
        return response()->json($surat);
    }


    public function tolak($id)
    {
        $suratKeterangan = SuratUsaha::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 4]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_usahas',
                'nama_surat' => 'SURAT KETERANGAN USAHA',
                'id_surat' => $id,
                'status_surat' => 4,
            ]);
            return response()->json(['message' => 'Data updated successfully.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }
}
