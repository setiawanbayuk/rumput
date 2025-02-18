<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Models\Pendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $response = Http::withoutVerifying()->withToken(env('API_TOKEN'))
        ->get(env('APP_URL', 'http://rumput.test') . '/api/resident?nik=' . $request->nik);
        $hasil = $response->json();
        // dd($hasil);
        if (isset($hasil['data'])) {
            $dt = unserialize($hasil['data']);
            $data = [
                'nik' => $hasil['nik'],
                'kk' => $dt['kk'],
                'name' => $dt['name'],
                'gender' => $dt['gender'],
                'gender_nm' => $dt['gender_nm'],
                'status_kwn' => $dt['status_kwn'],
                'status_kwn_nm' => $dt['status_kwn_nm'],
                'kewarganegaraan' => $dt['kewarganegaraan'],
                'kewarganegaraan_nm' => $dt['kewarganegaraan_nm'],
                'tempat_lhr' => $dt['tempat_lhr'],
                'tgl_lhr' =>  $dt['tgl_lhr'],
                'agama' => $dt['agama'],
                'agama_nm' => $dt['agama_nm'],
                'pendidikan' => $dt['pendidikan'],
                'pendidikan_nm' => $dt['pendidikan_nm'],
                'pekerjaan' => $dt['pekerjaan'],
                'pekerjaan_nm' => $dt['pekerjaan_nm'],
                'provinsi' => $dt['provinsi'],
                'provinsi_nm' => $dt['provinsi_nm'],
                'kabko' => $dt['kabko'],
                'kabko_nm' => $dt['kabko_nm'],
                'kecamatan' => $dt['kecamatan'],
                'kecamatan_nm' => $dt['kecamatan_nm'],
                'kelurahan' => $dt['kelurahan'],
                'kelurahan_nm' => $dt['kelurahan_nm'],
                'alamat' => $dt['alamat']
            ];
            return response()->json($data, 200);
        }
        return response()->json(['message' => 'Data Not Found!'], 404);
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
}
