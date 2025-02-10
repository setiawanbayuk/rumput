<?php

namespace App\Models;

use App\Traits\GetNoSurat;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SuratSkbn extends Model
{
    use HasFactory, LogsActivity, Compoships, GetNoSurat;

    protected $fillable = [
        'id_kel',
        'kd_jenis_surat',
        'no_urut_surat',
        'tgl_surat',
        'nik',
        'peruntukan',
        'kepada',
        'status',
        'file',
        'pengantar'
    ];

    public function history(): HasMany
    {
        return $this->hasMany(Log_surat::class, ['nik', 'id_surat'], ['nik', 'id']);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*']);
    }
    protected $appends = ['st', 'nomor_surat'];

    public function getNomorSuratAttribute(){
        return $this->getNoSrt($this);
    }


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
