<?php

namespace App\Models;

use App\Traits\GetNoSurat;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SuratKelahiran extends Model
{
    use HasFactory, LogsActivity, Compoships, GetNoSurat;

    protected $fillable = [
        'id_kel',
        'kd_jenis_surat',
        'no_urut_surat',
        'tgl_surat',
        'nama_pelapor',
        'nik_pelapor',
        'kk_pelapor',
        'kewarganegaraan_pelapor',
        'kewarganegaraan_pelapor_nm',
        'no_dokumen_perjalanan',
        'nama_saksi1',
        'nik_saksi1',
        'kk_saksi1',
        'kewarganegaraan_saksi1',
        'kewarganegaraan_saksi1_nm',
        'nama_saksi2',
        'nik_saksi2',
        'kk_saksi2',
        'kewarganegaraan_saksi2',
        'kewarganegaraan_saksi2_nm',
        'nama_ayah',
        'nik_ayah',
        'kk_ayah',
        'tempat_lhr_ayah',
        'tgl_lhr_ayah',
        'kewarganegaraan_ayah',
        'kewarganegaraan_ayah_nm',
        'nama_ibu',
        'nik_ibu',
        'kk_ibu',
        'tempat_lhr_ibu',
        'tgl_lhr_ibu',
        'kewarganegaraan_ibu',
        'kewarganegaraan_ibu_nm',
        'nama_anak',
        'gender_anak',
        'gender_anak_nm',
        'tempat_dilahirkan_anak',
        'tempat_kelahiran_anak',
        'hari_lhr_anak',
        'tgl_lhr_anak',
        'jam_lhr_anak',
        'jenis_klhr_anak',
        'klhr_ke_anak',
        'penolong_klhr_anak',
        'bb_anak',
        'tb_anak',
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
