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
            // $data = SuratTemplate::query()->with('skpd')->where('id_kel', '=', auth()->user()->id_instansi)
            //     ->orWhere('id_kel', '=', '0');
            $data = SuratTemplate::where('id_kel', '=', auth()->user()->id_instansi)->orWhere('id_kel', '=', '67')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $data = $row;
                    // dd($data);
                    return view('includes.button-template', compact('data'));
                })
                ->rawColumns(['action'])
                ->make(true);
        };
        $title = "TEMPLATE SURAT";
        return view('template.index', compact('title'));
    }

    public function add()
    {
        $title = "TEMPLATE SURAT";
        return view('template.add', compact('title'));
    }

    public function store(Request $request)
    {

        $template = SuratTemplate::where(['jenis' => $request->jenis, 'state' => 'custom'])->first();
        if ($template) {
            return Redirect::back()->withErrors(['msg' => 'Template sudah ada!!']);
        } else {
            $request->validate([
                'name' => ['required', 'string'],
                'jenis' => ['required', 'string'],
                'file' => ['mimes:docx']
            ]);

            if ($request->file('file')) {
                $path = 'templates/';
                $fileName = $request->file('file')->hashName();
                $fileLocation = $path . $fileName;
                $request->file('file')->move(public_path($path), $fileName);
            }
            $defaultTemplate = SuratTemplate::where(['jenis' => $request->jenis, 'state' => 'default'])->first();
            $defaultProcessor = new TemplateProcessor(public_path($defaultTemplate->path_docs));
            $defaultVariable = $defaultProcessor->getVariables();
            $templateProcessor = new TemplateProcessor(public_path($fileLocation));
            $variable = $templateProcessor->getVariables();

            $array_diff = array_values(array_diff($variable, $defaultVariable));
            // dd($arr_intersect);

            SuratTemplate::create([
                'id_kel' => auth()->user()->id_instansi,
                'name' => $request->name,
                'path_docs' => $request->file('file') ? $fileLocation : '',
                'jenis' => $request->jenis,
                'state' => 'custom',
                'variable' => serialize($array_diff)
            ]);

            return redirect()->route('template.index');
        }
    }

    public function download($id)
    {
        $template = SuratTemplate::find($id);
        $file = public_path() . '/' . $template->path_docs;
        $headers = array('Content-Type: application/pdf',);
        return Response::download($file, $template->name . '.docx', $headers);
    }

    public function hapus($id)
    {
        // return $id;
        $suratTemplate = SuratTemplate::find($id);
        if ($suratTemplate) {
            $suratTemplate->delete();
            return response()->json(['message' => 'Data berhasil dihapus.']);
        } else {
            return response()->json(['message' => 'Data updated failed.']);
        }
    }
}
