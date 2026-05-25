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
use App\Models\SuratPengajuan;
use App\Models\SuratSkbn;
use App\Models\SuratSktm;
use App\Models\SuratTemplate;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

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
        $r = Http::withBasicAuth(env('ESIGN_USER'), env('ESIGN_PASS'))->get(env('APP_URL_TTE') . '/user/status/' . $nik);
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

    // public function sign(Request $request)
    // {
    //     $outputPdf = "";
    //     parse_str($request->getContent(), $output);
    //     if ($output['jenis'] == 'suket') {
    //         $surat = SuratKeterangan::find($output['_id']);
    //         $tabel_surat = 'surat_keterangans';
    //         $nama_surat = 'SURAT KETERANGAN';
    //     } else if ($output['jenis'] == 'skbn') {
    //         $surat = SuratSkbn::find($output['_id']);
    //         $tabel_surat = 'surat_skbns';
    //         $nama_surat = 'SURAT KETERANGAN BELUM MENIKAH';
    //     } else if ($output['jenis'] == 'sktm') {
    //         $surat = SuratSktm::find($output['_id']);
    //         $surat['kepada_tgl_lhr'] = Carbon::parse($surat['kepada_tgl_lhr'])->isoFormat('D MMMM Y');
    //         $tabel_surat = 'surat_sktms';
    //         $nama_surat = 'SURAT KETERANGAN MISKIN';
    //     } else if ($output['jenis'] == 'skdom') {
    //         $surat = SuratDomisili::find($output['_id']);
    //         $surat['tgl_berlaku'] = Carbon::parse($surat['tgl_berlaku'])->isoFormat('D MMMM Y');
    //         $tabel_surat = 'surat_domisilis';
    //         $nama_surat = 'SURAT KETERANGAN DOMISILI';
    //     } else if ($output['jenis'] == 'skhsl') {
    //         $surat = SuratPenghasilan::find($output['_id']);
    //         $tabel_surat = 'surat_penghasilans';
    //         $nama_surat = 'SURAT KETERANGAN PENGHASILAN';
    //     } else if ($output['jenis'] == 'skusaha') {
    //         $surat = SuratUsaha::find($output['_id']);
    //         $tabel_surat = 'surat_usahas';
    //         $nama_surat = 'SURAT KETERANGAN USAHA';
    //     } else if ($output['jenis'] == 'skkelahiran') {
    //         $surat = SuratKelahiran::find($output['_id']);
    //         $tabel_surat = 'surat_kelahirans';
    //         $nama_surat = 'SURAT KETERANGAN KELAHIRAN';
    //     } else if ($output['jenis'] == 'skkematian') {
    //         $surat = SuratKematian::find($output['_id']);
    //         $tabel_surat = 'surat_kematians';
    //         $nama_surat = 'SURAT KETERANGAN KEMATIAN';
    //     } else if ($output['jenis'] == 'skboro') {
    //         $surat = SuratBoro::find($output['_id']);
    //         $tabel_surat = 'surat_boros';
    //         $nama_surat = 'SURAT KETERANGAN BORO';
    //     }

    //     $skpd = new Skpd_resource(Skpd::find($surat->id_kel));
    //     $pejabat = new Pejabat_resource(Pejabat::where('id_skpd', $surat->id_kel)->first());
    //     $tahunSrt = DateTime::createFromFormat('Y-m-d', $surat->tgl_surat);
    //     $tglSurat = Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
    //     $nomorSurat = $surat->kd_jenis_surat . '/' . $surat->no_urut_surat . '/' . $skpd->instansi_kode . '/' . $tahunSrt->format('Y');
    //     // $nomorSurat = $this->getNoSrt($surat);
    //     $verify = env('APP_URL', 'http://rumput.test') . '/verify/' . $output['jenis'] . '/' . $output['_id'];
    //     $url = base64_encode(QrCode::format('png')->size(256)->generate($verify));
    //     // dd($url);

    //     if ($output['jenis'] == 'skkelahiran' || $output['jenis'] == 'skkematian') {
    //         $nik = $surat->nik_pelapor;
    //     } else {
    //         $nik = $surat->nik;
    //     }

    //     // dd($nik);
    //     $resident = Resident::where('nik', $nik)->first();
    //     $penduduk = $resident->data;
    //     $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');

    // if ($output['jenis'] == 'suket') {
    //     $data = [
    //         'skpd_kec' => strtoupper($pejabat->skpd->kecamatan->nama),
    //         'skpd_kel' => strtoupper($pejabat->skpd->nama),
    //         'skpd_alamat' => $pejabat->skpd->instansi_alamat,
    //         'skpd_telp' => $pejabat->skpd->instansi_telp,
    //         'skpd_pos' => $pejabat->skpd->instansi_kode_pos,
    //         'skpd_kepala' => $pejabat->nama,
    //         'skpd_nip_kepala' => $pejabat->nip,
    //         'skpd_jabatan' => ucfirst($pejabat->jabatan->nama) . ' ' . ucfirst(strtolower($pejabat->skpd->nama)),
    //         'surat_no' => $nomorSurat,
    //         'surat_nama' => $penduduk['name'],
    //         'surat_nik' => $surat->nik,
    //         'surat_tmpl' => $penduduk['tempat_lhr'],
    //         'surat_tgll' => strtoupper($penduduk['tgl_lhr']),
    //         'surat_gender' => $penduduk['gender_nm'],
    //         'surat_perkawinan' => $penduduk['status_kwn_nm'],
    //         'surat_agama' => $penduduk['agama_nm'],
    //         'surat_pekerjaan' => $penduduk['pekerjaan_nm'],
    //         'surat_pendidikan' => $penduduk['pendidikan_nm'],
    //         'surat_alamat' => $penduduk['alamat'] . ' KEL. ' . $penduduk['kelurahan_nm'] . ' KEC. ' . $penduduk['kecamatan_nm'] . ' ' .  $penduduk['kabko_nm'],
    //         'surat_keterangan' => $surat->keterangan,
    //         'surat_kepada' => $surat->kepada,
    //         'surat_peruntukan' => $surat->peruntukan,
    //         'surat_tgl' => $tglSurat,
    //         'link' => $verify
    //     ];
    //     // dd($data);

    //     // Path template .docx
    //     $templateFile = public_path('templates/SUKET.docx');
    //     $outputPdf = hash('sha256', 'SUKET_' . $output['_id']) . '_signed';
    //     // Generate PDF dari template
    //     $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);
    // } else if ($output['jenis'] == 'skbn') {
    //     $data = [
    //         'skpd_kec' => strtoupper($pejabat->skpd->kecamatan->nama),
    //         'skpd_kel' => strtoupper($pejabat->skpd->nama),
    //         'skpd_alamat' => $pejabat->skpd->instansi_alamat,
    //         'skpd_telp' => $pejabat->skpd->instansi_telp,
    //         'skpd_pos' => $pejabat->skpd->instansi_kode_pos,
    //         'skpd_kepala' => $pejabat->nama,
    //         'skpd_nip_kepala' => $pejabat->nip,
    //         'skpd_jabatan' => ucfirst($pejabat->jabatan->nama) . ' ' . ucfirst(strtolower($pejabat->skpd->nama)),
    //         'surat_no' => $nomorSurat,
    //         'surat_nama' => $penduduk['name'],
    //         'surat_nik' => $surat->nik,
    //         'surat_tmpl' => $penduduk['tempat_lhr'],
    //         'surat_tgll' => strtoupper($penduduk['tgl_lhr']),
    //         'surat_gender' => $penduduk['gender_nm'],
    //         'surat_perkawinan' => $penduduk['status_kwn_nm'],
    //         'surat_agama' => $penduduk['agama_nm'],
    //         'surat_pekerjaan' => $penduduk['pekerjaan_nm'],
    //         'surat_pendidikan' => $penduduk['pendidikan_nm'],
    //         'surat_alamat' => $penduduk['alamat'] . ' KEL. ' . $penduduk['kelurahan_nm'] . ' KEC. ' . $penduduk['kecamatan_nm'] . ' ' .  $penduduk['kabko_nm'],
    //         'surat_keterangan' => 'Menurut pernyataan yang bersangkutan belum pernah menikah / kawin.',
    //         'surat_kepada' => $surat->kepada,
    //         'surat_peruntukan' => $surat->peruntukan,
    //         'surat_tgl' => $tglSurat,
    //         'link' => $verify
    //     ];

    //     // Path template .docx
    //     $template = SuratTemplate::where(['id_kel' => $surat->id_kel, 'jenis' => 'skbn'])->first();
    //     if (isset($template) && ($surat->variable != "")) {
    //         $var = $surat->variable;
    //         $templateFile = public_path($template->path_docs);
    //         $data = array_merge($data, $var);
    //     } else {
    //         $templateFile = public_path('templates/SKBN.docx');
    //     }
    //     $outputPdf = hash('sha256', 'SKBN_' . $output['_id']) . '_signed';
    //     // Generate PDF dari template
    //     $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);
    //     // dd($pdfPath);
    // } else if ($output['jenis'] == 'skboro') {
    //     $surat['tgl_awal'] = Carbon::parse($surat['tgl_awal'])->isoFormat('D MMMM Y');
    //     $surat['tgl_akhir'] = Carbon::parse($surat['tgl_akhir'])->isoFormat('D MMMM Y');
    //     $surat['pengikut'] = SuratBoroPengikut::where('boro_id', $output['_id'])->count();
    //     $pengikut = SuratBoroPengikut::where('boro_id', $output['_id'])->get();
    //     $data = [
    //         'skpd_kec' => strtoupper($pejabat->skpd->kecamatan->nama),
    //         'skpd_kel' => strtoupper($pejabat->skpd->nama),
    //         'skpd_alamat' => $pejabat->skpd->instansi_alamat,
    //         'skpd_telp' => $pejabat->skpd->instansi_telp,
    //         'skpd_pos' => $pejabat->skpd->instansi_kode_pos,
    //         'skpd_kepala' => $pejabat->nama,
    //         'skpd_nip_kepala' => $pejabat->nip,
    //         'skpd_jabatan' => ucfirst($pejabat->jabatan->nama) . ' ' . ucfirst(strtolower($pejabat->skpd->nama)),
    //         'surat_no' => $nomorSurat,
    //         'surat_nama' => $penduduk['name'],
    //         'surat_nik' => $surat->nik,
    //         'surat_tmpl' => $penduduk['tempat_lhr'],
    //         'surat_tgll' => strtoupper($penduduk['tgl_lhr']),
    //         'surat_gender' => $penduduk['gender_nm'],
    //         'surat_perkawinan' => $penduduk['status_kwn_nm'],
    //         'surat_agama' => $penduduk['agama_nm'],
    //         'surat_pekerjaan' => $penduduk['pekerjaan_nm'],
    //         'surat_pendidikan' => $penduduk['pendidikan_nm'],
    //         'surat_alamat' => $penduduk['alamat'] . ' KEL. ' . $penduduk['kelurahan_nm'] . ' KEC. ' . $penduduk['kecamatan_nm'] . ' ' .  $penduduk['kabko_nm'],
    //         'surat_tgl' => $tglSurat,
    //         'surat_tgl_berlaku' =>  $surat['tgl_awal'] . ' s/d ' .  $surat['tgl_akhir'],
    //         'surat_tujuan' => 'Desa / Kelurahan : ' . $surat['kel_boro_nm'] . ' Kecamatan : ' . $surat['kec_boro_nm'] . ' Kabupaten : ' . $surat['kabko_boro_nm'] . ' Provinsi : ' . $surat['prov_boro_nm'],
    //         'surat_keperluan' => $surat->peruntukan,
    //         'surat_jml_pengikut' => $surat->pengikut,
    //         'detail_pengikut' => collect($pengikut)->toArray(),
    //         'link' => $verify
    //     ];
    //     // dd($data);

    //     // Path template .docx
    //     $templateFile = public_path('templates/SKBORO.docx');
    //     $outputPdf = hash('sha256', 'SKBORO_' . $output['_id']) . '_signed';
    //     // Generate PDF dari template
    //     $pdfPath = $this->generatePdfTable($data, $templateFile, $outputPdf);
    // } else if ($output['jenis'] == 'skdom') {
    //     $data = [
    //         'skpd_kec' => strtoupper($pejabat->skpd->kecamatan->nama),
    //         'skpd_kel' => strtoupper($pejabat->skpd->nama),
    //         'skpd_alamat' => $pejabat->skpd->instansi_alamat,
    //         'skpd_telp' => $pejabat->skpd->instansi_telp,
    //         'skpd_pos' => $pejabat->skpd->instansi_kode_pos,
    //         'skpd_kepala' => $pejabat->nama,
    //         'skpd_nip_kepala' => $pejabat->nip,
    //         'skpd_jabatan' => ucfirst($pejabat->jabatan->nama) . ' ' . ucfirst(strtolower($pejabat->skpd->nama)),
    //         'surat_no' => $nomorSurat,
    //         'surat_nama' => $penduduk['name'],
    //         'surat_nik' => $surat->nik,
    //         'surat_tmpl' => $penduduk['tempat_lhr'],
    //         'surat_tgll' => strtoupper($penduduk['tgl_lhr']),
    //         'surat_gender' => $penduduk['gender_nm'],
    //         'surat_perkawinan' => $penduduk['status_kwn_nm'],
    //         'surat_agama' => $penduduk['agama_nm'],
    //         'surat_pekerjaan' => $penduduk['pekerjaan_nm'],
    //         'surat_pendidikan' => $penduduk['pendidikan_nm'],
    //         'surat_alamat' => $penduduk['alamat'] . ' KEL. ' . $penduduk['kelurahan_nm'] . ' KEC. ' . $penduduk['kecamatan_nm'] . ' ' .  $penduduk['kabko_nm'],
    //         'surat_keterangan' => $surat['jenis'] == 'perorangan' ?
    //             'Bahwa nama tersebut di atas benar - benar berdomisili di ' . $surat['alamat_domisili'] . ', Kel. ' . ucfirst(strtolower($penduduk['kelurahan_nm'])) . ' Kec. ' . ucfirst(strtolower($penduduk['kecamatan_nm'])) . ' ' .  ucwords(strtolower($penduduk['kabko_nm'])) :
    //             'Pendiri / pemilik usaha ' . $surat['nama_perusahaan'] . ' yang bertempat di ' . $surat['alamat_domisili'] . ', Kel. ' . ucfirst(strtolower($penduduk['kelurahan_nm'])) . ' Kec. ' . ucfirst(strtolower($penduduk['kecamatan_nm'])) . ' ' .  ucwords(strtolower($penduduk['kabko_nm'])) . ' yang berstatus bangunan ' . $surat['status_bangunan'] . ' dengan karyawan berjumlah ' . $surat['jumlah_karyawan'] . ' orang.',
    //         'surat_kepada' => $surat->kepada,
    //         'surat_peruntukan' => $surat->peruntukan,
    //         'surat_tgl' => $tglSurat,
    //         'link' => $verify
    //     ];
    //     // dd($data);

    //     // Path template .docx
    //     $templateFile = public_path('templates/SKDOM.docx');
    //     $outputPdf = hash('sha256', 'SKDOM_' . $output['_id']) . '_signed';
    //     // Generate PDF dari template
    //     $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);
    // } else if ($output['jenis'] == 'skkelahiran') {
    //     $nik = $surat->nik_pelapor;
    //     $tgl_lhr_ayah = explode('-', $surat->tgl_lhr_ayah);
    //     $y_lhr_ayah = $tgl_lhr_ayah[0];
    //     $m_lhr_ayah = $tgl_lhr_ayah[1];
    //     $d_lhr_ayah = $tgl_lhr_ayah[2];

    //     $tgl_lhr_ibu = explode('-', $surat->tgl_lhr_ibu);

    //     $y_lhr_ibu = $tgl_lhr_ibu[0];
    //     $m_lhr_ibu = $tgl_lhr_ibu[1];
    //     $d_lhr_ibu = $tgl_lhr_ibu[2];


    //     $tgl_lhr_anak = explode('-', $surat->tgl_lhr_anak);

    //     $y_lhr_anak = $tgl_lhr_anak[0];
    //     $m_lhr_anak = $tgl_lhr_anak[1];
    //     $d_lhr_anak = $tgl_lhr_anak[2];


    //     $jam_lhr_anak = explode(':', $surat->jam_lhr_anak);

    //     $hh_lhr_anak = $jam_lhr_anak[0];
    //     $mm_lhr_anak = $jam_lhr_anak[1];


    //     $num = $surat->klhr_ke_anak;
    //     $num_padded = sprintf("%02d", $num);

    //     $pdf = Pdf::loadView('skkelahiran.pdf', compact(
    //         'surat',
    //         'nomorSurat',
    //         'pejabat',
    //         'tglSurat',
    //         'url',
    //         'y_lhr_ayah',
    //         'm_lhr_ayah',
    //         'd_lhr_ayah',
    //         'y_lhr_ibu',
    //         'm_lhr_ibu',
    //         'd_lhr_ibu',
    //         'y_lhr_anak',
    //         'm_lhr_anak',
    //         'd_lhr_anak',
    //         'hh_lhr_anak',
    //         'mm_lhr_anak',
    //         'num_padded'
    //     ))->setPaper('legal', 'portrait');
    //     $path = '/public/pdf/';
    //     $content = $pdf->download()->getOriginalContent();
    //     $outputPdf = hash('sha256', 'SKKELAHIRAN_' . $output['_id']) . '_signed';
    //     Storage::put($path . '/' . $outputPdf . '.pdf', $content);
    //     $pdfPath = storage_path('app/public/pdf') . '/' . $outputPdf . '.pdf';
    // } else if ($output['jenis'] == 'skkematian') {
    //     $nik = $surat->nik_pelapor;

    //     $tgl_lhr_ayah = explode('-', $surat->tgl_lhr_ayah);

    //     $y_lhr_ayah = $tgl_lhr_ayah[0];
    //     $m_lhr_ayah = $tgl_lhr_ayah[1];
    //     $d_lhr_ayah = $tgl_lhr_ayah[2];

    //     $tgl_lhr_ibu = explode('-', $surat->tgl_lhr_ibu);

    //     $y_lhr_ibu = $tgl_lhr_ibu[0];
    //     $m_lhr_ibu = $tgl_lhr_ibu[1];
    //     $d_lhr_ibu = $tgl_lhr_ibu[2];

    //     $tgl_kematian = explode('-', $surat->tgl_kematian);
    //     $y_kematian = $tgl_kematian[0];
    //     $m_kematian = $tgl_kematian[1];
    //     $d_kematian = $tgl_kematian[2];


    //     $jam_kematian = explode(':', $surat->jam_kematian);

    //     $hh_kematian = $jam_kematian[0];
    //     $mm_kematian = $jam_kematian[1];

    //     $pdf = Pdf::loadView('skkematian.pdf', compact(
    //         'surat',
    //         'nomorSurat',
    //         'pejabat',
    //         'tglSurat',
    //         'url',
    //         'y_lhr_ayah',
    //         'm_lhr_ayah',
    //         'd_lhr_ayah',
    //         'y_lhr_ibu',
    //         'm_lhr_ibu',
    //         'd_lhr_ibu',
    //         'y_kematian',
    //         'm_kematian',
    //         'd_kematian',
    //         'hh_kematian',
    //         'mm_kematian',
    //     ))->setPaper('legal', 'portrait');
    //     $path = '/public/pdf/';
    //     $content = $pdf->download()->getOriginalContent();
    //     $outputPdf = hash('sha256', 'SKKEMATIAN_' . $output['_id']) . '_signed';
    //     Storage::put($path . '/' . $outputPdf . '.pdf', $content);
    //     $pdfPath = storage_path('app/public/pdf') . '/' . $outputPdf . '.pdf';
    // } else if ($output['jenis'] == 'skhsl') {
    //     $data = [
    //         'skpd_kec' => strtoupper($pejabat->skpd->kecamatan->nama),
    //         'skpd_kel' => strtoupper($pejabat->skpd->nama),
    //         'skpd_alamat' => $pejabat->skpd->instansi_alamat,
    //         'skpd_telp' => $pejabat->skpd->instansi_telp,
    //         'skpd_pos' => $pejabat->skpd->instansi_kode_pos,
    //         'skpd_kepala' => $pejabat->nama,
    //         'skpd_nip_kepala' => $pejabat->nip,
    //         'skpd_jabatan' => ucfirst($pejabat->jabatan->nama) . ' ' . ucfirst(strtolower($pejabat->skpd->nama)),
    //         'surat_no' => $nomorSurat,
    //         'surat_nama' => $penduduk['name'],
    //         'surat_nik' => $surat->nik,
    //         'surat_tmpl' => $penduduk['tempat_lhr'],
    //         'surat_tgll' => strtoupper($penduduk['tgl_lhr']),
    //         'surat_gender' => $penduduk['gender_nm'],
    //         'surat_perkawinan' => $penduduk['status_kwn_nm'],
    //         'surat_agama' => $penduduk['agama_nm'],
    //         'surat_pekerjaan' => $penduduk['pekerjaan_nm'],
    //         'surat_pendidikan' => $penduduk['pendidikan_nm'],
    //         'surat_alamat' => $penduduk['alamat'] . ' KEL. ' . $penduduk['kelurahan_nm'] . ' KEC. ' . $penduduk['kecamatan_nm'] . ' ' .  $penduduk['kabko_nm'],
    //         'surat_keterangan' => 'Adalah benar-benar dengan penghasilan perbulan sebesar Rp. ' . number_format($surat['penghasilan'], 2, ',', '.') . ' (' . $surat['terbilang'] . ').',
    //         'surat_kepada' => $surat->kepada,
    //         'surat_kepada_tempat_lhr' => $surat->kepada_tempat_lhr,
    //         'surat_kepada_tgl_lhr' => $surat->kepada_tgl_lhr,
    //         'surat_kepada_sekolah' => $surat->kepada_sekolah,
    //         'surat_kepada_kelas' => $surat->kepada_kelas,
    //         'surat_kepada_gender_nm' => ucfirst(strtolower($surat->kepada_gender_nm)),
    //         'surat_kepada_hubungan' => $surat->kepada_hubungan,
    //         'surat_peruntukan' => $surat->peruntukan,
    //         'surat_tgl' => $tglSurat,
    //         'link' => $verify
    //     ];
    //     // dd($data);
    //     $template = SuratTemplate::where(['id_kel' => $surat->id_kel, 'jenis' => 'skhsl'])->first();
    //     if (isset($template) && ($surat->variable != "")) {
    //         $var = $surat->variable;
    //         $templateFile = public_path($template->path_docs);
    //         $data = array_merge($data, $var);
    //     } else {
    //         $templateFile = public_path('templates/SKHSL.docx');
    //     }
    //     $outputPdf = hash('sha256', 'SKHSL_' . $output['_id']) . '_signed';
    //     // Generate PDF dari template
    //     $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);
    // } else if ($output['jenis'] == 'sktm') {
    //     if ($output['role'] == 5) {
    //         // $pdfPath = asset($surat->file);
    //         $flname = explode('/', $surat->file);
    //         $outputPdf = end($flname);
    //         $pdfPath = storage_path('app/public/pdf/') . $outputPdf;
    //         // dd($pdfPath);
    //     } else {
    //         if ($surat->jenis == 'sekolah') {
    //             $data = [
    //                 'skpd_kec' => strtoupper($pejabat->skpd->kecamatan->nama),
    //                 'skpd_kel' => strtoupper($pejabat->skpd->nama),
    //                 'skpd_alamat' => $pejabat->skpd->instansi_alamat,
    //                 'skpd_telp' => $pejabat->skpd->instansi_telp,
    //                 'skpd_pos' => $pejabat->skpd->instansi_kode_pos,
    //                 'skpd_kepala' => $pejabat->nama,
    //                 'skpd_nip_kepala' => $pejabat->nip,
    //                 'skpd_jabatan' => ucfirst($pejabat->jabatan->nama) . ' ' . ucfirst(strtolower($pejabat->skpd->nama)),
    //                 'surat_no' => $nomorSurat,
    //                 'surat_nama' => $penduduk['name'],
    //                 'surat_nik' => $surat->nik,
    //                 'surat_tmpl' => $penduduk['tempat_lhr'],
    //                 'surat_tgll' => strtoupper($penduduk['tgl_lhr']),
    //                 'surat_gender' => $penduduk['gender_nm'],
    //                 'surat_perkawinan' => $penduduk['status_kwn_nm'],
    //                 'surat_agama' => $penduduk['agama_nm'],
    //                 'surat_pekerjaan' => $penduduk['pekerjaan_nm'],
    //                 'surat_pendidikan' => $penduduk['pendidikan_nm'],
    //                 'surat_alamat' => $penduduk['alamat'] . ' KEL. ' . $penduduk['kelurahan_nm'] . ' KEC. ' . $penduduk['kecamatan_nm'] . ' ' .  $penduduk['kabko_nm'],
    //                 'surat_keterangan' => 'Benar-benar dalam keadaan miskin.',
    //                 'surat_kepada' => $surat->kepada,
    //                 'surat_kepada_tempat_lhr' => $surat->kepada_tempat_lhr,
    //                 'surat_kepada_tgl_lhr' => $surat->kepada_tgl_lhr,
    //                 'surat_kepada_sekolah' => $surat->kepada_sekolah,
    //                 'surat_kepada_kelas' => $surat->kepada_kelas,
    //                 'surat_kepada_gender_nm' => ucfirst(strtolower($surat->kepada_gender_nm)),
    //                 'surat_kepada_hubungan' => $surat->kepada_hubungan,
    //                 'surat_peruntukan' => $surat->peruntukan,
    //                 'surat_tgl' => $tglSurat,
    //                 'surat_kategori' => $surat->kategori,
    //                 'link' => $verify
    //             ];
    //             // Path template .docx
    //             $templateFile = public_path('templates/SKTM_SEKOLAH.docx');
    //         } else {
    //             $data = [
    //                 'skpd_kec' => strtoupper($pejabat->skpd->kecamatan->nama),
    //                 'skpd_kel' => strtoupper($pejabat->skpd->nama),
    //                 'skpd_alamat' => $pejabat->skpd->instansi_alamat,
    //                 'skpd_telp' => $pejabat->skpd->instansi_telp,
    //                 'skpd_pos' => $pejabat->skpd->instansi_kode_pos,
    //                 'skpd_kepala' => $pejabat->nama,
    //                 'skpd_nip_kepala' => $pejabat->nip,
    //                 'skpd_jabatan' => ucfirst($pejabat->jabatan->nama) . ' ' . ucfirst(strtolower($pejabat->skpd->nama)),
    //                 'surat_no' => $nomorSurat,
    //                 'surat_nama' => $penduduk['name'],
    //                 'surat_nik' => $surat->nik,
    //                 'surat_tmpl' => $penduduk['tempat_lhr'],
    //                 'surat_tgll' => strtoupper($penduduk['tgl_lhr']),
    //                 'surat_gender' => $penduduk['gender_nm'],
    //                 'surat_perkawinan' => $penduduk['status_kwn_nm'],
    //                 'surat_agama' => $penduduk['agama_nm'],
    //                 'surat_pekerjaan' => $penduduk['pekerjaan_nm'],
    //                 'surat_pendidikan' => $penduduk['pendidikan_nm'],
    //                 'surat_alamat' => $penduduk['alamat'] . ' KEL. ' . $penduduk['kelurahan_nm'] . ' KEC. ' . $penduduk['kecamatan_nm'] . ' ' .  $penduduk['kabko_nm'],
    //                 'surat_keterangan' => 'Benar-benar dalam keadaan miskin.',
    //                 'surat_peruntukan' => $surat->peruntukan,
    //                 'surat_tgl' => $tglSurat,
    //                 'surat_kategori' => $surat->kategori,
    //                 'link' => $verify
    //             ];
    //             // Path template .docx
    //             $templateFile = public_path('templates/SKTM_PERORANGAN.docx');
    //         }
    //         $outputPdf = hash('sha256', 'SKTM_' . $output['_id']) . '_signed';
    //         // Generate PDF dari template
    //         $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);
    //     }
    // } else if ($output['jenis'] == 'skusaha') {
    //     $data = [
    //         'skpd_kec' => strtoupper($pejabat->skpd->kecamatan->nama),
    //         'skpd_kel' => strtoupper($pejabat->skpd->nama),
    //         'skpd_alamat' => $pejabat->skpd->instansi_alamat,
    //         'skpd_telp' => $pejabat->skpd->instansi_telp,
    //         'skpd_pos' => $pejabat->skpd->instansi_kode_pos,
    //         'skpd_kepala' => $pejabat->nama,
    //         'skpd_nip_kepala' => $pejabat->nip,
    //         'skpd_jabatan' => ucfirst($pejabat->jabatan->nama) . ' ' . ucfirst(strtolower($pejabat->skpd->nama)),
    //         'surat_no' => $nomorSurat,
    //         'surat_nama' => $penduduk['name'],
    //         'surat_nik' => $surat->nik,
    //         'surat_tmpl' => $penduduk['tempat_lhr'],
    //         'surat_tgll' => strtoupper($penduduk['tgl_lhr']),
    //         'surat_gender' => $penduduk['gender_nm'],
    //         'surat_perkawinan' => $penduduk['status_kwn_nm'],
    //         'surat_agama' => $penduduk['agama_nm'],
    //         'surat_pekerjaan' => $penduduk['pekerjaan_nm'],
    //         'surat_pendidikan' => $penduduk['pendidikan_nm'],
    //         'surat_alamat' => $penduduk['alamat'] . ' KEL. ' . $penduduk['kelurahan_nm'] . ' KEC. ' . $penduduk['kecamatan_nm'] . ' ' .  $penduduk['kabko_nm'],
    //         'surat_keterangan' => 'Menurut pernyataannya memiliki kegiatan / usaha ' . $surat['nama_usaha'] . ' yang beralamat di ' . $surat['alamat_usaha'],
    //         'surat_kepada' => $surat->kepada,
    //         'surat_kepada_tempat_lhr' => $surat->kepada_tempat_lhr,
    //         'surat_kepada_tgl_lhr' => $surat->kepada_tgl_lhr,
    //         'surat_kepada_sekolah' => $surat->kepada_sekolah,
    //         'surat_kepada_kelas' => $surat->kepada_kelas,
    //         'surat_kepada_gender_nm' => ucfirst(strtolower($surat->kepada_gender_nm)),
    //         'surat_kepada_hubungan' => $surat->kepada_hubungan,
    //         'surat_peruntukan' => $surat->peruntukan,
    //         'surat_tgl' => $tglSurat,
    //         'link' => $verify
    //     ];
    //     // dd($data);

    //     // Path template .docx
    //     $templateFile = public_path('templates/SKUSAHA.docx');
    //     $outputPdf = hash('sha256', 'SKUSAHA_' . $output['_id']) . '_signed';
    //     // Generate PDF dari template
    //     $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);
    // } else {
    //     $nik = $surat->nik;
    //     $resident = Resident::where('nik', $surat->nik)->first();
    //     $penduduk = $resident->data;
    //     $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');
    //     $pdf = Pdf::loadView($output['jenis'] . '.pdf', compact(
    //         'surat',
    //         'penduduk',
    //         'nomorSurat',
    //         'pejabat',
    //         'tglSurat',
    //         'url'
    //     ))->setPaper(array(0, 0, 609.4488, 935.433), 'portrait');
    // }

    //     if ($output['role'] == 5) {
    //         $kec = new Skpd_resource(Skpd::where('id_region', $pejabat->skpd->kecamatan->id)->first());
    //         $camat = new Pejabat_resource(Pejabat::where('id_skpd', $kec->id)->first());
    //         // dd($surat);
    //         $imgTte = $this->generateTte($camat, true, $surat->no_register);
    //     } else {
    //         $imgTte = $this->generateTte($pejabat);
    //     }
    //     $request = [
    //         'path' => $pdfPath,
    //         'file_name' => $output['role'] == 5 ? $outputPdf : $outputPdf . '.pdf',
    //         'nik' => $output['nik'],
    //         'passphrase' => $output['passphrase'],
    //         'qr_loc' => $output['role'] == 5 ? 'qr_camat' : 'qr_here',
    //         'verify' => $verify,
    //         'is_visible' => true,
    //         'type' => 'image',
    //         'image_path' => $imgTte['filename']
    //     ];
    //     if ($output['jenis'] == 'skkelahiran') {
    //         $request2 = [
    //             'jenis' => 'skkelahiran',
    //         ];
    //         $request = array_merge($request, $request2);
    //     } else if ($output['jenis'] == 'skkematian') {
    //         $request2 = [
    //             'jenis' => 'skkematian',
    //         ];
    //         $request = array_merge($request, $request2);
    //     }
    //     // dd($request);
    //     $res = $this->TTE_sign($request);
    //     // dd($res);
    //     unlink(public_path($imgTte['path']));
    //     // dd($outputPdf);
    //     $surat->update(['status' => ($output['role'] == 5 ? 6 : 3), 'file' => 'storage/pdf/' . ($output['role'] == 5 ? $outputPdf : $outputPdf . '.pdf')]);

    //     Log_surat::create([
    //         'nik' => $nik,
    //         'tabel_surat' => $tabel_surat,
    //         'nama_surat' => $nama_surat,
    //         'id_surat' => $surat->id,









    //         'status_surat' => 3,
    //     ]);

    //     ///Notif WA ke Pengaju

    //     return response()->json(['message' => 'Esign done successfully.', 'status' => 'success'], 200);
    // }


    protected function decodeFlexibleValueUniversal($value): array
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
            if ($unserialized !== false && is_array($unserialized)) {
                return $unserialized;
            }
        }

        return [];
    }

    protected function resolveTemplateFileUniversal(string $jenis, int $idKel, array $variableData = []): string
    {
        $custom = SuratTemplate::where('id_kel', $idKel)->where('jenis', $jenis)->first();
        if ($custom && !empty($custom->path_docs)) {
            $customPath = public_path($custom->path_docs);
            if (file_exists($customPath)) {
                return $customPath;
            }
        }

        $fallbackMap = [
            'skbn'    => 'templates/SKBN.docx',
            'sktm'    => (strtolower((string) ($variableData['register_as'] ?? $variableData['kategori'] ?? 'perorangan')) === 'sekolah' ? 'templates/SKTM_SEKOLAH.docx' : 'templates/SKTM_PERORANGAN.docx'),
            'skdom'   => 'templates/SKDOM.docx',
            'skusaha' => 'templates/SKUSAHA.docx',
            'skhsl'   => 'templates/SKHSL.docx',
            'skboro'  => 'templates/SKBORO.docx',
            'suket'   => 'templates/SUKET.docx',
        ];

        $relative = $fallbackMap[$jenis] ?? null;
        abort_unless($relative && file_exists(public_path($relative)), 404, 'Template surat tidak ditemukan.');
        return public_path($relative);
    }


    protected function pejabatRoleColumn(): string
    {
        static $column = null;

        if ($column !== null) {
            return $column;
        }

        try {
            // Struktur database yang benar: pejabats.id_jabatan
            // Lurah wajib id_jabatan = 1, Camat wajib id_jabatan = 2.
            if (Schema::hasColumn('pejabats', 'id_jabatan')) {
                return $column = 'id_jabatan';
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal membaca struktur kolom pejabats.', [
                'message' => $e->getMessage(),
            ]);
        }

        return $column = 'id_jabatan';
    }

    protected function findPejabatBySkpdAndRole($idSkpd, int $idJabatan): ?Pejabat
    {
        $idSkpd = (int) $idSkpd;
        if ($idSkpd <= 0) {
            return null;
        }

        // Struktur yang dipakai: pejabats.id_skpd menentukan wilayah,
        // pejabats.id_jabatan menentukan jenis pejabat.
        // Lurah = id_jabatan 1, Camat = id_jabatan 2.
        // Tidak memakai first() tanpa id_jabatan agar Kasi/Sekkel/Admin tidak ikut terbaca.
        return Pejabat::with(['skpd.kecamatan', 'jabatan', 'pangkat'])
            ->where('id_skpd', $idSkpd)
            ->where($this->pejabatRoleColumn(), $idJabatan)
            ->latest('id')
            ->first();
    }

    protected function pejabatJabatanName($pejabat, string $fallback): string
    {
        $name = trim((string) optional(optional($pejabat)->jabatan)->nama);
        return $name !== '' ? $name : $fallback;
    }

    protected function resolveCamatByDistrictName(?string $districtName): ?Pejabat
    {
        $name = strtoupper(trim((string) $districtName));

        $mapping = [
            'MOJOROTO'  => 64,
            'KOTA'      => 65,
            'PESANTREN' => 66,
        ];

        $idSkpdCamat = $mapping[$name] ?? null;
        if (!$idSkpdCamat) {
            return null;
        }

        // Camat wajib dari pejabats berdasarkan wilayah kecamatan + id_jabatan = 2.
        // Tidak boleh fallback ke data pertama karena bisa tertukar Sekcam/Kasi/Admin.
        return $this->findPejabatBySkpdAndRole($idSkpdCamat, 2);
    }

    /**
     * Mengambil nama kecamatan untuk header template Word/PDF.
     * Fallback dipakai jika relasi skpds->kecamatan tidak terbaca.
     */
    protected function resolveSkpdKecamatanName($skpd, array $residentData = []): string
    {
        $name = optional(optional($skpd)->kecamatan)->nama;

        if (!$name && !empty($skpd->id_kec)) {
            $kecamatanSkpd = Skpd::where('id_region', trim((string) $skpd->id_kec))->first();
            $name = optional($kecamatanSkpd)->nama;
        }

        if (!$name && !empty($residentData['kecamatan_nm'])) {
            $name = $residentData['kecamatan_nm'];
        }

        return strtoupper(trim((string) $name));
    }

    protected function isSuperAdminUser($user = null): bool
    {
        $user = $user ?: Auth::user();
        return $user && (int) $user->role_id === 7;
    }

    protected function currentUserKelurahanIds($user = null): ?array
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return [];
        }

        if ($this->isSuperAdminUser($user)) {
            return null;
        }

        $roleId = (int) $user->role_id;
        $idInstansi = (int) ($user->id_instansi ?? 0);

        if ($idInstansi <= 0) {
            return [];
        }

        if (in_array($roleId, [5, 6], true)) {
            $skpdKecamatan = Skpd::find($idInstansi);
            $idKec = trim((string) optional($skpdKecamatan)->id_kec);

            if ($idKec === '') {
                return [];
            }

            return Skpd::query()
                ->where('id_kec', $idKec)
                ->get(['id', 'id_region'])
                ->filter(fn ($skpd) => substr_count((string) $skpd->id_region, '.') >= 3)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        return [$idInstansi];
    }

    protected function scopeSuratPengajuanToCurrentUser($query, $user = null)
    {
        $user = $user ?: Auth::user();
        $kelurahanIds = $this->currentUserKelurahanIds($user);

        if ($kelurahanIds === null) {
            return $query;
        }

        if (empty($kelurahanIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('id_kel', $kelurahanIds);
    }

    protected function resolveCamatForKelurahanId($idKel): ?Pejabat
    {
        $kelurahanSkpd = Skpd::with('kecamatan')->find((int) $idKel);
        if (!$kelurahanSkpd) {
            return null;
        }

        $idKec = trim((string) $kelurahanSkpd->id_kec);
        $kecamatanSkpd = $idKec !== '' ? Skpd::where('id_region', $idKec)->first() : null;

        if (!$kecamatanSkpd) {
            return $this->resolveCamatByDistrictName(optional($kelurahanSkpd->kecamatan)->nama);
        }

        // Camat wajib cocok: pejabats.id_skpd = SKPD kecamatan dan id_jabatan = 2.
        return $this->findPejabatBySkpdAndRole($kecamatanSkpd->id, 2);
    }

    protected function resolveLurahForKelurahanId($idKel): ?Pejabat
    {
        // Lurah wajib cocok: pejabats.id_skpd = SKPD kelurahan dan id_jabatan = 1.
        // Tidak boleh fallback ke data pertama karena tabel pejabats juga berisi Sekkel/Kasi/dll.
        return $this->findPejabatBySkpdAndRole($idKel, 1);
    }

    protected function resolveSignerContext(?SuratPengajuan $surat = null): array
    {
        $authUser = Auth::user();
        $user = null;

        if ($authUser instanceof User) {
            $user = $authUser->loadMissing('skpd.kecamatan');
        } elseif ($authUser && isset($authUser->id)) {
            $user = User::with('skpd.kecamatan')->find($authUser->id);
        } elseif (auth()->id()) {
            $user = User::with('skpd.kecamatan')->find(auth()->id());
        }

        $instansiId = null;
        if ($surat && !empty($surat->id_kel)) {
            $instansiId = (int) $surat->id_kel;
        } elseif ($user && !empty($user->id_instansi)) {
            $instansiId = (int) $user->id_instansi;
        }

        $skpd = $instansiId ? Skpd::with('kecamatan')->find($instansiId) : null;
        $pejabat = $instansiId
            ? $this->resolveLurahForKelurahanId($instansiId)
            : null;

        return [
            'user' => $user,
            'instansi_id' => $instansiId,
            'skpd' => $skpd,
            'pejabat' => $pejabat,
        ];
    }

    protected function requireUniversalSignerContext(SuratPengajuan $surat): array
    {
        $context = $this->resolveSignerContext($surat);

        if (empty($context['instansi_id'])) {
            throw new \RuntimeException('ID instansi user login tidak ditemukan.');
        }

        if (!$context['skpd']) {
            throw new \RuntimeException('Data SKPD untuk instansi user tidak ditemukan.');
        }


        if (!$context['pejabat']) {
            throw new \RuntimeException('Data Lurah penandatangan untuk kelurahan surat belum disetting.');
        }

        return $context;
    }


    protected function buildTteImagePlaceholder($pejabat, bool $isCamat = false, ?string $noRegister = null): ?array
    {
        if (!$pejabat) {
            return null;
        }

        try {
            $imgTte = $this->generateTte($pejabat, $isCamat, $noRegister);
            $relativePath = $imgTte['path'] ?? null;
            $fullPath = $imgTte['full_path'] ?? ($relativePath ? public_path($relativePath) : null);

            if ($fullPath && file_exists($fullPath) && filesize($fullPath) > 0 && @getimagesize($fullPath) !== false) {
                return [
                    'path' => $fullPath,
                    'width' => 225,
                    'height' => 75,
                    'ratio' => true,
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal membuat gambar kartu TTE.', [
                'is_camat' => $isCamat,
                'pejabat_id' => $pejabat->id ?? null,
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    protected function attachTteImagePlaceholders(array &$data, $lurahPejabat, $camatPejabat = null, bool $includeCamat = false, ?string $noRegister = null, bool $includeLurah = true): void
    {
        $cleanupImages = [];

        if ($includeLurah) {
            $lurahImage = $this->buildTteImagePlaceholder($lurahPejabat, false, null);
            if ($lurahImage) {
                $data['tte_lurah'] = $lurahImage;
                $cleanupImages[] = $lurahImage['path'];
            } else {
                $data['tte_lurah'] = '';
            }
        } else {
            $data['tte_lurah'] = '';
        }

        if ($includeCamat) {
            $camatImage = $this->buildTteImagePlaceholder($camatPejabat, true, $noRegister);
            if ($camatImage) {
                $data['tte_camat'] = $camatImage;
                $cleanupImages[] = $camatImage['path'];
            } else {
                $data['tte_camat'] = '';
            }
        } else {
            $data['tte_camat'] = '';
        }

        $data['__cleanup_images'] = array_values(array_unique($cleanupImages));
    }

    protected function buildPdfDataUniversal(SuratPengajuan $surat, ?int $tteRole = null): array
    {
        $resident = Resident::where('nik', $surat->nik)->first();
        $residentData = $this->decodeFlexibleValueUniversal(optional($resident)->data);
        $variableData = $this->decodeFlexibleValueUniversal($surat->variable);

        if (!empty($residentData['tgl_lhr'])) {
            try {
                $residentData['tgl_lhr'] = Carbon::parse($residentData['tgl_lhr'])->isoFormat('D MMMM Y');
            } catch (\Throwable $e) {
            }
        }

        $context = $this->requireUniversalSignerContext($surat);
        $skpd = $context['skpd'];
        $pejabat = $context['pejabat'];
        $camat = $this->resolveCamatForKelurahanId((int) $surat->id_kel);

        $tglSurat = Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
        $nomorSurat = $this->getNoSrt($surat);
        $verifyUrl = config('app.url') . '/verify/' . $surat->jenis_surat . '/' . $surat->id;

        $alamatLengkap = trim(
            ($residentData['alamat'] ?? '') .
            (!empty($residentData['kelurahan_nm']) ? ' KEL. ' . $residentData['kelurahan_nm'] : '') .
            (!empty($residentData['kecamatan_nm']) ? ' KEC. ' . $residentData['kecamatan_nm'] : '') .
            (!empty($residentData['kabko_nm']) ? ' ' . $residentData['kabko_nm'] : '')
        );

        $data = [
            'skpd_kec'         => $this->resolveSkpdKecamatanName($skpd, $residentData),
            'skpd_kel'         => strtoupper($skpd->nama ?? ''),
            'skpd_alamat'      => $skpd->instansi_alamat ?? '',
            'skpd_telp'        => $skpd->instansi_telp ?? '',
            'skpd_pos'         => $skpd->instansi_kode_pos ?? '',
            'skpd_kepala'         => $pejabat->nama ?? '',
            'skpd_nip_kepala'     => $pejabat->nip ?? '',
            'skpd_jabatan'        => trim(ucfirst(strtolower($this->pejabatJabatanName($pejabat, 'LURAH'))) . ' ' . ucfirst(strtolower($skpd->nama ?? ''))),
            'skpd_camat'          => $camat->nama ?? '',
            'skpd_nip_camat'      => $camat->nip ?? '',
            'skpd_jabatan_camat'  => strtoupper($this->pejabatJabatanName($camat, 'CAMAT')),
            'surat_no'            => $nomorSurat,
            'surat_tgl'        => $tglSurat,
            'surat_nama'       => $residentData['name'] ?? '',
            'surat_nik'        => $surat->nik ?? '',
            'surat_tmpl'       => $residentData['tempat_lhr'] ?? '',
            'surat_tgll'       => strtoupper($residentData['tgl_lhr'] ?? ''),
            'surat_gender'     => $residentData['gender_nm'] ?? '',
            'surat_perkawinan' => $residentData['status_kwn_nm'] ?? '',
            'surat_agama'      => $residentData['agama_nm'] ?? '',
            'surat_pekerjaan'  => $residentData['pekerjaan_nm'] ?? '',
            'surat_pendidikan' => $residentData['pendidikan_nm'] ?? '',
            'surat_alamat'     => $alamatLengkap,
            'surat_kepada'     => $surat->kepada ?? '',
            'surat_peruntukan' => $surat->peruntukan ?? '',
            'surat_keterangan' => $variableData['keterangan_tambahan'] ?? $variableData['keperluan'] ?? '',
            'surat_kategori'   => $variableData['surat_kategori'] ?? '',
            'surat_catatan'    => $variableData['surat_catatan'] ?? '',
            'link'             => $verifyUrl,
            // Jangan isi qr dengan gambar saat proses TTE. Template cukup menyisakan tag ~ untuk Lurah.
            'qr'               => '${qr}',
            // Tag Camat sengaja berupa teks biasa agar tetap bisa dicari API saat TTE Camat.
            'qr_camat'         => '~camat~',
        ];

        foreach ($residentData as $key => $value) {
            if (!is_array($value)) {
                $data[$key] = $value;
            }
        }

        foreach ($variableData as $key => $value) {
            if (!is_array($value)) {
                $data[$key] = $value;
            }
        }


        if (strtolower((string) $surat->jenis_surat) === 'sktm') {
            $takeSktmValue = function (string $key, string $fallbackKey = null) use ($variableData, $surat) {
                $fallbackKey = $fallbackKey ?: $key;
                $value = $variableData[$key] ?? $variableData[$fallbackKey] ?? null;

                if (($value === null || $value === '') && isset($surat->{$key})) {
                    $value = $surat->{$key};
                }

                if (($value === null || $value === '') && isset($surat->{$fallbackKey})) {
                    $value = $surat->{$fallbackKey};
                }

                return $value ?? '';
            };

            $formatSktmDate = function ($value) {
                $value = trim((string) ($value ?? ''));
                if ($value === '') {
                    return '';
                }

                try {
                    // Kalau masih format tanggal database/input, ubah menjadi format surat.
                    if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value) || preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                        return strtoupper(Carbon::parse($value)->isoFormat('D MMMM Y'));
                    }
                } catch (\Throwable $e) {
                    // Biarkan nilai asli kalau tidak bisa diparse.
                }

                return strtoupper($value);
            };

            // Data anak/penerima SKTM Sekolah wajib diisi ulang saat proses TTE.
            // Sebelumnya hanya terisi saat preview admin, sehingga saat naik ke Sekkel/Lurah/Sekcam/Camat
            // placeholder SKTM_SEKOLAH masih muncul mentah di PDF.
            $data['surat_kepada'] = $surat->kepada ?? $takeSktmValue('kepada');
            $data['surat_kepada_tempat_lhr'] = strtoupper((string) $takeSktmValue('kepada_tempat_lhr'));
            $data['surat_kepada_tgl_lhr'] = $formatSktmDate($takeSktmValue('kepada_tgl_lhr'));
            $data['surat_kepada_sekolah'] = strtoupper((string) $takeSktmValue('kepada_sekolah'));
            $data['surat_kepada_kelas'] = strtoupper((string) $takeSktmValue('kepada_kelas'));
            $data['surat_kepada_gender'] = $takeSktmValue('kepada_gender');
            $data['surat_kepada_gender_nm'] = strtoupper((string) ($takeSktmValue('kepada_gender_nm', 'kepada_gender') ?: $takeSktmValue('kepada_gender')));
            $data['surat_kepada_hubungan'] = strtoupper((string) $takeSktmValue('kepada_hubungan'));
            $data['surat_kepada_alamat_sekolah'] = strtoupper((string) $takeSktmValue('kepada_alamat_sekolah'));
        }

        switch ($surat->jenis_surat) {
            case 'skbn':
                $data['surat_keterangan'] = $data['surat_keterangan'] ?: 'BENAR BAHWA YANG BERSANGKUTAN BELUM MENIKAH.';
                break;
            case 'sktm':
                $data['surat_keterangan'] = $data['surat_keterangan'] ?: 'BENAR-BENAR DALAM KEADAAN MISKIN.';
                break;
            case 'skdom':
                $data['surat_keterangan'] = $data['surat_keterangan'] ?: ('BERDOMISILI DI ' . ($variableData['alamat_domisili'] ?? ''));
                break;
            case 'skusaha':
                $data['surat_keterangan'] = $data['surat_keterangan'] ?: ('MEMILIKI USAHA ' . ($variableData['nama_usaha'] ?? ''));
                break;
            case 'skhsl':
                $data['surat_keterangan'] = $data['surat_keterangan'] ?: ($variableData['keperluan'] ?? '');
                break;
            case 'skboro':
                $data['surat_keterangan'] = $data['surat_keterangan'] ?: ($variableData['alamat_asal'] ?? '');
                break;
            case 'suket':
                $data['surat_keterangan'] = $data['surat_keterangan'] ?: ($variableData['keterangan_tambahan'] ?? '');
                break;
        }

        // Fokus final: semua TTE Lurah/Camat memakai 1 gambar gabungan QR + kartu TTE,
        // sehingga placeholder gambar template dikosongkan agar hasil rapi dan tidak dobel.
        $this->attachTteImagePlaceholders(
            $data,
            $pejabat,
            $camat,
            false,
            $surat->no_register ?? null,
            false
        );

        return $data;
    }

    public function sign(Request $request)
    {
        parse_str($request->getContent(), $output);
        $id = $output['_id'] ?? null;
        $jenisSurat = $output['jenis'] ?? null;
        $role = (int) ($output['role'] ?? 0);

        $suratPengajuan = $this->scopeSuratPengajuanToCurrentUser(SuratPengajuan::query())->whereKey($id)->first();
        if ($suratPengajuan) {
            $authRole = (int) optional(Auth::user())->role_id;
            if ($authRole !== 7 && $role !== $authRole) {
                return response()->json(['message' => 'Role TTE tidak sesuai dengan user login.', 'status' => 'error'], 403);
            }

            if ($authRole === 7 && !in_array($role, [3, 5], true)) {
                return response()->json(['message' => 'Super Admin hanya boleh menjalankan TTE pada level Lurah atau Camat.', 'status' => 'error'], 403);
            }

            if (!(($role === 3 && (int) $suratPengajuan->status === 3) || ($role === 5 && (int) $suratPengajuan->status === 8))) {
                return response()->json(['message' => 'Status surat belum sesuai untuk TTE pada level ini.', 'status' => 'error'], 422);
            }

            if ($role === 3 && !$this->resolveLurahForKelurahanId((int) $suratPengajuan->id_kel)) {
                return response()->json([
                    'message' => 'Data Lurah penandatangan belum disetting di tabel pejabats untuk wilayah ini. Syarat: id_skpd = kelurahan surat dan id_jabatan = 1.',
                    'status' => 'error'
                ], 422);
            }

            if ($role === 5 && !$this->resolveCamatForKelurahanId((int) $suratPengajuan->id_kel)) {
                return response()->json([
                    'message' => 'Data Camat penandatangan belum disetting di tabel pejabats untuk kecamatan surat. Syarat: id_skpd = kecamatan dan id_jabatan = 2.',
                    'status' => 'error'
                ], 422);
            }

            $customTteImage = null;
            $temporaryFilesToDelete = [];

            try {
                $variableData = $this->decodeFlexibleValueUniversal($suratPengajuan->variable);
                $registrasiKecamatan = trim((string) ($suratPengajuan->no_register ?? ($variableData['no_register_kecamatan'] ?? ($variableData['register_kecamatan'] ?? ''))));
                $outputPdfName = strtoupper($suratPengajuan->jenis_surat) . "_{$suratPengajuan->id}_signed" . ($role === 5 ? '_camat' : '');

                // KHUSUS CAMAT: pakai PDF hasil TTE Lurah yang sudah tersimpan.
                // Jangan generate ulang dari template agar QR/TTE Lurah tidak hilang.
                if ($role === 5 && !empty($suratPengajuan->file) && file_exists(public_path($suratPengajuan->file))) {
                    $pdfPath = public_path($suratPengajuan->file);
                } else {
                    $data = $this->buildPdfDataUniversal($suratPengajuan, $role);
                    $templateFile = $this->resolveTemplateFileUniversal($suratPengajuan->jenis_surat, (int) $suratPengajuan->id_kel, $variableData);
                    $pdfPath = $suratPengajuan->jenis_surat === 'skboro'
                        ? $this->generatePdfTable($data, $templateFile, $outputPdfName)
                        : $this->generatePdf($data, $templateFile, $outputPdfName);
                }

                $context = $this->requireUniversalSignerContext($suratPengajuan);
                $lurah = $context['pejabat'];
                $camat = $this->resolveCamatForKelurahanId((int) $suratPengajuan->id_kel);
                $verifyUrlForImage = config('app.url') . '/verify/' . $suratPengajuan->jenis_surat . '/' . $suratPengajuan->id;

                // Fokus final: semua TTE Lurah/Camat memakai 1 gambar gabungan QR + kartu TTE.
                if ($role === 3 && $lurah) {
                    $customTteImage = $this->generateTteWithQr($lurah, $verifyUrlForImage, false, $suratPengajuan->no_register ?? null);
                } elseif ($role === 5 && $camat) {
                    $customTteImage = $this->generateTteWithQr($camat, $verifyUrlForImage, true, $registrasiKecamatan ?: null);
                }

                if ($customTteImage && !empty($customTteImage['cleanup_paths'])) {
                    $temporaryFilesToDelete = array_merge($temporaryFilesToDelete, $customTteImage['cleanup_paths']);
                }
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => 'error'
                ], 422);
            }

            $pejabat = $context['pejabat'];
            $verifyUrl = config('app.url') . '/verify/' . $suratPengajuan->jenis_surat . '/' . $suratPengajuan->id;
            $cleanupTemporaryFiles = function () use (&$temporaryFilesToDelete) {
                foreach (array_values(array_unique($temporaryFilesToDelete)) as $tempFile) {
                    if (is_string($tempFile) && $tempFile !== '' && file_exists($tempFile)) {
                        @unlink($tempFile);
                    }
                }
            };

            // Fokus final hanya size dan posisi: kirim 1 gambar gabungan QR + kartu TTE ke BSrE
            // agar tampilan Lurah dan Camat seragam, rapi, dan proporsional.
            $res = $this->TTE_sign([
                'path'       => $pdfPath,
                'file_name'  => $outputPdfName . '.pdf',
                'nik'        => $output['nik'] ?? '',
                'passphrase' => $output['passphrase'] ?? '',
                'qr_loc'     => $role === 5
                    ? ['~camat~', '~camat', '[[qr_camat]]']
                    : ['${qr}', '${qr}~'],
                'verify'     => $verifyUrl,
                'use_custom_image' => !empty($customTteImage['full_path']),
                'image_path' => $customTteImage['full_path'] ?? null,
                'image_width' => 260,
                'image_height' => 75,
                'qr_size' => 75,
                'offset_x' => ($role === 3)?(strtolower((string) $suratPengajuan->jenis_surat) === 'sktm' ? 172 : 160): 0,
                'offset_y' => 0,
                'role'      => $role,
            ]);

            if ($res['status'] !== 'success') {
                $cleanupTemporaryFiles();
                return response()->json($res, 500);
            }

            // Alur final:
            // - Selain SKTM: TTE Lurah langsung selesai (status 4) seperti proses lama.
            // - SKTM Perorangan/Sekolah: setelah TTE Lurah belum final; Lurah masih harus klik Naikkan ke Kecamatan/Camat.
            // - Setelah TTE Camat, baru final (status 9).
            $finalStatus = $role === 5 ? 9 : 4;

            $suratPengajuan->update([
                'status' => $finalStatus,
                'file'   => 'storage/pdf/' . $outputPdfName . '.pdf',
            ]);

            Log_surat::create([
                'nik'          => $suratPengajuan->nik,
                'tabel_surat'  => 'surat_pengajuans',
                'id_surat'     => $suratPengajuan->id,
                'status_surat' => $finalStatus,
                'nama_surat'   => strtoupper($suratPengajuan->jenis_surat),
            ]);

            $cleanupTemporaryFiles();
            return response()->json(['message' => 'Esign berhasil.', 'status' => 'success'], 200);
        }

        // fallback lama untuk modul surat per-jenis yang masih memakai tabel masing-masing
        [$surat, $tabelSurat, $namaSurat, $templateDefault] = match ($jenisSurat) {
            'suket'       => [SuratKeterangan::find($id), 'surat_keterangans', 'SURAT KETERANGAN', 'SUKET.docx'],
            'skbn'        => [SuratSkbn::find($id), 'surat_skbns', 'SURAT KETERANGAN BELUM MENIKAH', 'SKBN.docx'],
            'sktm'        => [SuratSktm::find($id), 'surat_sktms', 'SURAT KETERANGAN MISKIN', 'SKTM_PERORANGAN.docx'],
            'skdom'       => [SuratDomisili::find($id), 'surat_domisilis', 'SURAT KETERANGAN DOMISILI', 'SKDOM.docx'],
            'skhsl'       => [SuratPenghasilan::find($id), 'surat_penghasilans', 'SURAT KETERANGAN PENGHASILAN', 'SKHSL.docx'],
            'skusaha'     => [SuratUsaha::find($id), 'surat_usahas', 'SURAT KETERANGAN USAHA', 'SKUSAHA.docx'],
            'skkelahiran' => [SuratKelahiran::find($id), 'surat_kelahirans', 'SURAT KETERANGAN KELAHIRAN', null],
            'skkematian'  => [SuratKematian::find($id), 'surat_kematians', 'SURAT KETERANGAN KEMATIAN', null],
            'skboro'      => [SuratBoro::find($id), 'surat_boros', 'SURAT KETERANGAN BORO', 'SKBORO.docx'],
            default       => abort(404, 'Jenis surat tidak dikenali'),
        };

        if (!$surat) {
            return response()->json([
                'message' => 'Data surat tidak ditemukan untuk proses TTE.',
                'status' => 'error'
            ], 404);
        }

        $skpd = Skpd::with('kecamatan')->find($surat->id_kel);
        if (!$skpd) {
            return response()->json([
                'message' => 'Data SKPD surat tidak ditemukan.',
                'status' => 'error'
            ], 422);
        }

        $pejabat = $this->resolveLurahForKelurahanId((int) $surat->id_kel);
        if (!$pejabat) {
            return response()->json([
                'message' => 'Data Lurah penandatangan belum disetting di tabel pejabats. Syarat: id_skpd = kelurahan surat dan id_jabatan = 1.',
                'status' => 'error'
            ], 422);
        }

        $camat = $this->resolveCamatForKelurahanId((int) $surat->id_kel);

        $tahun   = Carbon::parse($surat->tgl_surat)->format('Y');
        $nomorSurat = "{$surat->kd_jenis_surat}/{$surat->no_urut_surat}/{$skpd->instansi_kode}/{$tahun}";
        $verifyUrl  = config('app.url') . "/verify/{$jenisSurat}/{$id}";
        $nik      = in_array($jenisSurat, ['skkelahiran', 'skkematian']) ? $surat->nik_pelapor : $surat->nik;
        $resident = Resident::where('nik', $nik)->first();
        $penduduk = is_array(optional($resident)->data) ? $resident->data : (array) optional($resident)->data;
        $tglLhr   = !empty($penduduk['tgl_lhr']) ? Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y') : '';

        $data = [
            'skpd_kec'        => $this->resolveSkpdKecamatanName($skpd, $penduduk),
            'skpd_kel'        => strtoupper($skpd->nama ?? ''),
            'skpd_alamat'     => $skpd->instansi_alamat ?? '',
            'skpd_telp'       => $skpd->instansi_telp ?? '',
            'skpd_pos'        => $skpd->instansi_kode_pos ?? '',
            'skpd_kepala'        => $pejabat->nama ?? '',
            'skpd_nip_kepala'    => $pejabat->nip ?? '',
            'skpd_jabatan'       => ucfirst(strtolower($this->pejabatJabatanName($pejabat, 'LURAH'))) . ' ' . ucfirst(strtolower($skpd->nama ?? '')), 
            'skpd_camat'         => $camat->nama ?? '',
            'skpd_nip_camat'     => $camat->nip ?? '',
            'skpd_jabatan_camat' => strtoupper($this->pejabatJabatanName($camat, 'CAMAT')), 
            'surat_no'           => $nomorSurat,
            'surat_nama'      => $penduduk['name'] ?? '',
            'surat_nik'       => $nik,
            'surat_tmpl'      => $penduduk['tempat_lhr'] ?? '',
            'surat_tgll'      => strtoupper($tglLhr),
            'surat_gender'    => $penduduk['gender_nm'] ?? '',
            'surat_perkawinan' => $penduduk['status_kwn_nm'] ?? '',
            'surat_agama'     => $penduduk['agama_nm'] ?? '',
            'surat_pekerjaan' => $penduduk['pekerjaan_nm'] ?? '',
            'surat_pendidikan' => $penduduk['pendidikan_nm'] ?? '',
            'surat_alamat'    => trim(($penduduk['alamat'] ?? '') . ' KEL. ' . ($penduduk['kelurahan_nm'] ?? '') . ' KEC. ' . ($penduduk['kecamatan_nm'] ?? '') . ' ' . ($penduduk['kabko_nm'] ?? '')),
            'surat_tgl'       => Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y'),
            'link'            => $verifyUrl
        ];

        $data = match ($jenisSurat) {
            'suket' => array_merge($data, ['surat_keterangan' => $surat->keterangan, 'surat_kepada' => $surat->kepada, 'surat_peruntukan' => $surat->peruntukan]),
            'skbn' => array_merge($data, ['surat_keterangan' => 'Menurut pernyataan yang bersangkutan belum pernah menikah / kawin.', 'surat_kepada' => $surat->kepada, 'surat_peruntukan' => $surat->peruntukan]),
            'skboro' => array_merge($data, ['surat_tgl_berlaku' => Carbon::parse($surat->tgl_awal)->isoFormat('D MMMM Y') . ' s/d ' . Carbon::parse($surat->tgl_akhir)->isoFormat('D MMMM Y'), 'surat_tujuan' => "Desa/Kel: {$surat->kel_boro_nm} Kec: {$surat->kec_boro_nm} Kab: {$surat->kabko_boro_nm} Prov: {$surat->prov_boro_nm}", 'surat_keperluan' => $surat->peruntukan, 'surat_jml_pengikut' => SuratBoroPengikut::where('boro_id', $id)->count(), 'detail_pengikut' => SuratBoroPengikut::where('boro_id', $id)->get()->toArray()]),
            'skdom' => array_merge($data, ['surat_keterangan' => $surat->jenis == 'perorangan' ? "Bahwa nama tersebut di atas benar-benar berdomisili di {$surat->alamat_domisili}..." : "Pendiri/pemilik usaha {$surat->nama_perusahaan} yang bertempat di {$surat->alamat_domisili}...", 'surat_kepada' => $surat->kepada, 'surat_peruntukan' => $surat->peruntukan]),
            'skhsl' => array_merge($data, ['surat_keterangan' => "Adalah benar-benar dengan penghasilan perbulan sebesar Rp. " . number_format($surat->penghasilan, 2, ',', '.') . " ({$surat->terbilang}).", 'surat_kepada' => $surat->kepada, 'surat_peruntukan' => $surat->peruntukan]),
            'skusaha' => array_merge($data, ['surat_keterangan' => "Menurut pernyataannya memiliki kegiatan / usaha {$surat->nama_usaha} yang beralamat di {$surat->alamat_usaha}", 'surat_kepada' => $surat->kepada, 'surat_peruntukan' => $surat->peruntukan]),
            'sktm' => array_merge($data, ['surat_keterangan' => 'Benar-benar dalam keadaan miskin.', 'surat_kepada' => $surat->kepada, 'surat_peruntukan' => $surat->peruntukan, 'surat_kategori' => $surat->kategori]),
            default => $data
        };


        $outputPdfName = strtoupper($jenisSurat) . "_{$id}_signed";
        if (in_array($jenisSurat, ['skkelahiran', 'skkematian'])) {
            $pdfPath = $this->generateSpecialPdf($surat, $jenisSurat, $nomorSurat, $pejabat, $verifyUrl, $id);
        } else {
            $template = SuratTemplate::where(['id_kel' => $surat->id_kel, 'jenis' => $jenisSurat])->first();
            if ($template && !empty($surat->variable)) {
                $templateFile = public_path($template->path_docs);
                $data = array_merge($data, $surat->variable);
            } else {
                $templateFile = $jenisSurat == 'sktm'
                    ? ($surat->jenis == 'sekolah' ? public_path('templates/SKTM_SEKOLAH.docx') : public_path('templates/SKTM_PERORANGAN.docx'))
                    : public_path("templates/{$templateDefault}");
            }

            $this->attachTteImagePlaceholders(
                $data,
                $pejabat,
                $camat,
                false,
                $surat->no_register ?? null,
                false
            );

            $pdfPath = ($jenisSurat === 'skboro') ? $this->generatePdfTable($data, $templateFile, $outputPdfName) : $this->generatePdf($data, $templateFile, $outputPdfName);
        }

        $isCamat = ((int) ($output['role'] ?? 0) === 5);
        $customTteImage = null;
        $temporaryFilesToDelete = [];
        if ($isCamat && $camat) {
            $customTteImage = $this->generateTteWithQr($camat, $verifyUrl, true, $surat->no_register ?? null);
        } elseif (!$isCamat && $pejabat) {
            $customTteImage = $this->generateTteWithQr($pejabat, $verifyUrl, false, $surat->no_register ?? null);
        }

        if ($customTteImage && !empty($customTteImage['cleanup_paths'])) {
            $temporaryFilesToDelete = array_merge($temporaryFilesToDelete, $customTteImage['cleanup_paths']);
        }
        $cleanupTemporaryFiles = function () use (&$temporaryFilesToDelete) {
            foreach (array_values(array_unique($temporaryFilesToDelete)) as $tempFile) {
                if (is_string($tempFile) && $tempFile !== '' && file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }
        };

        $res = $this->TTE_sign([
            'path'       => $pdfPath,
            'file_name'  => $outputPdfName . '.pdf',
            'nik'        => $output['nik'],
            'passphrase' => $output['passphrase'],
            'qr_loc'     => $isCamat ? ['~camat~', '~camat', '[[qr_camat]]'] : ['${qr}', '${qr}~'],
            'verify'     => $verifyUrl,
            'use_custom_image' => !empty($customTteImage['full_path']),
            'image_path' => $customTteImage['full_path'] ?? null,
            'image_width' => 260,
            'image_height' => 75,
            'qr_size' => 75,
            'offset_x' => (!$isCamat && $jenisSurat !== 'sktm') ? 250 : 250,
            'offset_y' => 0,
            'role'      => (int) ($output['role'] ?? 0),
        ]);

        if ($res['status'] == 'success') {
            $surat->update([
                'status' => $isCamat ? 6 : 4,
                'file'   => 'storage/pdf/' . $outputPdfName . '.pdf'
            ]);

            Log_surat::create([
                'nik' => $nik,
                'tabel_surat' => $tabelSurat,
                'id_surat' => $id,
                'status_surat' => $isCamat ? 6 : 4,
                'nama_surat' => $namaSurat
            ]);

            $cleanupTemporaryFiles();
            return response()->json(['message' => 'Esign berhasil.', 'status' => 'success'], 200);
        }

        $cleanupTemporaryFiles();
        return response()->json($res, 500);
    }

}
