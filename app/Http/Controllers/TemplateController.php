<?php

namespace App\Http\Controllers;

use App\Models\SuratTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\DataTables;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (request()->ajax()) {
            $data = SuratTemplate::where(function ($q) {
                    $q->where('id_kel', auth()->user()->id_instansi)
                        ->orWhere('id_kel', '67')
                        ->orWhere('id_kel', '0');
                })
                ->orderByRaw("CASE WHEN state = 'custom' THEN 0 ELSE 1 END")
                ->orderByDesc('updated_at');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $data = $row;
                    return view('includes.button-template', compact('data'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $title = 'TEMPLATE SURAT';
        return view('template.index', compact('title'));
    }

    public function add()
    {
        $title = 'TEMPLATE SURAT';
        return view('template.add', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'jenis' => ['required', 'string'],
            'file' => ['required', 'mimes:docx'],
        ]);

        $path = 'templates/';
        $fileName = $request->file('file')->hashName();
        $fileLocation = $path . $fileName;
        $request->file('file')->move(public_path($path), $fileName);

        $defaultTemplate = SuratTemplate::where(['jenis' => $request->jenis, 'state' => 'default'])->first();
        if (! $defaultTemplate) {
            return Redirect::back()->withErrors(['msg' => 'Template default untuk jenis surat ini belum tersedia.']);
        }

        $defaultProcessor = new TemplateProcessor(public_path($defaultTemplate->path_docs));
        $defaultVariable = $defaultProcessor->getVariables();
        $templateProcessor = new TemplateProcessor(public_path($fileLocation));
        $variable = $templateProcessor->getVariables();
        $arrayDiff = array_values(array_diff($variable, $defaultVariable));

        SuratTemplate::updateOrCreate(
            [
                'id_kel' => auth()->user()->id_instansi,
                'jenis' => $request->jenis,
                'state' => 'custom',
            ],
            [
                'name' => $request->name,
                'path_docs' => $fileLocation,
                'variable' => $arrayDiff,
            ]
        );

        return redirect()->route('template.index')->with('status', 'Template berhasil disimpan. Preview/cetak jenis surat tersebut akan memakai template custom terbaru untuk kelurahan ini.');
    }

    public function download($id)
    {
        $template = SuratTemplate::findOrFail($id);
        $file = public_path() . '/' . $template->path_docs;
        $headers = ['Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        return Response::download($file, $template->name . '.docx', $headers);
    }

    public function hapus($id)
    {
        $suratTemplate = SuratTemplate::where('id', $id)
            ->where('state', 'custom')
            ->where('id_kel', auth()->user()->id_instansi)
            ->first();

        if ($suratTemplate) {
            $suratTemplate->delete();
            return response()->json(['message' => 'Template custom berhasil dihapus. Sistem akan kembali memakai template default.']);
        }

        return response()->json(['message' => 'Template default tidak boleh dihapus atau data tidak ditemukan.'], 422);
    }
}
