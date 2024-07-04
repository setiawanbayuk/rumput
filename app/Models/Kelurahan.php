<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Kelurahan extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'nama',
        'kode_provinsi',
        'kode_kabkota',
        'kode_kecamatan',
    ];
    protected $casts = ['id' => 'string'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*']);
    }
}
