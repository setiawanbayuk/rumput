<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SuratKeterangan extends Model
{
    use HasFactory, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*']);
    }

    protected $appends = ['st'];

    public function getStAttribute()
    {
        if ($this->status == 1) {
            return ['name' => 'Proses', 'color' => 'blue'];
        } else if ($this->status == 2) {
            return ['name' => 'Dinaikan', 'color' => 'orange'];
        } else if ($this->status == 3) {
            return ['name' => 'Disetujui', 'color' => 'green'];
        } else if ($this->status == 4) {
            return ['name' => 'Ditolak', 'color' => 'red'];
        } else {
            return ['name' => 'Pengajuan', 'color' => 'black'];
        }
    }
}
