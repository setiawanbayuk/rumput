<?php

namespace App\Http\Controllers;

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
        $jumlah_skbn = SuratSkbn::where('nik', auth()->user()->nik)->get()->count();
        $jumlah_boro = SuratBoro::where('nik', auth()->user()->nik)->get()->count();
        $jumlah_domisili = SuratDomisili::where('nik', auth()->user()->nik)->get()->count();
        $jumlah_kelahiran = SuratKelahiran::where('nik_pelapor', auth()->user()->nik)->get()->count();
        $jumlah_kematian = SuratKematian::where('nik_pelapor', auth()->user()->nik)->get()->count();
        $jumlah_sktm = SuratSktm::where('nik', auth()->user()->nik)->get()->count();
        $jumlah_penghasilan = SuratPenghasilan::where('nik', auth()->user()->nik)->get()->count();
        $jumlah_usaha = SuratUsaha::where('nik', auth()->user()->nik)->get()->count();
        $jumlah_suket = SuratKeterangan::where('nik', auth()->user()->nik)->get()->count();
        return view('warga', compact(
            'jumlah_skbn',
            'jumlah_boro',
            'jumlah_domisili',
            'jumlah_kelahiran',
            'jumlah_kematian',
            'jumlah_sktm',
            'jumlah_penghasilan',
            'jumlah_usaha',
            'jumlah_suket',
            'jumlah_suket'
        ));
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
