<?php

namespace App\Http\Controllers;

use App\Http\Resources\User_resource;
use App\Models\Agama;
use App\Models\Gender;
use App\Models\Kewarganegaraan;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Regional;
use App\Models\Resident;
use App\Models\Skbn;
use App\Models\Status_kwn;
use App\Models\User;
use App\Traits\GetNoSurat;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class SkbnController extends Controller
{
    use GetNoSurat;
    public function index()
    {

        if (request()->ajax()) {
            $data = Skbn::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $nomorSurat = $this->getNoSrt($row);
                    $id = $row->id;
                    $route = 'skbn.edit';
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
        $title = "USULAN PENGAJUAN SURAT KETERANGAN BELUM MENIKAH";
        return view('skbn.index', compact('title'));
    }

    public function add()
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KELURAHAN";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $no_urut_surat = Skbn::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
        $no_urut_surat = intval($no_urut_surat) + 1;
        return view('skbn.add', compact('title', 'currentUser', 'no_urut_surat'));
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
            'peruntukan' => ['required', 'max:100'],
            'kepada' => ['required'],
            'pengantar' => ['mimes:jpg,bmp,png']
        ]);

        if ($request->file('pengantar')) {
            Storage::disk('local')->makeDirectory('/public/pengantar/' . date('Y') . '/skbn');
            $path = '/public/pengantar/' . date('Y') . '/skbn';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skbn/' . $fileName;
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

        Skbn::create([
            'id_kel'    => 1,
            'kd_jenis_surat' => $request->kd_jenis_surat,
            'no_urut_surat' => $request->no_urut_surat,
            'kd_instansi' => $request->kd_instansi,
            'tahun' => $request->tahun,
            'tgl_surat' => $request->tgl_surat,
            'nik' => $request->nik,
            'peruntukan' => $request->peruntukan,
            'kepada' => $request->kepada,
            'status' => 1,
            'pengantar' => $request->file('pengantar') ? $fileLocation : ''
        ]);

        return redirect()->route('skbn.index');
    }
}
