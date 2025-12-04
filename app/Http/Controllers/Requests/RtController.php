<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Rt_resource;
use App\Models\RtRw;
use Illuminate\Http\Request;

class RtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->id)) {
            $rt = RtRw::where('id', $request->id)->get();
        } else {
            $rt = RtRw::where('kode_kelurahan', $request->kode_kelurahan)
                ->where('rw', $request->rw)
                ->whereNotNull('rt')
                ->whereNotIn('rt', ['0', '00', '000', ''])
                ->where('rt', 'like', '%' . $request->q . '%')
                ->select('kode_kelurahan', 'rw', 'rt')
                ->distinct()
                ->orderBy('rt')
                ->get();
        }

        $data = Rt_resource::collection($rt);
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
