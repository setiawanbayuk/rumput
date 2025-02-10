<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Status_perkawinan_resource;
use App\Models\Status_kwn;
use App\Models\StatusKwn;
use Illuminate\Http\Request;

class StatusPerkawinanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->id)) {
            $status_kwn = StatusKwn::where('id', $request->id)->paginate();
        } else {
            $status_kwn = StatusKwn::where('nama', 'like', '%' . $request->q . '%')->paginate();
        }
        $data = Status_perkawinan_resource::collection($status_kwn);
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
