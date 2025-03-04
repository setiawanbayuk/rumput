<?php

namespace App\Http\Controllers;

use App\Models\SuratTemplate;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TemplateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (request()->ajax()) {
            // dd(auth()->user());
            $data = SuratTemplate::query()->with('skpd')->where('id_kel', '=', auth()->user()->id_instansi)
                ->orWhere('id_kel', '=', '0');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    // $nomorSurat = $this->getNoSrt($row);
                    // $id = $row->id;
                    // $route = 'suket.edit';
                    // $status = $row->status;
                    // $jenis = 'suket';
                    // $role = auth()->user()->role_id;
                    // if (auth()->user()->role_id == 1) {
                    //     return view('includes.button-admin', compact('id', 'route', 'status'));
                    // } else if (auth()->user()->role_id == 3) {
                    //     return view('includes.button-kaopd', compact('id', 'status', 'nomorSurat', 'jenis', 'role'));
                    // } else {
                    //     return view('includes.button-verifikator', compact('id', 'status', 'role'));
                    // }
                })
                ->rawColumns(['action'])
                ->make(true);
        };
        $title = "TEMPLATE SURAT";
        return view('template.index', compact('title'));
    }
}
