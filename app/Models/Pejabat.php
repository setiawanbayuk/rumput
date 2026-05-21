<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pejabat extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_skpd',
        'nip',
        'nama',
        'id_jabatan',
        'id_pangkat',
    ];
    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id');
    }
    public function pangkat(): BelongsTo
    {
        return $this->belongsTo(Pangkat::class, 'id_pangkat', 'id');
    }
    public function skpd(): BelongsTo
    {
        return $this->belongsTo(Skpd::class, 'id_skpd', 'id');
    }
}
