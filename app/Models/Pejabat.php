<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pejabat extends Model
{
    use HasFactory;
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
