<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Kewarganegaraan_resource;
use App\Models\Kewarganegaraan;
use Illuminate\Http\Request;

class KewarganegaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->id)) {
            $kewarganegaraan = Kewarganegaraan::where('id', $request->id)->paginate();
        } else {
            $kewarganegaraan = Kewarganegaraan::where('nama', 'like', '%' . $request->q . '%')->paginate();
        }
        $data = Kewarganegaraan_resource::collection($kewarganegaraan);
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
}
