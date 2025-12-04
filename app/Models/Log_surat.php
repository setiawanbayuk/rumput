<?php

namespace App\Models;

use App\Traits\HasUiStatus;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Log_surat extends Model
{
    use HasFactory, LogsActivity, Compoships, HasUiStatus;
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

    // app/Models/Log_surat.php
    public function getNoSuratAttribute()
    {
        $model = match ($this->tabel_surat) {
            'surat_skbns' => SuratSkbn::class,
            'surat_boros' => SuratBoro::class,
            'surat_domisilis' => SuratDomisili::class,
            'surat_penghasilans' => SuratPenghasilan::class,
            'surat_kelahirans' => SuratKelahiran::class,
            'surat_kematians' => SuratKematian::class,
            'surat_sktms' => SuratSktm::class,
            'surat_usahas' => SuratUsaha::class,
            'surat_keterangans' => SuratKeterangan::class,
            default => null,
        };

        if (!$model) return '-';

        $surat = $model::find($this->id_surat);
        return $surat?->no_urut_surat ?? '-';
    }

    public function getStAttribute()
    {
        if ($this->status_surat == 1) {
            return ['name' => 'Proses', 'color' => 'blue', 'keterangan' => 'Pengajuan surat diproses RT dan dinaikkan ke Operator'];
        } else if ($this->status_surat == 2) {
            return ['name' => 'Dinaikkan ke Sekkel', 'color' => 'orange', 'keterangan' => 'Pengajuan surat dinaikkan ke Sekkel'];
        } else if ($this->status_surat == 3) {
            return ['name' => 'Dinaikkan ke Lurah', 'color' => 'orange', 'keterangan' => 'Pengajuan surat dinaikkan ke Lurah'];
        } else if ($this->status_surat == 4) {
            return ['name' => 'Disetujui Lurah', 'color' => 'green', 'keterangan' => 'Pengajuan surat disetujui Lurah'];
        } else if ($this->status_surat == 5) {
            return ['name' => 'Dinilai', 'color' => '#EFBF04'];
        } else if ($this->status_surat == 6) {
            return ['name' => 'Ditolak', 'color' => 'red', 'keterangan' => 'Pengajuan surat disetujui Lurah'];
        } else if ($this->status_surat == 7) {
            return ['name' => 'Dihapus', 'color' => 'red'];
        } else if ($this->status_surat == 8) {
            return ['name' => 'Dinaikkan ke Camat', 'color' => 'brown', 'keterangan' => 'Pengajuan surat disetujui Lurah'];
        } else if ($this->status_surat == 9) {
            return ['name' => 'Disetujui Camat', 'color' => 'purple', 'keterangan' => 'Pengajuan surat disetujui Lurah'];
        } else {
            return ['name' => 'Pengajuan', 'color' => 'black', 'keterangan' => 'Pengajuan surat dibuat'];
        }
    }
}
