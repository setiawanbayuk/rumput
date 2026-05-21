<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;

class JenisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (request()->ajax()) {
            $columns = Schema::getColumnListing('jenis_surats');
            $data = JenisSurat::query()->orderBy('id')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_label', function ($row) {
                    return $row->nama ?? $row->name ?? $row->jenis ?? '-';
                })
                ->addColumn('detail_label', function ($row) use ($columns) {
                    return in_array('detail', $columns, true) ? ($row->detail ?: '-') : 'Kolom detail belum ada di database';
                })
                ->addColumn('persyaratan_label', function ($row) use ($columns) {
                    return in_array('persyaratan', $columns, true) ? ($row->persyaratan ?: '-') : 'Kolom persyaratan belum ada di database';
                })
                ->addColumn('action', function ($row) {
                    $id = $row->id;
                    $route = 'jenis.edit';
                    return view('includes.button-jenis', compact('id', 'route'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $title = 'JENIS SURAT';
        $hasDetail = Schema::hasColumn('jenis_surats', 'detail');
        $hasPersyaratan = Schema::hasColumn('jenis_surats', 'persyaratan');

        return view('jenis.index', compact('title', 'hasDetail', 'hasPersyaratan'));
    }

    public function edit($id)
    {
        $title = 'DETAIL JENIS SURAT';
        $surat = JenisSurat::findOrFail($id);
        $hasDetail = Schema::hasColumn('jenis_surats', 'detail');
        $hasPersyaratan = Schema::hasColumn('jenis_surats', 'persyaratan');

        return view('jenis.edit', compact('title', 'surat', 'hasDetail', 'hasPersyaratan'));
    }

    public function update(Request $request, $id)
    {
        $surat = JenisSurat::findOrFail($id);
        $payload = [];

        if (Schema::hasColumn('jenis_surats', 'detail')) {
            $request->validate(['detail' => ['nullable', 'string']]);
            $payload['detail'] = $request->detail;
        }

        if (Schema::hasColumn('jenis_surats', 'persyaratan')) {
            $request->validate(['persyaratan' => ['nullable', 'string']]);
            $payload['persyaratan'] = $request->persyaratan;
        }

        if (! $payload) {
            return redirect()->route('jenis.index')
                ->with('error', 'Tabel jenis_surats di database saat ini hanya menyimpan nama jenis surat. Tambahkan kolom detail/persyaratan jika ingin mengubah deskripsi dari menu ini.');
        }

        $surat->update($payload);

        return redirect()->route('jenis.index')->with('status', 'Jenis surat berhasil diperbarui.');
    }
}
