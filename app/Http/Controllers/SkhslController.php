<?php

namespace App\Http\Controllers;

use App\Http\Resources\Kelurahan_resource;
use App\Http\Resources\Pejabat_resource;
use App\Http\Resources\User_resource;
use App\Models\Agama;
use App\Models\Gender;
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
use App\Models\SuratPenghasilan;
use App\Models\User;
use App\Models\Pejabat;
use App\Traits\GetNoSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class SkhslController extends Controller
{
    use GetNoSurat;

    public function index()
    {
        if (request()->ajax()) {
            $data = SuratPenghasilan::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $nomorSurat = $this->getNoSrt($row);
                    $id = $row->id;
                    $route = 'skhsl.edit';
                    $status = $row->status;
                    $jenis = 'skhsl';
                    if (auth()->user()->role_id == 1) {
                        return view('includes.button-admin', compact('id', 'route', 'status'));
                    } else if (auth()->user()->role_id == 3) {
                        return view('includes.button-kaopd', compact('id', 'status', 'nomorSurat', 'jenis'));
                    } else {
                        return view('includes.button-verifikator', compact('id', 'status'));
                    }
                })
                ->addColumn('no_surat', function ($row) {
                    return $this->getNoSrt($row);
                })
                ->rawColumns(['action', 'no_surat'])
                ->make(true);
        };
        $title = "USULAN PENGAJUAN SURAT KETERANGAN PENGHASILAN";
        return view('skhsl.index', compact('title'));
    }

    public function warga()
    {
        // dd(auth()->user()->nik);
        if (request()->ajax()) {
            $data = SuratPenghasilan::query();
            $data->where('nik', auth()->user()->nik);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $nomorSurat = $this->getNoSrt($row);
                    $id = $row->id;
                    $status = $row->status;
                    $jenis = 'skhsl';

                    return view('includes.button-warga', compact('id', 'status'));
                })
                ->addColumn('no_surat', function ($row) {
                    return $this->getNoSrt($row);
                })
                ->rawColumns(['action', 'no_surat'])
                ->make(true);
        };
        $title = "USULAN PENGAJUAN SURAT KETERANGAN PENGHASILAN";
        $nik = auth()->user()->nik;
        return view('skhsl.warga', compact('title', 'nik'));
    }

    public function add()
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN PENGHASILAN";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $no_urut_surat = SuratPenghasilan::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
        $no_urut_surat = intval($no_urut_surat) + 1;
        return view('skhsl.add', compact('title', 'currentUser', 'no_urut_surat'));
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
            'kepada' => ['required', 'string'],
            'kepada_tempat_lhr' => ['required', 'string'],
            'kepada_tgl_lhr' => ['required', 'string'],
            'kepada_gender' => ['required', 'string'],
            'kepada_hubungan' => ['required', 'string'],
            'kepada_sekolah' => ['required', 'string'],
            'kepada_kelas' => ['required', 'string'],
            'kepada_alamat_sekolah' => ['required', 'string'],
            'penghasilan' => ['required', 'string'],
            'terbilang' => ['required', 'string'],
            'peruntukan' => ['required', 'string'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png'],
        ]);

        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skhsl', 0755);
            $path = '/public/pengantar/' . date('Y') . '/skhsl';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skhsl/' . $fileName;
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


        $kepada_gender = Gender::find($request->kepada_gender);

        $suket = SuratPenghasilan::create([
            'id_kel' => auth()->user()->id_instansi,
            'kd_jenis_surat' => $request->kd_jenis_surat,
            'no_urut_surat' => $request->no_urut_surat,

            'tgl_surat' => $request->tgl_surat,
            'nik' => $request->nik,
            'kepada' => $request->kepada,
            'kepada_tempat_lhr' => $request->kepada_tempat_lhr,
            'kepada_tgl_lhr' => $request->kepada_tgl_lhr,
            'kepada_gender' => $request->kepada_gender,
            'kepada_gender_nm' => $kepada_gender->nama,
            'kepada_hubungan' => $request->kepada_hubungan,
            'kepada_sekolah' => $request->kepada_sekolah,
            'kepada_kelas' => $request->kepada_kelas,
            'kepada_alamat_sekolah' => $request->kepada_alamat_sekolah,
            'penghasilan' => $request->penghasilan,
            'terbilang' => $request->terbilang,
            'peruntukan' => $request->peruntukan,
            'pengantar' => $request->pengantar,
            'status' => 1,
            'pengantar' => $request->file('pengantar') ? $fileLocation : ''
        ]);

        Log_surat::create([
            'nik' => $request->nik,
            'tabel_surat' => 'surat_penghasilans',
            'nama_surat' => 'SURAT KETERANGAN PENGHASILAN',
            'id_surat' => $suket->id,
            'status_surat' => 1,
        ]);

        return redirect()->route('skhsl.index');
    }


    public function edit($id)
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN PENGHASILAN";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $suratKeterangan = SuratPenghasilan::find($id);
        if ($suratKeterangan->no_urut_surat == 0) {
            $no_urut_surat = SuratPenghasilan::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
            $suratKeterangan->no_urut_surat = intval($no_urut_surat) + 1;
        }
        return view('skhsl.edit', compact('title', 'currentUser', 'suratKeterangan'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());

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
            'kepada' => ['required', 'string'],
            'kepada_tempat_lhr' => ['required', 'string'],
            'kepada_tgl_lhr' => ['required', 'string'],
            'kepada_gender' => ['required', 'string'],
            'kepada_hubungan' => ['required', 'string'],
            'kepada_sekolah' => ['required', 'string'],
            'kepada_kelas' => ['required', 'string'],
            'kepada_alamat_sekolah' => ['required', 'string'],
            'penghasilan' => ['required', 'string'],
            'terbilang' => ['required', 'string'],
            'peruntukan' => ['required', 'string'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png'],
        ]);

        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skhsl', 0755);
            $path = '/public/pengantar/' . date('Y') . '/skhsl';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skhsl/' . $fileName;
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

        $suratKeterangan = SuratPenghasilan::find($id);

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

            $kepada_gender = Gender::find($request->kepada_gender);

            $suratKeterangan->update([
                'kd_jenis_surat' => $request->kd_jenis_surat,
                'no_urut_surat' => $request->no_urut_surat,
                'tgl_surat' => $request->tgl_surat,
                'nik' => $request->nik,
                'kepada' => $request->kepada,
                'kepada_tempat_lhr' => $request->kepada_tempat_lhr,
                'kepada_tgl_lhr' => $request->kepada_tgl_lhr,
                'kepada_gender' => $request->kepada_gender,
                'kepada_gender_nm' => $kepada_gender->nama,
                'kepada_hubungan' => $request->kepada_hubungan,
                'kepada_sekolah' => $request->kepada_sekolah,
                'kepada_kelas' => $request->kepada_kelas,
                'kepada_alamat_sekolah' => $request->kepada_alamat_sekolah,
                'penghasilan' => $request->penghasilan,
                'terbilang' => $request->terbilang,
                'peruntukan' => $request->peruntukan,
                'pengantar' => $request->pengantar,
                'status' => 1,
                'pengantar' => $request->file('pengantar') ? $fileLocation : $suratKeterangan->pengantar
            ]);

            return redirect()->route('skhsl.index');
        } else {
            return redirect()->route('skhsl.index');
        }
    }

    public function naik($id)
    {
        $suratKeterangan = SuratPenghasilan::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 2]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_penghasilans',
                'nama_surat' => 'SURAT KETERANGAN PENGHASILAN',
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
        $surat = SuratPenghasilan::find($id);
        $surat['kepada_tgl_lhr'] = Carbon::parse($surat['kepada_tgl_lhr'])->isoFormat('D MMMM Y');
        $resident = Resident::where('nik', $surat->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
        $user = new User_resource(User::with('skpd')->find(Auth::id()));
        $pejabat = new Pejabat_resource(Pejabat::where('id_skpd', $user->id_instansi)->first());
        $tglSurat = Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
        $nomorSurat = $this->getNoSrt($surat);
        $url = '';
        $pdf = Pdf::loadView('skhsl.pdf', compact(
            'surat',
            'penduduk',
            'user',
            'nomorSurat',
            'pejabat',
            'tglSurat',
            'url'
        ))->setPaper(array(0, 0, 609.4488, 935.433), 'portrait');
        return $pdf->stream();
    }

    public function cetak($id)
    {
        $surat = SuratPenghasilan::find($id);
        return response()->json(['file' => asset($surat->file)]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'min:16'],
            'peruntukan' => ['required', 'max:100'],
            'kepada' => ['required', 'string'],
            'kepada_tempat_lhr' => ['required', 'string'],
            'kepada_tgl_lhr' => ['required', 'string'],
            'kepada_gender' => ['required', 'string'],
            'kepada_hubungan' => ['required', 'string'],
            'kepada_sekolah' => ['required', 'string'],
            'kepada_kelas' => ['required', 'string'],
            'kepada_alamat_sekolah' => ['required', 'string'],
            'penghasilan' => ['required', 'string'],
            'terbilang' => ['required', 'string'],
            'pengantar' => ['required', 'mimes:jpg,bmp,png']
        ]);

        //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skhsl', 0755);
        $path = '/public/pengantar/' . date('Y') . '/skhsl';
        $fileName = $request->file('pengantar')->hashName();
        $fileLocation = '/storage/pengantar/' . date('Y') . '/skhsl/' . $fileName;
        $request->file('pengantar')->storeAs($path, $fileName);
        $resident = Resident::where('nik', $request->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
        $regional = new Kelurahan_resource(Kelurahan::find($penduduk['kelurahan']));
        $kepada_gender = Gender::find($request->kepada_gender);

        // dd(isset($kepada_gender) ? $kepada_gender->nama : '');

        $suket = SuratPenghasilan::create([
            'id_kel'    => auth()->user()->id_instansi,
            'kd_jenis_surat' => 0,
            'no_urut_surat' => 0,
            'kd_instansi' => $regional['skpd']->instansi_kode,
            'tahun' => date('Y'),
            'tgl_surat' => date('Y-m-d'),
            'nik' => $request->nik,
            'peruntukan' => $request->peruntukan,
            'kepada' => $request->kepada,
            'kepada_tempat_lhr' => $request->kepada_tempat_lhr,
            'kepada_tgl_lhr' => $request->kepada_tgl_lhr,
            'kepada_gender' => $request->kepada_gender,
            'kepada_gender_nm' => isset($kepada_gender) ? $kepada_gender->nama : '',
            'kepada_hubungan' => $request->kepada_hubungan,
            'kepada_sekolah' => $request->kepada_sekolah,
            'kepada_kelas' => $request->kepada_kelas,
            'kepada_alamat_sekolah' => $request->kepada_alamat_sekolah,
            'penghasilan' => $request->penghasilan,
            'terbilang' => $request->terbilang,
            'peruntukan' => $request->peruntukan,
            'status' => 0,
            'pengantar' => $fileLocation
        ]);

        Log_surat::create([
            'nik' => $suket->nik,
            'tabel_surat' => 'surat_penghasilans',
            'nama_surat' => 'SURAT KETERANGAN PENGHASILAN',
            'id_surat' => $suket->id,
            'status_surat' => 0,
        ]);
        if ($request->segment(1) == 'api') {
            return response()->json(['message' => 'Pengajuan Surat Keterangan Berhasil!'], 200);
        } else {

            return redirect()->route('skhsl.warga');
        }
    }

    public function get(Request $request)
    {
        if (isset($request->nik)) {
            $surat = SuratPenghasilan::with(['history' => function ($query) {
                return $query->where('tabel_surat', 'surat_penghasilans');
            }])->where('nik', $request->nik)->orderBy('id', 'desc')->get();
        } else if (isset($request->id)) {
            $surat = SuratPenghasilan::with(['history' => function ($query) {
                return $query->where('tabel_surat', 'surat_penghasilans');
            }])->findOrFail($request->id);
        }
        return response()->json($surat);
    }


    public function tolak($id)
    {
        $suratKeterangan = SuratPenghasilan::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 4]);
            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_penghasilans',
                'nama_surat' => 'SURAT KETERANGAN PENGHASILAN',
                'id_surat' => $id,
                'status_surat' => 4,
            ]);
            return response()->json(['message' => 'Data updated successfully.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }
}
