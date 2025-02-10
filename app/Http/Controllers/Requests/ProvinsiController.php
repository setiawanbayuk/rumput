<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Provinsi_resource;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProvinsiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->id)) {
            $provinsi = Provinsi::where('id', $request->id)->paginate();
        } else {
            $provinsi = Provinsi::where('nama', 'like', '%' . $request->q . '%')->paginate();
        }
        $data = Provinsi_resource::collection($provinsi);
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
        $response = Http::get('https://api-splp.layanan.go.id/master_data_wilayah_provinsi/2.0/');
        $hasil = $response->json();
        $provinsi = array();
        foreach ($hasil['data'] as $key => $value) {
            $provinsi[] = ['id' => $value['kode_provinsi'], 'nama' => $value['nama_provinsi']];
        }

        Provinsi::truncate();
        Provinsi::insert($provinsi);
    }
}
