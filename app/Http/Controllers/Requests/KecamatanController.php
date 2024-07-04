<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Kecamatan_resource;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KecamatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->id)) {
            $kecamatan = Kecamatan::where('id', $request->id)->paginate();
        } else {
            $kecamatan = Kecamatan::where('kode_kabkota', $request->kode_kabkota)->where('nama', 'like', '%' . $request->q . '%')->paginate();
        }
        $data = Kecamatan_resource::collection($kecamatan);
        return response()->json($data, 200);
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

    public function splp()
    {
        $response = Http::get('https://api-splp.layanan.go.id/master_data_kecamatan/2.0/');
        $hasil = $response->json();
        $provinsi = array();
        foreach ($hasil['data'] as $key => $value) {
            $provinsi[] = ['id' => $value['kode_kecamatan'], 'kode_provinsi' => $value['kode_provinsi'], 'kode_kabkota' => $value['kode_kabkota'], 'nama' => strtoupper($value['nama_kecamatan'])];
        }

        Kecamatan::truncate();
        Kecamatan::insert($provinsi);
    }
}
