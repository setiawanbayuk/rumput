<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Agama_resource;
use App\Models\Agama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AgamaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->id)) {
            $agama = Agama::where('id', $request->id)->paginate();
        } else {
            $agama = Agama::where('nama', 'like', '%' . $request->q . '%')->paginate();
        }
        $data = Agama_resource::collection($agama);
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
        $response = Http::get('https://api-splp.layanan.go.id/kodefikasi-umum/1.0/Agama');
        $hasil = $response->json();

        $agama = array();

        foreach ($hasil['Agama']['records'] as $key => $value) {
            $agama[] = [
                'id' => $value[1],
                'nama' => strtoupper($value[2])
            ];
        };
        Agama::truncate();
        Agama::insert($agama);
    }
}
