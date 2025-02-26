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
use App\Models\SuratKelahiran;
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
        if (request()->ajax()) {
            $data = SuratKelahiran::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $nomorSurat = $this->getNoSrt($row);
                    $id = $row->id;
                    $route = 'skkelahiran.edit';
                    $status = $row->status;
                    $jenis = 'skkelahiran';
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
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KELAHIRAN";
        return view('skkelahiran.index', compact('title'));
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
        $title = "USULAN PENGAJUAN SURAT KETERANGAN KELURAHAN";
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
                'status' => 1,
                'pengantar' => $request->file('pengantar') ? $fileLocation : $suratKeterangan->pengantar
            ]);

            return redirect()->route('skkelahiran.index');
        } else {
            return redirect()->route('skkelahiran.index');
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
            'nik' => ['required', 'min:16'],
            'keterangan' => ['required', 'max:450'],
            'peruntukan' => ['required', 'max:100'],
            'kepada' => ['required'],
            'pengantar' => ['required', 'mimes:jpg,bmp,png']
        ]);

        //Storage::makeDirectory('/public/pengantar/' . date('Y') . '/skkelahiran', 0755);
        $path = '/public/pengantar/' . date('Y') . '/skkelahiran';
        $fileName = $request->file('pengantar')->hashName();
        $fileLocation = '/storage/pengantar/' . date('Y') . '/skkelahiran/' . $fileName;
        $request->file('pengantar')->storeAs($path, $fileName);

        $resident = Resident::where('nik', $request->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
        $regional = new Kelurahan_resource(Kelurahan::find($penduduk['kelurahan']));

        $suket = SuratKelahiran::create([
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
            'tabel_surat' => 'surat_kelahirans',
            'nama_surat' => 'SURAT KETERANGAN KELAHIRAN',
            'id_surat' => $suket->id,
            'status_surat' => 0,
        ]);


        return response()->json(['message' => 'Pengajuan Surat Keterangan Berhasil!'], 200);
    }

    public function get(Request $request)
    {
        $surat = SuratKelahiran::with(['history' => function ($query) {
            return $query->where('tabel_surat', 'surat_kelahirans');
        }])->where('nik', $request->nik)->orderBy('id', 'desc')->get();
        return response()->json($surat);
    }


    public function tolak($id)
    {
        $suratKeterangan = SuratKelahiran::find($id);
        if ($suratKeterangan) {
            $suratKeterangan->update(['status' => 4]);

            Log_surat::create([
                'nik' => $suratKeterangan->nik,
                'tabel_surat' => 'surat_kelahirans',
                'nama_surat' => 'SURAT KETERANGAN KELAHIRAN',
                'id_surat' => $id,
                'status_surat' => 4,
            ]);

            return response()->json(['message' => 'Data updated successfully.', 'data' => $id]);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }
}
