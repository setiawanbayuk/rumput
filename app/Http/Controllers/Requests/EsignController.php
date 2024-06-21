<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pejabat_resource;
use App\Http\Resources\Skpd_resource;
use App\Http\Resources\User_resource;
use App\Models\Pejabat;
use App\Models\Resident;
use App\Models\Skbn;
use App\Models\Skpd;
use App\Models\SuratKeterangan;
use App\Models\User;
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
        $r = Http::withBasicAuth('esign', 'qwerty')->get('http://103.78.106.34/api/user/status/' . $nik);
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
        parse_str($request->getContent(), $output);
        if ($output['jenis'] == 'suket') {
            $surat = SuratKeterangan::find($output['_id']);
        } else if ($output['jenis'] == 'skbn') {
            $surat = Skbn::find($output['_id']);
        }
        $resident = Resident::where('nik', $surat->nik)->first();
        $penduduk = unserialize($resident->data);
        $penduduk['tgl_lhr'] = Carbon::parse($penduduk['tgl_lhr'])->isoFormat('D MMMM Y');

        $skpd = new Skpd_resource(Skpd::find($surat->id_kel));
        $pejabat = new Pejabat_resource(Pejabat::where('id_skpd', $surat->id_kel)->first());
        $tahunSrt = DateTime::createFromFormat('Y-m-d', $surat->tgl_surat);
        $tglSurat = Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
        $nomorSurat = $surat->kd_jenis_surat . '/' . $surat->no_urut_surat . '/' . $skpd->instansi_kode . '/' . $tahunSrt->format('Y');

        $verify = env('APP_URL', 'https://esuket.dev') . '/verify/' . $output['jenis'] . '/' . $output['_id'];
        $url = base64_encode(QrCode::format('png')->size(256)->generate($verify));

        $fileName = md5($nomorSurat . date("Y-m-d H:i:s")) . '.pdf';


        $pdf = Pdf::loadView($output['jenis'] . '.pdf', compact(
            'surat',
            'penduduk',
            'nomorSurat',
            'pejabat',
            'tglSurat',
            'url'
        ))->setPaper(array(0, 0, 609.4488, 935.433), 'portrait');
        // return $pdf->stream();
        Storage::disk('local')->makeDirectory('/public/pdf/' . date('Y') . '/' . $output['jenis']);
        $path = '/public/pdf/' . date('Y') . '/' . $output['jenis'];
        $content = $pdf->download()->getOriginalContent();
        Storage::put($path . '/' . $fileName, $content);
        $fileLocation = '/storage/pdf/' . date('Y') . '/' . $output['jenis'] . '/' . $fileName;

        $data = array(
            'nik' => $output['nik'],
            'passphrase' => $output['passphrase'],
            'tampilan' => 'invisible',
            'page' => 1,
            'reason' => 'Dokumen telah ditanda tangani secara digital',
            'location' => 'Kediri'
        );


        $query = http_build_query($data);
        $r = Http::withBasicAuth('esign', 'qwerty')
            ->asMultipart()
            ->attach('file', file_get_contents(asset($fileLocation)), $fileName)
            ->post('http://103.78.106.34/api/sign/pdf?' . $query);

        $fp = fopen(public_path($fileLocation), 'wb');
        fwrite($fp, $r);
        fclose($fp);

        $surat->update(['status' => 3, 'file' => $fileLocation]);

        return response()->json(['message' => 'Esign done successfully.', 'status' => 'success'], 200);
    }
}
