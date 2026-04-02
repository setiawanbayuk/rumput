<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SuratTemplate extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'id_kel',
        'name',
        'path_docs',
        'jenis',
        'state',
        'variable',
    ];
    protected $casts = [
        'variable' => 'array', // Otomatis mengubah JSON di DB menjadi Array PHP
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*']);
    }
    public function skpd(): BelongsTo
    {
        return $this->belongsTo(Skpd::class, 'id_kel', 'id');
    }
}
