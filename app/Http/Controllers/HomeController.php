<?php

namespace App\Http\Controllers;

use App\Http\Resources\Skpd_resource;
use App\Models\JenisSurat;
use App\Models\Skpd;
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

    public function warga()
    {
        $url = 'https://api-splp.layanan.go.id/t/kedirikota.go.id/web_kediri_kota/1.0/api/berita';
        $response = Http::withoutVerifying()->get($url);
        if ($response->status() !== 200) {
            $berita = [];
        } else {
            $berita = json_decode($response->json()['berita'], true);
            foreach ($berita as &$item) {
                $item['deskripsi'] = strip_tags($item['deskripsi']);
            }
        }
        $surat = JenisSurat::where(['is_active' => true])->get();
        $skpd = new Skpd_resource(Skpd::find(auth()->user()->id_instansi));
        // dd($skpd);
        return view('warga', compact('berita', 'surat', 'skpd'));
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
