<?php

namespace App\Http\Controllers;

use App\Http\Resources\Pejabat_resource;
use App\Http\Resources\Regional_resource;
use App\Http\Resources\Skpd_resource;
use App\Http\Resources\User_resource;
use App\Models\Agama;
use App\Models\Gender;
use App\Models\Kewarganegaraan;
use App\Models\Pejabat;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Regional;
use App\Models\Resident;
use App\Models\Skpd;
use App\Models\Status_kwn;
use App\Models\SuratKeterangan;
use App\Models\User;
use App\Traits\GetNoSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\DataTables;

class SuketController extends Controller
{
    use GetNoSurat;
    public function index()
    {
        if (request()->ajax()) {
            $data = SuratKeterangan::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $nomorSurat = $this->getNoSrt($row);$id = $row->id;
                    $route = 'suket.edit';
                    $status = $row->status;
                    if (auth()->user()->role_id == 1) {
                        return view('includes.button-admin', compact('id', 'route', 'status'));
                    } else if (auth()->user()->role_id == 3) {
                        return view('includes.button-kaopd', compact('id', 'status', 'nomorSurat'));
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
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KELURAHAN";
        return view('suket.index', compact('title'));
    }

    public function add()
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KELURAHAN";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $no_urut_surat = SuratKeterangan::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
        $no_urut_surat = intval($no_urut_surat) + 1;
        return view('suket.add', compact('title', 'currentUser', 'no_urut_surat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_jenis_surat' => ['required'],
            'kd_jenis_surat' => ['required'],
            'no_urut_surat' => ['required'],
            'kd_instansi' => ['required'],
            'tahun' => ['required'],
            'tgl_surat' => ['required', 'date'],
            'nik' => ['required', 'min:16'],
            'kk' => ['required', 'min:16'],
            'name' => ['required'],
            'gender' => ['required'],
            'status_kwn' => ['required'],
            'kewarganegaraan' => ['required'],
            'tempat_lhr' => ['required'],
            'tgl_lhr' =>  ['required', 'date'],
            'agama' => ['required'],
            'pendidikan' => ['required'],
            'pekerjaan' => ['required'],
            'kecamatan' => ['required'],
            'kelurahan' => ['required'],
            'alamat' => ['required', 'max:100'],
            'keterangan' => ['required', 'max:450'],
            'peruntukan' => ['required', 'max:100'],
            'kepada' => ['required'],
            'pengantar' => ['mimes:jpg,bmp,png']
        ]);

        if ($request->file('pengantar')) {
            Storage::disk('local')->makeDirectory('/public/pengantar/' . date('Y') . '/suket');
            $path = '/public/pengantar/' . date('Y') . '/suket';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/suket/' . $fileName;
            $request->file('pengantar')->storeAs($path, $fileName);
        }


        $gender = Gender::find($request->gender);
        $status_kwn = Status_kwn::find($request->status_kwn);
        $kewarganegaraan = Kewarganegaraan::find($request->kewarganegaraan);
        $agama = Agama::find($request->agama);
        $pendidikan = Pendidikan::find($request->pendidikan);
        $pekerjaan = Pekerjaan::find($request->pekerjaan);
        $kecamatan = Regional::find($request->kecamatan);
        $kelurahan = Regional::find($request->kelurahan);

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

        SuratKeterangan::create([
            'id_kel'    => 1,
            'kd_jenis_surat' => $request->kd_jenis_surat,
            'no_urut_surat' => $request->no_urut_surat,
            'kd_instansi' => $request->kd_instansi,
            'tahun' => $request->tahun,
            'tgl_surat' => $request->tgl_surat,
            'nik' => $request->nik,
            'keterangan' => $request->keterangan,
            'peruntukan' => $request->peruntukan,
            'kepada' => $request->kepada,
            'status' => 1,
            'pengantar' => $request->file('pengantar') ? $fileLocation : ''
        ]);

        return redirect()->route('suket.index');
    }

    public function edit($id)
    {
        // dd($id);
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KELURAHAN";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));

        $suratKeterangan = SuratKeterangan::find($id);
        if ($suratKeterangan->no_urut_surat == 0) {
            $no_urut_surat = SuratKeterangan::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
            $suratKeterangan->no_urut_surat = intval($no_urut_surat) + 1;
        }

        return view('suket.edit', compact('title', 'currentUser', 'suratKeterangan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kd_jenis_surat' => ['required'],
            'kd_jenis_surat' => ['required'],
            'no_urut_surat' => ['required'],
            'kd_instansi' => ['required'],
            'tahun' => ['required'],
            'tgl_surat' => ['required', 'date'],
            'nik' => ['required', 'min:16'],
            'kk' => ['required', 'min:16'],
            'name' => ['required'],
            'gender' => ['required'],
            'status_kwn' => ['required'],
            'kewarganegaraan' => ['required'],
            'tempat_lhr' => ['required'],
            'tgl_lhr' =>  ['required', 'date'],
            'agama' => ['required'],
            'pendidikan' => ['required'],
            'pekerjaan' => ['required'],
            'kecamatan' => ['required'],
            'kelurahan' => ['required'],
            'alamat' => ['required', 'max:100'],
            'keterangan' => ['required', 'max:450'],
            'peruntukan' => ['required', 'max:100'],
            'kepada' => ['required'],
            'pengantar' => ['mimes:jpg,bmp,png']
        ]);

        if ($request->file('pengantar')) {
            Storage::disk('local')->makeDirectory('/public/pengantar/' . date('Y') . '/suket');
            $path = '/public/pengantar/' . date('Y') . '/suket';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/suket/' . $fileName;
            $request->file('pengantar')->storeAs($path, $fileName);
        }

        $gender = Gender::find($request->gender);
        $status_kwn = Status_kwn::find($request->status_kwn);
        $kewarganegaraan = Kewarganegaraan::find($request->kewarganegaraan);
        $agama = Agama::find($request->agama);
        $pendidikan = Pendidikan::find($request->pendidikan);
        $pekerjaan = Pekerjaan::find($request->pekerjaan);
        $kecamatan = Regional::find($request->kecamatan);
        $kelurahan = Regional::find($request->kelurahan);

        $suratKeterangan = SuratKeterangan::find($id);

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

            $suratKeterangan->update([
                'kd_jenis_surat' => $request->kd_jenis_surat,
                'no_urut_surat' => $request->no_urut_surat,
                'kd_instansi' => $request->kd_instansi,
                'tahun' => $request->tahun,
                'tgl_surat' => $request->tgl_surat,
                'nik' => $request->nik,
                'keterangan' => $request->keterangan,
                'peruntukan' => $request->peruntukan,
                'kepada' => $request->kepada,
                'status' => 1,
                'pengantar' => $request->file('pengantar') ? $fileLocation : ''
            ]);

            return redirect()->route('suket.index');
        } else {
            return redirect()->route('suket.index');
        }
    }

    public function naik($id)
    {

        $suratKeterangan = SuratKeterangan::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 2]);

            return response()->json(['message' => 'Data updated successfully.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }

    public function preview($id)
    {
        $surat = SuratKeterangan::find($id);
        $resident = Resident::where('nik', $surat->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');

        $user = new User_resource(User::with('skpd')->find(Auth::id()));
        $pejabat = new Pejabat_resource(Pejabat::where('id_skpd', $user->id_instansi)->first());
        $tahunSrt = DateTime::createFromFormat('Y-m-d', $surat->tgl_surat);
        $tglSurat = Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
        $nomorSurat = $surat->kd_jenis_surat . '/' . $surat->no_urut_surat . '/' . $user->skpd->instansi_kode . '/' . $tahunSrt->format('Y');

        // $verify = env('APP_URL', 'https://esuket.dev') . '/verify/surat/' . $id;
        // $url = base64_encode(QrCode::format('png')->size(256)->generate($verify));

        $url = '';

        $pdf = Pdf::loadView('suket.pdf', compact(
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

    public function generate($id)
    {
        $surat = SuratKeterangan::find($id);
        $resident = Resident::where('nik', $surat->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
        $skpd = new Skpd_resource(Skpd::find($surat->id_kel));
        $pejabat = new Pejabat_resource(Pejabat::where('id_skpd', $surat->id_instansi)->first());
        $tahunSrt = DateTime::createFromFormat('Y-m-d', $surat->tgl_surat);
        $tglSurat = Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
        $nomorSurat = $surat->kd_jenis_surat . '/' . $surat->no_urut_surat . '/' . $skpd->instansi_kode . '/' . $tahunSrt->format('Y');

        $verify = env('APP_URL', 'https://esuket.dev') . '/verify/surat/' . $id;
        $url = base64_encode(QrCode::format('png')->size(256)->generate($verify));

        $pdf = Pdf::loadView('suket.pdf', compact(
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
        $surat = SuratKeterangan::find($id);
        return response()->json(['file' => asset($surat->file)]);
    }

    public function save(Request $request)
    {

        $request->validate([
            'nik' => ['required', 'min:16'],
            'keterangan' => ['required', 'max:450'],
            'peruntukan' => ['required', 'max:100'],
            'kepada' => ['required'],
            'pengantar' => ['required', 'mimes:jpg,bmp,png']
        ]);

        Storage::disk('local')->makeDirectory('/public/pengantar/' . date('Y') . '/suket');
        $path = '/public/pengantar/' . date('Y') . '/suket';
        $fileName = $request->file('pengantar')->hashName();
        $fileLocation = '/storage/pengantar/' . date('Y') . '/suket/' . $fileName;
        $request->file('pengantar')->storeAs($path, $fileName);

        $resident = Resident::where('nik', $request->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');

        $regional = new Regional_resource(Regional::find($penduduk['kelurahan']));

        SuratKeterangan::create([
            'id_kel'    => 1,
            'kd_jenis_surat' => 0,
            'no_urut_surat' => 0,
            'kd_instansi' => $regional['skpd']->instansi_kode,
            'tahun' => date('Y'),
            'tgl_surat' => date('Y-m-d'),
            'nik' => $request->nik,
            'keterangan' => $request->keterangan,
            'peruntukan' => $request->peruntukan,
            'kepada' => $request->kepada,
            'status' => 0,
            'pengantar' => $fileLocation
        ]);
        return response()->json(['message' => 'Pengajuan Surat Keterangan Berhasil!'], 200);
    }

    public function get(Request $request)
    {
        $surat = SuratKeterangan::where('nik', $request->nik)->get();
        return response()->json($surat);
    }


    public function tolak($id)
    {
        $suratKeterangan = SuratKeterangan::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 4]);

            return response()->json(['message' => 'Data updated successfully.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }
}
