<?php

namespace App\Http\Controllers;

use App\Http\Resources\User_resource;
use App\Models\Agama;
use App\Models\Gender;
use App\Models\Kewarganegaraan;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Resident;
use App\Models\Status_kwn;
use App\Models\SuratKeterangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class SuketController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            // dd(SuratKeterangan::get());
            $data = SuratKeterangan::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                        <div class="d-flex gap-1">
                            <button
                                class="edit btn btn-warning btn-sm"
                                data-id="' . $row->id . '"
                            >
                                <i class="ri-pencil-line" data-bs-toggle="tooltip" data-bs-title="Edit" title="Edit"></i>
                            </button>
                            <button class="print btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-title="Cetak" title="Cetak" id="' . $row->id . '" onclick="handleCetak(\'' . $row->id . '\')">
                                <i class="ri-printer-line"></i>
                            </button>
                        </div>
                    ';

                    return $actionBtn;
                })
                ->rawColumns(['action'])
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
            'alamat' => ['required'],
            'keterangan' => ['required'],
            'peruntukan' => ['required'],
            'kepada' => ['required']
        ]);

        $gender = Gender::find($request->gender);
        $status_kwn = Status_kwn::find($request->status_kwn);
        $kewarganegaraan = Kewarganegaraan::find($request->kewarganegaraan);
        $agama = Agama::find($request->agama);
        $pendidikan = Pendidikan::find($request->pendidikan);
        $pekerjaan = Pekerjaan::find($request->pekerjaan);

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
            'kd_jenis_surat' => $request->kd_jenis_surat,
            'no_urut_surat' => $request->no_urut_surat,
            'kd_instansi' => $request->kd_instansi,
            'tahun' => $request->tahun,
            'tgl_surat' => $request->tgl_surat,
            'nik' => $request->nik,
            'keterangan' => $request->keterangan,
            'peruntukan' => $request->peruntukan,
            'kepada' => $request->kepada,
            'status' => 1
        ]);

        return redirect()->route('suket.index');
    }
}
