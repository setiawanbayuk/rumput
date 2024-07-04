<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Kelurahan_resource;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KelurahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->id)) {
            $kelurahan = Kelurahan::where('id', $request->id)->paginate();
        } else {
            $kelurahan = Kelurahan::where('kode_kecamatan', $request->kode_kecamatan)->where('nama', 'like', '%' . $request->q . '%')->paginate();
        }
        $data = Kelurahan_resource::collection($kelurahan);
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
        $response = Http::get('https://api-splp.layanan.go.id/master_data_desakelurahan/2.0/');
        $hasil = $response->json();
        $provinsi = array();
        foreach ($hasil['data'] as $key => $value) {
            $provinsi[] = ['id' => $value['kode_desa_kelurahan'], 'kode_provinsi' => $value['kode_provinsi'], 'kode_kabkota' => $value['kode_kabkota'], 'kode_kecamatan' => $value['kode_kecamatan'], 'nama' => strtoupper($value['nama_desa_kelurahan'])];
        }
        $collection = collect($provinsi);
        $chunks = $collection->chunk(100);
        $chunks->toArray();
        Kelurahan::truncate();
        foreach ($chunks as $chunk) {
            Kelurahan::insert($chunk->toArray());
        }
    }
}
