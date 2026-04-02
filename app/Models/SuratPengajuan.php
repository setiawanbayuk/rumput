<?php

namespace App\Models;

use App\Traits\StatusSuratTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPengajuan extends Model
{
    use HasFactory, StatusSuratTrait;

    protected $guarded = [];
    protected $appends = ['st'];
    protected $casts = [
        'variable' => 'array', // Sangat Penting: mengubah JSON ke Array secara otomatis
        'tgl_surat' => 'date',
    ];

    // Relasi ke tabel Resident jika dibutuhkan
    public function penduduk()
    {
        return $this->belongsTo(Resident::class, 'nik', 'nik');
    }

    // Relasi ke SKPD/Kelurahan
    public function kelurahan()
    {
        return $this->belongsTo(Skpd::class, 'id_kel');
    }
}
