<?php

namespace App\Http\Controllers;

use App\Models\JenisSurat;
use App\Models\SuratBoro;
use App\Models\SuratDomisili;
use App\Models\SuratKelahiran;
use App\Models\SuratKematian;
use App\Models\SuratKeterangan;
use App\Models\SuratPenghasilan;
use App\Models\SuratSkbn;
use App\Models\SuratSktm;
use App\Models\SuratUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Activitylog\Models\Activity;

class HomeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (auth()->user()->role_id == 2) {
            return redirect()->route('warga');
        } else {
            return view('home');
        }
    }

    public function berita()
    {
        $url = 'https://api-splp.layanan.go.id/t/kedirikota.go.id/web_kediri_kota/1.0/api/berita';
        $response = Http::withoutVerifying()->get($url);

        if ($response->status() !== 200) {
            return response()->json(['error' => 'Something went wrong!'], $response->status());
        }

        return json_decode($response->json()['berita'], true);
    }

    public function warga()
    {
        $berita = $this->berita();
        foreach ($berita as &$item) {
            $item['deskripsi'] = strip_tags($item['deskripsi']);
        }
        $surat = JenisSurat::where(['is_active' => true])->get();
        // dd($surat);
        return view('warga', compact('berita', 'surat'));
    }

    public function activity()
    {
        return Activity::all();
    }


    public function last_activity()
    {
        return Activity::all()->last();
    }
}
