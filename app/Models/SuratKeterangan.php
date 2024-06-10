<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeterangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_kel',
        'kd_jenis_surat',
        'no_urut_surat',
        'tgl_surat',
        'nik',
        'peruntukan',
        'keterangan',
        'kepada',
        'penandatangan',
        'status',
    ];

    protected $appends = ['st'];

    public function getStAttribute(){
        return $this->status == 1 ? 'In Proses' : 'Disetujui';
    }
}
