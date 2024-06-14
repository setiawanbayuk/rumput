<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Kecamatan_resource;
use App\Http\Resources\Kelurahan_resource;
use App\Http\Resources\Regional_resource;
use App\Models\Regional;
use Illuminate\Http\Request;

class RegionalController extends Controller
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

    public function kelurahan(Request $request)
    {
        $regional = Regional::where('no_kel', '!=', '0000')->get();
        $data = Kelurahan_resource::collection($regional);
        return response()->json($data, 200);
    }

    public function kecamatan(Request $request)
    {
        $regional = Regional::where('no_kel', '0000')->where('no_kec', '!=', '00')->get();
        $data = Kecamatan_resource::collection($regional);
        return response()->json($data, 200);
    }
}
