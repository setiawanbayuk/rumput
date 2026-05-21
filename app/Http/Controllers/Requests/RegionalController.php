<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Http\Resources\Kecamatan_resource;
use App\Http\Resources\Kelurahan_resource;
use App\Http\Resources\Rw_resource;
use App\Http\Resources\Rt_resource;
use App\Http\Resources\Regional_resource;
use App\Models\Regional;
use App\Models\RtRw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * GET /api/regional/kelurahan
     *
     * Format response sengaja dibuat sama seperti response lama Super App:
     * [
     *   {
     *     "id": 12,
     *     "text": "CAMPUREJO",
     *     "nama": "CAMPUREJO",
     *     "skpd": null
     *   }
     * ]
     *
     * Yang dibenarkan hanya sumber ID dan nama, yaitu langsung dari tabel skpds.
     * Kelurahan di skpds dikenali dari id_region panjang 13, contoh 35.71.01.1012.
     */
    public function kelurahan(Request $request)
    {
        $data = DB::table('skpds')
            ->whereRaw('CHAR_LENGTH(id_region) = 13')
            ->when($request->filled('id'), function ($query) use ($request) {
                $query->where('id', $request->query('id'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->query('search'));
                $query->where('nama', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => (int) $item->id,
                    'text' => $item->nama,
                    'nama' => $item->nama,
                    'skpd' => null,
                ];
            })
            ->values();

        return response()->json($data, 200);
    }

    /**
     * GET /api/regional/kecamatan
     *
     * Format response tetap sama seperti kebutuhan Super App:
     * id, text, nama, skpd.
     * Kecamatan di skpds dikenali dari id_region panjang 8, contoh 35.71.01.
     */
    public function kecamatan(Request $request)
    {
        $data = DB::table('skpds')
            ->whereRaw('CHAR_LENGTH(id_region) = 8')
            ->where('id_region', 'like', '35.71.%')
            ->when($request->filled('id'), function ($query) use ($request) {
                $query->where('id', $request->query('id'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->query('search'));
                $query->where('nama', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => (int) $item->id,
                    'text' => $item->nama,
                    'nama' => $item->nama,
                    'skpd' => null,
                ];
            })
            ->values();

        return response()->json($data, 200);
    }

    public function rw($idKel)

    {
        $rw = RtRw::where('id_kel', $idKel)
            ->select('id_kel', 'rw')
            ->groupBy('id_kel', 'rw')
            ->whereNotIn('rw', ['0', '00', '000', ''])
            ->whereNotNull('rw')
            ->groupBy('id_kel', 'rw')
            ->orderBy('rw')
            ->get();

        $data = Rw_resource::collection($rw);
        return response()->json($data, 200);
    }

    public function rt($idKel, $rw)
    {
        $rt = RtRw::where('id_kel', $idKel)
            ->select('id_kel', 'rw', 'rt')
            ->groupBy('id_kel', 'rw', 'rt')
            ->where('rw', $rw)
            ->whereNotIn('rt', ['0', '00', '000', ''])
            ->whereNotNull('rt')
            ->groupBy('id_kel', 'rw', 'rt')
            ->orderBy('rt')
            ->get();

        $data = Rt_resource::collection($rt);
        return response()->json($data, 200);
    }
}
