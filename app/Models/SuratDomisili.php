<?php

namespace App\Models;

use App\Traits\GetNoSurat;
use App\Traits\HasUiStatus;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SuratDomisili extends Model
{
    use HasFactory, LogsActivity, Compoships, GetNoSurat, HasUiStatus;

    protected $fillable = [
        'id_kel',
        'id_rw',
        'id_rt',
        'kd_jenis_surat',
        'no_urut_surat',
        'tgl_surat',
        'nik',
        'nama_perusahaan',
        'status_bangunan',
        'jumlah_karyawan',
        'alamat_domisili',
        'peruntukan',
        'kepada',
        'tgl_berlaku',
        'jenis',
        'variable',
        'status',
        'rating',
        'komentar',
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
            return ['name' => 'Dinaikkan ke Sekkel', 'color' => 'orange'];
        } else if ($this->status == 3) {
            return ['name' => 'Dinaikkan ke Lurah', 'color' => 'orange'];
        } else if ($this->status == 4) {
            return ['name' => 'Disetujui', 'color' => 'green'];
        } else if ($this->status == 5) {
            return ['name' => 'Dinilai', 'color' => '#EFBF04'];
        } else if ($this->status == 6) {
            return ['name' => 'Ditolak', 'color' => 'red'];
        } else if ($this->status == 7) {
            return ['name' => 'Dihapus', 'color' => 'red'];
        } else {
            return ['name' => 'Pengajuan', 'color' => 'black'];
        }
    }
}
