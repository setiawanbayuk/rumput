<?php

namespace App\Traits;

use App\Http\Resources\User_resource;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Auth;

trait GetNoSurat
{
    public function getNoSrt($data)
    {
        $user = new User_resource(User::with('skpd')->find(Auth::id()));
        $tahunSrt = DateTime::createFromFormat('Y-m-d', $data->tgl_surat);
        $nomorSurat = $data->kd_jenis_surat . '/' . $data->no_urut_surat . '/' . $user->skpd->instansi_kode . '/' . $tahunSrt->format('Y');
        return $nomorSurat;
    }
}
