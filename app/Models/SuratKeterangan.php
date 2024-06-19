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
        'file',
        'pengantar'
    ];

    protected $appends = ['st'];

    public function getStAttribute()
    {
        if ($this->status == 1) {
            return ['name' => 'Proses', 'color' => 'blue'];
        } else if ($this->status == 2) {
            return ['name' => 'Dinaikan', 'color' => 'orange'];
        } else if ($this->status == 3) {
            return ['name' => 'Distujui', 'color' => 'green'];
        } else if ($this->status == 4) {
            return ['name' => 'Ditolak', 'color' => 'red'];
        } else {
            return ['name' => 'Pengajuan', 'color' => 'black'];
        }
    }
}
