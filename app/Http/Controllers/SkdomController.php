<?php

namespace App\Http\Controllers;

use App\Models\SuratDomisili;
use App\Traits\GetNoSurat;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SkdomController extends Controller
{
    use GetNoSurat;
    public function index()
    {
        if (request()->ajax()) {
            $data = SuratDomisili::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $nomorSurat = $this->getNoSrt($row);
                    $id = $row->id;
                    $route = 'skdom.edit';
                    $status = $row->status;
                    $jenis = 'skdom';
                    if (auth()->user()->role_id == 1) {
                        return view('includes.button-admin', compact('id', 'route', 'status'));
                    } else if (auth()->user()->role_id == 3) {
                        return view('includes.button-kaopd', compact('id', 'status', 'nomorSurat', 'jenis'));
                    } else {
                        return view('includes.button-verifikator', compact('id', 'status'));
                    }
                })
                ->addColumn('no_surat', function ($row) {
                    return $this->getNoSrt($row);
                })
                ->rawColumns(['action', 'no_surat'])
                ->make(true);
        };
        $title = "USULAN PENGAJUAN SURAT KETERANGAN DOMISILI";
        return view('skdom.index', compact('title'));
    }
}
