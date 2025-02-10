<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Kabko extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'nama',
        'kode_provinsi',
    ];
    protected $casts = ['id' => 'string'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*']);
    }
}
