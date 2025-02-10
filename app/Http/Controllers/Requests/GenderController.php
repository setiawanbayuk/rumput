<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Gender_resource;
use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->id)) {
            $gender = Gender::where('id', $request->id)->paginate();
        } else {
            $gender = Gender::where('nama', 'like', '%' . $request->q . '%')->paginate();
        }
        $data = Gender_resource::collection($gender);
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

    public function splp(){
        $response = Http::get('https://api-splp.layanan.go.id/kodefikasi-umum/1.0/JenisKelamin');
        $hasil = $response->json();

        $agama = array();

        foreach ($hasil['JenisKelamin']['records'] as $key => $value) {
            $agama[] = [
                'id' => $value[1],
                'nama' => strtoupper($value[2])
            ];
        };
        Gender::truncate();
        Gender::insert($agama);
    }
}
