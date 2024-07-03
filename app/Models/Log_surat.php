<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Log_surat extends Model
{
    use HasFactory, LogsActivity, Compoships;
    protected $fillable = [
        'nik',
        'tabel_surat',
        'nama_surat',
        'id_surat',
        'status_surat'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*']);
    }
    protected $appends = ['st'];

    public function getStAttribute()
    {
        if ($this->status_surat == 1) {
            return ['name' => 'Proses', 'color' => 'blue'];
        } else if ($this->status_surat == 2) {
            return ['name' => 'Dinaikan', 'color' => 'orange'];
        } else if ($this->status_surat == 3) {
            return ['name' => 'Disetujui', 'color' => 'green'];
        } else if ($this->status_surat == 4) {
            return ['name' => 'Ditolak', 'color' => 'red'];
        } else {
            return ['name' => 'Pengajuan', 'color' => 'black'];
        }
    }
}
