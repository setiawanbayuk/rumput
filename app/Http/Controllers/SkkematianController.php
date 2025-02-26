<?php

namespace App\Http\Controllers;

use App\Http\Resources\Kelurahan_resource;
use App\Http\Resources\Pejabat_resource;
use App\Http\Resources\User_resource;
use App\Models\Gender;
use App\Models\Kelurahan;
use App\Models\Kewarganegaraan;
use App\Models\Log_surat;
use App\Models\Pejabat;
use App\Models\Resident;
use App\Models\SuratKematian;
use App\Models\User;
use App\Traits\GetNoSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class SkkematianController extends Controller
{
    use GetNoSurat;
    public function index()
    {
        if (request()->ajax()) {
            $data = SuratKematian::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $nomorSurat = $this->getNoSrt($row);
                    $id = $row->id;
                    $route = 'skkematian.edit';
                    $status = $row->status;
                    $jenis = 'skkematian';
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
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KEMATIAN";
        return view('skkematian.index', compact('title'));
    }
    public function add()
    {
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KEMATIAN";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));
        $no_urut_surat = SuratKematian::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
        $no_urut_surat = intval($no_urut_surat) + 1;
        return view('skkematian.add', compact('title', 'currentUser', 'no_urut_surat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kd_jenis_surat' => ['required', 'string'],
            'no_urut_surat' => ['required', 'string'],
            'kd_instansi' => ['required', 'string'],
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
            'name_ayah' => ['required', 'string'],
            'tempat_lhr_ayah' => ['required', 'string'],
            'tgl_lhr_ayah' =>  ['required', 'date'],
            'kewarganegaraan_ayah' => ['required', 'string'],
            'nik_ibu' => ['required', 'min:16'],
            'name_ibu' => ['required', 'string'],
            'tempat_lhr_ibu' => ['required', 'string'],
            'tgl_lhr_ibu' =>  ['required', 'date'],
            'kewarganegaraan_ibu' => ['required', 'string'],
            'nik' => ['required', 'min:16'],
            'name' => ['required', 'string'],
            'tgl_kematian' =>  ['required', 'date'],
            'jam_kematian' =>  ['required'],
            'sebab_kematian' => ['required', 'string'],
            'tempat_kematian' => ['required', 'string'],
            'yang_menerangkan' => ['required', 'string'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png']
        ]);

        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skkematian', 0755);
            $path = '/public/pengantar/' . date('Y') . '/skkematian';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skkematian/' . $fileName;
            $request->file('pengantar')->storeAs($path, $fileName);
        }

        $kewarganegaraan_pelapor = Kewarganegaraan::find($request->kewarganegaraan_pelapor);
        $kewarganegaraan_saksi1 = Kewarganegaraan::find($request->kewarganegaraan_saksi1);
        $kewarganegaraan_saksi2 = Kewarganegaraan::find($request->kewarganegaraan_saksi2);
        $kewarganegaraan_ayah = Kewarganegaraan::find($request->kewarganegaraan_ayah);
        $kewarganegaraan_ibu = Kewarganegaraan::find($request->kewarganegaraan_ibu);

        $suket = SuratKematian::create([
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
            'tempat_lhr_ayah' => $request->tempat_lhr_ayah,
            'tgl_lhr_ayah' => $request->tgl_lhr_ayah,
            'kewarganegaraan_ayah' => $request->kewarganegaraan_ayah,
            'kewarganegaraan_ayah_nm' => $kewarganegaraan_ayah->nama,
            'nama_ibu' => $request->name_ibu,
            'nik_ibu' => $request->nik_ibu,
            'tempat_lhr_ibu' => $request->tempat_lhr_ibu,
            'tgl_lhr_ibu' => $request->tgl_lhr_ibu,
            'kewarganegaraan_ibu' => $request->kewarganegaraan_ibu,
            'kewarganegaraan_ibu_nm' => $kewarganegaraan_ibu->nama,
            'nik' => $request->nik,
            'nama' => $request->name,
            'tgl_kematian' => $request->tgl_kematian,
            'jam_kematian' => $request->jam_kematian,
            'sebab_kematian' => $request->sebab_kematian,
            'tempat_kematian' => $request->tempat_kematian,
            'yang_menerangkan' => $request->yang_menerangkan,
            'status' => 1,
            'pengantar' => $request->file('pengantar') ? $fileLocation : '',
        ]);

        Log_surat::create([
            'nik' => $request->nik_pelapor,
            'tabel_surat' => 'surat_kematians',
            'nama_surat' => 'SURAT KETERANGAN KEMATIAN',
            'id_surat' => $suket->id,
            'status_surat' => 1,
        ]);

        return redirect()->route('skkematian.index');
    }

    public function edit($id)
    {
        // dd($id);
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KELURAHAN";
        $currentUser = new User_resource(User::with('skpd')->find(Auth::id()));

        $suratKeterangan = SuratKematian::find($id);
        if ($suratKeterangan->no_urut_surat == 0) {
            $no_urut_surat = SuratKematian::where('id_kel', $currentUser->id_instansi)->whereYear('tgl_surat', date('Y'))->max('no_urut_surat');
            $suratKeterangan->no_urut_surat = intval($no_urut_surat) + 1;
        }

        return view('skkematian.edit', compact('title', 'currentUser', 'suratKeterangan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kd_jenis_surat' => ['required', 'string'],
            'no_urut_surat' => ['required', 'string'],
            'kd_instansi' => ['required', 'string'],
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
            'name_ayah' => ['required', 'string'],
            'tempat_lhr_ayah' => ['required', 'string'],
            'tgl_lhr_ayah' =>  ['required', 'date'],
            'kewarganegaraan_ayah' => ['required', 'string'],
            'nik_ibu' => ['required', 'min:16'],
            'name_ibu' => ['required', 'string'],
            'tempat_lhr_ibu' => ['required', 'string'],
            'tgl_lhr_ibu' =>  ['required', 'date'],
            'kewarganegaraan_ibu' => ['required', 'string'],
            'nik' => ['required', 'min:16'],
            'name' => ['required', 'string'],
            'tgl_kematian' =>  ['required', 'date'],
            'jam_kematian' =>  ['required'],
            'sebab_kematian' => ['required', 'string'],
            'tempat_kematian' => ['required', 'string'],
            'yang_menerangkan' => ['required', 'string'],
            'pengantar' => ['mimes:jpg,jpeg,bmp,png']
        ]);

        if ($request->file('pengantar')) {
            //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skkematian', 0755);
            $path = '/public/pengantar/' . date('Y') . '/skkematian';
            $fileName = $request->file('pengantar')->hashName();
            $fileLocation = '/storage/pengantar/' . date('Y') . '/skkematian/' . $fileName;
            $request->file('pengantar')->storeAs($path, $fileName);
        }

        $kewarganegaraan_pelapor = Kewarganegaraan::find($request->kewarganegaraan_pelapor);
        $kewarganegaraan_saksi1 = Kewarganegaraan::find($request->kewarganegaraan_saksi1);
        $kewarganegaraan_saksi2 = Kewarganegaraan::find($request->kewarganegaraan_saksi2);
        $kewarganegaraan_ayah = Kewarganegaraan::find($request->kewarganegaraan_ayah);
        $kewarganegaraan_ibu = Kewarganegaraan::find($request->kewarganegaraan_ibu);

        $suratKeterangan = SuratKematian::find($id);

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
                'tempat_lhr_ayah' => $request->tempat_lhr_ayah,
                'tgl_lhr_ayah' => $request->tgl_lhr_ayah,
                'kewarganegaraan_ayah' => $request->kewarganegaraan_ayah,
                'kewarganegaraan_ayah_nm' => $kewarganegaraan_ayah->nama,
                'nama_ibu' => $request->name_ibu,
                'nik_ibu' => $request->nik_ibu,
                'tempat_lhr_ibu' => $request->tempat_lhr_ibu,
                'tgl_lhr_ibu' => $request->tgl_lhr_ibu,
                'kewarganegaraan_ibu' => $request->kewarganegaraan_ibu,
                'kewarganegaraan_ibu_nm' => $kewarganegaraan_ibu->nama,
                'nik' => $request->nik,
                'nama' => $request->name,
                'tgl_kematian' => $request->tgl_kematian,
                'jam_kematian' => $request->jam_kematian,
                'sebab_kematian' => $request->sebab_kematian,
                'tempat_kematian' => $request->tempat_kematian,
                'yang_menerangkan' => $request->yang_menerangkan,
                'status' => 1,
                'pengantar' => $request->file('pengantar') ? $fileLocation : '',
            ]);

            return redirect()->route('skkematian.index');
        } else {
            return redirect()->route('skkematian.index');
        }
    }

    public function naik($id)
    {
        $suratKeterangan = SuratKematian::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 2]);

            Log_surat::create([
                'nik' => $suratKeterangan->nik_pelapor,
                'tabel_surat' => 'surat_kematians',
                'nama_surat' => 'SURAT KETERANGAN KEMATIAN',
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
        $surat = SuratKematian::find($id);
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

        $tgl_kematian = explode('-', $surat->tgl_kematian);

        $y_kematian = $tgl_kematian[0];
        $m_kematian = $tgl_kematian[1];
        $d_kematian = $tgl_kematian[2];


        $jam_kematian = explode(':', $surat->jam_kematian);

        $hh_kematian = $jam_kematian[0];
        $mm_kematian = $jam_kematian[1];

        $pdf = Pdf::loadView('skkematian.pdf', compact(
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
            'y_kematian',
            'm_kematian',
            'd_kematian',
            'hh_kematian',
            'mm_kematian',
        ))->setPaper('legal', 'portrait');
        return $pdf->stream();
    }

    public function cetak($id)
    {
        $surat = SuratKematian::find($id);
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

        //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skkematian', 0755);
        $path = '/public/pengantar/' . date('Y') . '/skkematian';
        $fileName = $request->file('pengantar')->hashName();
        $fileLocation = '/storage/pengantar/' . date('Y') . '/skkematian/' . $fileName;
        $request->file('pengantar')->storeAs($path, $fileName);

        $resident = Resident::where('nik', $request->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
        $regional = new Kelurahan_resource(Kelurahan::find($penduduk['kelurahan']));

        $suket = SuratKematian::create([
            'id_kel'    => auth()->user()->id_instansi,
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

        Log_surat::create([
            'nik' => $request->nik,
            'tabel_surat' => 'surat_kematians',
            'nama_surat' => 'SURAT KETERANGAN KEMATIAN',
            'id_surat' => $suket->id,
            'status_surat' => 0,
        ]);


        return response()->json(['message' => 'Pengajuan Surat Keterangan Berhasil!'], 200);
    }

    public function get(Request $request)
    {
        $surat = SuratKematian::with(['history' => function ($query) {
            return $query->where('tabel_surat', 'surat_kematians');
        }])->where('nik', $request->nik)->orderBy('id', 'desc')->get();
        return response()->json($surat);
    }


    public function tolak($id)
    {
        $suratKeterangan = SuratKematian::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 4]);

            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_kematians',
                'nama_surat' => 'SURAT KETERANGAN KEMATIAN',
                'id_surat' => $id,
                'status_surat' => 4,
            ]);

            return response()->json(['message' => 'Data updated successfully.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }
}
