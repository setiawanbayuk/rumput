<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Models\Pendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Resident;
use Illuminate\Support\Facades\Validator;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Validasi Input NIK
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|min:16'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'NIK wajib diisi dan minimal 16 karakter',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. Cari data Resident
        $resident = Resident::where('nik', $request->nik)->first();

        if (!$resident) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data penduduk tidak ditemukan!'
            ], 404);
        }

        /** * 3. Ambil Data
         * Karena sudah menggunakan casting 'array' di Model Resident,
         * $resident->data sudah otomatis menjadi array PHP.
         */
        $dt = $resident->data;

        // 4. Return Respon dengan format yang konsisten
        return response()->json([
            'status' => 'success',
            'data'   => [
                'nik'                 => $resident->nik,
                'kk'                  => $resident->kk,
                'name'                => $dt['name'] ?? null,
                'gender'              => $dt['gender'] ?? null,
                'gender_nm'           => $dt['gender_nm'] ?? null,
                'status_kwn'          => $dt['status_kwn'] ?? null,
                'status_kwn_nm'       => $dt['status_kwn_nm'] ?? null,
                'kewarganegaraan'     => $dt['kewarganegaraan'] ?? null,
                'kewarganegaraan_nm'  => $dt['kewarganegaraan_nm'] ?? null,
                'tempat_lhr'          => $dt['tempat_lhr'] ?? null,
                'tgl_lhr'             => $dt['tgl_lhr'] ?? null,
                'agama'               => $dt['agama'] ?? null,
                'agama_nm'            => $dt['agama_nm'] ?? null,
                'pendidikan'          => $dt['pendidikan'] ?? null,
                'pendidikan_nm'       => $dt['pendidikan_nm'] ?? null,
                'pekerjaan'           => $dt['pekerjaan'] ?? null,
                'pekerjaan_nm'        => $dt['pekerjaan_nm'] ?? null,
                'provinsi'            => $dt['provinsi'] ?? null,
                'provinsi_nm'         => $dt['provinsi_nm'] ?? null,
                'kabko'               => $dt['kabko'] ?? null,
                'kabko_nm'            => $dt['kabko_nm'] ?? null,
                'kecamatan'           => $dt['kecamatan'] ?? null,
                'kecamatan_nm'        => $dt['kecamatan_nm'] ?? null,
                'kelurahan'           => $dt['kelurahan'] ?? null,
                'kelurahan_nm'        => $dt['kelurahan_nm'] ?? null,
                'rw'                  => $dt['rw'] ?? null,
                'rw_nm'               => $dt['rw_nm'] ?? null,
                'rt'                  => $dt['rt'] ?? null,
                'rt_nm'               => $dt['rt_nm'] ?? null,
                'alamat'              => $dt['alamat'] ?? null,
            ]
        ], 200);
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
