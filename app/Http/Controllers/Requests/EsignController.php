<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pejabat_resource;
use App\Http\Resources\Skpd_resource;
use App\Http\Resources\User_resource;
use App\Models\Log_surat;
use App\Models\Pejabat;
use App\Models\Resident;
use App\Models\Skpd;
use App\Models\Surat_keterangan;
use App\Models\Surat_skbn;
use App\Models\SuratBoro;
use App\Models\SuratBoroPengikut;
use App\Models\SuratDomisili;
use App\Models\SuratKelahiran;
use App\Models\SuratKematian;
use App\Models\SuratKeterangan;
use App\Models\SuratPenghasilan;
use App\Models\SuratSkbn;
use App\Models\SuratSktm;
use App\Models\SuratUsaha;
use App\Models\User;
use App\Traits\GetNoSurat;
use App\Traits\GeneratePDF;
use App\Traits\GlobalFunction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EsignController extends Controller
{

    use GetNoSurat, GeneratePDF, GlobalFunction;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function check(string $nik)
    {
        $r = Http::withBasicAuth(env('ESIGN_USER'), env('ESIGN_PASS'))->get('http://103.78.106.34/api/user/status/' . $nik);
        $response = $r->json();

        if (!$response) {
            $rspn = $response->getError();
            $res['message'] = '<span style="color:red">' . $rspn['message'] . '</span>';
            $res['status_code'] = '0000';
        } else {
            if ($response['status_code'] == '1111') {
                $res['message'] = '<span style="color:green">' . $response['message'] . '</span>';
                $res['status_code'] = $response['status_code'];
            } else {
                $res['message'] = '<span style="color:red">' . $response['message'] . '</span>';
                $res['status_code'] = $response['status_code'];
            }
        }
        return response()->json($res, 200);
    }

    public function sign(Request $request)
    {
        $outputPdf = "";
        parse_str($request->getContent(), $output);
        if ($output['jenis'] == 'suket') {
            $surat = SuratKeterangan::find($output['_id']);
            $tabel_surat = 'surat_keterangans';
            $nama_surat = 'SURAT KETERANGAN';
        } else if ($output['jenis'] == 'skbn') {
            $surat = SuratSkbn::find($output['_id']);
            $tabel_surat = 'surat_skbns';
            $nama_surat = 'SURAT KETERANGAN BELUM MENIKAH';
        } else if ($output['jenis'] == 'sktm') {
            $surat = SuratSktm::find($output['_id']);
            $surat['kepada_tgl_lhr'] = Carbon::parse($surat['kepada_tgl_lhr'])->isoFormat('D MMMM Y');
            $tabel_surat = 'surat_sktms';
            $nama_surat = 'SURAT KETERANGAN MISKIN';
        } else if ($output['jenis'] == 'skdom') {
            $surat = SuratDomisili::find($output['_id']);
            $surat['tgl_berlaku'] = Carbon::parse($surat['tgl_berlaku'])->isoFormat('D MMMM Y');
            $tabel_surat = 'surat_domisilis';
            $nama_surat = 'SURAT KETERANGAN DOMISILI';
        } else if ($output['jenis'] == 'skhsl') {
            $surat = SuratPenghasilan::find($output['_id']);
            $tabel_surat = 'surat_penghasilans';
            $nama_surat = 'SURAT KETERANGAN PENGHASILAN';
        } else if ($output['jenis'] == 'skusaha') {
            $surat = SuratUsaha::find($output['_id']);
            $tabel_surat = 'surat_usahas';
            $nama_surat = 'SURAT KETERANGAN USAHA';
        } else if ($output['jenis'] == 'skkelahiran') {
            $surat = SuratKelahiran::find($output['_id']);
            $tabel_surat = 'surat_kelahirans';
            $nama_surat = 'SURAT KETERANGAN KELAHIRAN';
        } else if ($output['jenis'] == 'skkematian') {
            $surat = SuratKematian::find($output['_id']);
            $tabel_surat = 'surat_kematians';
            $nama_surat = 'SURAT KETERANGAN KEMATIAN';
        } else if ($output['jenis'] == 'skboro') {
            $surat = SuratBoro::find($output['_id']);
            $tabel_surat = 'surat_boros';
            $nama_surat = 'SURAT KETERANGAN BORO';
        }

        $skpd = new Skpd_resource(Skpd::find($surat->id_kel));
        $pejabat = new Pejabat_resource(Pejabat::where('id_skpd', $surat->id_kel)->first());
        $tahunSrt = DateTime::createFromFormat('Y-m-d', $surat->tgl_surat);
        $tglSurat = Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
        $nomorSurat = $surat->kd_jenis_surat . '/' . $surat->no_urut_surat . '/' . $skpd->instansi_kode . '/' . $tahunSrt->format('Y');
        // $nomorSurat = $this->getNoSrt($surat);
        $verify = env('APP_URL', 'http://rumput.test') . '/verify/' . $output['jenis'] . '/' . $output['_id'];
        // $url = base64_encode(QrCode::format('png')->size(256)->generate($verify));
        // dd($url);

        $fileName = hash('sha256', $nomorSurat . date("Y-m-d H:i:s")) . '.pdf';

        $nik = $surat->nik;

        if ($output['jenis'] == 'suket') {
        } else if ($output['jenis'] == 'skbn') {
            $resident = Resident::where('nik', $surat->nik)->first();
            $penduduk = unserialize($resident->data);
            $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');

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
                'surat_keterangan' => 'Menurut pernyataan yang bersangkutan belum pernah menikah / kawin.',
                'surat_kepada' => $surat->kepada,
                'surat_peruntukan' => $surat->peruntukan,
                'surat_tgl' => $tglSurat,
            ];

            // Path template .docx
            $templateFile = public_path('templates/SKBN.docx');
            $outputPdf = hash('sha256', 'SKBN_' . $output['_id']) . '_signed';
            // Generate PDF dari template
            $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);
            // dd($pdfPath);

            $request = [
                'path' => $pdfPath,
                'file_name' => $outputPdf . '.pdf',
                'nik' => $output['nik'],
                'passphrase' => $output['passphrase'],
                'qr_loc' => 'qr_here',
                'verify' => $verify,
                'is_visible' => true,
            ];
            $res = $this->TTE_sign($request);
            // dd($res);
        } else if ($output['jenis'] == 'skkelahiran') {
            $nik = $surat->nik_pelapor;
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
        } else if ($output['jenis'] == 'skkematian') {
            $nik = $surat->nik_pelapor;

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
        } else if ($output['jenis'] == 'skboro') {
            // $nik = $surat->nik;
            $surat['tgl_awal'] = Carbon::parse($surat['tgl_awal'])->isoFormat('D MMMM Y');
            $surat['tgl_akhir'] = Carbon::parse($surat['tgl_akhir'])->isoFormat('D MMMM Y');
            $surat['pengikut'] = SuratBoroPengikut::where('boro_id', $output['_id'])->count();
            $pengikut = SuratBoroPengikut::where('boro_id', $output['_id'])->get();
            $resident = Resident::where('nik', $surat->nik)->first();
            $penduduk = unserialize($resident->data);
            $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');

            // dd($surat);
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
                'surat_tgl' => $tglSurat,
                'surat_tgl_berlaku' =>  $surat['tgl_awal'] . 's/d' .  $surat['tgl_akhir'],
                'surat_tujuan' => 'Desa / Kelurahan : ' . $surat['kel_boro_nm'] . ' Kecamatan : ' . $surat['kec_boro_nm'] . ' Kabupaten : ' . $surat['kabko_boro_nm'] . ' Provinsi : ' . $surat['prov_boro_nm'],
                'surat_keperluan' => $surat->peruntukan,
                'surat_jml_pengikut' => $surat->pengikut,
                'detail_pengikut' => collect($pengikut)->toArray()
            ];
            // dd($data);

            // Path template .docx
            $templateFile = public_path('templates/SKBORO.docx');
            $outputPdf = hash('sha256', 'SKBN_' . $output['_id']) . '_signed';
            // Generate PDF dari template
            $pdfPath = $this->generatePdfTable($data, $templateFile, $outputPdf);
            // dd($pdfPath);

            $request = [
                'path' => $pdfPath,
                'file_name' => $outputPdf . '.pdf',
                'nik' => $output['nik'],
                'passphrase' => $output['passphrase'],
                'qr_loc' => 'qr_here',
                'verify' => $verify,
                'is_visible' => true,
            ];
            $res = $this->TTE_sign($request);
        } else {
            $nik = $surat->nik;
            $resident = Resident::where('nik', $surat->nik)->first();
            $penduduk = unserialize($resident->data);
            $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
            $pdf = Pdf::loadView($output['jenis'] . '.pdf', compact(
                'surat',
                'penduduk',
                'nomorSurat',
                'pejabat',
                'tglSurat',
                'url'
            ))->setPaper(array(0, 0, 609.4488, 935.433), 'portrait');
        }

        if ($output['jenis'] == 'sktm') {
            // $surat['kepada_tgl_lhr'] = $kepada_tgl_lhr;
            $surat = SuratSktm::find($output['_id']);
        } else if ($output['jenis'] == 'skdom') {
            // $surat['tgl_berlaku'] = $tgl_berlaku;
            $surat = SuratDomisili::find($output['_id']);
        } else if ($output['jenis'] == 'skboro') {
            // $surat['tgl_berlaku'] = $tgl_berlaku;
            $surat = SuratBoro::find($output['_id']);
        }

        $surat->update(['status' => 3, 'file' => 'storage/pdf/' . $outputPdf . '.pdf']);

        Log_surat::create([
            'nik' => $nik,
            'tabel_surat' => $tabel_surat,
            'nama_surat' => $nama_surat,
            'id_surat' => $surat->id,
            'status_surat' => 3,
        ]);

        ///Notif WA ke Pengaju

        return response()->json(['message' => 'Esign done successfully.', 'status' => 'success'], 200);
    }
}
