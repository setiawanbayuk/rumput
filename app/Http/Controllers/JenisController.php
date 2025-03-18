<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JenisController extends Controller
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
            $data = JenisSurat::get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $id = $row->id;
                    $route = 'jenis.edit';
                    return view('includes.button-jenis', compact('id', 'route'));
                })
                ->rawColumns(['action'])
                ->make(true);
        };
        $title = "JENIS SURAT";
        return view('jenis.index', compact('title'));
    }


    public function edit($id)
    {
        $title = "DETAIL SURAT KETERANGAN";

        $surat = JenisSurat::find($id);
        return view('jenis.edit', compact('title', 'surat'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'detail' => ['required', 'string'],
            'persyaratan' => ['required', 'string']
        ]);

        $surat = JenisSurat::find($id);
        if ($surat) {

            $surat->update([
                'detail' => $request->detail,
                'persyaratan' => $request->persyaratan,
            ]);

            return redirect()->route('jenis.index');
        } else {
            return redirect()->route('jenis.index');
        }
    }
}
